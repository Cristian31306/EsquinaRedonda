<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\CashShift;
use App\Models\Membership;
use App\Models\Tenant;

class SyncService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // En producción, estas variables vendrían del archivo .env o configuración
        $this->baseUrl = config('app.cloud_url', 'https://tu-vps-contabo.com/api/v1');
        $this->token = config('app.tenant_token');
    }

    /**
     * PUSH: Sincroniza datos locales pendientes hacia la nube.
     */
    public function push()
    {
        if (!$this->token) return ['success' => false, 'message' => 'Token de API no configurado.'];

        $payload = [
            'vehicles'    => Vehicle::where('sync_status', 'pending')->get(),
            'cash_shifts' => CashShift::where('sync_status', 'pending')->get(),
            'tickets'     => Ticket::where('sync_status', 'pending')->get(),
            'payments'    => Payment::where('sync_status', 'pending')->get(),
        ];

        // Si no hay nada que sincronizar, retornamos éxito inmediato
        if (collect($payload)->flatten()->isEmpty()) {
            return ['success' => true, 'message' => 'Todo está al día.'];
        }

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post("{$this->baseUrl}/sync/push", $payload);

            if ($response->successful()) {
                $this->markAsSynced($payload);
                return ['success' => true, 'message' => 'Sincronización exitosa.', 'synced_at' => now()];
            }

            return ['success' => false, 'message' => 'Error del servidor: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Fallo de conexión: ' . $e->getMessage()];
        }
    }

    /**
     * PULL: Descarga actualizaciones globales (tarifas, membresías) desde la nube.
     */
    public function pull()
    {
        if (!$this->token) return ['success' => false, 'message' => 'Token de API no configurado.'];

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->get("{$this->baseUrl}/sync/pull");

            if ($response->successful()) {
                $data = $response->json();
                
                // Actualizar Tarifas Locales
                if (isset($data['rates'])) {
                    foreach ($data['rates'] as $rate) {
                        DB::table('rates')->updateOrInsert(['id' => $rate['id']], $rate);
                    }
                }

                return ['success' => true, 'message' => 'Datos globales actualizados.'];
            }

            return ['success' => false, 'message' => 'Error al descargar datos.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()];
        }
    }

    /**
     * Marca los registros locales como sincronizados.
     */
    protected function markAsSynced($payload)
    {
        foreach ($payload as $table => $collections) {
            $ids = $collections->pluck('id')->toArray();
            if (!empty($ids)) {
                DB::table($table)->whereIn('id', $ids)->update([
                    'sync_status' => 'synced',
                    'last_synced_at' => now()
                ]);
            }
        }
    }
}
