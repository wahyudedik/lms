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
        Schema::create('authorization_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('resource_type'); // e.g., 'App\Models\Exam', 'App\Models\Course'
            $table->unsignedBigInteger('resource_id')->nullable(); // ID of the resource
            $table->string('action'); // e.g., 'view', 'update', 'delete', 'create'
            $table->string('ability'); // Policy ability name
            $table->enum('result', ['allowed', 'denied'])->default('denied');
            $table->text('reason')->nullable(); // Reason for denial
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('route')->nullable(); // Route name
            $table->string('method')->nullable(); // HTTP method
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('resource_type');
            $table->index('resource_id');
            $table->index('action');
            $table->index('result');
            $table->index('created_at');
            $table->index(['user_id', 'result']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization_logs');
    }
};

