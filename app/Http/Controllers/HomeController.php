<?php

namespace App\Http\Controllers;

use App\Models\HistoryCamel;
use App\Models\Queue;
use App\Models\ShareLevel;
use App\Models\ShareQueue;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $verifiedUser = User::count();
    $verifiedProgress = User::whereNotNull('email_verified_at')->count();

    $countHistoryCamel = HistoryCamel::count();

    $totalUser = ShareLevel::count() - 2;

    $data = [
      'verifiedUser' => $verifiedUser,
      'verifiedProgress' => $verifiedProgress == 0 ? 0 : number_format(($verifiedProgress / $verifiedUser) * 100, 1),
      'countHistoryCamel' => $countHistoryCamel,
      'totalUser' => $totalUser
    ];

    return view('dashboard', $data);
  }

  /**
   * @return JsonResponse
   */
  public function queue()
  {
    $target = Queue::count();
    $progress = Queue::where('status', false)->count();

    $data = [
      'progress' => $progress == 0 ? 0 : number_format(($progress / $target) * 100, 1),
      'target' => $target
    ];

    return response()->json($data);
  }

  /**
   * @return JsonResponse
   */
  public function shareQueue()
  {
    $target = ShareQueue::count();
    $progress = ShareQueue::where('status', false)->count();

    $data = [
      'progress' => $progress == 0 ? 0 : number_format(($progress / $target) * 100, 1),
      'target' => $target
    ];

    return response()->json($data);
  }
}
