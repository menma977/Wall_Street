<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Camel;
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
      'value' => 'required|numeric',
      'wallet' => 'required|string',
      'fake' => 'required|string',
    ]);

    if (Queue::where('user_id', Auth::id())->where('status', false)->count()) {
      return response()->json(['message' => 'your are on queue'], 500);
    }

    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      if ($request->input('fake') == 'true') {
        $targetUser = User::where('wallet_camel', $request->input('wallet'))->first();

        $formatDoge = number_format($request->input('value'), 8, '.', '');

        $balance = new Camel();
        $balance->user_id = $targetUser->id;
        $balance->description = "receive camel " . $formatDoge . " from " . Auth::user()->username;
        $balance->debit = $formatDoge;
        $balance->save();

        $balance = new Camel();
        $balance->user_id = Auth::id();
        $balance->description = "send camel " . $formatDoge . " to " . $targetUser->username;
        $balance->credit = $formatDoge;
        $balance->save();

        return response()->json(['message' => 'success transfer Camel Wall']);
      }
      Log::info("============WD===================");
      Log::info(Auth::user()->private_key);
      Log::info($request->input('wallet'));
      Log::info($request->input('value'));
      Log::info("=================================");
      $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
        'privkey' => Auth::user()->private_key,
        'to' => $request->input('wallet'),
        'amount' => $request->input('value'),
      ]);
      Log::info(Auth::user()->username . ' doge send ' . $request->input('value') . ' address ' . $request->input('wallet'));

      if ($withdraw->successful() && str_contains($withdraw->body(), 'Pending') === true) {
        return response()->json(['message' => 'success transfer Camel']);
      }

      return response()->json(['message' => 'connection has a problem or value to small'], 500);
    }

    return response()->json(['message' => 'your secondary password is incorrect'], 500);
  }
}
