<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
  /**
   * @param Request $request
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
      'password' => 'required|string|min:6'
    ]);

    try {
      if (Auth::attempt([$type => $request->input('username'), 'password' => $request->input('password')])) {
        foreach (Auth::user()->tokens as $id => $item) {
          $item->revoke();
        }
        $user = Auth::user();
        if ($user) {
          if ($user->suspend) {
            return response()->json(['message' => 'your account has been suspend'], 500);
          }

          if ($user->email_verified_at) {
            $user->active = true;
            $user->save();
          } else {
            if (!$user->active) {
              return response()->json(['message' => 'your account is not active. please active your account'], 500);
            }
          }
        }
      }
    } catch (Exception $e) {
      Log::error($e->getMessage() . " - " . $e->getFile() . " - " . $e->getLine());
      $data = [
        'message' => $e->getMessage(),
      ];
      return response()->json($data, 500);
    }
  }
}
