<?php

namespace App\Http\Controllers;

use App\Models\ShareQueue;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShareQueueController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Application|Factory|View|Response
   */
  public function index()
  {
    $shareQueue = ShareQueue::simplePaginate(20);
    $shareQueue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);

      return $item;
    });

    $data = [
      'queue' => $shareQueue
    ];

    return view('share.index', $data);
  }

  /**
   * Display the specified resource.
   *
   * @param Request $request
   * @return Application|Factory|View|Response
   */
  public function show(Request $request)
  {
    $idUser = User::where('username', 'like', $request->input('search'))->first();
    if ($idUser) {
      $queue = ShareQueue::where('user_id', $idUser->id)->orWhere('send', $idUser->id)->paginate(20);
    } else {
      $queue = ShareQueue::where('type', 'like', $request->input('search'))->orWhere('value', 'like', $request->input('search'))->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->send = User::find($item->send);

      return $item;
    });

    $data = [
      'queue' => $queue
    ];

    return view('share.index', $data);
  }
}
