# Manajemen Kursus dan Tugas

Panduan alur kerja membuat kursus, materi pembelajaran, dan tugas untuk siswa.

## Struktur Hierarki

```
Kursus
├── Materi (PDF, Video, PPT, File)
├── Tugas (dengan deadline dan penilaian)
└── Ujian (CBT dengan berbagai tipe soal)
```

## Membuat Kursus

### Langkah-langkah:
1. Buka **Manajemen > Kursus**
2. Klik **+ Add Course**
3. Isi informasi kursus:
   - Judul kursus
   - Kode kursus (otomatis atau manual)
   - Deskripsi
   - Pengajar (pilih dari daftar guru/dosen)
   - Kapasitas maksimal siswa (opsional)
   - Cover image (opsional)
4. Pilih status: **Draft** atau **Published**
5. Simpan

### Status Kursus:
- **Draft** — Belum terlihat oleh siswa
- **Published** — Siswa bisa mendaftar
- **Archived** — Tidak menerima pendaftaran baru

## Menambah Materi

### Dari halaman detail kursus:
1. Klik **Manage Materials**
2. Klik **+ Tambah Materi**
3. Pilih tipe materi:
   - **PDF** — Dokumen PDF
   - **Video** — Upload video
   - **YouTube** — Link YouTube
   - **PPT** — Presentasi PowerPoint
   - **File** — File umum (preview untuk format browser-viewable)
4. Upload file atau masukkan link
5. Set urutan dan status publikasi

## Membuat Tugas

### Dari halaman detail kursus:
1. Klik **Kelola Tugas** (tombol orange di sidebar)
2. Klik **+ Buat Tugas**
3. Isi detail tugas:
   - **Judul** — Nama tugas
   - **Deskripsi** — Instruksi untuk siswa
   - **Deadline** — Batas waktu pengumpulan
   - **Nilai Maksimal** — Skor tertinggi yang bisa didapat
   - **Tipe File** — Format file yang diizinkan (PDF, DOC, PPT, dll)
   - **Kebijakan Keterlambatan**:
     - `Allow` — Terima terlambat tanpa penalti
     - `Reject` — Tolak pengumpulan terlambat
     - `Penalty` — Terima dengan pengurangan nilai (%)
   - **Materi Terkait** — Link ke materi tertentu (opsional)
4. Centang **Publish** jika ingin langsung terlihat siswa
5. Simpan

### Notifikasi Otomatis:
- Saat tugas dipublikasikan → siswa mendapat notifikasi
- H-24 jam sebelum deadline → reminder otomatis ke siswa yang belum submit

## Menilai Tugas

1. Buka tugas yang ingin dinilai
2. Klik **Lihat Pengumpulan**
3. Untuk setiap submission:
   - Klik **Lihat** untuk preview file
   - Klik **Unduh** untuk download
   - Masukkan nilai dan feedback
   - Simpan penilaian

## Tips

- **Publish bertahap** — Buat tugas sebagai Draft dulu, publish saat materi sudah siap
- **Deadline realistis** — Beri waktu minimal 3-7 hari untuk tugas
- **File types** — Batasi tipe file untuk memudahkan penilaian (PDF direkomendasikan)
- **Penalti** — Gunakan penalty 10-20% per hari untuk kebijakan keterlambatan yang adil
