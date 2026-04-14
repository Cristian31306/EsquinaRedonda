<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearOperationalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-data {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todos los datos operativos (tickets, pagos, mensualidades, turnos y vehículos) pero conserva usuarios y configuraciones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿ESTÁS SEGURO? Esta acción borrará todos los registros de operación y no se puede deshacer.')) {
            $this->info('Operación cancelada.');
            return;
        }

        $tables = [
            'payments',
            'memberships',
            'tickets',
            'cash_shifts',
            'vehicles',
            'cache',
            'cache_locks',
            'jobs',
            'failed_jobs',
            'sessions'
        ];

        $this->info('Iniciando limpieza de datos...');

        try {
            DB::beginTransaction();

            // Desactivar llaves foráneas según el driver
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF');
            } else {
                Schema::disableForeignKeyConstraints();
            }

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("Tabla [{$table}] limpiada.");
                }
            }

            // Reactivar llaves foráneas
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON');
            } else {
                Schema::enableForeignKeyConstraints();
            }

            DB::commit();
            $this->info('¡Limpieza completada con éxito!');
            $this->warn('Los usuarios, las tarifas y la configuración han sido preservados.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Ocurrió un error: ' . $e->getMessage());
        }
    }
}
