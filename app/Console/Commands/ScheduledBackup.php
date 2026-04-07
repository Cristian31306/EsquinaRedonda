<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BackupToTelegram;
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
    protected $description = 'Ejecuta el respaldo de la base de datos y lo envía a Telegram (Programado)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando respaldo programado...');
        
        // Dispatch the job without a shift ID (general backup)
        BackupToTelegram::dispatch();
        
        $this->info('Respaldo encolado correctamente.');
        Log::info('Respaldo programado disparado desde el comando Artisan.');
    }
}
