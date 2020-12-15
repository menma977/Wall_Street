<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BTC;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BTCController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function create()
  {
    $btc = BTC::where('user_id', Auth::id())->sum('debit') - BTC::where('user_id', Auth::id())->sum('credit');
    $btcList = BTC::where('user_id', Auth::id())->paginate(20);
    $package = UpgradeList::all();

    $data = [
      'btc' => $btc,
      'btcList' => $btcList,
      'package' => $package
    ];

    return response()->json($data);
  }

  /**
   * @param Request $request
   * @param $username
   * @return JsonResponse
   * @throws ValidationException
   */
  public function store(Request $request, $username)
  {
    $this->validate($request, [
      'secondaryPassword' => 'required|digits:6|exists:users,secondary_password',
      'value' => 'required|numeric',
      'wallet' => 'required|string|exists:users,wallet_btc'
    ]);

    if (Auth::attempt(['username' => $username, 'secondary_password' => $request->input('secondaryPassword')])) {
      $targetUser = User::where('wallet_btc', $request->input('wallet'))->first();

      $formatBTC = number_format($request->input('value') / 10 ** 8, 8, ',', '.');

      $btc = new BTC();
      $btc->user_id = $targetUser->id;
      $btc->description = "receive btc " . $formatBTC . " from " . Auth::user()->username;
      $btc->debit = $request->input('value');
      $btc->save();

      $btc = new BTC();
      $btc->user_id = Auth::id();
      $btc->description = "send btc " . $formatBTC . " to " . $targetUser->username;
      $btc->credit = $request->input('value');
      $btc->save();

      return response()->json(['message' => 'success transfer BTC']);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
