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
            $table->string('nit')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('social_handle')->nullable(); // Ej: @esquinaredondavr
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['nit', 'address', 'phone', 'social_handle']);
        });
    }
};
