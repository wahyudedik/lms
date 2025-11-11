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
        // AI settings will be stored in the settings table using the existing key-value structure
        // This migration is a placeholder for documentation purposes
        // The actual settings will be seeded in SettingSeeder
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No columns to drop as we're using the existing settings table structure
    }
};
