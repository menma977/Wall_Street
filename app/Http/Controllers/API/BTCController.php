<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BTC;
use App\Models\UpgradeList;
use App\Models\User;
use Carbon\Carbon;
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
    $list = BTC::where('user_id', Auth::id())->simplePaginate(20);
    $list->getCollection()->transform(function ($item) {
      $item->balance = $item->debit != 0 ? $item->debit : $item->credit;
      $item->date = Carbon::parse($item->created_at)->format("d-M-Y");
      $item->color = $item->debit != 0 ? "in" : "out";

      return $item;
    });

    return response()->json([
      'list' => $list
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function create()
  {
    $balance = BTC::where('user_id', Auth::id())->sum('debit') - BTC::where('user_id', Auth::id())->sum('credit');
    $balanceList = BTC::where('user_id', Auth::id())->paginate(20);
    $package = UpgradeList::all();

    $data = [
      'balance' => $balance,
      'balanceList' => $balanceList,
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
      'wallet' => 'required|string',
      'fake' => 'required|string',
    ]);

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $targetUser = User::where('wallet_btc', $request->input('wallet'))->first();

        $formatBTC = number_format($request->input('value') / 10 ** 8, 8, '.', '');

        $balance = new BTC();
        $balance->user_id = $targetUser->id;
        $balance->description = "receive btc " . $formatBTC . " from " . Auth::user()->username;
        $balance->debit = $request->input('value');
        $balance->save();

        $balance = new BTC();
        $balance->user_id = Auth::id();
        $balance->description = "send btc " . $formatBTC . " to " . $targetUser->username;
        $balance->credit = $request->input('value');
        $balance->save();

        return response()->json(['message' => 'success transfer BTC Wall']);
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
