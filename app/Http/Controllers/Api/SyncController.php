<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\CashShift;
use App\Models\Rate;
use App\Models\Membership;

class SyncController extends Controller
{
    /**
     * PUSH: Recibe datos de la app local y los guarda/actualiza en la nube.
     */
    public function push(Request $request)
    {
        $tenant = $request->input('current_tenant');
        $payload = $request->all();

        DB::beginTransaction();
        try {
            // Desactivar temporalmente restricciones de integridad para permitir sincronización masiva
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Sincronizar Vehículos primero (dependencia de tickets)
            if (isset($payload['vehicles'])) {
                foreach ($payload['vehicles'] as $data) {
                    Vehicle::updateOrCreate(['id' => $data['id']], array_merge($data, ['tenant_id' => $tenant->id]));
                }
            }

            // 2. Sincronizar Turnos de Caja
            if (isset($payload['cash_shifts'])) {
                foreach ($payload['cash_shifts'] as $data) {
                    CashShift::updateOrCreate(['id' => $data['id']], array_merge($data, ['tenant_id' => $tenant->id]));
                }
            }

            // 3. Sincronizar Tickets
            if (isset($payload['tickets'])) {
                foreach ($payload['tickets'] as $data) {
                    Ticket::updateOrCreate(['id' => $data['id']], array_merge($data, ['tenant_id' => $tenant->id]));
                }
            }

            // 4. Sincronizar Pagos
            if (isset($payload['payments'])) {
                foreach ($payload['payments'] as $data) {
                    Payment::updateOrCreate(['id' => $data['id']], array_merge($data, ['tenant_id' => $tenant->id]));
                }
            }

            // Reactivar restricciones de integridad
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            DB::commit();
            return response()->json(['message' => 'Sincronización PUSH exitosa.', 'synced_at' => now()]);

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return response()->json(['message' => 'Error en sincronización: ' . $e->getMessage()], 500);
        }
    }

    /**
     * PULL: Envía datos actualizados desde la nube hacia la app local.
     */
    public function pull(Request $request)
    {
        $tenant = $request->get('current_tenant');

        return response()->json([
            'tenant'      => $tenant,
            'rates'       => Rate::where('tenant_id', $tenant->id)->get(),
            'memberships' => Membership::where('tenant_id', $tenant->id)->active()->get(),
            'settings'    => DB::table('settings')->where('tenant_id', $tenant->id)->get(),
            'users'       => DB::table('users')->where('tenant_id', $tenant->id)->where('is_active', true)->select('id', 'name', 'email', 'password', 'role', 'tenant_id')->get(),
            
            // --- NUEVO: Sincronización de Datos de Negocio ---
            'vehicles'    => Vehicle::where('tenant_id', $tenant->id)->get(),
            'tickets'     => Ticket::where('tenant_id', $tenant->id)->latest()->limit(500)->get(),
            'payments'    => Payment::where('tenant_id', $tenant->id)->latest()->limit(500)->get(),
            'cash_shifts' => CashShift::where('tenant_id', $tenant->id)->latest()->limit(100)->get(),
            
            'pulled_at'   => now()
        ]);
    }
}
