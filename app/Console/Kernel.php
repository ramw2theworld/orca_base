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
        // Modules commands...
        \App\Console\Commands\MakeModuleModel::class,
        \App\Console\Commands\MakeModuleController::class,
        \App\Console\Commands\MakeModuleMigration::class,
        \App\Console\Commands\MakeModuleRoute::class,
        \App\Console\Commands\MakeModuleTableAlterMigration::class,
        \App\Console\Commands\MakeModuleSeeder::class,
        \App\Console\Commands\MakeModuleSeeder::class,
        \App\Console\Commands\MakeModuleFactory::class,
        \App\Console\Commands\MakeModuleProvider::class,
        \App\Console\Commands\MakeModuleResource::class,
        \App\Console\Commands\MakeModuleRequest::class,
        \App\Console\Commands\MakeModuleEvent::class,
        \App\Console\Commands\MakeModuleListener::class,
        \App\Console\Commands\MakeModuleRule::class,
        \App\Console\Commands\MakeModuleRepositoryInterface::class,
        \App\Console\Commands\MakeModuleConfig::class,
        \App\Console\Commands\MakeFeatureTest::class,


    ];
    
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
