<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QueueController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @param null $queue
   * @return Application|Factory|View|Response
   */
  public function index($queue = null)
  {
    if (!$queue) {
      $queue = Queue::paginate(20)->withQueryString();
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->send = User::find($item->send);

      return $item;
    });

    $data = [
      'queue' => $queue
    ];

    return view('queue.index', $data);
  }

  public function show(Request $request)
  {
    $idUser = User::where('username', 'like', $request->input('search'))->first();
    if ($idUser) {
      $queue = Queue::where('user_id', $idUser->id)->orWhere('send', $idUser->id)->paginate(20);
    } else {
      $queue = Queue::where('type', 'like', $request->input('search'))->orWhere('value', 'like', $request->input('search'))->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->send = User::find($item->send);

      return $item;
    });

    $data = [
      'queue' => $queue
    ];

    return view('queue.index', $data);
  }
}
