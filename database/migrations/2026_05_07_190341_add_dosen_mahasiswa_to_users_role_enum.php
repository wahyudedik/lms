<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Expand the `role` enum on the `users` table to include
     * 'dosen' (equivalent to 'guru') and 'mahasiswa' (equivalent to 'siswa').
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ENUM or MODIFY — role is stored as TEXT,
            // so no schema change is needed. The application layer handles validation.
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'guru', 'siswa', 'dosen', 'mahasiswa') NOT NULL DEFAULT 'siswa'");
    }

    /**
     * Reverse the migrations.
     *
     * Revert to the original three-value enum.
     * Any existing 'dosen' rows become 'guru' and 'mahasiswa' rows become 'siswa'
     * before the column is narrowed, to avoid data loss.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Migrate data back even on SQLite
            DB::statement("UPDATE `users` SET `role` = 'guru'  WHERE `role` = 'dosen'");
            DB::statement("UPDATE `users` SET `role` = 'siswa' WHERE `role` = 'mahasiswa'");
            return;
        }

        // Migrate data back before narrowing the enum
        DB::statement("UPDATE `users` SET `role` = 'guru'  WHERE `role` = 'dosen'");
        DB::statement("UPDATE `users` SET `role` = 'siswa' WHERE `role` = 'mahasiswa'");

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'guru', 'siswa') NOT NULL DEFAULT 'siswa'");
    }
};
