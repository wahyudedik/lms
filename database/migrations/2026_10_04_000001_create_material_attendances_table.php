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
        Schema::create('material_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('attendance_date');
            // enum() aman di SQLite (Laravel 12 membuat varchar + check constraint),
            // konsisten dengan migration enum lain di project ini.
            $table->enum('status', ['hadir', 'sakit', 'ijin', 'keluar'])->default('hadir');
            $table->text('alasan')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['material_id', 'user_id', 'attendance_date'], 'material_attendances_unique_per_date');
            $table->index(['material_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_attendances');
    }
};
