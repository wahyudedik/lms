<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add correct_answer_multiple column to questions table.
     * This column stores the array of correct answers for mcq_multiple type questions,
     * separate from correct_answer which is used for mcq_single (string) and matching (array of pairs).
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->json('correct_answer_multiple')->nullable()->after('correct_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('correct_answer_multiple');
        });
    }
};
