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
        set_time_limit(120); // Allow up to 2 minutes for processing piggyback jobs
        try {
            // Buscamos trabajos que sean de BackupToTelegram en la tabla de jobs
            // Solo procesamos los que NO están reservados
            $jobs = DB::transaction(function () {
                $jobs = DB::table('jobs')
                    ->where('payload', 'like', '%BackupToTelegram%')
                    ->whereNull('reserved_at')
                    ->orderBy('id', 'asc')
                    ->limit(2)
                    ->get();

                foreach ($jobs as $job) {
                    DB::table('jobs')->where('id', $job->id)->update([
                        'reserved_at' => now()->getTimestamp()
                    ]);
                }

                return $jobs;
            });

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
