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
        // Unificación: No forzamos conexión 'nativephp'. Usamos la predeterminada (sqlite).
        // Esto permite que el Login y la Sincro compartan el mismo archivo.
        
        if (! $this->app->runningInConsole()) {
            try {
                // 1. Configuración de la Ventana Principal
                // Abrimos la ventana PRIMERO para que el usuario no sienta que la app no abre
                $isConfigured = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->exists();
                $initialUrl = $isConfigured ? url('/login') : url('/setup');

                Window::open()
                    ->title('ParkiApp - Gestión de Parqueadero')
                    ->width(1200)
                    ->height(800)
                    ->url($initialUrl)
                    ->showDevTools(true);

                // 2. Sincronización Automática de Arranque (PULL)
                // Se ejecuta después de abrir la ventana para no bloquear el inicio
                $token = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'tenant_sync_token')->value('value');
                if ($token) {
                    // Lo ejecutamos. Window::open() ya envió la señal a Electron, así que esto corre en paralelo
                    app(\App\Services\SyncService::class)->pull();
                }
            } catch (\Exception $e) {
                logger()->info('Error en arranque de NativePHP: ' . $e->getMessage());
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
