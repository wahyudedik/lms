<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Make essay-specific fields nullable in the questions table.
     * These fields are only relevant for essay-type questions; non-essay questions
     * (mcq_single, mcq_multiple, matching) should be able to store NULL for these columns.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->integer('essay_min_similarity')->nullable()->default(null)->change();
            $table->boolean('essay_case_sensitive')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->integer('essay_min_similarity')->nullable(false)->default(70)->change();
            $table->boolean('essay_case_sensitive')->nullable(false)->default(false)->change();
        });
    }
};
