<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Rbac\ShowRoles::class,
        Commands\Rbac\ShowPerms::class,
        Commands\Rbac\AddRole::class,
        Commands\Rbac\ResetPassword::class,
        Commands\Rbac\AddUser::class,
        Commands\Rbac\AddPerm::class,
        Commands\Rbac\AttachRole::class,
        Commands\Rbac\DetachRole::class,
        Commands\Rbac\AttachPerm::class,
        Commands\Rbac\DetachPerm::class,
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
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
