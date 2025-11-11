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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            // ✅ FIX BUG #1: Make user_id nullable to support guest exam attempts
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Siswa (nullable for guest access)

            // Waktu
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent')->nullable(); // Waktu yang digunakan dalam detik

            // Hasil
            $table->decimal('score', 5, 2)->nullable(); // Nilai (%)
            $table->decimal('total_points_earned', 10, 2)->nullable(); // Total poin yang didapat
            $table->decimal('total_points_possible', 10, 2)->nullable(); // Total poin maksimal
            $table->boolean('passed')->nullable(); // Lulus atau tidak
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');

            // Anti-Cheat Tracking
            $table->integer('tab_switches')->default(0); // Jumlah perpindahan tab
            $table->integer('fullscreen_exits')->default(0); // Jumlah keluar fullscreen
            $table->json('violations')->nullable(); // Log pelanggaran

            // Metadata
            $table->json('shuffled_question_ids')->nullable(); // Urutan soal yang diacak (untuk konsistensi)
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('exam_id');
            $table->index('user_id');
            $table->index('status');
            $table->index(['exam_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
