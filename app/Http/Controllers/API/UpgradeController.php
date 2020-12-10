<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\Queue;
use App\Models\ShareLevel;
use App\Models\Upgrade;
use App\Models\User;
use App\Models\WalletAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
  public function index(Request $request)
  {
    $request->validate([
      "type" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["ltc", "btc", "eth", "doge"])) {
          $fail($attr . " must be either ltc, btc, eth, or doge");
        }
      }],
      "upgrade_list" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["btc_usd", "doge_usd", "ltc_usd", "eth_usd"])) {
          $fail($attr . " must be either btc_usd, doge_usd, ltc_usd or eth_usd");
        }
      }],
      "balance" => "required|numeric"
    ]);
    $ok = Upgrade::where($request->upgrade_list, "<=", $request->balance)->first();
    $balance_left = $request->balance;
    if ($ok) {
      $level = ShareLevel::all();
      $current = Auth::id();

      $random_share_percent = $level->firstWhere("name", "IT")->percent + $level->firstWhere("name", "BuyWall")->percent;
      $wallet_it = $current * $level->firstWhere("name", "IT")->percent;
      $buy_wall = $current * $level->firstWhere("name", "BuyWall")->percent;

      $c_level = 1;
      while (true) {
        $binary = Binary::where("down_line", $current);
        if (!$binary) {
          break;
        }
        $cut = $request->balance * $level->firstWhere("name", "Level " . $c_level)->percent;
        $random_share_percent += $level->firstWhere("name", "Level " . $c_level)->percent;
        if ($c_level++ === 1) {
          $userBinary = User::where("id", $binary->sponsor);
        } else {
          $userBinary = User::where("id", $binary->upline);
          $current = $userBinary->id;
        }
        $balance_left -= $cut;
        $q = new Queue([
          "user_Id" => Auth::id(),
          "send" => $userBinary->id,
          "value" => $cut,
          "type" => $request->type . "_level",
          "total" => $balance_left,
        ]);
        $q->save();
      }

      $wallet_admin = WalletAdmin::where("name", $request->type);

      $balance_left -= $wallet_it;
      $it_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $wallet_it,
        "type" => $request->type . "_it",
        "total" => $balance_left,
      ]);
      $it_queue->save();

      $balance_left -= $buy_wall;
      $buy_wall_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $buy_wall,
        "type" => $request->type . "_buyWall",
        "total" => $balance_left,
      ]);
      $buy_wall_queue->save();

      $total_random_share = $request->balance * $random_share_percent;
      $balance_left -= $total_random_share;
      $share_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => 1,
        "value" => $total_random_share,
        "type" => $request->type . "_share",
        "total" => $balance_left,
      ]);
      $share_queue->save();
    }
  }
}
