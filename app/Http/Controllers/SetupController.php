<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SetupController extends Controller
{
    public function index()
    {
        // Si ya está configurado, mandar al login
        if (DB::table('settings')->where('key', 'tenant_sync_token')->exists()) {
            return redirect()->route('login');
        }

        return Inertia::render('Setup', [
            'apiUrl' => config('app.cloud_url', 'https://parkiapp.algorah.bond/api/v1')
        ]);
    }

    public function store(Request $request, SyncService $sync)
    {
        $request->validate([
            'token' => 'required|string|min:20',
        ]);

        // Guardar el token localmente (Manejo manual de UUID para evitar error de Integridad)
        $existing = DB::table('settings')->where('key', 'tenant_sync_token')->first();
        if ($existing) {
            DB::table('settings')->where('key', 'tenant_sync_token')->update(['value' => $request->token]);
        } else {
            DB::table('settings')->insert([
                'id' => (string) Str::uuid(),
                'key' => 'tenant_sync_token',
                'value' => $request->token,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Disparar sincronización inicial para traer Usuarios y Tarifas
        $result = $sync->setToken($request->token)->pull();

        if ($result['success']) {
            // Optimización: Marcar en caché que ya estamos configurados
            \Illuminate\Support\Facades\Cache::forever('desktop_configured', true);
            
            return redirect()->route('login')->with('success', 'Configuración completada. Ya puedes iniciar sesión.');
        }

        // Si falló el pull, el token probablemente sea inválido (limpiar para reintentar)
        DB::table('settings')->where('key', 'tenant_sync_token')->delete();
        
        return back()->withErrors(['token' => 'No se pudo sincronizar: ' . $result['message']]);
    }
}
