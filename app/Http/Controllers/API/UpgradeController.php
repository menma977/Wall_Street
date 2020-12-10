<?php

namespace App\Http\Controllers;

use App\Models\Binary;
use App\Models\Queue;
use App\Models\ShareLevel;
use App\Models\Upgrade;
use App\Models\User;
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

      $clevel = 1;
      while (true) {
        $binary = Binary::where("downline", $current);
        if (!$binary) break;
        $potongan = $request->balance * $level->firstWhere("name", "Level " . $clevel)->percent;
        $random_share_percent += $level->firstWhere("name", "Level " . $clevel)->percent;
        if ($clevel++ == 1) {
          $userBinary = User::where("id", $binary->sponsor);
        } else {
          $userBinary = User::where("id", $binary->upline);
          $current = $userBinary->id;
        }
        $q = new Queue([
          "user_Id"=>Auth::id(),
          "send"=>$userBinary->id,
          "value"=>$potongan,
          "type"=>$request->type,
          "total"=>$potongan,
        ]);
        $q->save();
      }

      $total_random_share = $request->balance * $random_share_percent;
    }
  }
}
