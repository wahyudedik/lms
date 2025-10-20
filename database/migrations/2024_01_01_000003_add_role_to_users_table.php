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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->date('birth_date')->nullable()->after('phone');
            $table->enum('gender', ['laki-laki', 'perempuan'])->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('gender');
            $table->string('profile_photo')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('profile_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'birth_date',
                'gender',
                'address',
                'profile_photo',
                'is_active'
            ]);
        });
    }
};
