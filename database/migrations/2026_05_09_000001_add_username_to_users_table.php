<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->nullable()->after('email');
        });

        // Generate usernames for all existing users
        $users = DB::table('users')->orderBy('id')->get(['id', 'name']);

        foreach ($users as $user) {
            $username = $this->generateUsername($user->name);
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }

    /**
     * Generate a unique username from a name using slug logic.
     *
     * Steps:
     * 1. Lowercase, replace spaces with underscores
     * 2. Remove non-alphanumeric characters except underscores
     * 3. Trim leading/trailing underscores
     * 4. If duplicate exists, append _2, _3, etc.
     */
    private function generateUsername(string $name): string
    {
        // Step 1: lowercase and replace spaces with underscores
        $base = strtolower($name);
        $base = str_replace(' ', '_', $base);

        // Step 2: remove non-alphanumeric characters except underscores
        $base = preg_replace('/[^a-z0-9_]/', '', $base);

        // Step 3: trim leading/trailing underscores
        $base = trim($base, '_');

        // Fallback if base is empty after sanitization
        if ($base === '') {
            $base = 'user';
        }

        // Truncate to leave room for suffix (max 50 chars total)
        $base = substr($base, 0, 45);

        // Step 4: ensure uniqueness by appending _2, _3, etc.
        $username = $base;
        $suffix = 2;

        while (DB::table('users')->where('username', $username)->exists()) {
            $username = $base . '_' . $suffix;
            $suffix++;
        }

        return $username;
    }
};
