<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        // En escritorio, buscamos el token en la base de datos local primero
        $dbToken = DB::table('settings')->where('key', 'tenant_sync_token')->value('value');
        
        $this->baseUrl = config('app.cloud_url', 'https://parkiapp.algorah.bond/api/v1');
        $this->token = $dbToken ?? config('app.tenant_token');
    }

    /**
     * Establece el token de forma dinámica (útil para configuración inicial).
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
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
                
                DB::beginTransaction();
                try {
                    // Actualizar Datos de Empresa
                    if (isset($data['tenant'])) {
                        $tenantData = $this->filterSchemaData('tenants', $data['tenant']);
                        unset($tenantData['api_token']); 
                        DB::table('tenants')->updateOrInsert(['id' => $tenantData['id']], $tenantData);
                    }

                    // Actualizar Usuarios (Fundamental para Login Local)
                    if (isset($data['users'])) {
                        foreach ($data['users'] as $user) {
                            $userData = $this->filterSchemaData('users', $user);
                            DB::table('users')->updateOrInsert(['id' => $userData['id']], $userData);
                        }
                    }

                    // Actualizar Tarifas Locales
                    if (isset($data['rates'])) {
                        foreach ($data['rates'] as $rate) {
                            $rateData = $this->filterSchemaData('rates', (array)$rate);
                            DB::table('rates')->updateOrInsert(['id' => $rateData['id']], $rateData);
                        }
                    }

                    // Actualizar Ajustes
                    if (isset($data['settings'])) {
                        foreach ($data['settings'] as $setting) {
                            $settingData = $this->filterSchemaData('settings', (array)$setting);
                            DB::table('settings')->updateOrInsert(['id' => $settingData['id']], $settingData);
                        }
                    }

                    DB::commit();
                    return ['success' => true, 'message' => 'Sincronización completa: Usuarios, Tarifas y Ajustes actualizados.'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    return ['success' => false, 'message' => 'Error al guardar datos locales: ' . $e->getMessage()];
                }
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

    /**
     * Filtra los datos para que solo contengan columnas que existen en la tabla local.
     */
    protected function filterSchemaData($table, $data)
    {
        // Determinamos la conexión
        $connection = config('nativephp.database_path') ? 'nativephp' : config('database.default');
        
        $columns = Schema::connection($connection)->getColumnListing($table);
        
        return array_intersect_key((array)$data, array_flip($columns));
    }
}
