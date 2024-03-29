<?php

namespace App\Console\Commands;

use App\Models\Camel;
use App\Models\CamelSetting;
use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShareQueueExecution extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'shareQueueExecution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Execute Share Queue to camel';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    $shareQueue = ShareQueue::where('status', false)->where('created_at', '<=', Carbon::now())->first();
    if ($shareQueue) {
      try {
        $user = User::find($shareQueue->user_id);
        $sumUpLineValue = Upgrade::where('from', $user->id)->where('to', $user->id)->sum('debit') - Upgrade::where('to', $user->id)->sum('credit');
        if ($sumUpLineValue > 0) {
          $shareValue = CamelSetting::find(1)->share_value;
          $camelValue = ($shareValue * UpgradeList::find(1)->camel) * 2;

          if ($this->withdraw(CamelSetting::find(1), $user->wallet_camel, $shareValue)) {
            $upgrade = new Upgrade();
            $upgrade->from = 1;
            $upgrade->to = $user->id;
            $upgrade->description = 'Random Share ' . $user->username;
            $upgrade->type = "camel";
            $upgrade->level = $user->level;
            $upgrade->credit = $camelValue;
            $upgrade->save();

            $formatBalanceTrue = number_format($shareValue, 8, '', '');

            $camel = new Camel();
            $camel->user_id = $user->id;
            $camel->debit = $formatBalanceTrue;
            $camel->description = 'Random Share ' . $user->username;
            $camel->save();

            $shareQueue->status = true;

          }
          $shareQueue->save();
        } else {
          ShareQueue::where('user_id', $user->id)->delete();
        }
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Share Line : ' . $e->getLine());
        // $shareQueue->created_at = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');Z
        // $shareQueue->save();
      }
    }
  }

  /**
   * @param $privateKey
   * @param $targetWallet
   * @param $value
   * @return bool
   */
  private function withdraw($user, $targetWallet, $value)
  {
    $withdraw = Http::asForm()->post('https://paseo.live/camelgold/SendToken', [
      'senderPrivateKey' => $user->privateKey,
      'senderAddress' => $user->wallet_camel,
      'receiverAddress' => $targetWallet,
      'tokenAmount' => $value,
    ]);

    Log::info("=================SEND RANDOM===================");
    Log::info($value . " - " . $targetWallet);
    Log::info($withdraw->body());
    Log::info($withdraw->json()['txid']);
    sleep(60);
    $validate = Http::get("https://api.cameltoken.io/tronapi/gettxstatus/" . $withdraw->json()['txid']);
    Log::info($validate->body());
    Log::info("====================================");

    return $withdraw->successful() && str_contains($withdraw->body(), 'failed') === false && str_contains($validate->body(), 'failed') === false;
  }
}
