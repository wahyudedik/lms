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
        Schema::create('information_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('card_type')->default('info'); // info, warning, success, danger
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->json('target_roles'); // ['siswa'], ['mahasiswa'], ['admin','guru','dosen','siswa','mahasiswa']
            $table->json('target_user_ids')->nullable(); // null = all users in target roles, [1,2,3] = specific users
            $table->enum('schedule_type', ['always', 'date_range', 'daily'])->default('always');
            $table->date('start_date')->nullable(); // for date_range
            $table->date('end_date')->nullable(); // for date_range
            $table->time('daily_start_time')->nullable(); // for daily
            $table->time('daily_end_time')->nullable(); // for daily
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('schedule_type');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_cards');
    }
};
