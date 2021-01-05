<?php

namespace App\Http\Controllers;

use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $users = User::paginate(20)->withQueryString();

    $data = [
      'users' => $users
    ];

    return view('users.index', $data);
  }

  /**
   * @param Request $request
   * @return Application|Factory|View
   */
  public function filter(Request $request)
  {
    $users = User::where('username', 'like', "%{$request->input('search')}%")
      ->orWhere('email', 'like', "%{$request->input('search')}%")
      ->orWhere('phone', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_camel', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_doge', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_eth', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_ltc', 'like', "%{$request->input('search')}%")
      ->orWhere('wallet_btc', 'like', "%{$request->input('search')}%")
      ->orWhere('id', 'like', $request->input('search'))
      ->paginate(20)
      ->withQueryString();

    $data = [
      'users' => $users
    ];

    return view('users.index', $data);
  }

  /**
   * @param $id
   * @return Application|Factory|View
   */
  public function show($id)
  {
    $user = User::find($id);
    $progress = Upgrade::where('to', $id)->sum('credit');
    $credit = Upgrade::where('to', $id)->sum('credit');
    $debit = Upgrade::where('to', $id)->where('from', $id)->sum('debit');

    $data = [
      'user' => $user,
      'progress' => $progress > 0 && $debit > 0 ? (int)number_format(($progress / $debit) * 100, 2, '.', '') : 0,
      'debit' => $debit,
      'credit' => $credit,
    ];

    dd($data);
  }
}
