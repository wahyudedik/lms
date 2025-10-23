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
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('question_bank_categories')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            // Question Content
            $table->enum('type', ['mcq_single', 'mcq_multiple', 'matching', 'essay']);
            $table->text('question_text');
            $table->string('question_image')->nullable();

            // Difficulty & Metadata
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->json('tags')->nullable(); // For searchability
            $table->decimal('default_points', 5, 2)->default(1.00);

            // MCQ Options (for mcq_single and mcq_multiple)
            $table->json('options')->nullable(); // ['A' => 'option1', 'B' => 'option2', ...]
            $table->string('correct_answer')->nullable(); // For mcq_single: 'A'
            $table->json('correct_answer_multiple')->nullable(); // For mcq_multiple: ['A', 'C']

            // Matching Pairs (for matching type)
            $table->json('pairs')->nullable(); // [{'left': 'term1', 'right': 'def1'}, ...]

            // Essay Grading
            $table->enum('essay_grading_mode', ['manual', 'keyword', 'similarity'])->default('manual');
            $table->json('essay_keywords')->nullable(); // [{'keyword': 'word', 'points': 1}, ...]
            $table->text('essay_model_answer')->nullable();
            $table->decimal('essay_min_similarity', 5, 2)->nullable();
            $table->decimal('essay_similarity_penalty', 5, 2)->nullable();
            $table->boolean('essay_case_sensitive')->default(false);

            // Explanation & Feedback
            $table->text('explanation')->nullable(); // Shown after answering
            $table->text('teacher_notes')->nullable(); // Private notes for teachers

            // Usage Statistics
            $table->integer('times_used')->default(0);
            $table->decimal('average_score', 5, 2)->nullable(); // Track performance
            $table->integer('times_correct')->default(0);
            $table->integer('times_incorrect')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false); // Quality check
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('created_by');
            $table->index('type');
            $table->index('difficulty');
            $table->index('is_active');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_bank');
    }
};
