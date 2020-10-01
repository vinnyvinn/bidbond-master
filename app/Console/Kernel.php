<?php

namespace App\Console;

use App\Console\Commands\BidbondExpireCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BidbondExpireCommand::class,
        \Illuminate\Console\KeyGenerateCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bidbond:expire')
            ->dailyAt("4:00")
            ->sendOutputTo(storage_path('logs/cron.log'))
            ->withoutOverlapping();
    }
}
