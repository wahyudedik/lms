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
        // Idempotency guard: tabel ini sudah ada di DB production (migration lama
        // 2025_01_01_000001_create_assignments_table yang sudah pernah dijalankan).
        // Nama file pernah di-rename (commit 6431dba) sehingga Laravel menganggapnya
        // migration baru — guard ini mencegah "Base table already exists".
        if (Schema::hasTable('assignments')) {
            return;
        }

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('deadline');
            $table->integer('max_score');
            $table->json('allowed_file_types')->nullable(); // null = semua tipe yang didukung
            $table->enum('late_policy', ['allow', 'reject', 'penalty'])->default('reject');
            $table->integer('penalty_percentage')->nullable(); // 1-100, wajib jika late_policy=penalty
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('course_id');
            $table->index('created_by');
            $table->index('is_published');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
