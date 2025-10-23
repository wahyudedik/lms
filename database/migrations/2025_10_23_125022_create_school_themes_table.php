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
        Schema::create('school_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();

            // Color Scheme
            $table->string('primary_color')->default('#3B82F6'); // Blue
            $table->string('secondary_color')->default('#8B5CF6'); // Purple
            $table->string('accent_color')->default('#10B981'); // Green
            $table->string('success_color')->default('#10B981'); // Green
            $table->string('warning_color')->default('#F59E0B'); // Yellow
            $table->string('danger_color')->default('#EF4444'); // Red
            $table->string('info_color')->default('#3B82F6'); // Blue
            $table->string('dark_color')->default('#1F2937'); // Gray-dark

            // Text Colors
            $table->string('text_primary')->default('#1F2937'); // Dark gray
            $table->string('text_secondary')->default('#6B7280'); // Medium gray
            $table->string('text_muted')->default('#9CA3AF'); // Light gray

            // Background
            $table->string('background_color')->default('#F9FAFB'); // Light gray
            $table->string('card_background')->default('#FFFFFF'); // White
            $table->string('navbar_background')->default('#FFFFFF'); // White
            $table->string('sidebar_background')->default('#1F2937'); // Dark

            // Typography
            $table->string('font_family')->default('Inter, sans-serif');
            $table->string('heading_font')->nullable(); // Optional different font for headings
            $table->integer('font_size')->default(16); // Base font size in px

            // Custom CSS
            $table->text('custom_css')->nullable();

            // Images
            $table->string('login_background')->nullable();
            $table->string('dashboard_hero')->nullable();

            // Settings
            $table->string('border_radius')->default('0.5rem'); // Rounded corners
            $table->string('box_shadow')->default('0 1px 3px 0 rgb(0 0 0 / 0.1)');
            $table->boolean('dark_mode')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_themes');
    }
};
