<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde registramos las rutas de la API para ParkiApp.
| Estas rutas son cargadas por el RouteServiceProvider y todas
| serán asignadas al grupo de middleware "api".
|
*/

Route::prefix('v1')->group(function () {
    
    // Ruta de estado
    Route::get('/status', function () {
        return response()->json(['status' => 'online', 'version' => '1.0.0']);
    });

    // Rutas de Sincronización protegidas por Token de Tenant
    Route::middleware([\App\Http\Middleware\CheckTenantToken::class])->group(function () {
        Route::post('/sync/push', [SyncController::class, 'push']);
        Route::get('/sync/pull', [SyncController::class, 'pull']);
        
        // Sincronización disparada desde el Dashboard/Botón
        Route::post('/sync/now', function (\App\Services\SyncService $sync) {
            $push = $sync->push();
            $pull = $sync->pull();
            return response()->json([
                'success' => $push['success'] && $pull['success'],
                'message' => $push['message'] . ' | ' . $pull['pull_message'] ?? '',
                'synced_at' => now()->format('d/m H:i')
            ]);
        });
    });

});
