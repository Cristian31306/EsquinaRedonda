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
        if (class_exists(\Native\Laravel\NativeServiceProvider::class)) {
            $this->app->register(\Native\Laravel\NativeServiceProvider::class);
        }

        if (class_exists(\App\Providers\NativeAppServiceProvider::class)) {
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
