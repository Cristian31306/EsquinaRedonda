<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);
    }
}
