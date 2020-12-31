<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AddUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendVerifyEmailController extends Controller
{
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request)
  {
    $user = User::where('email', $request->input('email'))->first();
    if ($user) {
      if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => "your account is has been verified"], 500);
      }

      $user->notify(new AddUser());
      return response()->json(['message' => "verified link has been send"]);
    }

    return response()->json(['message' => "your email does not exist"], 500);
  }
}
