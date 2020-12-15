<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
  public function index()
  {
    foreach (Auth::user()->tokens as $key => $value) {
      $value->delete();
    }
    return response('', 204);
  }
}
