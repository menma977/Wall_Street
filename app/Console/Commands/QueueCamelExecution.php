<?php

namespace App\Console\Commands;

use App\Models\CamelSetting;
use App\Models\HistoryCamel;
use App\Models\Queue;
use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use App\Models\WalletAdmin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class QueueCamelExecution extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queueCamelExecution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Execution Camel Only';

  /**
   * Execute the console command.
   *
   *
   */
  public function handle()
  {
    $queue = Queue::where('status', false)->where('created_at', '<=', Carbon::now())->where('type', 'like', 'camel_%')->first();
    if ($queue) {
      try {
        $user = User::find($queue->user_id);
        $upgradeList = UpgradeList::find(1);
        $formatValue = $queue->value / $upgradeList->camel;
        if ($queue->type === 'camel_level') {
          if ($this->level($user, User::find($queue->send), $formatValue, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($queue->type === 'camel_buyWall') {
          if ($this->buyWall($user, WalletAdmin::find($queue->send), $formatValue, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($queue->type === 'camel_it') {
          if ($this->it($user, $formatValue, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else {
          if ($this->withdraw($user->id, $user->private_key, CamelSetting::find(1)->wallet_camel, $formatValue)) {
            $this->share($queue->value);
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        }
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Line : ' . $e->getLine());
        $queue->created_at = Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s');
        $queue->save();
      }
    }
  }

  /**
   * @param $user
   * @param $targetUser
   * @param $value
   * @param $rawValue
   * @return bool
   */
  private function level($user, $targetUser, $value, $rawValue)
  {
    if ($this->withdraw($user->id, $user->private_key, $targetUser->wallet_camel, $value)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'upgrade level ' . $user->username;
      $upgrade->type = "camel";
      $upgrade->level = $user->level;
      $upgrade->credit = $rawValue;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $user
   * @param $targetUser
   * @param $value
   * @param $rawValue
   * @return bool
   */
  private function buyWall($user, $targetUser, $value, $rawValue)
  {
    if ($this->withdraw($user->id, $user->private_key, $targetUser->wallet_camel, $value)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'BuyWall ' . $user->username;
      $upgrade->type = "camel";
      $upgrade->level = $user->level;
      $upgrade->credit = $rawValue;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $user
   * @param $value
   * @param $rawValue
   * @return bool
   */
  private function it($user, $value, $rawValue)
  {
    if ($this->withdraw($user->id, $user->private_key, CamelSetting::find(1)->wallet_camel, $value)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = 1;
      $upgrade->description = 'IT ' . $user->username;
      $upgrade->type = "camel";
      $upgrade->level = $user->level;
      $upgrade->credit = $rawValue;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $value
   */
  private function share($value)
  {
    $balanceToShare = $value / 20;
    for ($i = 0; $i < 20; $i++) {
      $shareQueue = new ShareQueue();
      $shareQueue->user_id = User::where('id', '!=', 2)->inRandomOrder()->first()->id;
      $shareQueue->value = $balanceToShare;
      $shareQueue->type = "camel";
      $shareQueue->save();
    }
  }

  /**
   * @param $id
   * @param $privateKey
   * @param $targetWallet
   * @param $value
   * @return bool
   */
  private function withdraw($id, $privateKey, $targetWallet, $value)
  {
    $withdraw = Http::asForm()->post('https://api.cameltoken.io/tronapi/sendtoken', [
      'privkey' => $privateKey,
      'to' => $targetWallet,
      'amount' => $value,
    ]);
    Log::info("====================================");
    Log::info($value . " - " . $targetWallet);
    Log::info($withdraw->body());
    Log::info("====================================");

    if ($withdraw->successful() && str_contains($withdraw->body(), 'failed') === false) {
      $history = new HistoryCamel();
      $history->user_id = $id;
      $history->wallet = $targetWallet;
      $history->value = $value;
      $history->code = $withdraw->json()['txid'];
      $history->save();
      return true;
    }

    return false;
  }
}
