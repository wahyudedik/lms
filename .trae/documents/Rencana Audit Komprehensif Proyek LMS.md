Saya akan melakukan audit menyeluruh terhadap proyek LMS ini dengan langkah-langkah berikut:

## Fase 1: Analisis Dependensi dan Lingkungan
1.  Memeriksa `composer.json` dan `package.json` untuk mengidentifikasi versi framework, library yang digunakan, dan potensi kerentanan keamanan pada dependensi.
2.  Memeriksa konfigurasi lingkungan (`.env.example`) dan file konfigurasi di `config/` untuk memastikan pengaturan keamanan standar (debug mode, app key, session drivers).

## Fase 2: Audit Keamanan dan Arsitektur
1.  **Analisis Route & Middleware**: Memeriksa `routes/web.php` dan `api.php` untuk memastikan semua endpoint sensitif dilindungi oleh middleware (`auth`, `role`, `verified`) yang tepat.
2.  **Proteksi Input & Data**: Meninjau Controller utama (Admin, Guru, Siswa) untuk memastikan validasi input (`$request->validate`) dilakukan sebelum pemrosesan data.
3.  **Celah Keamanan Umum**: Mencari pola kode yang berisiko seperti penggunaan `{!! !!}` yang tidak aman (XSS), raw SQL queries (SQL Injection), dan mass assignment pada Model.

## Fase 3: Review Kualitas Kode dan Performa
1.  **Deteksi Bug & Error**: Mencari sisa kode debug (`dd()`, `dump()`, `print_r`), komentar `TODO`/`FIXME` yang belum terselesaikan, dan blok `try-catch` yang kosong.
2.  **Optimasi Database**: Memeriksa Migrations untuk memastikan penggunaan index yang tepat dan meninjau kode untuk mendeteksi masalah N+1 query (query dalam loop tanpa eager loading).
3.  **Struktur & Standarisasi**: Mengevaluasi konsistensi penamaan class, method, dan struktur folder View.

## Fase 4: Pengecekan Kelengkapan Fitur
1.  Memverifikasi apakah semua Route memiliki Controller dan View yang sesuai (seperti kasus `RouteNotFoundException` sebelumnya).
2.  Memeriksa keberadaan file testing (`tests/`) dan cakupannya.
3.  Menilai kelengkapan dokumentasi teknis (PHPDoc, README).

## Output
Laporan audit rinci yang mencakup:
*   Daftar temuan (Bug, Security, Performance, Incomplete Features).
*   Tingkat keparahan (High/Medium/Low).
*   Rekomendasi perbaikan spesifik.
*   Prioritas penanganan.