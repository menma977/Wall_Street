<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryCamel;
use Illuminate\Support\Facades\Auth;

class HistoryCamelController extends Controller
{
  public function index()
  {
    $list = HistoryCamel::where('user_id', Auth::id())->orderBy('id', 'DESC')->simplePaginate(20);

    return response()->json([
      'list' => $list,
    ]);
  }
}
