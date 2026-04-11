<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convertimos campos ENUM a STRING para evitar errores de 'CHECK constraint failed' en SQLite
        // y para dar flexibilidad total a nuevos planes sin tocar el esquema.
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan')->default('basico')->change();
            $table->string('status')->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('plan', ['basico', 'pro'])->default('basico')->change();
            $table->enum('status', ['active', 'suspended'])->default('active')->change();
        });
    }
};
