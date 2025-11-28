<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ... otros comandos programados que ya tengas ...

        // 🔹 Agregar esta línea para la auditoría diaria de citas
        $schedule->command('citas:auditar-solapadas')->daily();
        
        // También puedes agregarlo a una hora específica, por ejemplo a las 6 AM:
        // $schedule->command('citas:auditar-solapadas')->dailyAt('06:00');
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