<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinaryController extends Controller
{
  public function index(Request $request)
  {
    $token = $request->bearerToken();
    $binary = Binary::where('up_line', Auth::user()->id)->get();
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);

      return $item;
    });

    $data = [
      'binary' => $binary,
      'token' => $token
    ];

    return view('binary.index', $data);
  }

  public function show($id)
  {
    $binary = Binary::where('up_line', $id)->get();
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);

      return $item;
    });

    return $binary;
  }
}
