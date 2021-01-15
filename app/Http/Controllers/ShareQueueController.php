<?php

namespace App\Http\Controllers;

use App\Models\ShareQueue;
use App\Models\User;
use Carbon\Carbon;
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
    $shareQueue = ShareQueue::orderBy('created_at', 'desc')->paginate(20);
    $shareQueue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

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
      $queue = ShareQueue::where('user_id', $idUser->id)->orderBy('created_at', 'desc')->paginate(20);
    } else {
      $queue = ShareQueue::where('type', 'like', $request->input('search'))->orWhere('value', 'like', $request->input('search'))->orderBy('created_at', 'desc')->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

      return $item;
    });

    $queue->appends(['search' => $request->input('search')]);

    $data = [
      'queue' => $queue
    ];

    return view('share.index', $data);
  }
}
