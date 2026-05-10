# Panduan Ujian CBT

Panduan lengkap Computer-Based Testing (CBT) termasuk pembuatan ujian, bank soal, anti-cheat, dan mode offline.

## Tipe Soal

| Tipe | Keterangan | Auto-Grade |
|------|-----------|-----------|
| MCQ Single | Pilihan ganda (1 jawaban benar) | Ya |
| MCQ Multiple | Pilihan ganda (banyak jawaban benar) | Ya |
| Matching | Mencocokkan pasangan | Ya |
| Essay | Jawaban panjang/uraian | Tidak (manual) |

## Membuat Ujian

### Langkah-langkah:
1. Buka **Manajemen > Ujian**
2. Klik **+ Buat Ujian**
3. Isi konfigurasi:
   - **Kursus** — Pilih kursus terkait
   - **Judul dan Deskripsi**
   - **Durasi** — Waktu pengerjaan (menit)
   - **Waktu Mulai/Selesai** — Jadwal ujian (opsional)
   - **Maksimal Percobaan** — Berapa kali siswa boleh mengerjakan
   - **Passing Score** — Nilai minimum lulus (%)
4. Konfigurasi tampilan:
   - Acak urutan soal
   - Acak urutan opsi jawaban
   - Tampilkan hasil langsung
   - Tampilkan jawaban benar
5. Konfigurasi anti-cheat:
   - Wajib fullscreen
   - Deteksi tab switch
   - Maksimal tab switch sebelum auto-submit
6. Simpan dan tambahkan soal

## Bank Soal

### Keuntungan:
- Soal bisa dipakai ulang di banyak ujian
- Kategorisasi soal berdasarkan topik
- Import/export soal (Excel, JSON, PDF)
- Statistik penggunaan soal

### Membuat Soal di Bank:
1. Buka **Manajemen > Bank Soal**
2. Klik **+ Tambah Soal**
3. Pilih tipe, kategori, dan tingkat kesulitan
4. Isi soal dan jawaban
5. Simpan

### Import Soal ke Ujian:
1. Buka halaman soal ujian
2. Klik **Import from Bank**
3. Filter berdasarkan kategori, tipe, atau kesulitan
4. Pilih soal yang diinginkan
5. Klik **Import Terpilih**

## Sistem Anti-Cheat

### Fitur:
- **Fullscreen Mode** — Siswa wajib dalam mode fullscreen
- **Tab Switch Detection** — Mendeteksi jika siswa pindah tab/window
- **Warning System** — Peringatan bertingkat sebelum auto-submit
- **Incident Logging** — Semua pelanggaran dicatat
- **Auto-Block** — Siswa bisa diblokir login setelah pelanggaran berulang
- **Auto-Unblock** — Blokir otomatis dicabut setelah periode tertentu

### Alur Anti-Cheat:
1. Siswa pindah tab → Warning ke-1
2. Pindah tab lagi → Warning ke-2
3. Melebihi batas → Auto-submit ujian + catat insiden
4. Admin review insiden → Putuskan tindakan (blokir/abaikan)

### Manajemen Insiden:
- Buka **Manajemen > Pelanggaran**
- Lihat semua insiden kecurangan
- Bulk resolve dengan opsi reinstate login
- Export CSV dengan filter aktif

## Mode Offline

### Cara Kerja:
1. Guru mengaktifkan **Offline Mode** pada ujian
2. Siswa download ujian ke perangkat (IndexedDB)
3. Siswa mengerjakan tanpa koneksi internet
4. Saat online kembali, jawaban otomatis sync ke server

### Konfigurasi:
- **Cache Duration** — Berapa lama ujian tersimpan offline (1-168 jam)
- Hanya tersedia jika `PWA_ENABLED=true`

## Token Access (Ujian Tamu)

Untuk ujian yang bisa diakses tanpa login:
1. Aktifkan **Allow Token Access** saat membuat ujian
2. Sistem generate token unik
3. Bagikan link token ke peserta
4. Peserta bisa mengerjakan tanpa akun

Konfigurasi:
- Wajib isi nama tamu
- Wajib isi email tamu (opsional)
- Batas penggunaan token

## Penilaian

### Auto-Grade (MCQ, Matching):
- Nilai dihitung otomatis setelah submit
- Siswa bisa lihat hasil langsung (jika diaktifkan)

### Manual Grade (Essay):
1. Buka ujian → Lihat attempts
2. Pilih attempt yang perlu dinilai
3. Beri skor per soal essay
4. Simpan → Siswa dapat notifikasi

## Tips

- **Acak soal** — Aktifkan shuffle untuk mengurangi kecurangan
- **Waktu realistis** — 1-2 menit per soal MCQ, 10-15 menit per essay
- **Bank soal** — Buat pool besar, ambil random untuk setiap attempt
- **Passing score** — 60-70% untuk ujian standar
- **Anti-cheat** — Aktifkan fullscreen + tab detection untuk ujian penting
