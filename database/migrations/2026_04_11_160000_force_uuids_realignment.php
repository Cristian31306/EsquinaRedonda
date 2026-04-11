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

        // 1. Eliminar CLAVES FORÁNEAS en MySQL antes de cualquier cambio de columna
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
            }
        });

        // Repetir para otras tablas que tengan tenant_id como foreign key
        $withTenant = ['rates', 'tickets', 'payments', 'memberships', 'shifts'];
        foreach ($withTenant as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Solo intentar drop si el índice existe en MySQL
                    try {
                        $table->dropForeign($tableName . '_tenant_id_foreign');
                    } catch (\Exception $e) { /* Ignorar si no existe */ }
                });
            }
        }

        // 2. Ahora sí, limpiar datos y transformar
        Schema::disableForeignKeyConstraints();
        $tables = ['tenants', 'users', 'settings', 'rates', 'tickets', 'payments', 'memberships', 'shifts'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Reconfigurar TENANTS
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
        });

        // Reconfigurar USERS
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id', 'tenant_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->first();
            $table->uuid('tenant_id')->nullable()->after('id');
        });

        // Transformar las demás tablas a UUID para tenant_id
        foreach ($withTenant as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
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
