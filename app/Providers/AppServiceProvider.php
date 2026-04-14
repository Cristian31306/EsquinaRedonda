<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registro dinámico de NativePHP (Seguro para producción/VPS)
        // Verificamos la interfaz para evitar errores fatales al cargar la clase
        if (interface_exists(\Native\Laravel\Contracts\ProvidesPhpIni::class)) {
            $this->app->register(\Native\Laravel\NativeServiceProvider::class);
            $this->app->register(\App\Providers\NativeAppServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Activar modo WAL para SQLite (Mejora brutal de rendimiento concurrente en escritorio)
        if (config('database.default') === 'sqlite') {
            try {
                DB::statement('PRAGMA journal_mode = WAL;');
            } catch (\Exception $e) {
                // Silencioso si falla
            }
        }
    }
}
