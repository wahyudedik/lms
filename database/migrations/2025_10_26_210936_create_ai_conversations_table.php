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
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('context_type')->nullable(); // 'course', 'exam', 'material', 'general'
            $table->foreignId('context_id')->nullable(); // ID of the context (course_id, exam_id, etc)
            $table->integer('message_count')->default(0);
            $table->integer('tokens_used')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index('context_type');
            $table->index('is_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
