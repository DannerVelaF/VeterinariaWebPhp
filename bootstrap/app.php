<?php

use App\Http\Middleware\VerificarPermiso;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "permiso" => VerificarPermiso::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->withCommands([
        \App\Console\Commands\RevertirVentasPendientes::class
    ])->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('ventas:revertir-pendientes')->hourly();

        // Otras opciones disponibles:
        // ->everyMinute();           // Cada minuto
        // ->everyTwoMinutes();       // Cada 2 minutos
        // ->everyTenMinutes();       // Cada 10 minutos
        // ->everyFifteenMinutes();   // Cada 15 minutos
        // ->everyThirtyMinutes();    // Cada 30 minutos
        // ->hourly();                // Cada hora
        // ->daily();                 // Cada día a medianoche
        // ->dailyAt('13:00');        // Cada día a las 13:00
    })
    ->create();
