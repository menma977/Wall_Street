<?php

namespace App\Http\Controllers;

use App\Models\WalletAdmin;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class WalletAdminController extends Controller
{
  public function index()
  {
    $walletAdmin = WalletAdmin::all();
    return view("setting.walletadmin.index", ["walletAdmin" => $walletAdmin]);
  }

  public function create()
  {
    return view("setting.walletadmin.add");
  }

  public function update(Request $request)
  {
    $request->validate([
      "id" => "required|integer|exists:wallet_admins,id",
      "name" => "required|string",
      "camel" => "required|string",
      "btc" => "required|string",
      "doge" => "required|string",
      "ltc" => "required|string",
      "eth" => "required|string"
    ]);
    $walletAdmin = WalletAdmin::find($request->id);
    $walletAdmin->name = $request->name;
    $walletAdmin->wallet_camel = $request->camel;
    $walletAdmin->wallet_btc = $request->btc;
    $walletAdmin->wallet_ltc = $request->ltc;
    $walletAdmin->wallet_eth = $request->eth;
    $walletAdmin->wallet_doge = $request->doge;
    $walletAdmin->save();
    return redirect(route("setting.wallet-admin.index"))->withMessage($walletAdmin->name . " edited successfully");
  }

  public function store(Request $request)
  {
    $request->validate([
      "name" => "required|string",
      "camel" => "required|string",
      "btc" => "required|string",
      "doge" => "required|string",
      "ltc" => "required|string",
      "eth" => "required|string"
    ]);
    $walletAdmin = new WalletAdmin();
    $walletAdmin->name = $request->name;
    $walletAdmin->wallet_camel = $request->camel;
    $walletAdmin->wallet_btc = $request->btc;
    $walletAdmin->wallet_ltc = $request->ltc;
    $walletAdmin->wallet_eth = $request->eth;
    $walletAdmin->wallet_doge = $request->doge;
    $walletAdmin->save();
    return redirect(route("setting.wallet-admin.index"))->with("message", $walletAdmin->name . " created");
  }

  public function edit($id)
  {
    try {
      $id = Crypt::decrypt($id);
      $wallet = WalletAdmin::find($id);
      return view("setting.walletadmin.edit", ["wallet" => $wallet]);
    } catch (DecryptException $e) {
      Log::alert("failed decrypt " . $id . ", probably invalid");
      return redirect()->back()->withErrors("failed decrypt " . $id . ", probably invalid");
    }
  }

  public function destroy(Request $request, $id)
  {
    if (!$id) {
      return redirect(route("setting.upgrade-list.index"));
    }
    $id = Crypt::decrypt($id);
    $wallet = WalletAdmin::find($id);
    $name = $wallet->name;
    $wallet->delete();
    return redirect()->back()->with("message", $name . " has been deleted");
  }
}
