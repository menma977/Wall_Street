<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BTC;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BTCController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    return response()->json([
      'balance' => BTC::where('user_id', Auth::id())->sum('debit') - BTC::where('user_id', Auth::id())->sum('credit')
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    return response()->json([
      'list' => BTC::where('user_id', Auth::id())->simplePaginate(20)
    ]);
  }

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
   * @return JsonResponse
   * @throws ValidationException
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'secondary_password' => 'required|digits:6|exists:users,secondary_password_junk',
      'value' => 'required|numeric',
      'wallet' => 'required|string|exists:users,wallet_btc',
      'fake' => 'required|string',
    ]);

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $targetUser = User::where('wallet_btc', $request->input('wallet'))->first();

        $formatBTC = number_format($request->input('value') / 10 ** 8, 8, '.', '');

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

      $withdraw = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
        'a' => 'Withdraw',
        's' => Auth::user()->cookie,
        'Amount' => $request->input('value'),
        'Address' => $request->input('wallet'),
        'Currency' => 'btc',
      ]);
      Log::info($withdraw->body());

      if ($withdraw->successful() && str_contains($withdraw->body(), 'Pending') === true) {
        return response()->json(['message' => 'success transfer BTC']);
      }

      return response()->json(['message' => 'connection has a problem or value to small']);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
