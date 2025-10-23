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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siswa
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Kelas
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
            $table->integer('progress')->default(0); // Progress dalam persen (0-100)
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Prevent duplicate enrollments
            $table->unique(['user_id', 'course_id']);

            // Indexes
            $table->index('user_id');
            $table->index('course_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
