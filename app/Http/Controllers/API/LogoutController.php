<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
  public function index()
  {
    $token = Auth::user()->token();
    foreach ($token as $key => $value) {
      $value->delete();
    }
    return response('', 204);
  }
}
