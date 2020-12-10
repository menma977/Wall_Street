<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\User;
use http\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;

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
      'phone' => 'required|numeric|min:10',
      'password' => 'required|same:confirmation_password|min:6',
      'secondary_password' => 'required|same:confirmation_secondary_password|min:6'
    ]);

    $up_line = User::where($type, $request->input('sponsor'))->first();
    if ($up_line->email_verified_at) {
      $createAccount999Doge = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
        'a' => 'CreateAccount',
        'Key' => '1b4755ced78e4d91bce9128b9a053cad',
      ]);
      Log::info($createAccount999Doge);

      try {
        if ($createAccount999Doge->ok() && $createAccount999Doge->successful()) {
          $user = new User();
          $user->name = $request->input('name');
          $user->username = $request->input('username');
          $user->email = $request->input('email');
          $user->phone = $request->input('phone');
          $user->password = Hash::make($request->input('password'));
          $user->password_junk = $request->input('password');
          $user->secondary_password = Hash::make($request->input('secondary_password'));
          $user->secondary_password_junk = $request->input('secondary_password');
          $user->cookie = $createAccount999Doge->json()['SessionCookie'];

          $walletBTC = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'GetDepositAddress',
            's' => $createAccount999Doge->json()['SessionCookie'],
            'Currency' => "btc"
          ]);
          Log::info($walletBTC);

          if ($walletBTC->ok() && $walletBTC->successful()) {
            $user->wallet_btc = $walletBTC->json()['Address'];
          }

          $walletDoge = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'GetDepositAddress',
            's' => $createAccount999Doge->json()['SessionCookie'],
            'Currency' => "doge"
          ]);
          Log::info($walletDoge);

          if ($walletBTC->ok() && $walletBTC->successful()) {
            $user->wallet_doge = $walletBTC->json()['Address'];
          }

          $walletLTC = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'GetDepositAddress',
            's' => $createAccount999Doge->json()['SessionCookie'],
            'Currency' => "ltc"
          ]);
          Log::info($walletLTC);

          if ($walletBTC->ok() && $walletBTC->successful()) {
            $user->wallet_ltc = $walletBTC->json()['Address'];
          }

          $walletETH = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'GetDepositAddress',
            's' => $createAccount999Doge->json()['SessionCookie'],
            'Currency' => "eth"
          ]);
          Log::info($walletETH);

          if ($walletBTC->ok() && $walletBTC->successful()) {
            $user->wallet_eth = $walletBTC->json()['Address'];
          }

          $user->username_doge = $this->generateRandomString();
          $user->password_doge = $this->generateRandomString();

          $createUser999Doge = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
            'a' => 'CreateUser',
            's' => $createAccount999Doge->json()['SessionCookie'],
            'Username' => $user->username_doge,
            'Password' => $user->password_doge,
          ]);

          Log::info($createUser999Doge);

          if ($createUser999Doge->ok() && $createUser999Doge->successful()) {
            $user->save();

            $binary = new Binary();
            $sponsor = Binary::where('up_line', $up_line->id)->first();
            if ($sponsor) {
              $binary->sponsor = $sponsor->id;
              $binary->up_line = $up_line->id;
            } else {
              $binary->sponsor = $up_line->id;
              $binary->up_line = $up_line->id;
            }
            $binary->down_line = $user->id;
            $binary->save();

            event(new Registered($user));

            return response()->json(['message' => "your registration successful. please confirmation your email address"]);
          }

          return response()->json(['message' => "your registration failed"], 500);
        }

        return response()->json(['message' => "your registration failed"], 500);
      } catch (Exception $e) {
        Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
      }
    } else {
      return response()->json(['message' => "your sponsor is not registered properly"], 500);
    }
  }

  public function in()
  {

  }

  /**
   * generate Random string
   * @return string
   * @throws \Exception
   */
  public function generateRandomString()
  {
    $length = 20;
    $characters = '0123456789WALLSTREET';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
  }
}
