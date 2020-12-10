<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
  public function self()
  {
    return response()->json(["user" => Auth::user()]);
  }

  public function get($user_identifiable)
  {
    $user = User::where("username", $user_identifiable)
      ->orWhere("id", $user_identifiable)
      ->orWhere("phone", $user_identifiable)
      ->orWhere("email", $user_identifiable)
      ->first();
    if ($user)
      return response()->json(["user" => $user]);
    else
      return response()->json(["message", "User not found"], 404);
  }
}
