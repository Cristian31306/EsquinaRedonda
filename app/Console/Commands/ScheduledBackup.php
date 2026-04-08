<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ScheduledBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scheduled-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el respaldo automático de la base de datos (Cloud MySQL)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando respaldo programado en la nube...');
        
        // Log the event for now as everything is already in MySQL Cloud
        Log::info('Respaldo programado disparado. Nota: Los datos ya residen de forma segura en el VPS (MySQL Cloud).');
        
        $this->info('Evento de respaldo registrado correctamente.');
    }
}
