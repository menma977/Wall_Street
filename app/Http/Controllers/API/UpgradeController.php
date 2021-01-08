<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\BTC;
use App\Models\Camel;
use App\Models\Dice;
use App\Models\Doge;
use App\Models\ETH;
use App\Models\LTC;
use App\Models\Queue;
use App\Models\ShareLevel;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use App\Models\WalletAdmin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpgradeController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    $progress = Upgrade::where('to', Auth::id())->sum('credit');
    $target = Upgrade::where('to', Auth::id())->where('from', Auth::id())->sum('debit');

    $totalMember = User::whereNotNull('email_verified_at')->count();
    $totalDollar = "$ " . number_format(Upgrade::sum('debit') - Upgrade::sum('credit'), 3);
    $getTopBinary = Binary::selectRaw("up_line, count(*) as total")->groupBy('up_line')->orderBy('total', 'desc')->first();
    $topSponsor = User::find($getTopBinary->up_line)->name . ' - ' . $getTopBinary->total;

    $data = [
      'progress' => $progress > 0 && $target > 0 ? number_format(($progress / $target) * 100, 0, ',', '') : 0,
      'progress_value' => $progress,
      'target' => $target,
      'totalMember' => $totalMember,
      'totalDollar' => $totalDollar,
      'topSponsor' => $topSponsor
    ];

    return response()->json($data);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    $list = Upgrade::where('from', Auth::id())->orWhere('to', Auth::id())->simplePaginate(20);
    $list->getCollection()->transform(function ($item) {
      $item->balance = $item->debit != 0 ? $item->debit : $item->credit;
      $item->date = Carbon::parse($item->created_at)->format("d-M-Y");
      $item->color = $item->debit != 0 ? "in" : "out";

      return $item;
    });
    return response()->json([
      'list' => $list
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function create()
  {
    $upgradeList = UpgradeList::all();

    return response()->json(["upgradeList" => $upgradeList]);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request)
  {
    $request->validate([
      "type" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["ltc", "btc", "eth", "doge", "camel"])) {
          $fail($attr . " must be either ltc, btc, eth, doge or camel");
        }
      }],
      "upgrade_list" => "required|integer",
      "balance" => "required|numeric",
      "balance_fake" => "required|numeric",
      'secondary_password' => ["required", function ($attr, $val, $fail) {
        if (!Hash::check($val, User::find(Auth::id())->secondary_password)) {
          $fail("The $attr did not match!");
        }
      }],
    ]);

    if (Queue::where('user_id', Auth::id())->where('status', false)->count()) {
      return response()->json(['message' => 'your are on queue'], 500);
    }

    $camelResponse = Http::get("https://api.cameltoken.io/tronapi/getbalance/" . Auth::user()->wallet_camel);
    if ($camelResponse->ok() && $camelResponse->successful()) {
      $tronBalance = $camelResponse->json()["balance"];
      if ($tronBalance == 0) {
        return response()->json(["message" => "Required minimum 10 tron"], 500);
      }
    } else {
      return response()->json(["message" => "Failed load tron"], 500);
    }

    if ($request->type === "camel") {
      $request->balance_fake = number_format($request->balance_fake / 10 ** 8, 8, '.', '');
    }

    $upgradeList = UpgradeList::where("id", $request->upgrade_list)->first();
    $result = $this->converter($request->type, $request->balance, $request->balance_fake, $upgradeList);
    Log::info("==================Upgrade+++++++++++++++++++++++++++");
    Log::info("{$request->upgrade_list} - $upgradeList");
    Log::info($request->type);
    Log::info($request->balance);
    Log::info($request->balance_fake);
    Log::info($result);
    Log::info("==================Upgrade+++++++++++++++++++++++++++");

    Log::info("==================Upgrade--------------------------------");
    if ($result) {
      $upList = $upgradeList->dollar / 2;
      $balance_left = $upList;
      $level = ShareLevel::all();
      $current = Auth::id();
      $random_share_percent = $level->firstWhere("level", "IT")->percent + $level->firstWhere("level", "BuyWall")->percent;
      $wallet_it = $upList * $level->firstWhere("level", "IT")->percent;
      $buy_wall = $upList * $level->firstWhere("level", "BuyWall")->percent;

      $c_level = 1;
      while (true) {
        $binary = Binary::where("down_line", $current)->first();
        if (!$binary || $c_level >= 9) {
          break;
        }
        $cut = $upList * $level->firstWhere("level", "Level " . $c_level)->percent;
        $random_share_percent += $level->firstWhere("level", "Level " . $c_level)->percent;
        $userBinary = User::where("id", $binary->up_line)->first();
        $current = $userBinary->id ?? "";
        $sumUpLineValue = Upgrade::where('from', $userBinary->id)->where('to', $userBinary->id)->sum('debit') - Upgrade::where('to', $userBinary->id)->sum('credit');
        if ($sumUpLineValue > 0 && $cut > 0) {
          if ($sumUpLineValue >= $cut) {
            Log::info("idU: {$userBinary->id} cut : $cut  | $sumUpLineValue");
            $balance_left -= $cut;
            $q = new Queue([
              "user_id" => Auth::id(),
              "send" => $userBinary->id,
              "value" => $this->toFixed($this->toFixed($cut, 3), 3),
              "type" => $request->type . "_level",
              "total" => $this->toFixed($balance_left, 3),
            ]);
            $q->save();

            $this->cutFakeBalance($request->type, $userBinary->id, "bonus Level " . $c_level, $cut, $upgradeList);
          } else {
            $balance_left -= $sumUpLineValue;
            Log::info("idU: {$userBinary->id} sumUpLineValue : $sumUpLineValue | $sumUpLineValue");
            $q = new Queue([
              "user_id" => Auth::id(),
              "send" => $userBinary->id,
              "value" => $this->toFixed($this->toFixed($sumUpLineValue, 3), 3),
              "type" => $request->type . "_level",
              "total" => $this->toFixed($balance_left, 3),
            ]);
            $q->save();

            $this->cutFakeBalance($request->type, $userBinary->id, "bonus Level " . $c_level, $sumUpLineValue, $upgradeList);
          }
          $c_level++;
        }
      }
      Log::info("==================Upgrade--------------------------------");

      $balance_left -= $wallet_it;
      $it_queue = new Queue([
        "user_id" => Auth::id(),
        "send" => 1,
        "value" => $this->toFixed($wallet_it, 3),
        "type" => $request->type . "_it",
        "total" => $this->toFixed($balance_left, 3),
      ]);
      $it_queue->save();

      $wallet_admin = WalletAdmin::inRandomOrder()->first();

      $balance_left -= $buy_wall;
      $buy_wall_queue = new Queue([
        "user_id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $this->toFixed($buy_wall, 3),
        "type" => $request->type . "_buyWall",
        "total" => $this->toFixed($balance_left, 3),
      ]);
      $buy_wall_queue->save();

      $total_random_share = $upList * (1 - $random_share_percent);
      $balance_left -= $total_random_share;
      $share_queue = new Queue([
        "user_id" => Auth::id(),
        "send" => 2,
        "value" => $this->toFixed($total_random_share + $balance_left, 3),
        "type" => $request->type . "_share",
        "total" => $this->toFixed(0, 3),
      ]);
      $share_queue->save();

      $this->cutFakeBalance($request->type, 1, "BUY WALL|FEE|SHARE", ($total_random_share + $balance_left + $buy_wall + $wallet_it), $upgradeList);

      $upgrade = new Upgrade([
        'from' => Auth::id(),
        'to' => Auth::id(),
        'description' => Auth::user()->username . " did an upgrade",
        'debit' => $upgradeList->dollar * 3,
        'credit' => 0,
        'level' => $upgradeList->id,
        'type' => $request->type
      ]);
      $upgrade->save();

      Binary::where('down_line', Auth::id())->update(['active' => true]);
      $user = User::find(Auth::id());
      $user->level = $request->upgrade_list;
      $user->save();

      for ($i = 0; $i < ($upgradeList->dollar / 10); $i++) {
        $setDice = new Dice();
        $setDice->user_id = Auth::id();
        $setDice->save();
      }

      return response()->json(["message" => "Upgrade now queued"]);
    }

    if ($request->type === "camel") {
      $value = UpgradeList::where("id", $request->upgrade_list)->first()->camel_usd;
      return response()->json([
        "message" => "Insufficient balance amount. you need camel : $value and camel wall : $value"
      ], 500);
    }

    if ($request->type === "doge") {
      $value = number_format(UpgradeList::where("id", $request->upgrade_list)->first()->doge_usd / 10 ** 8, 8, '.', '');
      return response()->json([
        "message" => "Insufficient balance amount. you need doge : $value and doge wall : $value"
      ], 500);
    }

    if ($request->type === "btc") {
      $value = number_format(UpgradeList::where("id", $request->upgrade_list)->first()->btc_usd / 10 ** 8, 8, '.', '');
      return response()->json([
        "message" => "Insufficient balance amount. you need btc : $value and btc wall : $value"
      ], 500);
    }

    if ($request->type === "ltc") {
      $value = number_format(UpgradeList::where("id", $request->upgrade_list)->first()->ltc_usd / 10 ** 8, 8, '.', '');
      return response()->json([
        "message" => "Insufficient balance amount. you need ltc : $value and ltc wall : $value"
      ], 500);
    }

    $value = number_format(UpgradeList::where("id", $request->upgrade_list)->first()->eth_usd / 10 ** 8, 8, '.', '');
    return response()->json([
      "message" => "Insufficient balance amount. you need eth : $value and eth wall : $value"
    ], 500);
  }

  public function packages()
  {
    $packages = UpgradeList::select(["id", "dollar", "camel_usd", "btc_usd", "ltc_usd", "doge_usd", "eth_usd"])->get();
    return response()->json(["packages" => $packages]);
  }

  private function toFixed($number, $precision, $separator = ".")
  {
    return number_format($number, $precision, $separator, "");
  }

  private function cutFakeBalance($type, $upLine, $level, $cut, $package)
  {
    if ($type === "ltc") {
      $shareBalance = new LTC([
        "user_id" => $upLine,
        "description" => $level,
        "debit" => number_format((($cut * $package->idr) / $package->ltc), 8, '', '')
      ]);
      $cutBalance = new LTC([
        "user_id" => Auth::id(),
        "description" => $level,
        "credit" => number_format(($cut * $package->idr) / $package->ltc, 8, '', '')
      ]);
    } else if ($type === "btc") {
      $shareBalance = new BTC([
        "user_id" => $upLine,
        "description" => $level,
        "debit" => number_format(($cut * $package->idr) / $package->btc, 8, '', '')
      ]);
      $cutBalance = new BTC([
        "user_id" => Auth::id(),
        "description" => $level,
        "credit" => number_format(($cut * $package->idr) / $package->btc, 8, '', '')
      ]);
    } else if ($type === "eth") {
      $shareBalance = new ETH([
        "user_id" => $upLine,
        "description" => $level,
        "debit" => number_format(($cut * $package->idr) / $package->eth, 8, '', '')
      ]);
      $cutBalance = new ETH([
        "user_id" => Auth::id(),
        "description" => $level,
        "credit" => number_format(($cut * $package->idr) / $package->eth, 8, '', '')
      ]);
    } else if ($type === "doge") {
      $shareBalance = new Doge([
        "user_id" => $upLine,
        "description" => $level,
        "debit" => number_format(($cut * $package->idr) / $package->doge, 8, '', '')
      ]);
      $cutBalance = new Doge([
        "user_id" => Auth::id(),
        "description" => $level,
        "credit" => number_format(($cut * $package->idr) / $package->doge, 8, '', '')
      ]);
    } else {
      $shareBalance = new Camel([
        "user_id" => $upLine,
        "description" => $level,
        "debit" => number_format($cut / $package->camel, 8, '', '')
      ]);
      $cutBalance = new Camel([
        "user_id" => Auth::id(),
        "description" => $level,
        "credit" => number_format($cut / $package->camel, 8, '', '')
      ]);
    }
    $shareBalance->save();
    $cutBalance->save();
  }

  /**
   * @param $type
   * @param $balance
   * @param $fakeBalance
   * @param $package
   * @return bool
   */
  private function converter($type, $balance, $fakeBalance, $package)
  {
    if ($type === "doge") {
      return $package->doge_usd <= $balance && $package->doge_usd <= $fakeBalance;
    }

    if ($type === "camel") {
      return $package->camel_usd <= $balance && $package->camel_usd <= $fakeBalance;
    }

    if ($type === "btc") {
      return $package->btc_usd <= $balance && $package->btc_usd <= $fakeBalance;
    }

    if ($type === "ltc") {
      return $package->ltc_usd <= $balance && $package->ltc_usd <= $fakeBalance;
    }

    if ($type === "eth") {
      return $package->eth_usd <= $balance && $package->eth_usd <= $fakeBalance;
    }

    return false;
  }
}
