<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,              // 1. Create users first
            SchoolSeeder::class,            // 2. Create schools & assign users to schools
            CourseSeeder::class,            // 3. Create courses
            MaterialSeeder::class,          // 4. Create materials
            ExamSeeder::class,              // 5. Create exams
            ForumSeeder::class,             // 6. Create forum data
            SettingSeeder::class,           // 7. Create settings
            CertificateSeeder::class,       // 8. Create certificates (needs completed enrollments)
            AiConversationSeeder::class,    // 9. Create AI conversations (needs users & courses)
        ]);
    }
}
 