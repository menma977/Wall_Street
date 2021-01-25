<?php

namespace App\Console\Commands;

use App\Models\Camel;
use App\Models\QueueDaily;
use App\Models\QueueDailyBank;
use App\Models\QueueDailyLimiterList;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueueDailyExecution extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queueDailyExecution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Queue Daily Execution';

  /**
   * Execute the console command.
   *
   */
  public function handle()
  {
    $queueDaily = QueueDaily::where('send', false)->first();
    if ($queueDaily) {
      try {
        $bank = QueueDailyBank::find(1);
        $user = User::find($queueDaily->user_id);
        $sumUpLineValue = Upgrade::where('from', $user->id)->where('to', $user->id)->sum('debit') - Upgrade::where('to', $user->id)->sum('credit');
        if ($sumUpLineValue > 0) {
          $sumUpgrade = Upgrade::where('from', $user->id)->where('to', $user->id)->sum('debit') / 3;
          $shareValue = QueueDailyLimiterList::where('min', '<=', $sumUpgrade)->where('max', '>=', $sumUpgrade)->first();
          if ($shareValue) {
            $shareToUsd = ($shareValue->value * UpgradeList::find(1)->camel) * 2;
            if (self::withdraw($bank->private_key, $user->wallet_camel, $shareValue->value)) {
              $upgrade = new Upgrade();
              $upgrade->from = 1;
              $upgrade->to = $user->id;
              $upgrade->description = 'Share Pool ' . $user->username;
              $upgrade->type = "camel";
              $upgrade->level = $user->level;
              $upgrade->credit = $shareToUsd;
              $upgrade->save();

              $formatBalanceTrue = number_format($shareValue->value, 8, '', '');

              $camel = new Camel();
              $camel->user_id = $user->id;
              $camel->debit = $formatBalanceTrue;
              $camel->description = 'Share Pool ' . $user->username;
              $camel->save();

              $queueDaily->status = true;
              $queueDaily->save();
            }
          } else {
            Log::error("===" . $shareValue . "===");
          }
        } else {
          Log::error("===" . $sumUpLineValue . "===");
          QueueDaily::where('user_id', $user->id)->delete();
        }
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Share POOL Line : ' . $e->getLine());
      }
    }
  }

  /**
   * @param $privateKey
   * @param $targetWallet
   * @param $value
   * @return bool
   */
  private static function withdraw($privateKey, $targetWallet, $value)
  {
    $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
      'privkey' => $privateKey,
      'to' => $targetWallet,
      'amount' => $value,
    ]);
    Log::info("=================SEND RANDOM POOL===================");
    Log::info($value . " - " . $targetWallet);
    Log::info($withdraw->body());
    Log::info("====================================");

    return $withdraw->successful() && str_contains($withdraw->body(), 'failed') === false;
  }
}
