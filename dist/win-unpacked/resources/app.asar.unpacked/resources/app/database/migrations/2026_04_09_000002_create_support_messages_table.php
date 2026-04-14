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
        Schema::create('support_messages', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('support_ticket_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->text('message');
            $blueprint->string('attachment_path')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};
