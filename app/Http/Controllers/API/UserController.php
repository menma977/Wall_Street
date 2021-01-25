<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Camel;
use App\Models\Queue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  protected $dataUser;

  public function __construct()
  {
    $this->dataUser = $this->getUser(Auth::id());
  }

  public function self()
  {
    $user = User::find(Auth::id());

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

    if ($user->id == 1) {
      $dollar = 10000;
    } elseif ($user->level > 0) {
      $dollar = UpgradeList::find($user->level)->dollar;
    } else {
      $dollar = 0;
    }

    $user->level = $dollar;

    return response()->json([
      'cookie' => $user->cookie,
      'email' => $user->email,
      'username' => $user->username,
      'phone' => $user->phone,
      'wallet_btc' => $user->wallet_btc,
      'wallet_doge' => $user->wallet_doge,
      'wallet_ltc' => $user->wallet_ltc,
      'wallet_eth' => $user->wallet_eth,
      'private_key' => $user->private_key,
      'public_key' => $user->public_key,
      'wallet_camel' => $user->wallet_camel,
      'hex_camel' => $user->hex_camel,
      'level' => $dollar,
      'on_queue' => Queue::where('user_id', Auth::id())->count(),
      'profit' => $profit,
      'profitDollar' => $profitDollar,
    ]);
  }

  private function getUser($user_identifiable)
  {
    return User::select(
      'name',
      'username',
      'email',
      'phone',
      'password',
      'cookie',
      'wallet_btc',
      'wallet_ltc',
      'wallet_doge',
      'wallet_eth',
      'level',
      'suspend',
    )
      ->where("username", $user_identifiable)
      ->orWhere("id", $user_identifiable)
      ->orWhere("phone", $user_identifiable)
      ->orWhere("email", $user_identifiable)
      ->first();
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws ValidationException
   */
  public function update(Request $request)
  {
    if ($request->has('confirmation_secondary_password')) {
    //   $user = User::find(Auth::id());
    //   $this->validate($request, [
    //     'secondary_password' => 'required|same:confirmation_secondary_password|digits:6'
    //   ]);
    //   $user->secondary_password = Hash::make($request->input('secondary_password'));
    //   $user->secondary_password_junk = $request->input('secondary_password');
    //   $user->save();
      return response()->json(['message' => 'Change secondary password is not valid until the latest version update']);
    }

    $this->validate($request, [
      'secondary_password' => 'required|digits:6'
    ]);
    if (Hash::check($request->secondary_password, Auth::user()->secondary_password)) {
      $user = User::find(Auth::id());
      if ($request->has('name')) {
        $this->validate($request, [
          'name' => 'required|string'
        ]);
        $user->name = $request->input('name');
      } else if ($request->has('password')) {
        $this->validate($request, [
          'password' => 'required|same:confirmation_password|min:6',
        ]);
        $user->password = Hash::make($request->input('password'));
        $user->password_junk = $request->input('password');
      }
      $user->save();

      return response()->json(['message' => 'success update data']);
    }

    return response()->json(['message' => 'wrong secondary password'], 500);
  }
}
