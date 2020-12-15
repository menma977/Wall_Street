<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doge;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DogeController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function create()
  {
    $btc = Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit');
    $btcList = Doge::where('user_id', Auth::id())->paginate(20);
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

      $formatDoge = number_format($request->input('value') / 10 ** 8, 8, ',', '.');

      $btc = new Doge();
      $btc->user_id = $targetUser->id;
      $btc->description = "receive btc " . $formatDoge . " from " . Auth::user()->username;
      $btc->debit = $request->input('value');
      $btc->save();

      $btc = new Doge();
      $btc->user_id = Auth::id();
      $btc->description = "send btc " . $formatDoge . " to " . $targetUser->username;
      $btc->credit = $request->input('value');
      $btc->save();

      return response()->json(['message' => 'success transfer Doge']);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
