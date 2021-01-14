<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\BTC;
use App\Models\Camel;
use App\Models\Doge;
use App\Models\ETH;
use App\Models\LTC;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use http\Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function index(Request $request)
  {
    if (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {
      $type = 'email';
    } else if (filter_var($request->input('username'), FILTER_VALIDATE_INT)) {
      $type = 'phone';
    } else {
      $type = 'username';
    }

    $this->validate($request, [
      'username' => 'required|string|exists:users,' . $type,
      'password' => 'required|string'
    ]);

    try {
      if (Auth::attempt([$type => $request->input('username'), 'password' => $request->input('password')])) {
        Log::info('username: ' . $request->input('username') . ' | password: ' . $request->input('password') . ' | IP(' . $request->ip() . ')');
        foreach (Auth::user()->tokens as $id => $item) {
          //$item->revoke();
          $item->delete();
        }
        $user = Auth::user();
        if ($user) {
          if ($user->suspend) {
            return response()->json(['message' => 'your account has been suspended'], 500);
          }

          if (!$user->email_verified_at) {
            return response()->json(['message' => 'your account is not active. please active your account.'], 500);
          }

          if (Setting::find(1)->maintenance) {
            return response()->json(['message' => 'Under Maintenance.'], 500);
          }

          if (!$user->cookie) {
            //https://corsdoge.herokuapp.com/doge
            //https://www.999doge.com/api/web.aspx
            $doge999 = Http::asForm()->withHeaders([
              'referer' => 'https://bugnode.info/',
              'Origin' => 'https://bugnode.info/'
            ])->post('https://corsdoge.herokuapp.com/doge', [
              'a' => 'Login',
              'key' => 'ec01af0702f3467a808ba52679e1ee61',
              'username' => $user->username_doge,
              'password' => $user->password_doge
            ]);

            Log::info("Login : " . $doge999->body());

            if ($doge999->ok() && $doge999->successful() && str_contains($doge999->body(), 'LoginInvalid') === false && str_contains($doge999->body(), 'blocked for 2 minutes.') === false) {
              $user->cookie = $doge999->json()['SessionCookie'];
              $user->save();
            } else {
              return response()->json(['message' => 'CODE:401 - user is invalid or IP block.'], 500);
            }
          }

          $user->token = $user->createToken('Android')->accessToken;

          $coin = $this->coin($user);

          Log::info("get Balance : " . $coin->body());

          if (str_contains($coin->body(), ' IP are blocked for 2 minutes.') === true) {
            return response()->json(['message' => 'CODE:401 - user is invalid or IP block.'], 500);
          }

          $coin = collect($coin->json("Balances"));

          if ($user->id == 1) {
            $dollar = 10000;
          } elseif ($user->level > 0) {
            $dollar = UpgradeList::find($user->level)->dollar;
          } else {
            $dollar = 0;
          }

          $camelBalance = 0;
          $tronBalance = 0;
          $tronResponse = Http::get("https://api.cameltoken.io/tronapi/gettokenbalance/" . $user->wallet_camel);
          if ($tronResponse->ok() && $tronResponse->successful()) {
            $camelBalance = $tronResponse->json()["balance"];
          }

          $camelResponse = Http::get("https://api.cameltoken.io/tronapi/getbalance/" . $user->wallet_camel);
          if ($camelResponse->ok() && $camelResponse->successful()) {
            $tronBalance = $camelResponse->json()["balance"];
          }

          $profit = Camel::where('user_id', $user->id)->where('description', 'like', "Random Share%")->sum('debit');
          if (!$profit) {
            $profit = 0;
          } else {
            $profit *= 2;
          }

          $profitDollar = Upgrade::where('to', $user->id)->where('description', 'like', "Random Share%")->sum('credit');
          if (!$profitDollar) {
            $profitDollar = 0;
          }

          $totalMember = User::whereNotNull('email_verified_at')->count();
          $totalDollar = "$ " . number_format(Upgrade::sum('debit') - Upgrade::sum('credit'), 3);
          $getTopBinary = Binary::selectRaw("up_line, count(*) as total")->groupBy('up_line')->orderBy('total', 'desc')->first();
          $topSponsor = User::find($getTopBinary->up_line)->name . ' - ' . $getTopBinary->total;

          return response()->json([
            'token' => $user->token,
            'cookie' => $user->cookie,
            'email' => $user->email,
            'username' => $user->username,
            'phone' => $user->phone,
            'private_key' => $user->private_key,
            'public_key' => $user->public_key,
            'wallet_camel' => $user->wallet_camel,
            'hex_camel' => $user->hex_camel,
            'wallet_btc' => $user->wallet_btc,
            'wallet_doge' => $user->wallet_doge,
            'wallet_ltc' => $user->wallet_ltc,
            'wallet_eth' => $user->wallet_eth,
            'on_queue' => Queue::where('user_id', Auth::id())->where('status', false)->count(),
            'level' => $dollar,
            'btc_balance' => $coin->where('Currency', 'btc')->first()['Balance'],
            'doge_balance' => $coin->where('Currency', 'doge')->first()['Balance'],
            'ltc_balance' => $coin->where('Currency', 'ltc')->first()['Balance'],
            'eth_balance' => $coin->where('Currency', 'eth')->first()['Balance'],
            'camel_balance' => $camelBalance,
            'tron_balance' => $tronBalance,
            'fake_doge_balance' => Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit'),
            'fake_ltc_balance' => LTC::where('user_id', Auth::id())->sum('debit') - LTC::where('user_id', Auth::id())->sum('credit'),
            'fake_eth_balance' => ETH::where('user_id', Auth::id())->sum('debit') - ETH::where('user_id', Auth::id())->sum('credit'),
            'fake_btc_balance' => BTC::where('user_id', Auth::id())->sum('debit') - BTC::where('user_id', Auth::id())->sum('credit'),
            'fake_camel_balance' => Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit'),
            'totalMember' => $totalMember,
            'totalDollar' => $totalDollar,
            'topSponsor' => $topSponsor,
            'profit' => $profit,
            'profitDollar' => $profitDollar,
          ]);
        }

        return response()->json(['message' => 'CODE:401 - user is invalid.'], 500);
      }

      return response()->json(['message' => 'Invalid username and password.'], 500);
    } catch (Exception $e) {
      Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
      $data = [
        'message' => $e->getMessage(),
      ];
      return response()->json($data, 500);
    }
  }

  /**
   * @param $account
   * @return Response
   */
  private function coin($account): Response
  {
    //https://corsdoge.herokuapp.com/doge
    //https://www.999doge.com/api/web.aspx
    return Http::asForm()->withHeaders([
      'referer' => 'https://bugnode.info/',
      'origin' => 'https://bugnode.info/'
    ])->post('https://corsdoge.herokuapp.com/doge', [
      'a' => 'GetBalances',
      's' => $account->cookie
    ]);
  }
}
