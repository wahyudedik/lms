# Migrasi Produksi: Modul Kelas (school_classes)

Dokumen ini menjelaskan strategi migrasi non-destruktif untuk menambahkan modul **Kelas** pada aplikasi LMS yang sudah berjalan di produksi (50+ pengguna aktif), tanpa melakukan reset database.

## Ringkasan Perubahan Skema
- Tabel baru: `school_classes`
- Kolom baru pada `users`: `school_class_id` (nullable) + foreign key ke `school_classes` (null on delete)

File migrasi:
- [2026_02_06_000001_create_school_classes_table.php](file:///e:/PROJEKU/lms/database/migrations/2026_02_06_000001_create_school_classes_table.php)
- [2026_02_06_000002_add_school_class_id_to_users_table.php](file:///e:/PROJEKU/lms/database/migrations/2026_02_06_000002_add_school_class_id_to_users_table.php)

Seeder yang relevan:
- [SchoolClassSeeder.php](file:///e:/PROJEKU/lms/database/seeders/SchoolClassSeeder.php)

## Prinsip Migrasi (Aman untuk Data Produksi)
- Hanya melakukan operasi **additive** (create table, add nullable column).
- Migrasi dibuat **idempotent** dengan pengecekan `hasTable/hasColumn` untuk menghindari kegagalan bila sebagian perubahan sudah ada.
- Rollback disediakan lewat `down()` yang membatalkan perubahan (drop kolom FK + drop tabel).
- Data tambahan (Kelas Umum + backfill siswa lama) dilakukan via seeder dan dibungkus transaksi.

## Dampak ke Pengguna Eksisting
- Tidak ada data user yang dihapus.
- Setelah seeder dijalankan, siswa yang `school_class_id` masih `NULL` akan diisi ke **Kelas Umum**.
- Fitur baru: admin bisa memfilter daftar siswa pada halaman enroll untuk enroll massal berdasarkan kelas.

## Checklist Staging (WAJIB sebelum Produksi)
1. Siapkan database staging yang merupakan snapshot/restore dari produksi (struktur + data).
2. Pastikan `.env` staging menunjuk ke DB staging dan bukan produksi.
3. Aktifkan maintenance mode di staging bila perlu untuk mensimulasikan kondisi produksi.
4. Jalankan preflight:
   - `php artisan migrate:status`
   - `php artisan migrate --pretend`
5. Jalankan migrasi:
   - `php artisan migrate --force`
6. Jalankan seeder khusus Kelas:
   - `php artisan db:seed --class=Database\Seeders\SchoolClassSeeder --force`
7. Smoke test UI di staging:
   - Admin → Kelas (CRUD)
   - Admin → Pengguna (dropdown kelas)
   - Admin → Kursus → Enrollments (filter kelas + enroll massal)
8. Verifikasi data:
   - Pastikan jumlah user tidak berubah.
   - Pastikan siswa memiliki `school_class_id` setelah backfill.

## Prosedur Produksi (Runbook)
### 1) Persiapan
- Ambil backup database (full backup) dan simpan checksum / verifikasi restore.
- Pastikan ada jendela rilis dan rencana rollback.
- Jalankan `php artisan down` bila ingin meminimalkan race pada saat migrasi.

### 2) Deploy Kode
- Deploy kode yang berisi migrasi + seeder + perubahan aplikasi.
- Jalankan `php artisan config:cache` / `php artisan route:cache` bila itu bagian SOP rilis Anda.

### 3) Jalankan Migrasi (Non-destruktif)
- `php artisan migrate --force`

### 4) Seed Data Minimum + Backfill Aman
- `php artisan db:seed --class=Database\Seeders\SchoolClassSeeder --force`

### 5) Post-deploy Checks
- `php artisan up` (jika maintenance mode digunakan)
- Verifikasi endpoint admin kritikal tetap bisa diakses.
- Cek log aplikasi untuk error DB/constraint.

## Rollback
Rollback **skema** (bila benar-benar perlu) dapat dilakukan via:
- `php artisan migrate:rollback --step=2 --force`

Catatan: rollback schema bisa berdampak ke fitur yang sudah dipakai user setelah deploy. Jika sudah ada data yang bergantung pada kolom/tabel baru, rollback harus disertai keputusan operasional (mis. menahan fitur atau migrasi maju perbaikan).

## Catatan Transaksi
- Operasi schema pada MySQL dapat melakukan implicit commit; karena itu transaksi lebih relevan untuk operasi **data**.
- Backfill `users.school_class_id` dilakukan via seeder dalam `DB::transaction()` untuk menjaga konsistensi data.

