<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasTable('school_classes')) {
            return;
        }

        if (Schema::hasColumn('users', 'school_class_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_class_id')
                ->nullable()
                ->after('school_id')
                ->constrained('school_classes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasColumn('users', 'school_class_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_class_id');
        });
    }
};
