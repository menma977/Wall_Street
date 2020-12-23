<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\BTC;
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
use Illuminate\Support\Facades\Log;

class UpgradeController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    $progress = Upgrade::where('to', Auth::id())->sum('credit');
    $target = Upgrade::where('to', Auth::id())->sum('debit');

    $data = [
      'progress' => $progress != 0 ? number_format(($progress / $target) * 100, 0, ',', '') : 0,
      'progress_value' => $progress,
      'target' => $target,
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
        if (!in_array($val, ["ltc", "btc", "eth", "doge"])) {
          $fail($attr . " must be either ltc, btc, eth, or doge");
        }
      }],
      "upgrade_list" => "required|integer",
      "balance" => "required|numeric",
      'secondary_password' => ["required", function ($attr, $val, $fail) {
        if (!Hash::check($val, User::find(Auth::id())->secondary_password)) {
          $fail("The $attr did not match!");
        }
      }],
    ]);
    $upgradeList = UpgradeList::where($request->type . "_usd", "<=", $request->balance)->where("id", $request->upgrade_list)->first();
    if ($upgradeList) {
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
        if ($c_level++ === 1) {
          $userBinary = User::where("id", $binary->sponsor)->first();
        } else {
          $userBinary = User::where("id", $binary->up_line)->first();
          $current = $userBinary->id ?? "";
        }
        if ($userBinary->level >= $upgradeList->id) {
          $balance_left -= $cut;
          $q = new Queue([
            "user_id" => Auth::id(),
            "send" => $userBinary->id,
            "value" => $this->toFixed($this->toFixed($cut, 3), 3),
            "type" => $request->type . "_level",
            "total" => $this->toFixed($balance_left, 3),
          ]);
          $q->save();
          if ($request->type == "ltc") {
            $shareBalance = new LTC([
              "user_id" => $userBinary->id,
              "description" => "bonus Level " . $c_level,
              "debit" => number_format(($cut * $upgradeList->idr) / $upgradeList->ltc, 8, '', '')
            ]);
          } else if ($request->type == "btc") {
            $shareBalance = new BTC([
              "user_id" => $userBinary->id,
              "description" => "bonus Level " . $c_level,
              "debit" => number_format(($cut * $upgradeList->idr) / $upgradeList->ltc, 8, '', '')
            ]);
          } else if ($request->type == "eth") {
            $shareBalance = new ETH([
              "user_id" => $userBinary->id,
              "description" => "bonus Level " . $c_level,
              "debit" => number_format(($cut * $upgradeList->idr) / $upgradeList->ltc, 8, '', '')
            ]);
          } else {
            $shareBalance = new Doge([
              "user_id" => $userBinary->id,
              "description" => "bonus Level " . $c_level,
              "debit" => number_format(($cut * $upgradeList->idr) / $upgradeList->ltc, 8, '', '')
            ]);
          }
          $shareBalance->save();
        }
      }

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

      Log::info($wallet_admin);

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
        "value" => $this->toFixed($total_random_share, 3),
        "type" => $request->type . "_share",
        "total" => $this->toFixed($balance_left, 3),
      ]);
      $share_queue->save();

      $upgrade = new Upgrade([
        'from' => Auth::id(),
        'to' => Auth::id(),
        'description' => Auth::user()->username . " did an upgrade",
        'debit' => $upList * 3,
        'credit' => 0,
        'level' => $upgradeList->id,
        'type' => $request->type
      ]);
      $upgrade->save();

      Binary::where('down_line', Auth::id())->update(['active' => true]);

      return response()->json(["message" => "Upgrade now queued"]);
    }

    return response()->json(["message" => "Insufficient balance amount"], 400);
  }

  public function packages()
  {
    $packages = UpgradeList::select(["id", "dollar"])->get();
    return response()->json(["packages" => $packages]);
  }

  private function toFixed($number, $precision, $separator = ",")
  {
    return number_format($number, $precision, $separator, ".");
  }
}
