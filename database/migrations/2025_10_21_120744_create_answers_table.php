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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');

            // Jawaban Siswa
            // MCQ Single: "A"
            // MCQ Multiple: ["A", "C", "D"]
            // Matching: [{"left": "Item 1", "right": "Match A"}, ...]
            // Essay: "Teks jawaban siswa"
            $table->json('answer')->nullable();

            // Penilaian
            $table->boolean('is_correct')->nullable(); // Untuk MCQ & Matching (otomatis)
            $table->decimal('points_earned', 5, 2)->nullable(); // Poin yang didapat
            $table->text('feedback')->nullable(); // Feedback dari guru (untuk essay)

            // Metadata
            $table->json('shuffled_options')->nullable(); // Urutan opsi yang diacak (untuk MCQ)

            $table->timestamps();

            // Indexes
            $table->index('attempt_id');
            $table->index('question_id');
            $table->index('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
