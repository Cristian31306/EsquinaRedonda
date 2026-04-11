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

            if (DB::getDriverName() === 'sqlite') {
                // Estrategia para SQLite: Recrear la tabla
                Schema::dropIfExists($tableName);

                Schema::create($tableName, function (Blueprint $table) use ($tableName, $foreigns) {
                    $table->uuid('id')->primary();

                    // Reconstruir campos específicos según la tabla
                    switch ($tableName) {
                        case 'tenants':
                            $table->string('name');
                            $table->string('slug')->unique();
                            $table->enum('status', ['active', 'suspended'])->default('active');
                            $table->string('plan')->default('basico');
                            $table->string('nit')->nullable();
                            $table->string('address')->nullable();
                            $table->string('phone')->nullable();
                            $table->string('social_handle')->nullable();
                            $table->string('tax_regime')->nullable();
                            $table->string('business_hours')->nullable();
                            $table->text('welcome_message')->nullable();
                            $table->text('disclaimer_message')->nullable();
                            $table->string('billing_cycle')->nullable();
                            $table->timestamp('expires_at')->nullable();
                            break;

                        case 'users':
                            $table->string('name');
                            $table->string('email')->unique();
                            $table->timestamp('email_verified_at')->nullable();
                            $table->string('password');
                            $table->string('role')->default('operator');
                            $table->boolean('is_active')->default(true);
                            $table->rememberToken();
                            break;

                        case 'vehicles':
                            $table->string('plate')->unique();
                            $table->enum('type', ['carro', 'moto', 'pesado']);
                            $table->text('observation')->nullable();
                            break;

                        case 'rates':
                            $table->string('vehicle_type');
                            $table->string('concept');
                            $table->decimal('value', 10, 2);
                            $table->boolean('is_active')->default(true);
                            break;

                        case 'cash_shifts':
                            $table->uuid('user_id'); // Será UUID
                            $table->timestamp('start_time');
                            $table->timestamp('end_time')->nullable();
                            $table->decimal('opening_cash', 15, 2);
                            $table->decimal('closing_cash_declared', 15, 2)->nullable();
                            $table->enum('status', ['open', 'closed'])->default('open');
                            break;

                        case 'tickets':
                            $table->uuid('vehicle_id');
                            $table->uuid('user_id');
                            $table->timestamp('entry_time');
                            $table->timestamp('exit_time')->nullable();
                            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
                            $table->string('stay_type')->nullable()->default(null);
                            break;

                        case 'payments':
                            $table->uuid('ticket_id');
                            $table->uuid('cash_shift_id');
                            $table->decimal('amount', 15, 2);
                            $table->string('payment_method');
                            break;

                        case 'memberships':
                            $table->uuid('vehicle_id');
                            $table->uuid('cash_shift_id')->nullable();
                            $table->string('plate');
                            $table->string('vehicle_type');
                            $table->date('start_date');
                            $table->date('end_date');
                            $table->decimal('amount_paid', 10, 2)->default(0);
                            $table->string('notes')->nullable();
                            break;

                        case 'settings':
                            $table->string('key')->unique();
                            $table->text('value')->nullable();
                            break;

                        case 'support_tickets':
                            $table->uuid('user_id');
                            $table->string('subject');
                            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
                            $table->timestamp('last_reply_at')->nullable();
                            break;

                        case 'support_messages':
                            $table->uuid('support_ticket_id');
                            $table->uuid('user_id');
                            $table->text('message');
                            $table->string('attachment_path')->nullable();
                            break;
                    }

                    // Forzar tenant_id como UUID si aplica
                    if (in_array('tenant_id', $foreigns)) {
                        $table->uuid('tenant_id')->nullable();
                    }

                    $table->timestamps();
                    $table->timestamp('last_synced_at')->nullable();
                });
            } else {
                // Estrategia original para MySQL
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
