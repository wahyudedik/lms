<?php

namespace Database\Seeders;

use App\Models\InformationCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class InformationCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@lms.com')->first();
        $guru = User::where('email', 'guru@lms.com')->first();
        $dosen = User::where('email', 'dosen@lms.com')->first();

        if (!$admin || !$guru || !$dosen) {
            $this->command->warn('Required users not found. Run UserSeeder first.');
            return;
        }

        // =============================================
        // ADMIN cards → for all roles
        // =============================================

        // Card for all roles
        InformationCard::create([
            'created_by' => $admin->id,
            'title' => 'Pemeliharaan Sistem',
            'content' => '<p>Sistem akan mengalami <strong>pemeliharaan rutin</strong> pada hari Minggu, 18 Mei 2026 pukul 02:00 - 06:00 WIB.</p><p>Selama waktu tersebut, akses ke platform akan terbatas. Mohon simpan pekerjaan Anda sebelum waktu pemeliharaan.</p>',
            'card_type' => 'warning',
            'icon' => 'fas fa-exclamation-triangle',
            'target_roles' => ['admin', 'guru', 'dosen', 'siswa', 'mahasiswa'],
            'target_user_ids' => null,
            'schedule_type' => 'date_range',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Card for siswa & mahasiswa
        InformationCard::create([
            'created_by' => $admin->id,
            'title' => 'Pendaftaran Ujian Semester Genap',
            'content' => '<p>Pendaftaran ujian semester genap telah dibuka. Pastikan Anda telah:</p><ul><li>Menyelesaikan semua tugas yang tertunda</li><li>Mengisi formulir pendaftaran ujian</li><li>Mengecek jadwal ujian di menu Ujian</li></ul><p>Batas akhir pendaftaran: <strong>30 Mei 2026</strong></p>',
            'card_type' => 'info',
            'icon' => 'fas fa-clipboard-list',
            'target_roles' => ['siswa', 'mahasiswa'],
            'target_user_ids' => null,
            'schedule_type' => 'always',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Card for guru & dosen
        InformationCard::create([
            'created_by' => $admin->id,
            'title' => 'Reminder: Input Nilai Semester',
            'content' => '<p>Batas akhir input nilai semester genap adalah <strong>15 Juni 2026</strong>. Mohon segera menyelesaikan penilaian untuk semua mata kuliah/pelajaran yang diampu.</p>',
            'card_type' => 'danger',
            'icon' => 'fas fa-bell',
            'target_roles' => ['guru', 'dosen'],
            'target_user_ids' => null,
            'schedule_type' => 'always',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // =============================================
        // GURU card → for siswa only
        // =============================================

        InformationCard::create([
            'created_by' => $guru->id,
            'title' => 'Pengumuman Kelas Matematika',
            'content' => '<p>Perhatian untuk seluruh siswa:</p><ul><li>Quiz mingguan akan diadakan setiap <strong>Jumat pukul 10:00</strong></li><li>Materi bab 5 sudah tersedia di menu Kursus</li><li>Tugas PR harus dikumpulkan sebelum Kamis</li></ul><p>Semangat belajar! 📚</p>',
            'card_type' => 'success',
            'icon' => 'fas fa-graduation-cap',
            'target_roles' => ['siswa'],
            'target_user_ids' => null,
            'schedule_type' => 'daily',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // =============================================
        // DOSEN card → for mahasiswa only
        // =============================================

        InformationCard::create([
            'created_by' => $dosen->id,
            'title' => 'Jadwal Bimbingan Skripsi',
            'content' => '<p>Jadwal bimbingan skripsi untuk minggu ini:</p><ul><li><strong>Senin</strong>: 09:00 - 12:00 (Ruang 301)</li><li><strong>Rabu</strong>: 13:00 - 16:00 (Online via Zoom)</li><li><strong>Jumat</strong>: 10:00 - 12:00 (Ruang 301)</li></ul><p>Silakan booking slot melalui email terlebih dahulu.</p>',
            'card_type' => 'info',
            'icon' => 'fas fa-calendar-alt',
            'target_roles' => ['mahasiswa'],
            'target_user_ids' => null,
            'schedule_type' => 'always',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->command->info('Information cards seeded successfully!');
        $this->command->table(
            ['Creator', 'Title', 'Target Roles', 'Schedule'],
            InformationCard::with('creator')->get()->map(fn($c) => [
                $c->creator->name . ' (' . $c->creator->role . ')',
                $c->title,
                implode(', ', $c->target_roles),
                $c->schedule_type,
            ])->toArray()
        );
    }
}
