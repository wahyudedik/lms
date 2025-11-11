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
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system'])->default('user');
            $table->text('content');
            $table->integer('tokens')->default(0);
            $table->string('model')->nullable(); // e.g., 'gpt-4', 'gpt-3.5-turbo'
            $table->json('metadata')->nullable(); // Store additional data like finish_reason, etc
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index(['conversation_id', 'created_at']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};
