<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
  /**
   * Handle an incoming password reset link request.
   *
   * @param Request $request
   * @return Application|ResponseFactory|RedirectResponse|Response
   *
   */
  public function store(Request $request)
  {
    $request->validate([
      'email' => 'required|email|exists:users,email',
    ]);

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status == Password::RESET_LINK_SENT) {
      return response(['message' => $status]);
    }

    return response([
      'email' => $request->only('email'),
      'message' => $status
    ]);
  }
}
