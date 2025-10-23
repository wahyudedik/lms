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
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Make user_id nullable for guest access
            $table->foreignId('user_id')->nullable()->change();

            // Guest information
            $table->boolean('is_guest')->default(false)->after('user_id');
            $table->string('guest_name')->nullable()->after('is_guest');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_token')->nullable()->after('guest_email')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn(['is_guest', 'guest_name', 'guest_email', 'guest_token']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
