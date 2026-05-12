# Migrasi: Information Cards (Kartu Informasi)

Dokumen ini menjelaskan migrasi untuk fitur **Information Cards** — kartu pengumuman/informasi yang ditampilkan di dashboard pengguna berdasarkan role dan jadwal tertentu.

## Ringkasan Fitur

Information Cards memungkinkan admin dan guru/dosen membuat kartu informasi yang ditargetkan ke role atau pengguna tertentu, dengan dukungan penjadwalan (selalu tampil, rentang tanggal, atau harian). Guru/Dosen dapat membuat kartu yang ditampilkan ke siswa/mahasiswa mereka.

### Kemampuan Utama
- **Targeting berdasarkan role**: Kartu bisa ditampilkan ke satu atau beberapa role (admin, guru, dosen, siswa, mahasiswa)
- **Targeting per pengguna**: Opsional menargetkan user ID tertentu dalam role yang dipilih
- **Penjadwalan**: Tiga mode — `always` (selalu), `date_range` (rentang tanggal), `daily` (harian)
- **Tipe kartu**: `info`, `warning`, `success`, `danger` — masing-masing dengan warna berbeda
- **Ikon kustom**: Mendukung class FontAwesome
- **Lampiran file**: Upload file attachment (dokumen, gambar, dll.)
- **Video URL**: Embed video dari URL eksternal (YouTube, dll.)
- **Pengurutan**: Field `sort_order` untuk mengatur prioritas tampilan

## Perubahan Skema

### Tabel Baru: `information_cards`

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint (PK) | Primary key |
| `created_by` | foreignId → users | Admin yang membuat kartu |
| `title` | string | Judul kartu |
| `content` | text | Isi/konten kartu |
| `card_type` | string | Tipe: `info`, `warning`, `success`, `danger` (default: `info`) |
| `icon` | string (nullable) | Class ikon FontAwesome |
| `attachment_path` | string (nullable) | Path file lampiran di storage |
| `attachment_name` | string (nullable) | Nama asli file lampiran |
| `attachment_size` | unsignedBigInteger (nullable) | Ukuran file lampiran dalam bytes |
| `video_url` | string (nullable) | URL video eksternal (YouTube, dll.) |
| `target_roles` | json | Array role target, misal `["siswa", "mahasiswa"]` |
| `target_user_ids` | json (nullable) | Array user ID spesifik, `null` = semua user dalam role |
| `schedule_type` | enum | `always`, `date_range`, atau `daily` (default: `always`) |
| `start_date` | date (nullable) | Tanggal mulai (untuk `date_range`) |
| `end_date` | date (nullable) | Tanggal selesai (untuk `date_range`) |
| `daily_start_time` | time (nullable) | Waktu mulai harian (untuk `daily`) |
| `daily_end_time` | time (nullable) | Waktu selesai harian (untuk `daily`) |
| `is_active` | boolean | Status aktif (default: `true`) |
| `sort_order` | integer | Urutan tampil (default: `0`) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diperbarui |

### Index
- `is_active` — filter kartu aktif
- `schedule_type` — filter berdasarkan tipe jadwal
- `created_by` — query kartu per admin

File migrasi:
- `database/migrations/2026_05_12_000001_create_information_cards_table.php`
- `database/migrations/2026_05_12_000002_add_attachment_fields_to_information_cards_table.php`

## Model & Scopes

Model: `App\Models\InformationCard`

### Scope Utama
- `scopeActive()` — hanya kartu dengan `is_active = true`
- `scopeVisibleTo(User $user)` — kartu yang visible untuk user tertentu berdasarkan role, target user, dan jadwal

### Accessor
- `card_color_class` — CSS class Tailwind sesuai `card_type`
- `icon_color_class` — CSS class warna ikon sesuai `card_type`

## Akses & Route

- **Controller (Admin)**: `App\Http\Controllers\Admin\InformationCardController`
- **Controller (Guru/Dosen)**: `App\Http\Controllers\Guru\InformationCardController`
- **Akses Admin**: Admin dapat membuat, mengedit, dan menghapus kartu untuk semua role
- **Akses Guru/Dosen**: Guru/Dosen dapat membuat kartu yang ditargetkan ke siswa/mahasiswa mereka
- **Fitur toggle**: Admin dan Guru/Dosen dapat mengaktifkan/menonaktifkan kartu tanpa menghapusnya

### Route Guru/Dosen
- `GET /guru/information-cards` — Daftar kartu milik guru
- `GET /guru/information-cards/create` — Form buat kartu baru
- `POST /guru/information-cards` — Simpan kartu baru
- `GET /guru/information-cards/{id}/edit` — Form edit kartu
- `PUT /guru/information-cards/{id}` — Update kartu
- `DELETE /guru/information-cards/{id}` — Hapus kartu

Route dosen menggunakan prefix `/dosen/` dengan controller yang sama.

## Prosedur Migrasi Produksi

### 1) Persiapan
- Backup database (full backup)
- Migrasi ini bersifat **additive** (hanya membuat tabel baru, tidak mengubah tabel existing)

### 2) Jalankan Migrasi
```bash
php artisan migrate --force
```

### 3) Verifikasi
```bash
php artisan migrate:status
```
Pastikan migration `2026_05_12_000001_create_information_cards_table` berstatus **Ran**.

## Rollback

```bash
php artisan migrate:rollback --step=1 --force
```

Ini akan menghapus tabel `information_cards` beserta semua datanya.

## Dampak ke Pengguna Eksisting
- **Tidak ada dampak** ke data atau fitur yang sudah ada
- Fitur baru: admin mendapat menu untuk membuat kartu informasi untuk semua role
- Fitur baru: guru/dosen mendapat menu untuk membuat kartu informasi untuk siswa/mahasiswa mereka
- Dashboard pengguna akan menampilkan kartu yang ditargetkan ke role mereka
