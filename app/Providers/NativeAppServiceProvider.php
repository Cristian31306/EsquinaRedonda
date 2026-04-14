<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider extends ServiceProvider implements ProvidesPhpIni
{
    /**
     * Variable para asegurar que el arranque solo ocurra una vez por ejecución.
     */
    protected static $booted = false;

    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Unificación: No forzamos conexión 'nativephp'. Usamos la predeterminada (sqlite).
        // Evitamos re-ejecutar en navegaciones de Inertia o peticiones API internas
        if (static::$booted || $this->app->runningInConsole() || request()->hasHeader('X-Inertia') || request()->is('api/*')) {
            return;
        }

        try {
                // 1. Configuración de la Ventana Principal (Solo si estamos en NativePHP real)
                if (! class_exists('Native\Laravel\Facades\Window')) {
                    return;
                }

                $isConfigured = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->exists();
                $initialUrl = $isConfigured ? url('/login') : url('/setup');

                \Native\Laravel\Facades\Window::open()
                    ->title('ParkiApp - Gestión de Parqueadero')
                    ->width(1200)
                    ->height(800)
                    ->url($initialUrl)
                    ->showDevTools(false);

                // Establecer un menú vacío (oculta File, Edit, etc)
                if (class_exists('Native\Laravel\Facades\Menu')) {
                    \Native\Laravel\Facades\Menu::create();
                }

                // 2. Sincronización Automática de Arranque (PULL)
                $token = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->value('value');
                if ($token && ! \Illuminate\Support\Facades\Cache::has('startup_sync_done')) {
                    \App\Jobs\SyncDataJob::dispatch();
                    \Illuminate\Support\Facades\Cache::put('startup_sync_done', true, now()->addMinutes(30));
                }

                static::$booted = true;
            } catch (\Exception $e) {
                logger()->info('Error en arranque de NativePHP detectado (probablemente entorno web): ' . $e->getMessage());
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
