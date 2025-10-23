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
        Schema::table('questions', function (Blueprint $table) {
            // Essay grading configuration
            $table->enum('essay_grading_mode', ['manual', 'keyword', 'similarity'])
                ->default('manual')
                ->after('explanation')
                ->comment('Mode penilaian essay: manual, keyword matching, atau similarity');

            // Keyword matching settings
            $table->json('essay_keywords')
                ->nullable()
                ->after('essay_grading_mode')
                ->comment('Array kata kunci untuk keyword matching');

            $table->json('essay_keyword_points')
                ->nullable()
                ->after('essay_keywords')
                ->comment('Bobot poin per kata kunci');

            // Similarity matching settings
            $table->text('essay_model_answer')
                ->nullable()
                ->after('essay_keyword_points')
                ->comment('Jawaban model/referensi untuk similarity matching');

            $table->integer('essay_min_similarity')
                ->default(70)
                ->after('essay_model_answer')
                ->comment('Minimal % similarity untuk lulus (0-100)');

            // Case sensitivity
            $table->boolean('essay_case_sensitive')
                ->default(false)
                ->after('essay_min_similarity')
                ->comment('Apakah penilaian case-sensitive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'essay_grading_mode',
                'essay_keywords',
                'essay_keyword_points',
                'essay_model_answer',
                'essay_min_similarity',
                'essay_case_sensitive',
            ]);
        });
    }
};
