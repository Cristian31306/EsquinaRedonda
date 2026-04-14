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
    protected $columnCache = [];

    public function __construct()
    {
        // En escritorio, buscamos el token en la base de datos local primero
        $dbToken = DB::table('settings')->where('key', 'tenant_sync_token')->value('value');
        
        $this->baseUrl = config('app.cloud_url', 'https://parkiapp.algorah.bond/api/v1');
        $this->token = $dbToken ?? config('app.tenant_token');
    }

    /**
     * UNIFICACIÓN: Ejecuta PUSH y PULL de forma secuencial.
     */
    public function syncAll()
    {
        $push = $this->push();
        $pull = $this->pull();

        return [
            'push' => $push,
            'pull' => $pull,
            'success' => $push['success'] && $pull['success'],
            'message' => "Sincronización completa: {$push['message']} | {$pull['message']}"
        ];
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

        $pendingTickets = Ticket::where('sync_status', 'pending')->get();
        $pendingVehicles = Vehicle::where('sync_status', 'pending')->get();
        
        // Integridad: Asegurarnos de que si enviamos un ticket, el vehículo también se envíe
        // incluso si localmente ya cree estar "synced", por si acaso en la nube no está.
        $referencedVehicleIds = $pendingTickets->pluck('vehicle_id')->unique();
        $extraVehicles = Vehicle::whereIn('id', $referencedVehicleIds)
            ->where('sync_status', '!=', 'pending')
            ->get();

        $payload = [
            'vehicles'    => $pendingVehicles->merge($extraVehicles),
            'cash_shifts' => CashShift::where('sync_status', 'pending')->get(),
            'tickets'     => $pendingTickets,
            'payments'    => Payment::where('sync_status', 'pending')->get(),
        ];

        // Si no hay nada que sincronizar, retornamos éxito inmediato
        if ($pendingTickets->isEmpty() && $pendingVehicles->isEmpty() && collect($payload)->flatten()->isEmpty()) {
            return ['success' => true, 'message' => 'Todo está al día.'];
        }

        try {
            $response = Http::withToken($this->token)
                ->withoutVerifying()
                ->timeout(30)
                ->post("{$this->baseUrl}/sync/push?token={$this->token}", $payload);

            if ($response->successful()) {
                $this->markAsSynced($payload);
                return ['success' => true, 'message' => 'Sincronización exitosa.', 'synced_at' => now()];
            }

            logger()->error('SyncService PUSH falló: ' . $response->body());
            return ['success' => false, 'message' => 'Error del servidor: ' . $response->body()];
        } catch (\Exception $e) {
            logger()->error('SyncService PUSH excepción: ' . $e->getMessage());
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
                ->withoutVerifying()
                ->timeout(30)
                ->get("{$this->baseUrl}/sync/pull?token={$this->token}");

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
                        
                        logger()->info('SyncService: PULL exitoso. Datos actualizados localmente.');
                    }

                    // Actualizar Ajustes
                    if (isset($data['settings'])) {
                        foreach ($data['settings'] as $setting) {
                            $settingData = $this->filterSchemaData('settings', (array)$setting);
                            DB::table('settings')->updateOrInsert(['id' => $settingData['id']], $settingData);
                        }
                    }

                    // --- NUEVO: Sincronización de Datos de Negocio ---
                    
                    // Actualizar Vehículos
                    if (isset($data['vehicles'])) {
                        foreach ($data['vehicles'] as $vehicle) {
                            $vehicleData = $this->filterSchemaData('vehicles', (array)$vehicle);
                            $vehicleData['sync_status'] = 'synced'; // Marcamos como sincronizado
                            DB::table('vehicles')->updateOrInsert(['id' => $vehicleData['id']], $vehicleData);
                        }
                    }

                    // Actualizar Tickets (ENTRADAS/SALIDAS)
                    if (isset($data['tickets'])) {
                        foreach ($data['tickets'] as $ticket) {
                            $ticketData = $this->filterSchemaData('tickets', (array)$ticket);
                            $ticketData['sync_status'] = 'synced';
                            DB::table('tickets')->updateOrInsert(['id' => $ticketData['id']], $ticketData);
                        }
                    }

                    // Actualizar Pagos
                    if (isset($data['payments'])) {
                        foreach ($data['payments'] as $payment) {
                            $paymentData = $this->filterSchemaData('payments', (array)$payment);
                            $paymentData['sync_status'] = 'synced';
                            DB::table('payments')->updateOrInsert(['id' => $paymentData['id']], $paymentData);
                        }
                    }

                    // Actualizar Turnos (Cash Shifts)
                    if (isset($data['cash_shifts'])) {
                        foreach ($data['cash_shifts'] as $shift) {
                            $shiftData = $this->filterSchemaData('cash_shifts', (array)$shift);
                            $shiftData['sync_status'] = 'synced';
                            DB::table('cash_shifts')->updateOrInsert(['id' => $shiftData['id']], $shiftData);
                        }
                    }

                    DB::commit();
                    return ['success' => true, 'message' => 'Sincronización completa: Usuarios, Tarifas y Movimientos actualizados.'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    logger()->error('SyncService PULL error al guardar: ' . $e->getMessage());
                    return ['success' => false, 'message' => 'Error al guardar datos locales: ' . $e->getMessage()];
                }
            }

            logger()->error('SyncService PULL falló con estado: ' . $response->status());
            return ['success' => false, 'message' => 'Error al descargar datos.'];
        } catch (\Exception $e) {
            logger()->error('SyncService PULL excepción: ' . $e->getMessage());
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
        if (!isset($this->columnCache[$table])) {
            // Unificación total: usamos siempre la conexión por defecto del sistema
            $connection = config('database.default');
            $this->columnCache[$table] = Schema::connection($connection)->getColumnListing($table);
        }
        
        return array_intersect_key((array)$data, array_flip($this->columnCache[$table]));
    }
}
