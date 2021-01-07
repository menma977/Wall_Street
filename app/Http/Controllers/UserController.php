<?php

namespace App\Http\Controllers;

use App\Models\BTC;
use App\Models\Camel;
use App\Models\Doge;
use App\Models\ETH;
use App\Models\LTC;
use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $users = User::paginate(20)->withQueryString();

    $data = [
      'users' => $users
    ];

    return view('users.index', $data);
  }

  /**
   * @param Request $request
   * @return Application|Factory|View
   */
  public function filter(Request $request)
  {
    $users = User::where('username', 'like', "%{$request->input('search')}%")
      ->orWhere('email', 'like', "%{$request->input('search')}%")
      ->orWhere('phone', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_camel', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_doge', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_eth', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_ltc', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_btc', 'like', "%{$request->input('search')}%")
      ->orWhere('id', 'like', $request->input('search'))
      ->paginate(20)
      ->withQueryString();

    $data = [
      'users' => $users
    ];

    return view('users.index', $data);
  }

  /**
   * @param $id
   * @return Application|Factory|View
   */
  public function show($id)
  {
    $user = User::find($id);
    $progress = Upgrade::where('to', $id)->sum('credit');
    $credit = Upgrade::where('to', $id)->sum('credit');
    $debit = Upgrade::where('to', $id)->where('from', $id)->sum('debit');

    $data = [
      'user' => $user,
      'progress' => $progress > 0 && $debit > 0 ? (int)number_format(($progress / $debit) * 100, 2, '.', '') : 0,
      'debit' => $debit,
      'credit' => $credit,
      'camelBalance' => number_format((Camel::where('user_id', $id)->sum('debit') - Camel::where('user_id', $id)->sum('credit')) / 10 ** 8, 8),
      'dogeBalance' => number_format((Doge::where('user_id', $id)->sum('debit') - Doge::where('user_id', $id)->sum('credit')) / 10 ** 8, 8),
      'ethBalance' => number_format((ETH::where('user_id', $id)->sum('debit') - ETH::where('user_id', $id)->sum('credit')) / 10 ** 8, 8),
      'ltcBalance' => number_format((LTC::where('user_id', $id)->sum('debit') - LTC::where('user_id', $id)->sum('credit')) / 10 ** 8, 8),
      'btcBalance' => number_format((BTC::where('user_id', $id)->sum('debit') - BTC::where('user_id', $id)->sum('credit')) / 10 ** 8, 8),
    ];

    return view('users.show', $data);
  }

  /**
   * @param $id
   * @return JsonResponse
   */
  public function balance($id)
  {
    $user = User::find($id);

    $camel = $this->camel($user->wallet_camel);
    $tron = $this->tron($user->wallet_camel);
    $coin = $this->coin($user);

    if (str_contains($camel->body(), 'success') === true && str_contains($tron->body(), 'success') === true && str_contains($coin->body(), 'LoginInvalid') === false) {
      $coin = collect($coin->json("Balances"));
      return response()->json([
        'message' => 'balance hash been load',
        'camel' => number_format($camel->json('balance'), 8),
        'tron' => number_format($tron->json('balance'), 8),
        'btc' => number_format($coin->where('Currency', 'btc')->first()['Balance'] / 10 ** 8, 8),
        'doge' => number_format($coin->where('Currency', 'doge')->first()['Balance'] / 10 ** 8, 8),
        'ltc' => number_format($coin->where('Currency', 'ltc')->first()['Balance'] / 10 ** 8, 8),
        'eth' => number_format($coin->where('Currency', 'eth')->first()['Balance'] / 10 ** 8, 8),
      ]);
    }

    return response()->json([
      'message' => 'response failed',
      'camel' => number_format(0, 8),
      'tron' => number_format(0, 8),
      'btc' => number_format(0, 8),
      'doge' => number_format(0, 8),
      'ltc' => number_format(0, 8),
      'eth' => number_format(0, 8),
    ]);
  }

  /**
   * @param $account
   * @return Response
   */
  private function coin($account): Response
  {
    return Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'GetBalances',
      's' => $account->cookie
    ]);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private function camel($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/gettokenbalance/" . $wallet);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private function tron($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/getbalance/" . $wallet);
  }
}
