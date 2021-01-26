<?php

namespace App\Console\Commands;

use App\Models\QueueDaily;
use App\Models\User;
use Illuminate\Console\Command;

class QueueDailySet extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'queueDailySet';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Queue Daily Set in 12 AM';

  /**
   * Execute the console command.
   *
   */
  public function handle()
  {
    $users = User::where('level', '>', 0)->whereNotBetween('id', [1, 16])->whereNotIn('id', [489])->get();
    foreach ($users as $user) {
      $getUser = QueueDaily::where('user_id', $user->id)->where('send', false)->count();
      if (!$getUser) {
        $queueDaily = new QueueDaily();
        $queueDaily->user_id = $user->id;
        $queueDaily->save();
      }
    }

    QueueDaily::where('send', true)->delete();
  }
}
