<?php

namespace App\Console\Commands;

use App\Models\Queue;
use App\Models\Setting;
use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\User;
use App\Models\WalletAdmin;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QueueExecution extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queueExecution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Execute Queue';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    $queue = Queue::where('status', false)->where('created_at', '<=', Carbon::now())->first();
    if ($queue) {
      try {
        $user = User::find($queue->user_id);
        $type = explode('_', $queue->type);
        $typeBalance = $type[1];
        $targetBalance = $type[0];
        if (!$user->cookie) {
          $user->cookie = $this->getUserCookie($user->username_doge, $user->password_doge);
          $user->save();
        }
        if ($typeBalance === 'level') {
          if ($targetBalance === 'btc') {
            $walletTarget = User::find($queue->send)->wallet_btc;
          } else if ($targetBalance === 'doge') {
            $walletTarget = User::find($queue->send)->wallet_doge;
          } else if ($targetBalance === 'eth') {
            $walletTarget = User::find($queue->send)->wallet_eth;
          } else {
            $walletTarget = User::find($queue->send)->wallet_ltc;
          }
          if ($this->level($targetBalance, $user, User::find($queue->send), $walletTarget, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($typeBalance === 'buyWall') {
          if ($targetBalance === 'btc') {
            $walletTarget = WalletAdmin::find($queue->send)->wallet_btc;
          } else if ($targetBalance === 'doge') {
            $walletTarget = WalletAdmin::find($queue->send)->wallet_doge;
          } else if ($targetBalance === 'eth') {
            $walletTarget = WalletAdmin::find($queue->send)->wallet_eth;
          } else {
            $walletTarget = WalletAdmin::find($queue->send)->wallet_ltc;
          }
          if ($this->buyWall($targetBalance, $user, WalletAdmin::find($queue->send), $walletTarget, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($typeBalance === 'it') {
          if ($targetBalance === 'btc') {
            $walletTarget = Setting::find($queue->send)->wallet_btc;
          } else if ($targetBalance === 'doge') {
            $walletTarget = Setting::find($queue->send)->wallet_doge;
          } else if ($targetBalance === 'eth') {
            $walletTarget = Setting::find($queue->send)->wallet_eth;
          } else {
            $walletTarget = Setting::find($queue->send)->wallet_ltc;
          }
          if ($this->it($targetBalance, $user, $walletTarget, $queue->value)) {
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else {
          if ($targetBalance === 'btc') {
            $walletTarget = Setting::find($queue->send)->wallet_btc;
          } else if ($targetBalance === 'doge') {
            $walletTarget = Setting::find($queue->send)->wallet_doge;
          } else if ($targetBalance === 'eth') {
            $walletTarget = Setting::find($queue->send)->wallet_eth;
          } else {
            $walletTarget = Setting::find($queue->send)->wallet_ltc;
          }
          if ($this->withdraw($user->cookie, $queue->value, $walletTarget, $type)) {
            $this->share($targetBalance, $queue->value);
            $queue->status = true;
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
            $queue->save();
          }
        }
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Line : ' . $e->getLine());
        $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        $queue->save();
      }
    }
  }

  /**
   * @param $type
   * @param $user
   * @param $targetUser
   * @param $walletTarget
   * @param $value
   * @return bool
   */
  private function level($type, $user, $targetUser, $walletTarget, $value)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'upgrade level ' . $user->username;
      $upgrade->type = $type;
      $upgrade->level = $user->level;
      $upgrade->credit = $value;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $type
   * @param $user
   * @param $targetUser
   * @param $walletTarget
   * @param $value
   * @return bool
   */
  private function buyWall($type, $user, $targetUser, $walletTarget, $value)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'BuyWall ' . $user->username;
      $upgrade->type = $type;
      $upgrade->level = $user->level;
      $upgrade->credit = $value;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $type
   * @param $user
   * @param $walletTarget
   * @param $value
   * @return bool
   */
  private function it($type, $user, $walletTarget, $value)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = 1;
      $upgrade->description = 'Share ' . $user->username;
      $upgrade->type = $type;
      $upgrade->level = $user->level;
      $upgrade->credit = $value;
      $upgrade->save();

      return true;
    }

    return false;
  }

  /**
   * @param $type
   * @param $value
   */
  private function share($type, $value)
  {
    $balanceToShare = $value / 20;
    for ($i = 0; $i < 20; $i++) {
      $shareQueue = new ShareQueue();
      $shareQueue->user_id = User::inRandomOrder()->first()->id;
      $shareQueue->value = $balanceToShare;
      $shareQueue->type = $type;
      $shareQueue->save();
    }
  }

  /**
   * @param $cookie
   * @param $value
   * @param $wallet
   * @param $type
   * @return bool
   */
  private function withdraw($cookie, $value, $wallet, $type)
  {
    $withdraw = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'Withdraw',
      's' => $cookie,
      'Amount' => $value,
      'Address' => $wallet,
      'Totp ' => '',
      'Currency' => $type,
    ]);
    Log::info($withdraw->body());

    return $withdraw->successful() && str_contains($withdraw->body(), 'Pending') === true;
  }

  /**
   * @param $usernameDoge
   * @param $passwordDoge
   * @return string
   */
  private function getUserCookie($usernameDoge, $passwordDoge)
  {
    $getCookie = Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'Login',
      'Key' => '1b4755ced78e4d91bce9128b9a053cad',
      'username' => $usernameDoge,
      'password' => $passwordDoge,
      'Totp' => ''
    ]);
    Log::info($getCookie->body());

    return $getCookie->json()['SessionCookie'];
  }
}
