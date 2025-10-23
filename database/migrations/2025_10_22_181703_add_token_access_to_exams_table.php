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
            $table->boolean('allow_token_access')->default(false)->after('show_correct_answers');
            $table->string('access_token', 32)->unique()->nullable()->after('allow_token_access');
            $table->boolean('require_guest_name')->default(true)->after('access_token');
            $table->boolean('require_guest_email')->default(false)->after('require_guest_name');
            $table->integer('max_token_uses')->nullable()->after('require_guest_email')->comment('Null = unlimited');
            $table->integer('current_token_uses')->default(0)->after('max_token_uses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'allow_token_access',
                'access_token',
                'require_guest_name',
                'require_guest_email',
                'max_token_uses',
                'current_token_uses'
            ]);
        });
    }
};
