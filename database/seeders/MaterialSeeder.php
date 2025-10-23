<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Material;
use App\Models\MaterialComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Get some courses and users
        $courses = Course::all();
        $instructors = User::where('role', 'guru')->get();
        $students = User::where('role', 'siswa')->get();

        if ($courses->isEmpty() || $instructors->isEmpty()) {
            $this->command->warn('No courses or instructors found. Please run CourseSeeder first.');
            return;
        }

        foreach ($courses as $index => $course) {
            $instructor = $instructors->random();

            // Create 3-5 materials per course
            $materialCount = rand(3, 5);

            for ($i = 1; $i <= $materialCount; $i++) {
                $type = ['file', 'youtube', 'link'][array_rand(['file', 'youtube', 'link'])];

                $material = Material::create([
                    'course_id' => $course->id,
                    'created_by' => $course->instructor_id ?? $instructor->id,
                    'title' => $this->getMaterialTitle($i, $type),
                    'description' => $this->getMaterialDescription($type),
                    'type' => $type,
                    'file_name' => $type === 'file' ? $this->getFileName($i) : null,
                    'file_size' => $type === 'file' ? rand(100000, 5000000) : null,
                    'url' => $type !== 'file' ? $this->getUrl($type, $i) : null,
                    'order' => $i,
                    'is_published' => true,
                    'published_at' => now()->subDays(rand(1, 30)),
                ]);

                // Add 0-3 comments per material
                if ($students->isNotEmpty()) {
                    $commentCount = rand(0, 3);

                    for ($j = 0; $j < $commentCount; $j++) {
                        $comment = MaterialComment::create([
                            'material_id' => $material->id,
                            'user_id' => $students->random()->id,
                            'comment' => $this->getComment($j),
                        ]);

                        // 50% chance to add a reply
                        if (rand(0, 1)) {
                            MaterialComment::create([
                                'material_id' => $material->id,
                                'user_id' => $course->instructor_id ?? $instructor->id,
                                'parent_id' => $comment->id,
                                'comment' => $this->getReply(),
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('Materials seeded successfully!');
    }

    private function getMaterialTitle($number, $type): string
    {
        $titles = [
            'file' => [
                "Materi Pertemuan $number",
                "Modul Pembelajaran $number",
                "Slide Presentasi Week $number",
                "Handout Bab $number",
            ],
            'youtube' => [
                "Video Tutorial Part $number",
                "Video Penjelasan Materi $number",
                "Pembelajaran Online Week $number",
            ],
            'link' => [
                "Resource Tambahan $number",
                "Referensi Pembelajaran $number",
                "Link Materi External $number",
            ],
        ];

        return $titles[$type][array_rand($titles[$type])];
    }

    private function getMaterialDescription($type): string
    {
        $descriptions = [
            'file' => 'File PDF berisi materi lengkap untuk dipelajari. Silakan download dan pelajari dengan seksama.',
            'youtube' => 'Video pembelajaran yang menjelaskan konsep-konsep penting. Tonton sampai selesai untuk pemahaman yang lebih baik.',
            'link' => 'Link ke resource external yang berguna untuk memperdalam pemahaman materi.',
        ];

        return $descriptions[$type];
    }

    private function getFileName($number): string
    {
        $extensions = ['pdf', 'pptx', 'docx'];
        $ext = $extensions[array_rand($extensions)];
        return "materi_$number.$ext";
    }

    private function getUrl($type, $number): string
    {
        if ($type === 'youtube') {
            $videoIds = [
                'dQw4w9WgXcQ',
                'jNQXAC9IVRw',
                '9bZkp7q19f0',
                'kJQP7kiw5Fk',
            ];
            return 'https://www.youtube.com/watch?v=' . $videoIds[array_rand($videoIds)];
        }

        return 'https://example.com/resource/' . $number;
    }

    private function getComment($index): string
    {
        $comments = [
            'Terima kasih atas materinya, Pak/Bu! Sangat membantu.',
            'Apakah ada materi tambahan untuk topik ini?',
            'Materi ini sangat jelas dan mudah dipahami.',
            'Saya masih bingung di bagian tertentu, bisa dijelaskan lagi?',
            'Video ini sangat membantu pemahaman saya!',
        ];

        return $comments[array_rand($comments)];
    }

    private function getReply(): string
    {
        $replies = [
            'Sama-sama! Semangat belajarnya.',
            'Silakan cek di materi selanjutnya ya.',
            'Terima kasih atas feedbacknya!',
            'Nanti akan saya jelaskan di pertemuan berikutnya.',
            'Senang mendengar materi ini membantu!',
        ];

        return $replies[array_rand($replies)];
    }
}
