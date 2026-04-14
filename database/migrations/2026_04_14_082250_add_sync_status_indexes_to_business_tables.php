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
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('sync_status');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('sync_status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('sync_status');
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['sync_status']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['sync_status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['sync_status']);
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->dropIndex(['sync_status']);
        });
    }
};
