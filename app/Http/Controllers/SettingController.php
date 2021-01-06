<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\CamelSetting;
use App\Models\Dice;
use App\Models\ShareQueue;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
  public function deleteDice($id)
  {
    Dice::where('user_id', $id)->delete();
    ShareQueue::where('user_id', $id)->delete();

    return redirect()->back()->with(['message' => 'success delete dice']);
  }

  /**
   * @return JsonResponse
   */
  public function balance()
  {
    $camelBank = CamelSetting::find(1);
    $bankCoin = BankAccount::find(1);

    $camel = $this->camel($camelBank->wallet_camel);
    $tron = $this->tron($camelBank->wallet_camel);
    $coin = $this->coin($bankCoin);

    if (str_contains($camel->body(), 'success') === true && str_contains($tron->body(), 'success') === true && str_contains($coin->body(), 'LoginInvalid') === false) {
      return response()->json([
        'message' => 'balance hash been load',
        'camel' => number_format($camel->json()['balance'], 8),
        'tron' => number_format($tron->json()['balance'], 8),
        'btc' => number_format($coin->json()['Balance'] / 10 ** 8, 8),
        'doge' => number_format($coin->json()['Doge']['Balance'] / 10 ** 8, 8),
        'ltc' => number_format($coin->json()['LTC']['Balance'] / 10 ** 8, 8),
        'eth' => number_format($coin->json()['ETH']['Balance'] / 10 ** 8, 8),
      ]);
    }

    return response()->json([
      'message' => 'response failed',
      'camel' => number_format(0, 8),
      'tron' => number_format(0, 8),
      'btc' => number_format(0, 8),
      'doge' => number_format(0, 8),
      'ltc' => number_format(0, 8),
      'eth' => number_format(0, 8),
    ]);
  }

  /**
   * @param $account
   * @return Response
   */
  private function coin($account): Response
  {
    return Http::asForm()->post('https://www.999doge.com/api/web.aspx', [
      'a' => 'Login',
      'key' => 'ec01af0702f3467a808ba52679e1ee61',
      'username' => $account->username,
      'password' => $account->password
    ]);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private function camel($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/getbalance/" . $wallet);
  }

  /**
   * @param $wallet
   * @return Response
   */
  private function tron($wallet): Response
  {
    return Http::get("https://api.cameltoken.io/tronapi/gettokenbalance/" . $wallet);
  }
}
