<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinaryController extends Controller
{
  public function index(Request $request)
  {
    $token = $request->bearerToken();
    $binary = Binary::where('up_line', Auth::user()->id)->get();
    $dollar = Upgrade::where('to', Auth::id())->where('from', Auth::id())->sum('debit');
    $packageUser = "$" . number_format($dollar / 3, 2, ',', '.');
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);
      $dollar = Upgrade::where('to', $item->userDownLine->id)->where('from', $item->userDownLine->id)->sum('debit');
      $item->userDownLine->level = "$" . number_format($dollar / 3, 2, ',', '.');

      return $item;
    });

    $data = [
      'binary' => $binary,
      'packageUser' => $packageUser,
      'token' => $token
    ];

    return view('binary.index', $data);
  }

  public function show($id)
  {
    $binary = Binary::where('up_line', $id)->get();
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);
      $dollar = Upgrade::where('to', $item->userDownLine->id)->where('from', $item->userDownLine->id)->sum('debit');
      $item->userDownLine->level = "$" . number_format($dollar / 3, 2, ',', '.');

      return $item;
    });

    return $binary;
  }
}
