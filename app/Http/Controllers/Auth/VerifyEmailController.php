<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
  /**
   * Mark the authenticated user's email address as verified.
   *
   * @param $id
   * @param $hash
   * @return RedirectResponse
   * @throws AuthorizationException
   */
  public function __invoke($id, $hash)
  {
    $user = User::find($id);
    if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
      throw new AuthorizationException;
    }

    if ($user->markEmailAsVerified()) {
      event(new Verified($user));
    }

    $binary = Binary::where('down_line', $user->id)->first();
    if ($binary) {
      $binary->active = true;
      $binary->save();
    }

    return redirect('/valid')->with('verified', true);
  }
}
