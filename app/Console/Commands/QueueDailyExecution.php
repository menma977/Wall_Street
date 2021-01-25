<?php

namespace App\Console\Commands;

use App\Models\QueueDaily;
use App\Models\QueueDailyBank;
use App\Models\Upgrade;
use App\Models\User;
use Illuminate\Console\Command;

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

        }
      } catch (Exception $e) {

      }
    }
  }
}
