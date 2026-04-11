<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta migración fuerza la conversión de IDs a UUID en MySQL (Cloud) 
     * para asegurar la compatibilidad con el código actual.
     */
    public function up(): void
    {
        // Solo actuar si estamos en MySQL (El entorno Cloud)
        if (config('database.default') !== 'mysql') {
            return;
        }

        $dbName = config('database.connections.mysql.database');

        // 1. LIMPIEZA DINÁMICA DE CLAVES FORÁNEAS
        // Buscamos todas las restricciones que apuntan a tenants o users en esta base de datos
        $foreignKeys = DB::select("
            SELECT TABLE_NAME, CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_SCHEMA = ? 
            AND REFERENCED_TABLE_NAME IN ('tenants', 'users')
        ", [$dbName]);

        Schema::disableForeignKeyConstraints();

        foreach ($foreignKeys as $fk) {
            try {
                Schema::table($fk->TABLE_NAME, function (Blueprint $table) use ($fk) {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                });
            } catch (\Exception $e) {
                // Si falla el borrado de una FK específica, seguimos adelante
            }
        }

        // 2. LIMPIAR DATOS DE TODAS LAS TABLAS DEL SISTEMA (Última vez)
        // Obtenemos todas las tablas de la base de datos para no dejarnos ninguna
        $tables = DB::select("SHOW TABLES");
        $tableKey = \"Tables_in_\" . $dbName;

        foreach ($tables as $table) {
            $tableName = $table->\$tableKey;
            
            // Solo truncamos tablas del core que sabemos que deben resetearse para el cambio de UUID
            $coreTables = ['tenants', 'users', 'settings', 'rates', 'tickets', 'payments', 'memberships', 'shifts', 'cash_shifts', 'vehicle_types'];
            if (in_array($tableName, $coreTables)) {
                DB::table($tableName)->truncate();
            }
        }

        // 3. TRANSFORMAR TENANTS
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
        });

        // 4. TRANSFORMAR USERS
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id', 'tenant_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
            $table->uuid('tenant_id')->nullable()->after('id');
        });

        // 5. TRANSFORMAR TODA COLUMNA 'tenant_id' DE CUALQUIER TABLA A UUID
        foreach ($tables as $table) {
            $tableName = $table->\$tableKey;
            if (Schema::hasColumn($tableName, 'tenant_id') && $tableName !== 'users') {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->uuid('tenant_id')->nullable()->change();
                });
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay vuelta atrás segura sin perder datos en este punto de transición
    }
};
