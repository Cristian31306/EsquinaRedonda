<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $config = [
            'support_messages' => ['support_ticket_id', 'user_id'],
            'support_tickets'  => ['user_id', 'tenant_id'],
            'payments'         => ['ticket_id', 'cash_shift_id', 'tenant_id'],
            'memberships'      => ['vehicle_id', 'cash_shift_id', 'tenant_id'],
            'tickets'          => ['vehicle_id', 'user_id', 'tenant_id'],
            'cash_shifts'      => ['user_id', 'tenant_id'],
            'rates'            => ['tenant_id'],
            'vehicles'         => ['tenant_id'],
            'users'            => ['tenant_id'],
            'settings'         => ['tenant_id'],
            'tenants'          => [],
        ];

        // FASE 1: Eliminar todas las llaves foráneas primero para que MySQL permita borrar columnas ID
        foreach ($config as $table => $foreigns) {
            Schema::table($table, function (Blueprint $table) use ($foreigns) {
                foreach ($foreigns as $foreign) {
                    // En MySQL es más seguro usar dropForeign con el nombre del índice o el array
                    try {
                        $table->dropForeign([$foreign]);
                    } catch (\Exception $e) { /* Ignorar si no existe */ }
                }
            });
        }

        // FASE 2: Transformar columnas a UUID
        foreach ($config as $tableName => $foreigns) {
            // Limpiar datos previos
            DB::table($tableName)->truncate();

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('id');
            });

            Schema::table($tableName, function (Blueprint $table) use ($foreigns) {
                $table->uuid('id')->primary()->first();
                
                foreach ($foreigns as $foreign) {
                    $table->uuid($foreign)->nullable()->change();
                }

                if (!Schema::hasColumn($table->getTable(), 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable();
                }
            });
        }

        // FASE 3: Restaurar llaves foráneas como UUID
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('cash_shift_id')->references('id')->on('cash_shifts')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('cash_shift_id')->references('id')->on('cash_shifts')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();
    }
};
