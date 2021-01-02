<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doge;
use App\Models\Queue;
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

class DogeController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    return response()->json([
      'balance' => Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit'),
      'on_queue' => Queue::where('user_id', Auth::id())->where('status', false)->count(),
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    $list = Doge::where('user_id', Auth::id())->simplePaginate(20);
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
    $balance = Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit');
    $balanceList = Doge::where('user_id', Auth::id())->paginate(20);
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

    if (Queue::where('user_id', Auth::id())->where('status', false)->count()) {
      return response()->json(['message' => 'your are on queue'], 500);
    }

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $currentBalance = Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit');
        if ($request->input('value') > $currentBalance) {
          return response()->json(['message' => 'your balance to small'], 500);
        }

        $targetUser = User::where('wallet_doge', $request->input('wallet'))->first();
        if (!$targetUser) {
          return response()->json(['message' => 'wallet undefined'], 500);
        }

        $formatDoge = number_format($request->input('value') / 10 ** 8, 8, '.', '');

        $balance = new Doge();
        $balance->user_id = $targetUser->id;
        $balance->description = "receive doge " . $formatDoge . " from " . Auth::user()->username;
        $balance->debit = $request->input('value');
        $balance->save();

        $balance = new Doge();
        $balance->user_id = Auth::id();
        $balance->description = "send doge " . $formatDoge . " to " . $targetUser->username;
        $balance->credit = $request->input('value');
        $balance->save();

        return response()->json(['message' => 'success transfer Doge Wall']);
      }

      $withdraw = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
        'a' => 'Withdraw',
        's' => Auth::user()->cookie,
        'Amount' => $request->input('value'),
        'Address' => $request->input('wallet'),
        'Currency' => 'doge',
      ]);
      Log::info(Auth::user()->username . ' doge send ' . $request->input('value') . ' address ' . $request->input('wallet'));

      if ($withdraw->successful() && str_contains($withdraw->body(), 'Pending') === true) {
        return response()->json(['message' => 'success transfer Doge']);
      }

      return response()->json(['message' => 'connection has a problem or value to small'], 500);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
