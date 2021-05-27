<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Camel;
use App\Models\HistoryCamel;
use App\Models\Queue;
use App\Models\UpgradeList;
use App\Models\User;
use App\Http\Controllers\CamelController as CamelGet;
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
      'balance' => Camel::where('user_id', Auth::id())->where("type", "camel")->sum('debit') - Camel::where('user_id', Auth::id())->where("type", "camel")->sum('credit'),
      'balance_gold' => Camel::where('user_id', Auth::id())->where("type", "gold")->sum('debit') - Camel::where('user_id', Auth::id())->where("type", "gold")->sum('credit'),
      'on_queue' => Queue::where('user_id', Auth::id())->where('status', false)->count(),
    ]);
  }

  /**
   * @return JsonResponse
   */
  public function show()
  {
    $list = Camel::where('user_id', Auth::id())->where("type", "camel")->orderBy('id', 'DESC')->simplePaginate(20);
    $list->getCollection()->transform(function ($item) {
      $item->balance = $item->debit !== 0 ? $item->debit : $item->credit;
      $item->date = Carbon::parse($item->created_at)->format("d-M-Y");
      $item->color = $item->debit !== 0 ? "in" : "out";

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
    $camel = Camel::where('user_id', Auth::id())->where("type", "camel")->sum('debit') - Camel::where('user_id', Auth::id())->where("type", "camel")->sum('credit');
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
      'value' => 'required|numeric',
      'wallet' => 'required|string',
      'fake' => 'required|string',
      'tron' => 'required|string',
      'type' => 'nullable|string|in:camel,tron,gold',
    ]);

    if (Queue::where('user_id', Auth::id())->where('status', false)->count()) {
      return response()->json(['message' => 'your are on queue'], 500);
    }

    Log::info("CAMEL/CAMEL GOLD/TRON value : " . $request->input('value') . " - fake : " . $request->input('fake') . " - tron : " . $request->input('tron') . " - wallet : " . $request->input('wallet'));

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') === 'true') {
        $currentBalance = Camel::where('user_id', Auth::id())->where("type", "camel")->sum('debit') - Camel::where('user_id', Auth::id())->where("type", "camel")->sum('credit');
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
        $balance->type = $request->input("type") ?? "camel";
        $balance->save();

        $balance = new Camel();
        $balance->user_id = Auth::id();
        $balance->description = "send camel " . $formatBalance . " to " . $targetUser->username;
        $balance->credit = $request->input('value');
        $balance->type = $request->input("type") ?? "camel";
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
        Log::info(Auth::user()->username . ' TRON send ' . $request->input('value') . ' address ' . $request->input('wallet'));
      } elseif ($request->input("type") === "gold") {
        $withdraw = Http::asForm()->post('https://paseo.live/camelgold/SendToken', [
          'senderPrivateKey' => Auth::user()->private_key,
          'senderAddress' => Auth::user()->wallet_camel,
          'receiverAddress' => $request->input('wallet'),
          'tokenAmount' => $request->input('value'),
        ]);
        Log::info(Auth::user()->username . ' Camel gold send ' . $request->input('value') . ' address ' . $request->input('wallet'));
      } else {
        $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
          'privkey' => Auth::user()->private_key,
          'to' => $request->input('wallet'),
          'amount' => $request->input('value'),
        ]);
        Log::info(Auth::user()->username . ' Camel send ' . $request->input('value') . ' address ' . $request->input('wallet'));
      }
      Log::info($withdraw);

      if ($withdraw->ok() && str_contains($withdraw->body(), 'success') === true) {
        $history = new HistoryCamel();
        $history->user_id = Auth::id();
        if ($request->input("type") === "tron") {
          $history->wallet = $request->input('wallet');
          $history->value = $request->input('value');
          $history->code = $withdraw->json()['txid'];
          $history->type = "tron";
        } else if ($request->input("type") === "gold") {
          $history->wallet = $request->input('wallet');
          $history->value = $request->input('value');
          $history->code = $withdraw->json()["data"]['txid'];
          $history->type = "gold";
        } else {
          $history->wallet = $request->input('wallet');
          $history->value = $request->input('value');
          $history->code = $withdraw->json()['txid'];
          $history->type = "camel";
        }
        $history->save();

        return response()->json(['message' => 'success transfer Camel/Camel Gold/tron']);
      }

      return response()->json(['message' => 'connection has a problem/value to small/tron to small'], 500);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }

  public function balances()
  {
    $wallet = Auth::user()->wallet_camel;

    $camel = CamelGet::camelBalance($wallet);
    $gold = CamelGet::goldBalance($wallet);
    $tron = CamelGet::tronBalance($wallet);

    return response()->json([
      "camel" => round($camel / 10 ** 6, 6),
      "gold" => round($gold / 10 ** 8, 8),
      "tron" => round($tron / 10 ** 6, 6),
    ]);
  }
}
