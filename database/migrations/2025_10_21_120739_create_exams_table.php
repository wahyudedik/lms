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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Guru yang membuat
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable(); // Instruksi khusus untuk ujian

            // Pengaturan Waktu
            $table->integer('duration_minutes')->default(60); // Durasi ujian dalam menit
            $table->dateTime('start_time')->nullable(); // Waktu mulai ujian
            $table->dateTime('end_time')->nullable(); // Waktu akhir ujian

            // Pengaturan Ujian
            $table->integer('max_attempts')->default(1); // Maksimal percobaan
            $table->boolean('shuffle_questions')->default(false); // Acak urutan soal
            $table->boolean('shuffle_options')->default(false); // Acak urutan opsi jawaban
            $table->boolean('show_results_immediately')->default(true); // Tampilkan hasil langsung
            $table->boolean('show_correct_answers')->default(true); // Tampilkan jawaban benar saat review
            $table->decimal('pass_score', 5, 2)->default(60.00); // Nilai minimum lulus (%)

            // Pengaturan Anti-Cheat
            $table->boolean('require_fullscreen')->default(false); // Wajib fullscreen
            $table->boolean('detect_tab_switch')->default(false); // Deteksi perpindahan tab
            $table->integer('max_tab_switches')->default(3); // Maksimal perpindahan tab sebelum auto-submit

            // Status & Publikasi
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('course_id');
            $table->index('created_by');
            $table->index('is_published');
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
