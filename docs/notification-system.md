# Sistem Notifikasi

Penjelasan lengkap kapan dan bagaimana notifikasi dikirim ke pengguna.

## Channel Notifikasi

Sistem mendukung 2 channel:
- **Database** — Notifikasi in-app (bell icon di navbar)
- **Push** — Web push notification (browser)

Setiap siswa bisa mengatur preferensi channel di halaman **Preferensi Notifikasi**.

## Jenis Notifikasi

### Untuk Siswa/Mahasiswa

| Event | Notifikasi | Trigger |
|-------|-----------|---------|
| Tugas baru dipublikasikan | AssignmentPublished | Guru/Admin publish tugas |
| Ujian baru dipublikasikan | ExamScheduled | Guru/Admin publish ujian |
| Deadline tugas H-24 jam | AssignmentDeadlineReminder | Scheduler (tiap jam) |
| Materi baru dipublikasikan | MaterialPublished | Guru publish materi |
| Nilai tugas keluar | SubmissionGraded | Guru menilai submission |
| Nilai ujian keluar | ExamGraded | Sistem auto-grade |
| Sertifikat tersedia | CertificateAvailable | Kursus selesai |
| Enrollment diterima | EnrollmentCreated | Admin/sistem approve |
| Disebut di forum | UserMentioned | User lain mention |
| Balasan forum | ForumReplyReceived | Ada reply di thread |
| Komentar materi | MaterialCommentReceived | Ada komentar baru |

### Untuk Guru/Dosen

| Event | Notifikasi | Trigger |
|-------|-----------|---------|
| Siswa submit tugas | AssignmentSubmitted | Siswa upload tugas |
| Thread forum baru | ForumThreadCreated | Siswa buat thread |
| Kecurangan terdeteksi | CheatingIncidentDetected | Anti-cheat system |

## Kapan Notifikasi Terkirim

### Tugas (Assignment)
- **Saat dipublikasikan** — Semua siswa aktif di kursus mendapat notifikasi
- **Saat di-toggle publish** — Jika dari unpublish ke publish, notifikasi terkirim
- **H-24 jam deadline** — Hanya siswa yang BELUM submit yang dapat reminder
- **Tidak ada duplikat** — Reminder hanya dikirim 1x per hari per tugas

### Ujian (Exam)
- **Saat dipublikasikan** — Semua siswa aktif di kursus mendapat notifikasi
- **Saat di-toggle publish** — Jika dari unpublish ke publish, notifikasi terkirim
- Berlaku untuk aksi dari **Guru/Dosen DAN Admin**

### Materi
- **Saat dipublikasikan** — Semua siswa aktif di kursus mendapat notifikasi

## Scheduler

Notifikasi yang dijadwalkan (perlu cron aktif):

```
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

| Command | Jadwal | Fungsi |
|---------|--------|--------|
| `assignments:send-deadline-reminders` | Setiap jam | Kirim reminder H-24 jam |
| `cheating:auto-unblock` | Harian | Unblock user yang masa blokir habis |

## Queue

Semua notifikasi menggunakan queue (async) supaya tidak memperlambat request. Pastikan queue worker berjalan:

```bash
php artisan queue:work --sleep=3 --tries=3
```

Atau via Supervisor untuk production (lihat Panduan Deployment).

## Preferensi Pengguna

Siswa bisa mengatur notifikasi di **Profil > Preferensi Notifikasi**:
- Aktifkan/nonaktifkan per tipe notifikasi
- Pilih channel (database, push, atau keduanya)
- Default: semua aktif

## Push Notifications

Untuk mengaktifkan push notifications:
1. Buka **Platform > Pengaturan > Notifikasi**
2. Generate VAPID keys
3. Aktifkan push notifications
4. Siswa perlu mengizinkan notifikasi di browser mereka

## Troubleshooting

### Notifikasi tidak terkirim
1. Cek queue worker berjalan: `php artisan queue:work`
2. Cek tabel `jobs` di database — ada job yang gagal?
3. Cek `failed_jobs` table untuk error detail
4. Pastikan siswa terdaftar aktif di kursus

### Push notification tidak muncul
1. Pastikan VAPID keys sudah di-generate
2. Pastikan push notifications enabled di settings
3. Siswa harus allow notification permission di browser
4. HTTPS wajib untuk push notifications (tidak bisa di HTTP)
