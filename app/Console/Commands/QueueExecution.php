<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use App\Models\BTC;
use App\Models\Doge;
use App\Models\ETH;
use App\Models\LTC;
use App\Models\Queue;
use App\Models\ShareIt;
use App\Models\ShareQueue;
use App\Models\Upgrade;
use App\Models\UpgradeList;
use App\Models\User;
use App\Models\WalletAdmin;
use Carbon\Carbon;
use Exception;
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
    $queue = Queue::where('status', false)->where('created_at', '<=', Carbon::now())->where('type', 'not like', 'camel_%')->first();
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
        $upgradeList = UpgradeList::find(1);

        $formatValue = number_format(($queue->value * $upgradeList->idr) / $upgradeList->$targetBalance, 8, '', '');
        $value = $formatValue;

        switch ($typeBalance) {
          case "btc":
            $wallet_class = BTC::class;
            break;
          case "ltc":
            $wallet_class = LTC::class;
            break;
          case "eth":
            $wallet_class = ETH::class;
            break;
          case "doge":
            $wallet_class = Doge::class;
            break;
        }

        if ($typeBalance === 'level') {
          $wallet = "wallet_" . $targetBalance;
          $walletTarget = User::find($queue->send)->$wallet;
          if ($this->level($targetBalance, $user, User::find($queue->send), $walletTarget, $value, $queue->value)) {
            $queue->status = true;
            $this->updateFakeBalance($user->id, false, $wallet_class, "level cut for " . $queue->send, $targetBalance);
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($typeBalance === 'buyWall') {
          $wallet = "wallet_" . $targetBalance;
          $walletTarget = WalletAdmin::find($queue->send)->$wallet;
          if ($this->buyWall($targetBalance, $user, WalletAdmin::find($queue->send), $walletTarget, $value, $queue->value)) {
            $queue->status = true;
            $this->updateFakeBalance($user->id, false, $wallet_class, "buy wall cut", $targetBalance);
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else if ($typeBalance === 'it') {
          $wallet = "wallet_" . $targetBalance;
          $walletTarget = ShareIt::find(1)->$wallet;
          if ($this->it($targetBalance, $user, $walletTarget, $value, $queue->value)) {
            $queue->status = true;
            $this->updateFakeBalance($user->id, false, $wallet_class, "IT cut ", $targetBalance);
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
        } else {
          $wallet = "wallet_" . $targetBalance;
          $walletTarget = BankAccount::find($queue->send)->$wallet;
          if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
            $this->share($targetBalance, $queue->value);
            $queue->status = true;
            $this->updateFakeBalance($user->id, false, $wallet_class, "Share cut", $targetBalance);
          } else {
            $queue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
          }
          $queue->save();
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
   * @param $rawValue
   * @return bool
   */
  private function level($type, $user, $targetUser, $walletTarget, $value, $rawValue)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'upgrade level ' . $user->username;
      $upgrade->type = $type;
      $upgrade->level = $user->level;
      $upgrade->credit = $rawValue;
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
   * @param $rawValue
   * @return bool
   */
  private function buyWall($type, $user, $targetUser, $walletTarget, $value, $rawValue)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
      $upgrade = new Upgrade();
      $upgrade->from = $user->id;
      $upgrade->to = $targetUser->id;
      $upgrade->description = 'BuyWall ' . $user->username;
      $upgrade->type = $type;
      $upgrade->level = $user->level;
      $upgrade->credit = $rawValue;
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
   * @param $rawValue
   * @return bool
   */
  private function it($type, $user, $walletTarget, $value, $rawValue)
  {
    if ($this->withdraw($user->cookie, $value, $walletTarget, $type)) {
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
   * @param $type
   * @param $value
   */
  private function share($type, $value)
  {
    $balanceToShare = $value / 20;
    for ($i = 0; $i < 20; $i++) {
      $shareQueue = new ShareQueue();
      $shareQueue->user_id = User::where('id', '!=', 2)->inRandomOrder()->first()->id;
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
    Log::info("====================================");
    Log::info($value . " - " . $wallet);
    Log::info("====================================");

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
      'Key' => 'ec01af0702f3467a808ba52679e1ee61',
      'username' => $usernameDoge,
      'password' => $passwordDoge,
      'Totp' => ''
    ]);
    Log::info($getCookie->body());

    return $getCookie->json()['SessionCookie'];
  }

  /**
   * @param $user_id
   * @param $isDebit
   * @param BTC|LTC|ETH|Doge $type
   * @param $description
   * @param $value
   * @return object
   */
  private function updateFakeBalance($user_id, $isDebit, $type, $description, $value)
  {
    $fakeWallet = new $type([
      'user_id' => $user_id,
      'description' => $description,
      'debit' => '0',
      'credit' => '0',
    ]);
    if ($isDebit) {
      $fakeWallet->debit = $value;
    } else {
      $fakeWallet->credit = $value;
    }
    $fakeWallet->save();
    return $fakeWallet;
  }
}
