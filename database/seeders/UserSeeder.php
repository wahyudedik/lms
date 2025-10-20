<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user dengan password fixed
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@lms.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'birth_date' => '1990-01-01',
            'gender' => 'laki-laki',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample guru
        User::create([
            'name' => 'Guru Sample',
            'email' => 'guru@lms.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '081234567891',
            'birth_date' => '1985-05-15',
            'gender' => 'perempuan',
            'address' => 'Jl. Guru No. 2, Jakarta',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample siswa
        User::create([
            'name' => 'Siswa Sample',
            'email' => 'siswa@lms.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'phone' => '081234567892',
            'birth_date' => '2000-08-20',
            'gender' => 'laki-laki',
            'address' => 'Jl. Siswa No. 3, Jakarta',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Display credentials
        echo "\n";
        echo "╔════════════════════════════════════════════════╗\n";
        echo "║          DEFAULT USER CREDENTIALS              ║\n";
        echo "╠════════════════════════════════════════════════╣\n";
        echo "║ ADMIN:                                         ║\n";
        echo "║   Email    : admin@lms.com                     ║\n";
        echo "║   Password : admin123                          ║\n";
        echo "╠════════════════════════════════════════════════╣\n";
        echo "║ GURU (Sample):                                 ║\n";
        echo "║   Email    : guru@lms.com                      ║\n";
        echo "║   Password : password                          ║\n";
        echo "╠════════════════════════════════════════════════╣\n";
        echo "║ SISWA (Sample):                                ║\n";
        echo "║   Email    : siswa@lms.com                     ║\n";
        echo "║   Password : password                          ║\n";
        echo "╚════════════════════════════════════════════════╝\n";
        echo "\n";
    }
}
