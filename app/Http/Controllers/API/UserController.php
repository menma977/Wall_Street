<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
    return response()->json(["user" => $this->dataUser]);
  }

  private function getUser($user_identifiable)
  {
    return User::select(
      'name',
      'username',
      'email',
      'phone',
      'password',
      'account_cookie',
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
    $this->validate($request, [
      'secondary_password' => 'required|digits:6'
    ]);
    if (Auth::attempt(['secondary_password' => $request->input('secondary_password')])) {
      $user = User::find(Auth::id());
      if ($request->has('name')) {
        $this->validate($request, [
          'secondary_password' => 'required|string'
        ]);
        $user->name = $request->input('name');
      }

      if ($request->has('password')) {
        $this->validate($request, [
          'password' => 'required|same:confirmation_password|min:6',
        ]);
        $user->password = Hash::make($request->input('password'));
        $user->password_junk = $request->input('password');
      }

      if ($request->has('secondary_password')) {
        $this->validate($request, [
          'secondary_password' => 'required|same:confirmation_secondary_password|digits:6'
        ]);
        $user->secondary_password = Hash::make($request->input('secondary_password'));
        $user->secondary_password_junk = $request->input('secondary_password');
      }
      $user->save();

      return response()->json(['message' => 'success update data'], 500);
    }

    return response()->json(['message' => 'wrong secondary password'], 500);
  }
}
