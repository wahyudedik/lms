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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('student_name');
            $table->string('course_title');
            $table->text('course_description')->nullable();
            $table->date('issue_date');
            $table->date('completion_date');
            $table->integer('final_score')->nullable();
            $table->string('grade')->nullable();
            $table->string('instructor_name')->nullable();
            $table->text('signature')->nullable(); // Could store signature image path
            $table->json('metadata')->nullable(); // For additional data like hours, topics, etc
            $table->boolean('is_valid')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoke_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('certificate_number');
            $table->index(['user_id', 'course_id']);
            $table->index('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
