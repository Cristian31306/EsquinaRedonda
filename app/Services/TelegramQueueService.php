<?php

namespace App\Services;

use App\Jobs\BackupToTelegram;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramQueueService
{
    /**
     * Intenta procesar trabajos de BackupToTelegram pendientes en la tabla 'jobs'.
     * Diseñado para ejecutarse de forma "piggyback" al final de otras peticiones.
     */
    public static function processPending()
    {
        try {
            // Buscamos trabajos que sean de BackupToTelegram en la tabla de jobs
            $jobs = DB::table('jobs')
                ->where('payload', 'like', '%BackupToTelegram%')
                ->orderBy('id', 'asc')
                ->limit(2) // Solo procesamos un par para no alargar mucho la espera del usuario
                ->get();

            foreach ($jobs as $jobData) {
                $payload = json_decode($jobData->payload, true);
                $command = unserialize($payload['data']['command']);

                if ($command instanceof BackupToTelegram) {
                    // Ejecutamos el handle manualmente
                    $command->handle();
                    
                    // Si no lanzó excepción, eliminamos el trabajo de la cola manual
                    DB::table('jobs')->where('id', $jobData->id)->delete();
                }
            }
        } catch (\Exception $e) {
            // Si falla por falta de internet o timeout, simplemente lo dejamos para el próximo intento
            Log::info('Telegram Piggyback failed (expected if offline): ' . $e->getMessage());
        }
    }
}
