<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add essay_similarity_penalty column to questions table.
     * This column stores the penalty multiplier applied when similarity is below the minimum threshold.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->decimal('essay_similarity_penalty', 5, 2)->nullable()->after('essay_min_similarity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('essay_similarity_penalty');
        });
    }
};
