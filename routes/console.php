<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Programar backup automático de seguridad extrema cada 4 horas
use Illuminate\Support\Facades\Schedule;
Schedule::command('backup:telegram')->everyFourHours();
