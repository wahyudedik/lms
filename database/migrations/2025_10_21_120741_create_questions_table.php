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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');

            // Tipe Soal: mcq_single (pilihan ganda), mcq_multiple (jawaban lebih dari satu), matching (menjodohkan), essay
            $table->enum('type', ['mcq_single', 'mcq_multiple', 'matching', 'essay'])->default('mcq_single');

            $table->text('question_text'); // Pertanyaan
            $table->text('question_image')->nullable(); // Gambar untuk soal (opsional)

            // Data untuk MCQ (pilihan ganda)
            // Format JSON: [{"id": "A", "text": "Opsi A"}, {"id": "B", "text": "Opsi B"}, ...]
            $table->json('options')->nullable();

            // Data untuk Matching (menjodohkan)
            // Format JSON: [{"left": "Item 1", "right": "Match 1"}, {"left": "Item 2", "right": "Match 2"}, ...]
            $table->json('pairs')->nullable();

            // Jawaban Benar
            // MCQ Single: "A"
            // MCQ Multiple: ["A", "C", "D"]
            // Matching: [{"left": "Item 1", "right": "Match 1"}, ...]
            // Essay: null (dinilai manual)
            $table->json('correct_answer')->nullable();

            // Pengaturan
            $table->decimal('points', 5, 2)->default(1.00); // Bobot nilai
            $table->integer('order')->default(0); // Urutan soal
            $table->text('explanation')->nullable(); // Penjelasan jawaban (opsional)

            $table->timestamps();

            // Indexes
            $table->index('exam_id');
            $table->index('type');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
