<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cheating_incidents', function (Blueprint $table) {
            // Graduated response: track how many warnings were issued before blocking
            $table->unsignedTinyInteger('warning_count')->default(0)->after('type');
            // Auto-unblock: allow setting an expiry time for the block
            $table->timestamp('auto_unblock_at')->nullable()->after('blocked_at');
        });

        Schema::table('users', function (Blueprint $table) {
            // Track total warnings across all exams for graduated response
            $table->unsignedSmallInteger('cheat_warning_count')->default(0)->after('login_blocked_reason');
        });
    }

    public function down(): void
    {
        Schema::table('cheating_incidents', function (Blueprint $table) {
            $table->dropColumn(['warning_count', 'auto_unblock_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cheat_warning_count');
        });
    }
};
