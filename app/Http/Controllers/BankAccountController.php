<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
  public function update(Request $request)
  {
    $request->validate([
      "username" => "required|string",
      "password" => "required|string",
      "btc" => "required|string",
      "eth" => "required|string",
      "ltc" => "required|string",
      "doge" => "required|string",
    ]);

    $bankSetting = BankAccount::find(1);
    $bankSetting->username = $request->input('username');
    $bankSetting->password = $request->input('password');
    $bankSetting->wallet_btc = $request->input('btc');
    $bankSetting->wallet_eth = $request->input('eth');
    $bankSetting->wallet_ltc = $request->input('ltc');
    $bankSetting->wallet_doge = $request->input('doge');
    $bankSetting->save();

    return redirect()->back();
  }
}
