<?php

namespace App\Http\Controllers;

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
        if (!in_array($val, ["ltc", "btc", "eth", "doge"]))
          $fail($attr . " must be either ltc, btc, eth, or doge");
      }],
      "upgrade_list" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["btc_usd", "doge_usd", "ltc_usd", "eth_usd"]))
          $fail($attr . " must be either btc_usd, doge_usd, ltc_usd or eth_usd");
      }],
      "balance" => "requied|numeric"
    ]);
    $ok = Upgrade::where($request->upgrade_list, "<=", $request->balance)->first();
    $balanceleft = $request->balance;
    if ($ok) {
      $level = ShareLevel::all();
      $current = Auth::id();

      $random_share_percent = $level->firstWhere("name", "IT")->percent + $level->firstWhere("name", "BuyWall")->percent;
      $wallet_it = $current * $level->firstWhere("name", "IT")->percent;
      $buywall = $current * $level->firstWhere("name", "BuyWall")->percent;

      $c_level = 1;
      while (true) {
        $binary = Binary::where("downline", $current);
        if (!$binary) break;
        $potongan = $request->balance * $level->firstWhere("name", "Level " . $c_level)->percent;
        $random_share_percent += $level->firstWhere("name", "Level " . $c_level)->percent;
        if ($c_level++ == 1) {
          $userBinary = User::where("id", $binary->sponsor);
        } else {
          $userBinary = User::where("id", $binary->upline);
          $current = $userBinary->id;
        }
        $balanceleft -= $potongan;
        $q = new Queue([
          "user_Id" => Auth::id(),
          "send" => $userBinary->id,
          "value" => $potongan,
          "type" => $request->type . "_level",
          "total" => $balanceleft,
        ]);
        $q->save();
      }

      $wallet_admin = WalletAdmin::where("name", $request->type);

      $balanceleft -= $wallet_it;
      $it_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $wallet_it,
        "type" => $request->type . "_it",
        "total" => $balanceleft,
      ]);
      $it_queue->save();

      $balanceleft -= $buywall;
      $buywall_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $buywall,
        "type" => $request->type . "_buywall",
        "total" => $balanceleft,
      ]);
      $buywall_queue->save();

      $total_random_share = $request->balance * $random_share_percent;
      $balanceleft -= $total_random_share;
      $share_queue = new Queue([
        "user_Id" => Auth::id(),
        "send" => $wallet_admin->id,
        "value" => $total_random_share,
        "type" => $request->type . "_share",
        "total" => $balanceleft,
      ]);
      $share_queue->save();
    }
  }
}
