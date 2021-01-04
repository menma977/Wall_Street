<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryCamel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryCamelController extends Controller
{
  public function index()
  {
    $list = HistoryCamel::where('user_id', Auth::id())->simplePaginate(20);

    return response()->json([
      'list' => $list,
    ]);
  }
}
