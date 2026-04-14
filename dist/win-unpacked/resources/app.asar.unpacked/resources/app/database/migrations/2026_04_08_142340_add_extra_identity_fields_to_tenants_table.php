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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('tax_regime')->nullable(); // Ej: No Responsable de IVA
            $table->string('business_hours')->nullable(); // Ej: 6:00 AM - 10:00 PM
            $table->text('welcome_message')->nullable(); // Ej: ¡Gracias por su visita!
            $table->text('disclaimer_message')->nullable(); // Ej: No nos responsabilizamos...
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['tax_regime', 'business_hours', 'welcome_message', 'disclaimer_message']);
        });
    }
};
