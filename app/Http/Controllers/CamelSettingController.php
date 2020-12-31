<?php

namespace App\Http\Controllers;

use App\Models\CamelSetting;
use Illuminate\Http\Request;

class CamelSettingController extends Controller
{
  public function show()
  {
    $camelSetting = CamelSetting::find(1);
    return view("setting.camel", ["camelSetting" => $camelSetting]);
  }

  public function update(Request $request)
  {
    $request->validate([
      "privateKey" => "required|string",
      "publicKey" => "required|string",
      "walletCamel" => "required|string",
      "hexCamel" => "required|string",
      "toDollar" => "required|numeric"
    ]);
    $camelSetting = CamelSetting::find(1);
    $camelSetting->private_key = $request->privateKey;
    $camelSetting->public_key = $request->publicKey;
    $camelSetting->wallet_camel = $request->walletCamel;
    $camelSetting->hex_camel = $request->hexCamel;
    $camelSetting->to_dollar = $request->toDollar;
    $camelSetting->save();
    return redirect()->back();
  }
}
