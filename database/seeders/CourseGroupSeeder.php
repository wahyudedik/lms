<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CourseGroupSeeder extends Seeder
{
    /**
     * Seed Course Groups demo data.
     *
     * Skenario:
     * - 1 Dosen punya 1 kursus "Pemrograman Web Lanjut"
     * - 10 Mahasiswa enrolled di kursus tersebut
     * - 2 Kelompok: "Kelompok Frontend" (5 mahasiswa) dan "Kelompok Backend" (5 mahasiswa)
     * - Materi & Tugas ungrouped (untuk semua) + targeted per kelompok
     *
     * Flow demo:
     * - Pertemuan 1: Materi + Tugas untuk SEMUA (ungrouped)
     * - Pertemuan 2: Materi + Tugas berbeda per kelompok (targeted)
     * - Pertemuan 3: Materi + Tugas untuk SEMUA lagi (ungrouped)
     */
    public function run(): void
    {
        echo "\n👥 Creating Course Groups demo data...\n\n";

        // ============================================================
        // 1. CREATE USERS
        // ============================================================

        // Dosen (course owner)
        $dosen = User::firstOrCreate(
            ['email' => 'dosen.web@lms.com'],
            [
                'name' => 'Dr. Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'phone' => '081300000001',
                'birth_date' => '1980-03-15',
                'gender' => 'laki-laki',
                'address' => 'Jl. Kampus No. 1, Bandung',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        echo "✅ Dosen: {$dosen->name} ({$dosen->email})\n";

        // 10 Mahasiswa
        $mahasiswaData = [
            ['name' => 'Andi Pratama', 'email' => 'andi@lms.com', 'gender' => 'laki-laki'],
            ['name' => 'Bela Safitri', 'email' => 'bela@lms.com', 'gender' => 'perempuan'],
            ['name' => 'Cahyo Wibowo', 'email' => 'cahyo@lms.com', 'gender' => 'laki-laki'],
            ['name' => 'Dina Rahayu', 'email' => 'dina@lms.com', 'gender' => 'perempuan'],
            ['name' => 'Eko Saputra', 'email' => 'eko@lms.com', 'gender' => 'laki-laki'],
            ['name' => 'Fitri Handayani', 'email' => 'fitri@lms.com', 'gender' => 'perempuan'],
            ['name' => 'Gilang Ramadhan', 'email' => 'gilang@lms.com', 'gender' => 'laki-laki'],
            ['name' => 'Hana Permata', 'email' => 'hana@lms.com', 'gender' => 'perempuan'],
            ['name' => 'Irfan Maulana', 'email' => 'irfan@lms.com', 'gender' => 'laki-laki'],
            ['name' => 'Jasmine Putri', 'email' => 'jasmine@lms.com', 'gender' => 'perempuan'],
        ];

        $mahasiswas = collect();
        foreach ($mahasiswaData as $i => $data) {
            $mhs = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'phone' => '08130000' . str_pad($i + 10, 4, '0', STR_PAD_LEFT),
                    'birth_date' => '200' . ($i % 4) . '-0' . (($i % 9) + 1) . '-' . (10 + $i),
                    'gender' => $data['gender'],
                    'address' => 'Jl. Mahasiswa No. ' . ($i + 1) . ', Bandung',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $mahasiswas->push($mhs);
        }
        echo "✅ 10 Mahasiswa created\n";

        // ============================================================
        // 2. CREATE COURSE
        // ============================================================

        $course = Course::firstOrCreate(
            ['code' => 'WEB201'],
            [
                'title' => 'Pemrograman Web Lanjut',
                'description' => 'Kursus pemrograman web lanjut yang membahas frontend (React/Vue) dan backend (Laravel/Node). Di pertengahan semester, mahasiswa dibagi menjadi kelompok Frontend dan Backend untuk mengerjakan proyek berbeda.',
                'instructor_id' => $dosen->id,
                'status' => 'published',
                'max_students' => 20,
                'published_at' => now()->subDays(30),
            ]
        );
        echo "✅ Kursus: {$course->title} ({$course->code})\n";

        // ============================================================
        // 3. ENROLL ALL MAHASISWA
        // ============================================================

        foreach ($mahasiswas as $mhs) {
            Enrollment::firstOrCreate(
                ['user_id' => $mhs->id, 'course_id' => $course->id],
                [
                    'status' => 'active',
                    'progress' => rand(10, 60),
                    'enrolled_at' => now()->subDays(rand(20, 30)),
                ]
            );
        }
        echo "✅ 10 Mahasiswa enrolled di kursus\n";

        // ============================================================
        // 4. CREATE GROUPS (Kelompok Frontend & Backend)
        // ============================================================

        $groupFrontend = CourseGroup::firstOrCreate(
            ['course_id' => $course->id, 'name' => 'Kelompok Frontend'],
        );

        $groupBackend = CourseGroup::firstOrCreate(
            ['course_id' => $course->id, 'name' => 'Kelompok Backend'],
        );

        echo "✅ 2 Kelompok: Frontend & Backend\n";

        // ============================================================
        // 5. ASSIGN MAHASISWA TO GROUPS (5 per group, exclusive)
        // ============================================================

        // First 5 → Frontend
        $frontendMembers = $mahasiswas->slice(0, 5);
        foreach ($frontendMembers as $mhs) {
            if (!$groupFrontend->members()->where('user_id', $mhs->id)->exists()) {
                $groupFrontend->members()->attach($mhs->id);
            }
        }

        // Last 5 → Backend
        $backendMembers = $mahasiswas->slice(5, 5);
        foreach ($backendMembers as $mhs) {
            if (!$groupBackend->members()->where('user_id', $mhs->id)->exists()) {
                $groupBackend->members()->attach($mhs->id);
            }
        }

        echo "✅ Kelompok Frontend: " . $frontendMembers->pluck('name')->join(', ') . "\n";
        echo "✅ Kelompok Backend: " . $backendMembers->pluck('name')->join(', ') . "\n";

        // ============================================================
        // 6. CREATE MATERIALS & ASSIGNMENTS
        // ============================================================

        // --- PERTEMUAN 1: Ungrouped (semua mahasiswa bisa lihat) ---
        $materi1 = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Pertemuan 1: Pengantar Web Modern'],
            [
                'description' => 'Pengenalan arsitektur web modern, HTTP, REST API, dan tools development.',
                'type' => 'link',
                'url' => 'https://developer.mozilla.org/en-US/docs/Learn',
                'created_by' => $dosen->id,
                'is_published' => true,
                'order' => 1,
            ]
        );

        $tugas1 = Assignment::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Tugas 1: Setup Environment'],
            [
                'description' => 'Install Node.js, PHP, Composer, dan buat project Hello World.',
                'deadline' => now()->addDays(7),
                'max_score' => 100,
                'late_policy' => 'penalty',
                'penalty_percentage' => 10,
                'allowed_file_types' => ['pdf', 'doc', 'docx'],
                'created_by' => $dosen->id,
                'is_published' => true,
                'published_at' => now()->subDays(25),
            ]
        );

        echo "\n📚 Pertemuan 1 (Semua Mahasiswa):\n";
        echo "   Materi: {$materi1->title}\n";
        echo "   Tugas: {$tugas1->title}\n";

        // --- PERTEMUAN 2: Targeted per kelompok ---

        // Materi Frontend (hanya Kelompok Frontend)
        $materiFE = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Pertemuan 2: React.js Fundamentals'],
            [
                'description' => 'Belajar React.js: Components, Props, State, dan Hooks.',
                'type' => 'link',
                'url' => 'https://react.dev/learn',
                'created_by' => $dosen->id,
                'is_published' => true,
                'order' => 2,
            ]
        );
        // Associate with Frontend group
        if (!DB::table('course_group_content')->where('course_group_id', $groupFrontend->id)->where('contentable_type', Material::class)->where('contentable_id', $materiFE->id)->exists()) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $groupFrontend->id,
                'contentable_type' => Material::class,
                'contentable_id' => $materiFE->id,
                'created_at' => now(),
            ]);
        }

        // Materi Backend (hanya Kelompok Backend)
        $materiBE = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Pertemuan 2: Laravel API Development'],
            [
                'description' => 'Belajar Laravel: Routing, Controllers, Eloquent ORM, dan API Resources.',
                'type' => 'link',
                'url' => 'https://laravel.com/docs',
                'created_by' => $dosen->id,
                'is_published' => true,
                'order' => 3,
            ]
        );
        // Associate with Backend group
        if (!DB::table('course_group_content')->where('course_group_id', $groupBackend->id)->where('contentable_type', Material::class)->where('contentable_id', $materiBE->id)->exists()) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $groupBackend->id,
                'contentable_type' => Material::class,
                'contentable_id' => $materiBE->id,
                'created_at' => now(),
            ]);
        }

        // Tugas Frontend (hanya Kelompok Frontend)
        $tugasFE = Assignment::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Tugas 2: Buat Komponen React'],
            [
                'description' => 'Buat 3 komponen React: Header, Card, dan Footer dengan props dan state.',
                'deadline' => now()->addDays(14),
                'max_score' => 100,
                'late_policy' => 'penalty',
                'penalty_percentage' => 15,
                'allowed_file_types' => ['pdf', 'doc', 'docx'],
                'created_by' => $dosen->id,
                'is_published' => true,
                'published_at' => now()->subDays(18),
            ]
        );
        if (!DB::table('course_group_content')->where('course_group_id', $groupFrontend->id)->where('contentable_type', Assignment::class)->where('contentable_id', $tugasFE->id)->exists()) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $groupFrontend->id,
                'contentable_type' => Assignment::class,
                'contentable_id' => $tugasFE->id,
                'created_at' => now(),
            ]);
        }

        // Tugas Backend (hanya Kelompok Backend)
        $tugasBE = Assignment::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Tugas 2: Buat REST API Laravel'],
            [
                'description' => 'Buat REST API CRUD untuk resource "Products" dengan validasi dan pagination.',
                'deadline' => now()->addDays(14),
                'max_score' => 100,
                'late_policy' => 'penalty',
                'penalty_percentage' => 15,
                'allowed_file_types' => ['pdf', 'doc', 'docx'],
                'created_by' => $dosen->id,
                'is_published' => true,
                'published_at' => now()->subDays(18),
            ]
        );
        if (!DB::table('course_group_content')->where('course_group_id', $groupBackend->id)->where('contentable_type', Assignment::class)->where('contentable_id', $tugasBE->id)->exists()) {
            DB::table('course_group_content')->insert([
                'course_group_id' => $groupBackend->id,
                'contentable_type' => Assignment::class,
                'contentable_id' => $tugasBE->id,
                'created_at' => now(),
            ]);
        }

        echo "\n📚 Pertemuan 2 (Per Kelompok):\n";
        echo "   [Frontend] Materi: {$materiFE->title}\n";
        echo "   [Frontend] Tugas: {$tugasFE->title}\n";
        echo "   [Backend]  Materi: {$materiBE->title}\n";
        echo "   [Backend]  Tugas: {$tugasBE->title}\n";

        // --- PERTEMUAN 3: Ungrouped lagi (semua mahasiswa) ---
        $materi3 = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Pertemuan 3: Deployment & DevOps'],
            [
                'description' => 'Belajar deploy aplikasi web: Docker, CI/CD, dan cloud hosting.',
                'type' => 'link',
                'url' => 'https://docs.docker.com/get-started/',
                'created_by' => $dosen->id,
                'is_published' => true,
                'order' => 4,
            ]
        );

        $tugas3 = Assignment::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Tugas 3: Deploy Aplikasi ke Cloud'],
            [
                'description' => 'Deploy project kelompok ke platform cloud (Vercel/Railway/Render). Submit URL dan screenshot.',
                'deadline' => now()->addDays(21),
                'max_score' => 100,
                'late_policy' => 'reject',
                'allowed_file_types' => ['pdf', 'doc', 'docx'],
                'created_by' => $dosen->id,
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ]
        );

        echo "\n📚 Pertemuan 3 (Semua Mahasiswa):\n";
        echo "   Materi: {$materi3->title}\n";
        echo "   Tugas: {$tugas3->title}\n";

        // ============================================================
        // SUMMARY
        // ============================================================

        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║           COURSE GROUPS DEMO - SETUP COMPLETE               ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        echo "║                                                             ║\n";
        echo "║  KURSUS: Pemrograman Web Lanjut (WEB201)                    ║\n";
        echo "║  DOSEN : Dr. Budi Santoso (dosen.web@lms.com)               ║\n";
        echo "║                                                             ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        echo "║  KELOMPOK FRONTEND (5 orang):                               ║\n";
        echo "║    - Andi Pratama    (andi@lms.com)                          ║\n";
        echo "║    - Bela Safitri    (bela@lms.com)                          ║\n";
        echo "║    - Cahyo Wibowo    (cahyo@lms.com)                         ║\n";
        echo "║    - Dina Rahayu     (dina@lms.com)                          ║\n";
        echo "║    - Eko Saputra     (eko@lms.com)                           ║\n";
        echo "║                                                             ║\n";
        echo "║  KELOMPOK BACKEND (5 orang):                                ║\n";
        echo "║    - Fitri Handayani (fitri@lms.com)                         ║\n";
        echo "║    - Gilang Ramadhan (gilang@lms.com)                        ║\n";
        echo "║    - Hana Permata    (hana@lms.com)                          ║\n";
        echo "║    - Irfan Maulana   (irfan@lms.com)                         ║\n";
        echo "║    - Jasmine Putri   (jasmine@lms.com)                       ║\n";
        echo "║                                                             ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        echo "║  KONTEN:                                                    ║\n";
        echo "║                                                             ║\n";
        echo "║  [SEMUA]     Materi: Pengantar Web Modern                   ║\n";
        echo "║  [SEMUA]     Tugas:  Setup Environment                      ║\n";
        echo "║  [FRONTEND]  Materi: React.js Fundamentals                  ║\n";
        echo "║  [FRONTEND]  Tugas:  Buat Komponen React                    ║\n";
        echo "║  [BACKEND]   Materi: Laravel API Development                ║\n";
        echo "║  [BACKEND]   Tugas:  Buat REST API Laravel                  ║\n";
        echo "║  [SEMUA]     Materi: Deployment & DevOps                    ║\n";
        echo "║  [SEMUA]     Tugas:  Deploy Aplikasi ke Cloud               ║\n";
        echo "║                                                             ║\n";
        echo "╠══════════════════════════════════════════════════════════════╣\n";
        echo "║  PASSWORD SEMUA USER: password                              ║\n";
        echo "║                                                             ║\n";
        echo "║  TEST LOGIN:                                                ║\n";
        echo "║  • Admin    → admin@lms.com (lihat semua)                   ║\n";
        echo "║  • Dosen    → dosen.web@lms.com (kelola kelompok)           ║\n";
        echo "║  • Frontend → andi@lms.com (lihat materi FE saja)           ║\n";
        echo "║  • Backend  → fitri@lms.com (lihat materi BE saja)          ║\n";
        echo "║                                                             ║\n";
        echo "║  EXPECTED BEHAVIOR:                                         ║\n";
        echo "║  • Andi login → lihat 3 materi + 3 tugas (umum + FE)        ║\n";
        echo "║  • Fitri login → lihat 3 materi + 3 tugas (umum + BE)       ║\n";
        echo "║  • Andi TIDAK bisa lihat materi/tugas Backend               ║\n";
        echo "║  • Fitri TIDAK bisa lihat materi/tugas Frontend             ║\n";
        echo "║                                                             ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
    }
}
