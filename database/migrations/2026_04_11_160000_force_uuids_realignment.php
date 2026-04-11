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

        // 1. ELIMINAR CLAVES FORÁNEAS (De forma segura y tolerante a fallos)
        $tablesWithTenant = ['users', 'rates', 'tickets', 'payments', 'memberships', 'shifts'];
        
        foreach ($tablesWithTenant as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        // Laravel intentará encontrar el nombre correcto de la FK basándose en la columna
                        $table->dropForeign(['tenant_id']);
                    });
                } catch (\Exception $e) {
                    // Si no existe, ignoramos y seguimos
                }
            }
        }

        // 2. LIMPIAR DATOS (Última vez para asegurar integridad de tipos)
        $allTables = ['tenants', 'users', 'settings', 'rates', 'tickets', 'payments', 'memberships', 'shifts'];
        foreach ($allTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // 3. TRANSFORMAR TENANTS
        Schema::table('tenants', function (Blueprint $table) {
            // En MySQL, para cambiar un PK auto-increment, a veces es mejor drop y add
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

        // 5. TRANSFORMAR TABLAS DEPENDIENTES
        foreach (['rates', 'tickets', 'payments', 'memberships', 'shifts'] as $tableName) {
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
