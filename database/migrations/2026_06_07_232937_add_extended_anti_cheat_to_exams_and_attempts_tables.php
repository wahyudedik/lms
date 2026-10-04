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
        if (! Schema::hasTable('exams') || ! Schema::hasTable('exam_attempts')) {
            return;
        }

        Schema::table('exams', function (Blueprint $table) {
            if (! Schema::hasColumn('exams', 'detect_window_blur')) {
                $table->boolean('detect_window_blur')->default(false)->after('max_tab_switches');
            }
            if (! Schema::hasColumn('exams', 'max_window_blurs')) {
                $table->integer('max_window_blurs')->default(3)->after('detect_window_blur');
            }
            if (! Schema::hasColumn('exams', 'detect_multiple_screens')) {
                $table->boolean('detect_multiple_screens')->default(false)->after('max_window_blurs');
            }
            if (! Schema::hasColumn('exams', 'detect_inactivity')) {
                $table->boolean('detect_inactivity')->default(false)->after('detect_multiple_screens');
            }
            if (! Schema::hasColumn('exams', 'max_inactivity_minutes')) {
                $table->integer('max_inactivity_minutes')->default(3)->after('detect_inactivity');
            }
            if (! Schema::hasColumn('exams', 'block_keys_and_copy')) {
                $table->boolean('block_keys_and_copy')->default(false)->after('max_inactivity_minutes');
            }
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_attempts', 'window_blurs')) {
                $table->integer('window_blurs')->default(0)->after('fullscreen_exits');
            }
            if (! Schema::hasColumn('exam_attempts', 'multiple_screen_detections')) {
                $table->integer('multiple_screen_detections')->default(0)->after('window_blurs');
            }
            if (! Schema::hasColumn('exam_attempts', 'inactivity_triggers')) {
                $table->integer('inactivity_triggers')->default(0)->after('multiple_screen_detections');
            }
            if (! Schema::hasColumn('exam_attempts', 'key_blocks')) {
                $table->integer('key_blocks')->default(0)->after('inactivity_triggers');
            }
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
                'block_keys_and_copy',
            ]);
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'window_blurs',
                'multiple_screen_detections',
                'inactivity_triggers',
                'key_blocks',
            ]);
        });
    }
};
