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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique(); // Kode kelas untuk enrollment
            $table->text('description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade'); // Guru yang mengajar
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('cover_image')->nullable();
            $table->integer('max_students')->nullable(); // Batas maksimal siswa
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('instructor_id');
            $table->index('status');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
