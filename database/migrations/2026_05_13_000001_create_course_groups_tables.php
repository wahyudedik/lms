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
        Schema::create('course_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('name');
            $table->string('name_normalized')->virtualAs('LOWER(TRIM(`name`))');
            $table->timestamps();

            // Case-insensitive uniqueness per course
            $table->unique(['course_id', 'name_normalized']);

            // Index for course lookups
            $table->index('course_id');
        });

        Schema::create('course_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_group_id')->constrained('course_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at')->nullable();

            // Prevent duplicate membership
            $table->unique(['course_group_id', 'user_id']);

            // Indexes
            $table->index('course_group_id');
            $table->index('user_id');
        });

        Schema::create('course_group_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_group_id')->constrained('course_groups')->onDelete('cascade');
            $table->string('contentable_type');
            $table->unsignedBigInteger('contentable_id');
            $table->timestamp('created_at')->nullable();

            // Prevent duplicate associations
            $table->unique(['course_group_id', 'contentable_type', 'contentable_id'], 'cgc_group_type_id_unique');

            // Index for reverse lookups (find which groups a content belongs to)
            $table->index(['contentable_type', 'contentable_id'], 'cgc_contentable_index');

            // Index for group lookups
            $table->index('course_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_group_content');
        Schema::dropIfExists('course_group_members');
        Schema::dropIfExists('course_groups');
    }
};
