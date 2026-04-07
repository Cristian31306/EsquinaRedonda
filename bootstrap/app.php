<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'shift_open' => \App\Http\Middleware\EnsureCashShiftIsOpen::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $backupTime = \App\Models\Setting::where('key', 'backup_time')->value('value') ?: '23:00';
        $schedule->command('app:scheduled-backup')->dailyAt($backupTime);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
