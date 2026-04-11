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

        Schema::disableForeignKeyConstraints();

        $tables = ['tenants', 'users', 'settings', 'rates', 'tickets', 'payments', 'memberships', 'shifts'];

        // Limpiar datos previos para evitar conflictos de conversión de tipos en IDs
        // Dado que es un entorno de pruebas/SaaS inicial, priorizamos la integridad del esquema
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Reconfigurar TENANTS (La raíz)
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

        // Reconfigurar OTROS modelos que usan tenant_id
        $withTenant = ['rates', 'tickets', 'payments', 'memberships', 'shifts'];
        foreach ($withTenant as $tableName) {
            if (Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
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
