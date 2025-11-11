<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'offline_enabled')) {
                $table->boolean('offline_enabled')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('exams', 'offline_cache_duration')) {
                $table->integer('offline_cache_duration')->default(24)->after('offline_enabled')->comment('Hours');
            }
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_attempts', 'is_offline')) {
                $table->boolean('is_offline')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('exam_attempts', 'correct_answers')) {
                $table->integer('correct_answers')->nullable()->after('score');
            }
            if (!Schema::hasColumn('exam_attempts', 'total_questions')) {
                $table->integer('total_questions')->nullable()->after('correct_answers');
            }
        });

        // Add saved_at column to answers table
        if (Schema::hasTable('answers')) {
            Schema::table('answers', function (Blueprint $table) {
                if (!Schema::hasColumn('answers', 'saved_at')) {
                    $table->timestamp('saved_at')->nullable()->after('created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'offline_enabled')) {
                $table->dropColumn('offline_enabled');
            }
            if (Schema::hasColumn('exams', 'offline_cache_duration')) {
                $table->dropColumn('offline_cache_duration');
            }
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('exam_attempts', 'is_offline')) {
                $table->dropColumn('is_offline');
            }
            if (Schema::hasColumn('exam_attempts', 'correct_answers')) {
                $table->dropColumn('correct_answers');
            }
            if (Schema::hasColumn('exam_attempts', 'total_questions')) {
                $table->dropColumn('total_questions');
            }
        });

        if (Schema::hasTable('answers')) {
            Schema::table('answers', function (Blueprint $table) {
                if (Schema::hasColumn('answers', 'saved_at')) {
                    $table->dropColumn('saved_at');
                }
            });
        }
    }
};
