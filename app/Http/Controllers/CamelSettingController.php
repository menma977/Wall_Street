<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CamelSetting;
use Illuminate\Http\Request;

class CamelSettingController extends Controller
{
  public function show()
  {
    $camelSetting = CamelSetting::find(1);
    $bankSetting = BankAccount::find(1);
    return view("setting.camel", [
      "camelSetting" => $camelSetting,
      "bankSetting" => $bankSetting,
    ]);
  }

  public function update(Request $request)
  {
    $request->validate([
      "privateKey" => "required|string",
      "publicKey" => "required|string",
      "walletCamel" => "required|string",
      "hexCamel" => "required|string",
      "share_time" => "required|numeric|min:1",
      "share_value" => "required|numeric|min:1",
    ]);
    $camelSetting = CamelSetting::find(1);
    $camelSetting->private_key = $request->privateKey;
    $camelSetting->public_key = $request->publicKey;
    $camelSetting->wallet_camel = $request->walletCamel;
    $camelSetting->hex_camel = $request->hexCamel;
    $camelSetting->share_time = $request->share_time;
    $camelSetting->share_value = $request->share_value;
    $camelSetting->save();
    return redirect()->back();
  }
}
