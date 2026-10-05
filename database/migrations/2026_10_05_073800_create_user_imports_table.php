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
        Schema::create('user_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_name'); // nama file asli hasil upload
            $table->string('file_path'); // path relatif di disk local (storage non-public)
            $table->string('status')->default('pending'); // pending / processing / completed / failed
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('created_count')->nullable();
            $table->unsignedInteger('updated_count')->nullable();
            $table->unsignedInteger('failed_count')->nullable();
            $table->json('errors')->nullable(); // detail "Baris N: ..." per kegagalan
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_imports');
    }
};
