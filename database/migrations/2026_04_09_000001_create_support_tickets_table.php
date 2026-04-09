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
        Schema::create('support_tickets', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $blueprint->string('subject');
            $blueprint->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $blueprint->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $blueprint->timestamp('last_reply_at')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
