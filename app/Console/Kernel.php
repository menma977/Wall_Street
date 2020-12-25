<?php

namespace App\Console;

use App\Models\CamelSetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param Schedule $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
//    $schedule->command('upgradeList')->everyFiveMinutes()->withoutOverlapping();
    $schedule->command('upgradeList')->everyMinute()->withoutOverlapping();

    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueExecution')->everyMinute()->withoutOverlapping();

    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();
    $schedule->command('queueCamelExecution')->everyMinute()->withoutOverlapping();

    $camelSetting = CamelSetting::find(1)->share_time;
    $schedule->command('shareQueueExecution')->cron("*/$camelSetting * * * *")->withoutOverlapping();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
