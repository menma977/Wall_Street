<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\User;
use App\Notifications\AddUser;
use http\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function out(Request $request)
  {
    if (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {
      $type = 'email';
    } else if (filter_var($request->input('username'), FILTER_VALIDATE_INT)) {
      $type = 'phone';
    } else {
      $type = 'username';
    }

    $this->validate($request, [
      'sponsor' => 'required|string|exists:users,' . $type,
      'name' => 'required|string',
      'username' => 'required|string|unique:users',
      'email' => 'required|email|unique:users',
      'phone' => 'required|numeric|min:10|unique:users',
      'password' => 'required|same:confirmation_password|min:6',
      'secondary_password' => 'required|same:confirmation_secondary_password|digits:6'
    ]);

    $up_line = User::where($type, $request->input('sponsor'))->first();
    if ($up_line->email_verified_at) {
      try {
        $account = $this->createAccount();

        if ($account['code'] == 200) {
          $user = new User();
          $user->name = $request->input('name');
          $user->username = $request->input('username');
          $user->email = $request->input('email');
          $user->phone = $request->input('phone');
          $user->password = Hash::make($request->input('password'));
          $user->password_junk = $request->input('password');
          $user->secondary_password = Hash::make($request->input('secondary_password'));
          $user->secondary_password_junk = $request->input('secondary_password');
          $user->cookie = $account['cookie'];

          $wallet = $this->getWallet($account['cookie']);
          if ($wallet['code'] == 200) {
            $user->private_key = $account['privateKey'];
            $user->public_key = $account['publicKey'];
            $user->wallet_camel = $account['walletCamel'];
            $user->hex_camel = $account['hexCamel'];

            $user->wallet_btc = $wallet['btc'];
            $user->wallet_doge = $wallet['doge'];
            $user->wallet_ltc = $wallet['ltc'];
            $user->wallet_eth = $wallet['eth'];

            $user->username_doge = $this->generateRandomString();
            $user->password_doge = $this->generateRandomString();

            $addUser = $this->addUser($account['cookie'], $user->username_doge, $user->password_doge);

            if ($addUser['code'] == 200) {
              $user->save();

              $binary = new Binary();
              $binaryData = Binary::where('up_line', $up_line->id)->first();
              if ($binaryData) {
                $binary->sponsor = $binaryData->sponsor;
                $binary->up_line = $up_line->id;
              } else {
                $binary->sponsor = $up_line->id;
                $binary->up_line = $up_line->id;
              }
              $binary->down_line = $user->id;
              $binary->save();

              $user->notify(new AddUser());

              return response()->json(['message' => "your registration successful. please confirmation your email address"]);
            }

            return response()->json(['message' => $addUser['message']], 500);
          }

          return response()->json(['message' => $wallet['message']], 500);
        }

        return response()->json(['message' => $account['message']], 500);
      } catch (Exception $e) {
        Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
        return response()->json(['message' => $e->getMessage()], 500);
      }
    }
    return response()->json(['message' => "your sponsor didnt exist or not verified user"], 500);
  }

  public function in(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|string',
      'username' => 'required|string|unique:users',
      'email' => 'required|email|unique:users',
      'phone' => 'required|numeric|min:10|unique:users',
    ]);

    try {
      $account = $this->createAccount();

      if ($account['code'] == 200) {
        $user = new User();
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $generatePassword = $this->generateRandomString(6);
        $generateSecondaryPassword = random_int(100000, 999999);
        $user->password = Hash::make($generatePassword);
        $user->password_junk = $generatePassword;
        $user->secondary_password = Hash::make($generateSecondaryPassword);
        $user->secondary_password_junk = $generateSecondaryPassword;
        $user->cookie = $account['cookie'];

        $wallet = $this->getWallet($account['cookie']);
        if ($wallet['code'] == 200) {
          $user->private_key = $account['privateKey'];
          $user->public_key = $account['publicKey'];
          $user->wallet_camel = $account['walletCamel'];
          $user->hex_camel = $account['hexCamel'];

          $user->wallet_btc = $wallet['btc'];
          $user->wallet_doge = $wallet['doge'];
          $user->wallet_ltc = $wallet['ltc'];
          $user->wallet_eth = $wallet['eth'];

          $user->username_doge = $this->generateRandomString();
          $user->password_doge = $this->generateRandomString();

          $addUser = $this->addUser($account['cookie'], $user->username_doge, $user->password_doge);

          if ($addUser['code'] == 200) {
            $user->save();

            $binary = new Binary();
            $binaryData = Binary::where('up_line', Auth::id())->first();
            if ($binaryData) {
              $binary->sponsor = $binaryData->sponsor;
              $binary->up_line = Auth::id();
            } else {
              $binary->sponsor = Auth::id();
              $binary->up_line = Auth::id();
            }
            $binary->down_line = $user->id;
            $binary->save();

            $user->notify(new AddUser());

            return response()->json(['message' => "your registration successful. please confirmation your email address"]);
          }

          return response()->json(['message' => $addUser['message']], 500);
        }

        return response()->json(['message' => $wallet['message']], 500);
      }

      return response()->json(['message' => $account['message']], 500);
    } catch (Exception $e) {
      Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
    }

    return response()->json(['message' => $e->getMessage()], 500);
  }

  /**
   * generate Random string
   * @param int $length
   * @return string
   * @throws \Exception
   */
  public function generateRandomString($length = 20)
  {
    $characters = '0123456789WALLSTREETwallstreet';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  /**
   * @return array
   */
  public function createAccount()
  {
    $doge = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'CreateAccount',
      'Key' => 'ec01af0702f3467a808ba52679e1ee61',
    ]);

    $camel = Http::get("https://api.cameltoken.io/tronapi/createaccount");

    if ($doge->ok() && $doge->successful() && $camel->ok() && $camel->successful()) {
      return [
        'code' => 200,
        'cookie' => $doge->json()['SessionCookie'],
        'privateKey' => $camel->json()['privateKey'],
        'publicKey' => $camel->json()['publicKey'],
        'walletCamel' => $camel->json()['address']['base58'],
        'hexCamel' => $camel->json()['address']['hex'],
      ];
    }

    return [
      'code' => 500,
      'message' => "failed create account",
    ];
  }

  /**
   * @param $cookie
   * @return array
   */
  public function getWallet($cookie)
  {
    $btc = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'GetDepositAddress',
      's' => $cookie,
      'Currency' => "btc"
    ]);

    $doge = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'GetDepositAddress',
      's' => $cookie,
      'Currency' => "doge"
    ]);

    $ltc = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'GetDepositAddress',
      's' => $cookie,
      'Currency' => "ltc"
    ]);

    $eth = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'GetDepositAddress',
      's' => $cookie,
      'Currency' => "eth"
    ]);

    if ($btc->ok() && $btc->successful() && $doge->ok() && $doge->successful() && $ltc->ok() && $ltc->successful() && $eth->ok() && $eth->successful()) {
      return [
        'code' => 200,
        'btc' => $btc->json()['Address'],
        'doge' => $doge->json()['Address'],
        'ltc' => $ltc->json()['Address'],
        'eth' => $eth->json()['Address'],
      ];
    }

    return [
      'code' => 500,
      'message' => "failed to get wallet",
    ];
  }

  public function addUser($cookie, $username, $password)
  {
    $createUser = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'CreateUser',
      's' => $cookie,
      'Username' => $username,
      'Password' => $password,
    ]);

    if ($createUser->ok() && $createUser->successful()) {
      return [
        'code' => 200,
        'username' => $username,
        'password' => $password,
      ];
    }

    return [
      'code' => 500,
      'message' => "Failed to add user",
    ];
  }
}
