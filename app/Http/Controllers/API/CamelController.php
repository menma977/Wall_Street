<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Camel;
use App\Models\HistoryCamel;
use App\Models\Queue;
use App\Models\UpgradeList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CamelController extends Controller
{
  /**
   * @return JsonResponse
   */
  public function index()
  {
    return response()->json([
      'balance' => Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit'),
      'on_queue' => Queue::where('user_id', Auth::id())->where('status', false)->count(),
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    $list = Camel::where('user_id', Auth::id())->simplePaginate(20);
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
    $camel = Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit');
    $camelList = Camel::where('user_id', Auth::id())->paginate(20);
    $package = UpgradeList::all();

    $data = [
      'camel' => $camel,
      'camelList' => $camelList,
      'package' => $package
    ];

    return response()->json($data);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'secondary_password' => 'required|digits:6|exists:users,secondary_password_junk',
      'value' => 'required|numeric|min:1',
      'wallet' => 'required|string',
      'fake' => 'required|string',
      'tron' => 'required|string'
    ]);

    if (Queue::where('user_id', Auth::id())->where('status', false)->count()) {
      return response()->json(['message' => 'your are on queue'], 500);
    }

    Log::info("value : " . $request->input('value') . " - fake : " . $request->input('fake') . " - tron : " . $request->input('tron'));

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $currentBalance = Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit');
        if ($request->input('value') > $currentBalance) {
          return response()->json(['message' => 'your balance to small'], 500);
        }

        $targetUser = User::where('wallet_camel', $request->input('wallet'))->first();
        if (!$targetUser) {
          return response()->json(['message' => 'wallet undefined'], 500);
        }

        $formatBalance = number_format($request->input('value') / 10 ** 8, 8, '.', '');

        $balance = new Camel();
        $balance->user_id = $targetUser->id;
        $balance->description = "receive camel " . $formatBalance . " from " . Auth::user()->username;
        $balance->debit = $request->input('value');
        $balance->save();

        $balance = new Camel();
        $balance->user_id = Auth::id();
        $balance->description = "send camel " . $formatBalance . " to " . $targetUser->username;
        $balance->credit = $request->input('value');
        $balance->save();

        return response()->json(['message' => 'success transfer Camel Wall']);
      }

      Log::info("============WD===================");
      Log::info(Auth::user()->private_key);
      Log::info($request->input('wallet'));
      Log::info($request->input('value'));
      Log::info("=================================");
      if ($request->input("tron") === "true") {
        $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtrx', [
          'privkey' => Auth::user()->private_key,
          'to' => $request->input('wallet'),
          'amount' => $request->input('value'),
        ]);
      } else {
        $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
          'privkey' => Auth::user()->private_key,
          'to' => $request->input('wallet'),
          'amount' => $request->input('value'),
        ]);
      }

      Log::info(Auth::user()->username . ' Camel send ' . $request->input('value') . ' address ' . $request->input('wallet'));
      Log::info($withdraw);

      if ($withdraw->ok() && str_contains($withdraw->body(), 'success') === true) {
        $history = new HistoryCamel();
        $history->user_id = Auth::id();
        $history->wallet = $request->input('wallet');
        $history->value = $request->input('value');
        $history->code = $withdraw->json()['txid'];
        $history->save();

        return response()->json(['message' => 'success transfer Camel']);
      }

      return response()->json(['message' => 'connection has a problem or value to small or tron to small'], 500);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
