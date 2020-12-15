<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\Queue;
use App\Models\ShareLevel;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use App\Models\WalletAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpgradeController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    $progress = Upgrade::where('from', Auth::user())->sum('credit');
    $target = Upgrade::where('from', Auth::user())->sum('debit');

    $data = [
      'progress' => ($progress / $target) * 100,
      'target' => $target,
    ];

    return response()->json($data);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function upgrade(Request $request)
  {
    $request->validate([
      "type" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["ltc", "btc", "eth", "doge"])) {
          $fail($attr . " must be either ltc, btc, eth, or doge");
        }
      }],
      "upgrade_list" => "required|integer",
      "balance" => "required|numeric"
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
            "user_Id" => Auth::id(),
            "send" => $userBinary->id,
            "value" => $this->toFixed($this->toFixed($cut, 3), 3),
            "type" => $request->type . "_level",
            "total" => $this->toFixed($balance_left, 3),
          ]);
          $q->save();
        }
      }

      $balance_left -= $wallet_it;
      $it_queue = new Queue([
        "user_Id" => Auth::id(),
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
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $this->toFixed($buy_wall, 3),
        "type" => $request->type . "_buyWall",
        "total" => $this->toFixed($balance_left, 3),
      ]);
      $buy_wall_queue->save();

      $total_random_share = $upList * (1 - $random_share_percent);
      $balance_left -= $total_random_share;
      $share_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $this->toFixed($total_random_share, 3),
        "type" => $request->type . "_share",
        "total" => $this->toFixed($balance_left, 3),
      ]);
      $share_queue->save();

      $upgrade = new Upgrade([
        'from' => Auth::id(),
        'to' => Auth::id(),
        'description' => Auth::user()->username . " did an upgrade",
        'debit' => $upList * 5,
        'credit' => 0,
        'level' => $upgradeList->id,
        'type' => $request->type
      ]);
      $upgrade->save();

      Binary::where('down_line', Auth::id())->update(['active' => true]);

      return response()->json(["message" => "Upgrade now queued"]);
    }

    return response()->json(["message" => "Incorrect balance amount"], 400);
  }

  private function toFixed($number, $precision, $separator = ",")
  {
    return number_format($number, $precision, $separator, ".");
  }
}
