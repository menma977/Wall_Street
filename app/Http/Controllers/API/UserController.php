<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
}
