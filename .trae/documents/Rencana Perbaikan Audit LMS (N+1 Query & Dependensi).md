Saya akan melakukan perbaikan berdasarkan temuan audit yang Anda soroti:

1. **Perbaiki N+1 Query di AnalyticsController**:

   * Pada method `examPerformanceStats`, saya akan mengoptimalkan pengambilan data statistik untuk menghindari query berulang dalam loop, terutama pada perhitungan rata-rata nilai dan *pass rate*.

2. **Perbaiki N+1 Query di QuestionBankController**:

   * Pada method `statistics`, saya akan mengganti query `QuestionBankCategory::withCount('questions')->having('questions_count', '>', 0)->get()` dan query lain yang berpotensi berat menjadi lebih efisien.

   * Pada method `importFromBank` (jika ada N+1 yang terdeteksi dalam logika yang tidak terlihat di file saat ini namun disebutkan di audit), saya akan memastikannya efisien.

   * *Catatan*: Dari pembacaan file `QuestionBankController.php` saat ini, method `importFromBank` tidak ditemukan (kemungkinan ada di file lain atau sudah dihapus/berubah nama), namun saya akan fokus mengoptimalkan method `statistics` dan `getForImport` yang menggunakan `map` setelah query.

3. **Perbaiki Dependensi di** **`composer.json`**:

   * Menurunkan `laravel/framework` ke `^11.0` (Stable) untuk keamanan dan stabilitas.

   * Mengunci versi `maatwebsite/excel` ke `^3.1`.

   * Mengunci versi `spatie/laravel-medialibrary` ke `^10.0` (atau versi stabil terbaru yang kompatibel).

   * Menjalankan `composer update` (simulasi via instruksi manual karena keterbatasan environment) untuk memastikan lock file terupdate.

**Rencana Eksekusi:**

1. **Edit** **`app/Http/Controllers/Admin/AnalyticsController.php`**: Optimalkan query di `examPerformanceStats` menggunakan agregasi database langsung.
2. **Edit** **`composer.json`**: Perbaiki versi paket.
3. **Verifikasi**: Jalankan `php artisan test` untuk memastikan tidak ada *breaking changes*.

