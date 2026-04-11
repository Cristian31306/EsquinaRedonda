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
        // Solo ejecutar migraciones nativas si la conexión existe (evita errores en la nube/VPS)
        if (config('database.connections.nativephp')) {
            try {
                Artisan::call('migrate', [
                    '--force' => true,
                    '--database' => 'nativephp'
                ]);
            } catch (\Exception $e) {
                logger()->error('Error ejecutando migraciones nativas: ' . $e->getMessage());
            }
        }

        if (config('database.connections.nativephp') && ! $this->app->runningInConsole()) {
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
                logger()->info('NativePHP Window no pudo abrirse: ' . $e->getMessage());
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
