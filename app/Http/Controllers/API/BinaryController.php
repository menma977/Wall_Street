<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Binary;
use App\Models\UpgradeList;
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
      if ($item->userDownLine->level == 0) {
        $dollar = 0;
      } elseif ($item->userDownLine->level == 10) {
        $dollar = 10000;
      } else {
        $dollar = UpgradeList::find($item->userDownLine->level)->dollar;
      }
      $item->userDownLine->level = "$" . number_format($dollar, 2, ',', '.');

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
      if ($item->id == 1) {
        $dollar = 10000;
      } else {
        $dollar = UpgradeList::find($item->userDownLine->level)->dollar;
      }
      $item->userDownLine->level = "$" . number_format($dollar, 2, ',', '.');

      return $item;
    });

    return $binary;
  }
}
