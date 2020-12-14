<?php

namespace App\Console\Commands;

use App\Models\ShareQueue;
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
  protected $signature = 'ShareQueueExecution';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Execute Share Queue';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    $shareQueue = ShareQueue::where('status', false)->where('created_at', Carbon::now())->first();
    if ($shareQueue) {
      try {
        $upgradeList = UpgradeList::find(1);
        $userAdmin = User::find(2);

        if ($shareQueue->type === 'btc') {
          $walletTarget = User::find($shareQueue->send)->wallet_btc;
          $formatValue = number_format(($shareQueue->value * $upgradeList->idr) / $upgradeList->btc, 8, ',', '');
          $value = str_replace(',', '', $formatValue);
        } else if ($shareQueue->type === 'doge') {
          $walletTarget = User::find($shareQueue->send)->wallet_doge;
          $formatValue = number_format(($shareQueue->value * $upgradeList->idr) / $upgradeList->doge, 8, ',', '');
          $value = str_replace(',', '', $formatValue);
        } else if ($shareQueue->type === 'eth') {
          $walletTarget = User::find($shareQueue->send)->wallet_eth;
          $formatValue = number_format(($shareQueue->value * $upgradeList->idr) / $upgradeList->eth, 8, ',', '');
          $value = str_replace(',', '', $formatValue);
        } else {
          $walletTarget = User::find($shareQueue->send)->wallet_ltc;
          $formatValue = number_format(($shareQueue->value * $upgradeList->idr) / $upgradeList->ltc, 8, ',', '');
          $value = str_replace(',', '', $formatValue);
        }

        if (!$userAdmin->cookie) {
          $userAdmin->cookie = $this->getUserCookie($userAdmin->username_doge, $userAdmin->password_doge);
          $userAdmin->save();
        }

        if ($this->withdraw($userAdmin->cookie, $value, $walletTarget, $shareQueue->type)) {
          $shareQueue->status = true;
        } else {
          $shareQueue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        }
        $shareQueue->save();
      } catch (Exception $e) {
        Log::error($e->getMessage() . ' | Queue Line : ' . $e->getLine());
        $shareQueue->created_at = Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s');
        $shareQueue->save();
      }
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
    Log::info($value);
    Log::info($wallet);
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
      'Key' => '1b4755ced78e4d91bce9128b9a053cad',
      'username' => $usernameDoge,
      'password' => $passwordDoge,
      'Totp' => ''
    ]);
    Log::info($getCookie->body());

    return $getCookie->json()['SessionCookie'];
  }
}
