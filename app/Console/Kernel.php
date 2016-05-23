<?php

namespace lde\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use lde\Initiative;
use lde\MetaInitiative;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //Checks (every minute) if there are some expired (meta)initiative and if so get the result
        //Here is the only Cron entry you need to add to your server:
        //       * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
        $schedule->call(function () {
            Initiative::checkVoting();
            Metainitiative::checkVoting();
        })->everyMinute();
    }
}
