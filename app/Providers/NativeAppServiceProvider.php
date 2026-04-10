<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider extends ServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            try {
                Window::open()
                    ->title('ParkiApp - Gestión de Parqueadero')
                    ->width(1200)
                    ->height(800)
                    ->url(url('/dashboard'))
                    ->showDevTools(false);
            } catch (\Exception $e) {
                // Silenciamos el error si no estamos en entorno NativePHP
                // Esto permite que la app funcione en el navegador normal
                logger()->info('NativePHP Window no pudo abrirse: el servidor local no está activo.');
            }
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
