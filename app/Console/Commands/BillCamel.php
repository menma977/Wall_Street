<?php

namespace App\Console\Commands;

use App\Models\Binary;
use App\Models\Upgrade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BillCamel extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'billCamel';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Bill to user for camel upgrade';

  /**
   * Execute the console command.
   *
   */
  public function handle()
  {
    $bill = BillCamel::where('status', false)->where('created_at', '<=', Carbon::now());
    // TODO: tampungan ngambil dmna?
    $from = "";
    $to = User::find($bill->user)->id;
    if ($bill) {
      if ($this->withdraw($from, $to, $bill->value)) {
        $bill->status = true;
        $bill->save();
      } else {
        $bill->last_try = Carbon::now()->addMinutes(10);
        $bill->save();
      }
    }
    // $queue = Queue::where('status', false)->where('created_at', '<=', Carbon::now())->where('type', 'like', 'camel_%')->first();
    // if ($upgrade) {
    //   $this->binaryHandler($upgrade->from, $upgrade->debit / 3);
    //   $upgrade->status = true;
    //   $upgrade->save();
    // }
  }

  private function withdraw($privateKey, $targetWallet, $value)
  {
    $withdraw = Http::asForm()->post(
      'https://api.cameltoken.io/tronapi/sendtoken',
      [
        'privkey' => $privateKey,
        'to' => $targetWallet,
        'amount' => $value,
      ]
    );
    sleep(60);
    $validate = Http::get("https://api.cameltoken.io/tronapi/gettxstatus/" . $withdraw->json()['txid']);

    return ($withdraw->successful()
      && str_contains($withdraw->body(), 'failed') === false
      && str_contains($validate->body(), 'failed') === false);
  }
}
