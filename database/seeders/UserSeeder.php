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
            'password' => Hash::make('password'),
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

        // Create additional gurus
        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru2@lms.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '081234567893',
            'birth_date' => '1985-03-10',
            'gender' => 'laki-laki',
            'address' => 'Jl. Guru No. 4, Jakarta',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru Bahasa',
            'email' => 'guru3@lms.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '081234567894',
            'birth_date' => '1987-07-25',
            'gender' => 'perempuan',
            'address' => 'Jl. Guru No. 5, Jakarta',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional students
        for ($i = 2; $i <= 10; $i++) {
            User::create([
                'name' => 'Siswa ' . $i,
                'email' => 'siswa' . $i . '@lms.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'phone' => '08123456789' . $i,
                'birth_date' => '200' . ($i % 5) . '-0' . ($i % 9 + 1) . '-' . (10 + $i),
                'gender' => ($i % 2 == 0) ? 'perempuan' : 'laki-laki',
                'address' => 'Jl. Siswa No. ' . ($i + 2) . ', Jakarta',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

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
