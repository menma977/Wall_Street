<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
          $item->revoke();
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

          if ($doge999->ok() && $doge999->successful()) {
            $user->account_cookie = $doge999->json()['SessionCookie'];

            $user->token = $user->createToken('Android')->accessToken;

            return response()->json([
              'token' => $user->token,
              'account_cookie' => $user->account_cookie,
              'email' => $user->email,
              'username' => $user->username,
              'phone' => $user->phone,
              'wallet_btc' => $user->wallet_btc,
              'wallet_doge' => $user->wallet_doge,
              'wallet_ltc' => $user->wallet_ltc,
              'wallet_eth' => $user->wallet_eth,
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
