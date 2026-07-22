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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size'); // bytes
            $table->enum('status', ['submitted', 'late', 'graded'])->default('submitted');
            $table->integer('score')->nullable(); // raw score given by guru
            $table->decimal('final_score', 5, 2)->nullable(); // after penalty calculation
            $table->text('feedback')->nullable();
            $table->integer('penalty_applied')->nullable(); // penalty % that was applied
            $table->integer('revision_count')->default(0);
            $table->timestamp('submitted_at');
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Unique constraint: one submission per student per assignment
            $table->unique(['assignment_id', 'user_id']);

            // Indexes
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
