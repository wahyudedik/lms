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
            $table->foreignId('question_bank_id')
                ->nullable()
                ->after('exam_id')
                ->constrained('question_bank')
                ->onDelete('set null')
                ->comment('Link to reusable question from bank');

            $table->boolean('is_from_bank')->default(false)->after('question_bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['question_bank_id']);
            $table->dropColumn(['question_bank_id', 'is_from_bank']);
        });
    }
};
