<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CamelSetting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CamelSettingController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function show()
  {
    $camelSetting = CamelSetting::find(1);
    $bankSetting = BankAccount::find(1);
    return view("setting.camel", [
      "camelSetting" => $camelSetting,
      "bankSetting" => $bankSetting,
    ]);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      "type_wallet" => ["required", function ($attr, $val, $fail) {
        if (!in_array($val, ["tron", "camel"])) {
          $fail($attr . " must be either tron or camel");
        }
      }],
      'wallet' => 'required|string',
      'amount' => 'required|string',
    ]);

    if ($request->input("type_wallet") === "tron") {
      $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtrx', [
        'privkey' => CamelSetting::find(1)->private_key,
        'to' => $request->input('wallet'),
        'amount' => $request->input('amount'),
      ]);
    } else {
      $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
        'privkey' => CamelSetting::find(1)->private_key,
        'to' => $request->input('wallet'),
        'amount' => $request->input('value'),
      ]);
    }

    if ($withdraw->ok() && str_contains($withdraw->body(), 'success') === true) {
      return redirect()->back()->withInput(['success' => "camel/tron has been send"]);
    }

    return redirect()->back()->withInput(['success' => "failed to send camel/tron"]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   */
  public function update(Request $request)
  {
    $request->validate([
      "privateKey" => "required|string",
      "publicKey" => "required|string",
      "walletCamel" => "required|string",
      "hexCamel" => "required|string",
      "share_time" => "required|numeric|min:1",
      "share_value" => "required|numeric",
    ]);
    $camelSetting = CamelSetting::find(1);
    $camelSetting->private_key = $request->privateKey;
    $camelSetting->public_key = $request->publicKey;
    $camelSetting->wallet_camel = $request->walletCamel;
    $camelSetting->hex_camel = $request->hexCamel;
    $camelSetting->share_time = $request->share_time;
    $camelSetting->share_value = $request->share_value;
    $camelSetting->save();

    return redirect()->back()->withInput(['success' => "setting has been updated"]);
  }
}
