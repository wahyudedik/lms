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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Guru yang upload
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['file', 'video', 'link', 'youtube'])->default('file');
            $table->string('file_path')->nullable(); // Path untuk file upload
            $table->string('file_name')->nullable(); // Original filename
            $table->integer('file_size')->nullable(); // Size in bytes
            $table->string('url')->nullable(); // URL untuk link/youtube
            $table->integer('order')->default(0); // Untuk sorting
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('course_id');
            $table->index('created_by');
            $table->index('type');
            $table->index('is_published');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
