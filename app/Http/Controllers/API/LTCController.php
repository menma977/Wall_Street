<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LTC;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LTCController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    return response()->json([
      'balance' => LTC::where('user_id', Auth::id())->sum('debit') - LTC::where('user_id', Auth::id())->sum('credit')
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    $list = LTC::where('user_id', Auth::id())->get();
    $list->map(function ($item) {
      $item->balance = $item->debit != 0 ? $item->debit : $item->credit;

      return $item;
    })->simplePaginate(20);

    return response()->json([
      'list' => $list
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function create()
  {
    $btc = LTC::where('user_id', Auth::id())->sum('debit') - LTC::where('user_id', Auth::id())->sum('credit');
    $btcList = LTC::where('user_id', Auth::id())->paginate(20);
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
      'wallet' => 'required|string|exists:users,wallet_ltc',
      'fake' => 'required|string',
    ]);

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $targetUser = User::where('wallet_ltc', $request->input('wallet'))->first();

        $formatLTC = number_format($request->input('value') / 10 ** 8, 8, '.', '');

        $btc = new LTC();
        $btc->user_id = $targetUser->id;
        $btc->description = "receive ltc " . $formatLTC . " from " . Auth::user()->username;
        $btc->debit = $request->input('value');
        $btc->save();

        $btc = new LTC();
        $btc->user_id = Auth::id();
        $btc->description = "send ltc " . $formatLTC . " to " . $targetUser->username;
        $btc->credit = $request->input('value');
        $btc->save();

        return response()->json(['message' => 'success transfer LTC']);
      }

      $withdraw = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
        'a' => 'Withdraw',
        's' => Auth::user()->cookie,
        'Amount' => $request->input('value'),
        'Address' => $request->input('wallet'),
        'Currency' => 'ltc',
      ]);
      Log::info($withdraw->body());

      if ($withdraw->successful() && str_contains($withdraw->body(), 'Pending') === true) {
        return response()->json(['message' => 'success transfer LTC']);
      }

      return response()->json(['message' => 'connection has a problem or value to small']);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
