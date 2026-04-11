<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
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
        // Forzar migraciones automáticas en la base de datos nativa al iniciar
        try {
            Artisan::call('migrate', [
                '--force' => true,
                '--database' => 'nativephp'
            ]);
        } catch (\Exception $e) {
            logger()->error('Error ejecutando migraciones nativas: ' . $e->getMessage());
        }

        if (! $this->app->runningInConsole()) {
            try {
                // Optimización de arranque: Usar caché rápida para evitar bloqueos por DB
                $isConfigured = \Illuminate\Support\Facades\Cache::rememberForever('desktop_configured', function() {
                    return \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->exists();
                });

                $initialUrl = $isConfigured ? url('/dashboard') : url('/setup');

                Window::open()
                    ->title('ParkiApp - Gestión de Parqueadero')
                    ->width(1200)
                    ->height(800)
                    ->url($initialUrl)
                    ->showDevTools(true);
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
