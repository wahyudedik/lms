<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a guru user
        $guru = User::where('role', 'guru')->first();

        if (!$guru) {
            $this->command->error('No guru found. Please run UserSeeder first.');
            return;
        }

        // Get courses
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->error('No courses found. Please run CourseSeeder first.');
            return;
        }

        foreach ($courses->take(2) as $index => $course) {
            // Create an exam for each course
            $exam = Exam::create([
                'course_id' => $course->id,
                'created_by' => $course->instructor_id ?? $guru->id,
                'title' => 'Ujian ' . ($index + 1) . ' - ' . $course->title,
                'description' => 'Ujian tengah semester untuk mata pelajaran ' . $course->title,
                'instructions' => "Instruksi Ujian:\n1. Baca setiap soal dengan teliti\n2. Pilih jawaban yang paling tepat\n3. Waktu ujian tidak dapat dijeda\n4. Jangan keluar dari halaman ujian",
                'duration_minutes' => 60,
                'start_time' => now(),
                'end_time' => now()->addDays(30),
                'max_attempts' => 2,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'pass_score' => 70.00,
                'require_fullscreen' => true,
                'detect_tab_switch' => true,
                'max_tab_switches' => 3,
                'is_published' => true,
                'published_at' => now(),
            ]);

            $this->createQuestionsForExam($exam);
        }

        // Create a draft exam
        $draftExam = Exam::create([
            'course_id' => $courses->first()->id,
            'created_by' => $courses->first()->instructor_id ?? $guru->id,
            'title' => 'Ujian Final (Draft)',
            'description' => 'Ujian akhir semester - Masih dalam tahap persiapan',
            'instructions' => 'Instruksi akan ditambahkan kemudian',
            'duration_minutes' => 90,
            'max_attempts' => 1,
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'show_results_immediately' => false,
            'show_correct_answers' => false,
            'pass_score' => 75.00,
            'require_fullscreen' => true,
            'detect_tab_switch' => true,
            'max_tab_switches' => 2,
            'is_published' => false,
        ]);

        $this->createQuestionsForExam($draftExam);

        $this->command->info('Exams created successfully!');
    }

    /**
     * Create sample questions for an exam
     */
    private function createQuestionsForExam(Exam $exam): void
    {
        // MCQ Single - Question 1
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq_single',
            'question_text' => 'Apa ibukota Indonesia?',
            'options' => [
                ['id' => 'A', 'text' => 'Jakarta'],
                ['id' => 'B', 'text' => 'Bandung'],
                ['id' => 'C', 'text' => 'Surabaya'],
                ['id' => 'D', 'text' => 'Medan'],
            ],
            'correct_answer' => 'A',
            'points' => 10,
            'order' => 1,
            'explanation' => 'Jakarta adalah ibukota Republik Indonesia sejak kemerdekaan.',
        ]);

        // MCQ Single - Question 2
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq_single',
            'question_text' => 'Siapa proklamator kemerdekaan Indonesia?',
            'options' => [
                ['id' => 'A', 'text' => 'Soekarno dan Mohammad Hatta'],
                ['id' => 'B', 'text' => 'Soeharto dan Adam Malik'],
                ['id' => 'C', 'text' => 'Joko Widodo dan Jusuf Kalla'],
                ['id' => 'D', 'text' => 'BJ Habibie dan Megawati'],
            ],
            'correct_answer' => 'A',
            'points' => 10,
            'order' => 2,
            'explanation' => 'Ir. Soekarno dan Drs. Mohammad Hatta memproklamasikan kemerdekaan Indonesia pada 17 Agustus 1945.',
        ]);

        // MCQ Multiple - Question 3
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq_multiple',
            'question_text' => 'Pilih pulau-pulau besar di Indonesia (pilih semua yang benar):',
            'options' => [
                ['id' => 'A', 'text' => 'Jawa'],
                ['id' => 'B', 'text' => 'Sumatra'],
                ['id' => 'C', 'text' => 'Kalimantan'],
                ['id' => 'D', 'text' => 'Tasmania'],
                ['id' => 'E', 'text' => 'Sulawesi'],
            ],
            'correct_answer' => ['A', 'B', 'C', 'E'],
            'points' => 15,
            'order' => 3,
            'explanation' => 'Jawa, Sumatra, Kalimantan, dan Sulawesi adalah pulau-pulau besar di Indonesia. Tasmania adalah pulau di Australia.',
        ]);

        // MCQ Multiple - Question 4
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq_multiple',
            'question_text' => 'Manakah yang termasuk negara ASEAN? (pilih semua yang benar)',
            'options' => [
                ['id' => 'A', 'text' => 'Indonesia'],
                ['id' => 'B', 'text' => 'Malaysia'],
                ['id' => 'C', 'text' => 'Tiongkok'],
                ['id' => 'D', 'text' => 'Thailand'],
                ['id' => 'E', 'text' => 'Singapura'],
            ],
            'correct_answer' => ['A', 'B', 'D', 'E'],
            'points' => 15,
            'order' => 4,
            'explanation' => 'Indonesia, Malaysia, Thailand, dan Singapura adalah anggota ASEAN. Tiongkok bukan anggota ASEAN.',
        ]);

        // Matching - Question 5
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'matching',
            'question_text' => 'Cocokkan provinsi dengan ibukotanya:',
            'pairs' => [
                ['left' => 'Jawa Barat', 'right' => 'Bandung'],
                ['left' => 'Jawa Tengah', 'right' => 'Semarang'],
                ['left' => 'Jawa Timur', 'right' => 'Surabaya'],
                ['left' => 'Bali', 'right' => 'Denpasar'],
            ],
            'correct_answer' => [
                ['left' => 'Jawa Barat', 'right' => 'Bandung'],
                ['left' => 'Jawa Tengah', 'right' => 'Semarang'],
                ['left' => 'Jawa Timur', 'right' => 'Surabaya'],
                ['left' => 'Bali', 'right' => 'Denpasar'],
            ],
            'points' => 20,
            'order' => 5,
            'explanation' => 'Masing-masing provinsi memiliki ibukota yang berbeda.',
        ]);

        // Matching - Question 6
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'matching',
            'question_text' => 'Cocokkan tokoh dengan perannya:',
            'pairs' => [
                ['left' => 'R.A. Kartini', 'right' => 'Pahlawan Emansipasi Wanita'],
                ['left' => 'Cut Nyak Dien', 'right' => 'Pahlawan dari Aceh'],
                ['left' => 'Diponegoro', 'right' => 'Pemimpin Perang Jawa'],
                ['left' => 'Ki Hajar Dewantara', 'right' => 'Bapak Pendidikan Indonesia'],
            ],
            'correct_answer' => [
                ['left' => 'R.A. Kartini', 'right' => 'Pahlawan Emansipasi Wanita'],
                ['left' => 'Cut Nyak Dien', 'right' => 'Pahlawan dari Aceh'],
                ['left' => 'Diponegoro', 'right' => 'Pemimpin Perang Jawa'],
                ['left' => 'Ki Hajar Dewantara', 'right' => 'Bapak Pendidikan Indonesia'],
            ],
            'points' => 20,
            'order' => 6,
            'explanation' => 'Setiap tokoh memiliki kontribusi penting dalam sejarah Indonesia.',
        ]);

        // Essay - Question 7 (Manual Grading)
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'essay',
            'question_text' => 'Jelaskan secara singkat (min. 100 kata) tentang pentingnya pendidikan karakter bagi generasi muda Indonesia!',
            'points' => 30,
            'order' => 7,
            'essay_grading_mode' => 'manual',
            'essay_case_sensitive' => false,
            'explanation' => 'Jawaban harus mencakup nilai-nilai karakter, implementasi, dan dampaknya bagi bangsa.',
        ]);

        // Essay - Question 8 (Keyword Matching)
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'essay',
            'question_text' => 'Jelaskan proses fotosintesis pada tumbuhan!',
            'points' => 25,
            'order' => 8,
            'essay_grading_mode' => 'keyword',
            'essay_keywords' => ['fotosintesis', 'klorofil', 'cahaya matahari', 'karbon dioksida', 'oksigen', 'glukosa'],
            'essay_keyword_points' => [4, 4, 4, 4, 4, 5],
            'essay_case_sensitive' => false,
            'explanation' => 'Fotosintesis adalah proses pembuatan makanan oleh tumbuhan menggunakan cahaya matahari, air, dan CO2 dengan bantuan klorofil, menghasilkan glukosa dan oksigen.',
        ]);

        // Essay - Question 9 (Similarity Matching)
        Question::create([
            'exam_id' => $exam->id,
            'type' => 'essay',
            'question_text' => 'Apa yang dimaksud dengan pemanasan global (global warming)?',
            'points' => 20,
            'order' => 9,
            'essay_grading_mode' => 'similarity',
            'essay_model_answer' => 'Pemanasan global adalah peningkatan suhu rata-rata atmosfer, laut, dan daratan bumi yang disebabkan oleh meningkatnya konsentrasi gas rumah kaca seperti CO2, metana, dan N2O di atmosfer. Hal ini terjadi karena aktivitas manusia seperti pembakaran bahan bakar fosil, deforestasi, dan industrialisasi. Dampaknya meliputi pencairan es di kutub, kenaikan permukaan laut, perubahan iklim ekstrem, dan gangguan ekosistem.',
            'essay_min_similarity' => 70,
            'essay_case_sensitive' => false,
            'explanation' => 'Jawaban yang baik mencakup definisi, penyebab, dan dampak pemanasan global.',
        ]);

        $this->command->info("  - Created 9 questions for exam: {$exam->title} (including 3 essay types)");
    }
}
