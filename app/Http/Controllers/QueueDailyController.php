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
use http\Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class QueueDailyController extends Controller
{
  /**
   * @param null $queue
   * @return Application|Factory|View
   */
  public function index($queue = null)
  {
    if (!$queue) {
      $queue = QueueDaily::orderBy('user_id', 'asc')->paginate(20);
    }
    $queue->getCollection()->transform(function ($item) {
      $item->user = User::find($item->user_id);

      $sumUpgrade = Upgrade::where('from', $item->user->id)->where('to', $item->user->id)->sum('debit') / 3;
      $shareValue = QueueDailyLimiterList::where('min', '<=', (integer)$sumUpgrade)->where('max', '>=', (integer)$sumUpgrade)->first();

      $item->totalUpgrade = $sumUpgrade;
      $item->shareUsd = number_format(($shareValue->value * UpgradeList::find(1)->camel) * 2, 8, '.', '');
      $item->shareValue = $shareValue->value;
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

      return $item;
    });

    $queueDailySetting = QueueDailySetting::find(1);

    $valueList = QueueDailyLimiterList::all();

    $bank = QueueDailyBank::find(1);

    try {
      $camel = self::camel($bank->wallet_camel)["balance"];
      $tron = self::tron($bank->wallet_camel)["balance"];
    } catch (Exception $e) {
      $camel = "-";
      $tron = "-";
    }

    $data = [
      'queue' => $queue,
      'queueDailySetting' => $queueDailySetting,
      'valueList' => $valueList,
      'bank' => $bank,
      'camel' => $camel,
      'tron' => $tron,
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
      $shareValue = QueueDailyLimiterList::where('min', '<=', (integer)$sumUpgrade)->where('max', '>=', (integer)$sumUpgrade)->first();

      $item->totalUpgrade = $sumUpgrade;
      $item->shareUsd = number_format(($shareValue->value * UpgradeList::find(1)->camel) * 2, 8, '.', '');
      $item->shareValue = $shareValue->value;
      $item->date = Carbon::parse($item->created_at)->format('d/m/Y H:i:s');

      return $item;
    });

    $queue->appends(['search' => $request->input('search')]);

    $queueDailySetting = QueueDailySetting::find(1);

    $valueList = QueueDailyLimiterList::all();

    $bank = QueueDailyBank::find(1);

    try {
      $camel = self::camel($bank->wallet_camel)["balance"];
      $tron = self::tron($bank->wallet_camel)["balance"];
    } catch (Exception $e) {
      $camel = "-";
      $tron = "-";
    }

    $data = [
      'queue' => $queue,
      'queueDailySetting' => $queueDailySetting,
      'valueList' => $valueList,
      'bank' => $bank,
      'camel' => $camel,
      'tron' => $tron,
    ];

    return view('sharePool.index', $data);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private static function camel($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/gettokenbalance/" . $wallet);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private static function tron($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/getbalance/" . $wallet);
  }
}
