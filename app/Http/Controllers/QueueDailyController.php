<?php

namespace App\Http\Controllers;

use App\Models\QueueDaily;
use App\Models\QueueDailyBank;
use App\Models\QueueDailyLimiterList;
use App\Models\QueueDailySetting;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class QueueDailyController extends Controller
{
  public function index($queue = null)
  {
    if (!$queue) {
      $queue = QueueDaily::orderBy('created_at', 'desc')->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);

      $sumUpgrade = Upgrade::where('from', $item->user->id)->where('to', $item->user->id)->sum('debit') / 3;
      $shareValue = QueueDailyLimiterList::where('min', '<=', $sumUpgrade)->where('max', '>=', $sumUpgrade)->first();

      $item->totalUpgrade = $sumUpgrade;
      $item->shareUsd = ($shareValue->value * UpgradeList::find(1)->camel) * 2;
      $item->shareValue = $shareValue->value;
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

      return $item;
    });

    $queueDailySetting = QueueDailySetting::find(1);

    $valueList = QueueDailyLimiterList::all();

    $bank = QueueDailyBank::find(1);

    $data = [
      'queue' => $queue,
      'queueDailySetting' => $queueDailySetting,
      'valueList' => $valueList,
      'bank' => $bank,
    ];

    return view('sharePool.index', $data);
  }

  /**
   * @param Request $request
   * @return Application|Factory|View
   * @throws ValidationException
   */
  public function show(Request $request)
  {
    $this->validate($request, [
      'search' => 'required'
    ]);

    $idUser = User::where('username', 'like', $request->input('search'))->first();
    if ($idUser) {
      $queue = QueueDaily::where('user_id', $idUser->id)->orWhere('send', $idUser->id)->orderBy('created_at', 'desc')->paginate(20);
    } else {
      $queue = QueueDaily::where('type', 'like', $request->input('search'))->orWhere('value', 'like', $request->input('search'))->orderBy('created_at', 'desc')->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);

      $sumUpgrade = Upgrade::where('from', $item->user->id)->where('to', $item->user->id)->sum('debit') / 3;
      $shareValue = QueueDailyLimiterList::where('min', '<=', $sumUpgrade)->where('max', '>=', $sumUpgrade)->first();

      $item->totalUpgrade = $sumUpgrade;
      $item->shareUsd = ($shareValue->value * UpgradeList::find(1)->camel) * 2;
      $item->shareValue = $shareValue->value;
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

      return $item;
    });

    $queue->appends(['search' => $request->input('search')]);

    $queueDailySetting = QueueDailySetting::find(1);

    $valueList = QueueDailyLimiterList::all();

    $data = [
      'queue' => $queue,
      'queueDailySetting' => $queueDailySetting,
      'valueList' => $valueList,
    ];

    return view('sharePool.index', $data);
  }
}
