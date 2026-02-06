<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_classes')) {
            return;
        }

        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('education_level')->nullable();
            $table->unsignedInteger('capacity');
            $table->boolean('is_general')->default(false);
            $table->timestamps();

            $table->index('education_level');
            $table->index('is_general');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
