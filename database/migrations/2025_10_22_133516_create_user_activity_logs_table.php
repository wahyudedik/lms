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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // login, logout, page_view, exam_start, exam_submit, etc.
            $table->string('activity_name')->nullable(); // Descriptive name
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data (IP, device, browser, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('duration_seconds')->nullable(); // Time spent on activity
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('activity_type');
            $table->index('created_at');
            $table->index(['user_id', 'activity_type']);
        });

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('browser')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('session_id');
            $table->index('login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('user_activity_logs');
    }
};
