<?php

namespace App\Console;

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
			\Laravelista\LumenVendorPublish\VendorPublishCommand::class,
            Commands\Emailnotaryusers::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
        /* envia las credenciales pendientes de entregar de usuarios de notarias */
        $schedule
            ->command('mailing:notaryusers')
            ->everyFiveMinutes()
            ->runInBackground();
    }
}
