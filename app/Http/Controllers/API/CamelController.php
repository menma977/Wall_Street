<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Camel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CamelController extends Controller
{
  public function index()
  {
    return response()->json([
      'balance' => Camel::where('user_id', Auth::id())->sum('debit') - Camel::where('user_id', Auth::id())->sum('credit')
    ]);
  }

  public function show()
  {
    $list = Camel::where('user_id', Auth::id())->simplePaginate(20);
  }
}
