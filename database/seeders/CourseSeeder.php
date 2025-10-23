<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🎓 Creating sample courses...\n\n";

        // Get guru users
        $guru = User::where('role', 'guru')->first();

        if (!$guru) {
            echo "⚠️  No guru found. Please seed users first.\n";
            return;
        }

        // Get siswa users for enrollment
        $siswaUsers = User::where('role', 'siswa')->get();

        // Sample courses
        $courses = [
            [
                'title' => 'Matematika Dasar',
                'code' => 'MTK001',
                'description' => 'Kelas matematika dasar untuk pemula. Mempelajari konsep dasar aljabar, geometri, dan aritmatika.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 30,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Bahasa Indonesia',
                'code' => 'BHS001',
                'description' => 'Pembelajaran bahasa Indonesia yang baik dan benar. Materi mencakup tata bahasa, sastra, dan penulisan.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 25,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Fisika Umum',
                'code' => 'FIS001',
                'description' => 'Pengenalan konsep fisika dasar meliputi mekanika, termodinamika, dan listrik magnet.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 20,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Kimia Dasar',
                'code' => 'KIM001',
                'description' => 'Mempelajari struktur atom, ikatan kimia, dan reaksi kimia dasar.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 25,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Biologi Umum',
                'code' => 'BIO001',
                'description' => 'Pengenalan dunia biologi dari sel hingga ekosistem.',
                'instructor_id' => $guru->id,
                'status' => 'draft',
                'max_students' => 30,
            ],
            [
                'title' => 'Sejarah Indonesia',
                'code' => 'SEJ001',
                'description' => 'Mempelajari sejarah Indonesia dari masa pra-sejarah hingga modern.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 35,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Bahasa Inggris Conversation',
                'code' => 'ENG001',
                'description' => 'Praktik conversation bahasa Inggris untuk komunikasi sehari-hari.',
                'instructor_id' => $guru->id,
                'status' => 'published',
                'max_students' => 20,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Pemrograman Web',
                'code' => 'WEB001',
                'description' => 'Belajar membuat website dengan HTML, CSS, dan JavaScript.',
                'instructor_id' => $guru->id,
                'status' => 'draft',
                'max_students' => 15,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create($courseData);
            echo "✅ Created: {$course->title} ({$course->code})\n";

            // Auto-enroll 3-5 random students to published courses
            if ($course->status === 'published' && $siswaUsers->count() > 0) {
                $enrollCount = rand(3, min(5, $siswaUsers->count()));
                $studentsToEnroll = $siswaUsers->random($enrollCount);

                foreach ($studentsToEnroll as $student) {
                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'status' => 'active',
                        'progress' => rand(0, 75),
                        'enrolled_at' => now()->subDays(rand(1, 10)),
                    ]);
                }

                echo "   📝 Enrolled {$enrollCount} students\n";
            }
        }

        echo "\n";
        echo "╔════════════════════════════════════════════════╗\n";
        echo "║       SAMPLE COURSES CREATED SUCCESSFULLY      ║\n";
        echo "╠════════════════════════════════════════════════╣\n";
        echo "║ Published Courses: 6                           ║\n";
        echo "║ Draft Courses: 2                               ║\n";
        echo "║ Total Courses: 8                               ║\n";
        echo "╠════════════════════════════════════════════════╣\n";
        echo "║ Sample Course Codes:                           ║\n";
        echo "║   MTK001 - Matematika Dasar                    ║\n";
        echo "║   BHS001 - Bahasa Indonesia                    ║\n";
        echo "║   FIS001 - Fisika Umum                         ║\n";
        echo "║   ENG001 - Bahasa Inggris Conversation         ║\n";
        echo "╚════════════════════════════════════════════════╝\n";
        echo "\n";
    }
}
