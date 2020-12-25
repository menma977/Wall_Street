<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BTC;
use App\Models\Camel;
use App\Models\Doge;
use App\Models\ETH;
use App\Models\LTC;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\UpgradeList;
use http\Exception;
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

          $doge999 = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'Login',
            'key' => 'f3023b66b9304852abddc71ccd8237e9',
            'username' => $user->username_doge,
            'password' => $user->password_doge
          ]);

          Log::info("Login" . $doge999->body());

          if ($doge999->ok() && $doge999->successful()) {
            $user->cookie = $doge999->json()['SessionCookie'];
            $user->save();

            $user->token = $user->createToken('Android')->accessToken;

            if ($user->id == 1) {
              $dollar = 10000;
            } else {
              $dollar = UpgradeList::find($user->level)->dollar;
            }

            $tronResponse = Http::get("https://api.cameltoken.io/tronapi/gettokenbalance/" . $user->wallet_camel);
            $tronBalance = 0;
            if ($tronResponse->ok() && $tronResponse->successful()) {
              $tronBalance = $tronResponse->json()["balance"];
            }

            $camelResponse = Http::get("https://api.cameltoken.io/tronapi/getbalance/" . $user->wallet_camel);
            $camelBalance = 0;
            if ($camelResponse->ok() && $camelResponse->successful()) {
              $camelBalance = $camelResponse->json()["balance"];
            }

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
              'doge_balance' => $doge999->json()["Doge"]["Balance"],
              'ltc_balance' => $doge999->json()["LTC"]["Balance"],
              'eth_balance' => $doge999->json()["ETH"]["Balance"],
              'btc_balance' => $doge999->json()["Balance"],
              'camel_balance' => $camelBalance,
              'tron_balance' => $tronBalance,
              'fake_doge_balance' => Doge::where('user_id', Auth::id())->sum('debit') - Doge::where('user_id', Auth::id())->sum('credit'),
              'fake_ltc_balance' => LTC::where('user_id', Auth::id())->sum('debit') - LTC::where('user_id', Auth::id())->sum('credit'),
              'fake_eth_balance' => ETH::where('user_id', Auth::id())->sum('debit') - ETH::where('user_id', Auth::id())->sum('credit'),
              'fake_btc_balance' => BTC::where('user_id', Auth::id())->sum('debit') - BTC::where('user_id', Auth::id())->sum('credit'),
              'fake_camel_balance' => Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit'),
            ]);
          }

          Log::info($doge999);
          return response()->json(['message' => 'CODE:401 - user is invalid.'], 401);
        }

        return response()->json(['message' => 'CODE:401 - user is invalid.'], 401);
      }

      return response()->json(['message' => 'Invalid username and password.'], 401);
    } catch (Exception $e) {
      Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
      $data = [
        'message' => $e->getMessage(),
      ];
      return response()->json($data, 500);
    }
  }
}
