<?php

namespace App\Console\Commands;

use App\Models\Camel;
use App\Models\CamelSetting;
use App\Models\ShareQueue;
use App\Models\Upgrade;
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

        if ($this->withdraw(CamelSetting::find(1)->private_key, $user->wallet_camel, $shareQueue->value)) {
          $upgrade = new Upgrade();
          $upgrade->from = 1;
          $upgrade->to = $user->id;
          $upgrade->description = 'Random Share ' . $user->username;
          $upgrade->type = "camel";
          $upgrade->level = $user->level;
          $upgrade->credit = $shareQueue->value;
          $upgrade->save();
          
          $formatBalanceTrue = number_format($shareQueue->value / 10 ** 8, 8, '', '');

          $camel = new Camel();
          $camel->user_id = $user->id;
          $camel->debit = $formatBalanceTrue;
          $camel->description = 'Random Share ' . $user->username;
          $camel->save();

          $shareQueue->status = true;

        } else {
          $shareQueue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        }
        $shareQueue->save();
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Line : ' . $e->getLine());
        $shareQueue->created_at = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
        $shareQueue->save();
      }
    }
  }

  /**
   * @param $privateKey
   * @param $targetWallet
   * @param $value
   * @return bool
   */
  private function withdraw($privateKey, $targetWallet, $value)
  {
    $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
      'privkey' => $privateKey,
      'to' => $targetWallet,
      'amount' => $value,
    ]);
    Log::info("=================SEND RANDOM===================");
    Log::info($value . " - " . $targetWallet);
    Log::info($withdraw->body());
    Log::info("====================================");

    return $withdraw->successful() && str_contains($withdraw->body(), 'failed') === false;
  }
}
