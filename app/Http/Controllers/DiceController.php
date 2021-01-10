<?php

namespace App\Http\Controllers;

use App\Models\Dice;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiceController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $dice = Dice::selectRaw("user_id as username, COUNT(*) as total")->groupBy('user_id')->paginate(20);
    $dice->getCollection()->transform(function ($item) {
      $item->username = User::find($item->username)->username;

      return $item;
    });

    $data = [
      'dice' => $dice
    ];

    return view('dice.index', $data);
  }

  /**
   * @param Request $request
   * @return Application|Factory|View|RedirectResponse
   */
  public function show(Request $request)
  {
    $user = User::where('username', 'like', $request->input('search'))->first();
    if ($user) {
      $dice = Dice::selectRaw("user_id as username, COUNT(*) as total")->where('user_id', $user->id)->groupBy('user_id')->paginate(20);
      $dice->getCollection()->transform(function ($item) {
        $item->username = User::find($item->username)->username;

        return $item;
      });

      $dice->appends(['search' => $request->input('search')]);

      $data = [
        'dice' => $dice
      ];

      return view('dice.index', $data);
    }
    return redirect()->back()->with(['message' => "username not found"]);
  }

  /**
   * @param Request $request
   * @return RedirectResponse
   * @throws ValidationException
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'type' => 'required|string|in:add,remove',
      'username' => 'required|string|exists:users,username',
      'value' => 'required|numeric|min:1'
    ]);

    $user = User::where('username', $request->input('username'))->first();
    if ($request->input("type") === 'add') {
      for ($i = 0; $i < $request->input("value"); $i++) {
        $dice = new Dice();
        $dice->user_id = $user->id;
        $dice->save();
      }

      return redirect()->back()->with(['message' => "add {$request->input("value")} dice to Username : {$request->input("username")}"]);
    }

    Dice::where('user_id', $user->id)->take($request->input("value"))->delete();
    return redirect()->back()->with(['message' => "remove {$request->input("value")} dice to Username : {$request->input("username")}"]);
  }
}
