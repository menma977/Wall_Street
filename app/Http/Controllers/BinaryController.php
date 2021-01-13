<?php

namespace App\Http\Controllers;

use App\Models\Binary;
use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BinaryController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $binary = Binary::where('up_line', Auth::user()->id)->get();
    $dollar = Upgrade::where('to', Auth::id())->where('from', Auth::id())->sum('debit');
    $packageUser = "$" . number_format($dollar / 3, 2, ',', '.');
    $profit = "$" . number_format($binary->sum('profit'), 2, ',', '.');
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);
      $dollar = Upgrade::where('to', $item->userDownLine->id)->where('from', $item->userDownLine->id)->sum('debit');
      $item->userDownLine->level = "$" . number_format($dollar / 3, 2, ',', '.');
      $profit = Binary::where('up_line', $item->userDownLine->id)->sum('profit');
      $item->userDownLine->profit = "$" . number_format($profit, 2, ',', '.');

      return $item;
    });

    $data = [
      'binary' => $binary,
      'packageUser' => $packageUser,
      'profit' => $profit,
    ];

    return view('binary.admin.index', $data);
  }

  /**
   * @param $id
   * @return mixed
   */
  public function show($id)
  {
    $binary = Binary::where('up_line', $id)->get();
    $binary->map(function ($item) {
      $item->userDownLine = User::find($item->down_line);
      $dollar = Upgrade::where('to', $item->userDownLine->id)->where('from', $item->userDownLine->id)->sum('debit');
      $item->userDownLine->level = "$" . number_format($dollar / 3, 2, ',', '.');
      $profit = Binary::where('up_line', $item->userDownLine->id)->sum('profit');
      $item->userDownLine->profit = "$" . number_format($profit, 2, ',', '.');

      return $item;
    });

    return $binary;
  }
}
