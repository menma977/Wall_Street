<?php

namespace App\Http\Controllers;

use App\Models\Camel;
use App\Models\Queue;
use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
  protected $user;
  protected $camel;
  protected $upgrade;
  protected $queue;
  protected $share;

  /**
   * HomeController constructor.
   */
  public function __construct()
  {
    $this->user = User::all();
    $this->camel = Camel::where('description', 'like', 'Random Share%')->get();
    $this->upgrade = Upgrade::where('description', 'like', '%did an upgrade%')->get();
    $this->queue = Queue::all();
    $this->share = ShareQueue::all();
  }

  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $verifiedUser = $this->user->count();
    $verifiedProgress = $this->user->whereNotNull('email_verified_at')->count();

    $camelPrice = UpgradeList::find(1)->camel;

    $camel = $this->camel->whereNotBetween('user_id', [1, 16]);
    $upgradeIn = $this->upgrade->whereNotBetween('to', [1, 16]);
    $share = $this->share->whereNotBetween('user_id', [1, 16]);

    $total_member = User::count();
    $total_member_today = User::where('email_verified_at', 'like', Carbon::now()->format("Y-m-d") . '%')->count();

    $turnover = $upgradeIn->sum('debit') / 3;
    $turnover_today = $upgradeIn->filter(function ($item) {
        return Carbon::parse($item->created_at)->format("Y-m-d") === Carbon::now()->format("Y-m-d");
      })->sum('debit') / 3;

    $total_random_share = number_format(($share->sum('value') * $camelPrice) + ($camel->sum('debit') / 10 ** 8), 8);
    $total_random_share_send = number_format(($share->where('status', true)->sum('value') * $camelPrice) + ($camel->sum('debit') / 10 ** 8), 8);
    $total_random_share_not_send = number_format($share->where('status', false)->sum('value') * $camelPrice, 8);

    $chartUser = User::whereNotNull('email_verified_at')->orderBy('email_verified_at', 'asc')->get()->countBy(function ($item) {
      return Carbon::parse($item->email_verified_at)->format("y-m-d");
    });

    $chartCamel = Camel::whereNotBetween('user_id', [1, 16])->where('description', 'like', "Random Share%")->orderBy('created_at', 'asc')->get()->groupBy(function ($item) {
      return Carbon::parse($item->created_at)->format("y-m-d");
    })->map(function ($item) {
      return (float)number_format($item->sum('debit') / 10 ** 8, 8, '.', '');
    });

    $chartUpgrade = Upgrade::whereNotBetween('from', [1, 16])->whereNotBetween('to', [1, 16])->orderBy('created_at', 'asc')->get()->groupBy(function ($item) {
      return Carbon::parse($item->updated_at)->format("y-m-d");
    });

    $chartUpgradeDebit = $chartUpgrade->map(function ($item) {
      return (float)number_format($item->sum('debit') / 3, 8, '.', '');
    });

    $chartUpgradeCredit = $chartUpgrade->map(function ($item) {
      return (float)number_format($item->sum('credit') / 3, 8, '.', '');
    });

    $chartUpgradeTotal = $chartUpgrade->map(function ($item) {
      return (float)number_format(($item->sum('debit') - $item->sum('credit')) / 3, 8, '.', '');
    });

    $data = [
      'verifiedUser' => $verifiedUser,
      'verifiedProgress' => $verifiedProgress == 0 ? 0 : number_format(($verifiedProgress / $verifiedUser) * 100, 1),
      'verifiedRemaining' => $verifiedUser - $verifiedProgress,
      'total_member' => $total_member,
      'total_member_today' => $total_member_today,
      'turnover' => $turnover,
      'turnover_today' => $turnover_today,
      'total_random_share' => $total_random_share,
      'total_random_share_send' => $total_random_share_send,
      'total_random_share_not_send' => $total_random_share_not_send,
      'chartUser' => $chartUser,
      'chartUpgradeDebit' => $chartUpgradeDebit,
      'chartUpgradeCredit' => $chartUpgradeCredit,
      'chartUpgradeTotal' => $chartUpgradeTotal,
      'chartCamel' => $chartCamel,
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
      'target' => $target,
      'remaining' => $target - $progress
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
      'target' => $target,
      'remaining' => $target - $progress
    ];

    return response()->json($data);
  }
}
