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
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('detect_window_blur')->default(false)->after('max_tab_switches');
            $table->integer('max_window_blurs')->default(3)->after('detect_window_blur');
            $table->boolean('detect_multiple_screens')->default(false)->after('max_window_blurs');
            $table->boolean('detect_inactivity')->default(false)->after('detect_multiple_screens');
            $table->integer('max_inactivity_minutes')->default(3)->after('detect_inactivity');
            $table->boolean('block_keys_and_copy')->default(false)->after('max_inactivity_minutes');
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->integer('window_blurs')->default(0)->after('fullscreen_exits');
            $table->integer('multiple_screen_detections')->default(0)->after('window_blurs');
            $table->integer('inactivity_triggers')->default(0)->after('multiple_screen_detections');
            $table->integer('key_blocks')->default(0)->after('inactivity_triggers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'detect_window_blur',
                'max_window_blurs',
                'detect_multiple_screens',
                'detect_inactivity',
                'max_inactivity_minutes',
                'block_keys_and_copy'
            ]);
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'window_blurs',
                'multiple_screen_detections',
                'inactivity_triggers',
                'key_blocks'
            ]);
        });
    }
};
