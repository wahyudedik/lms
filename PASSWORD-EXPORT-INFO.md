# 🔐 Export File Password Protection

## Password untuk File Export

**Password**: `lms2024`

## Cara Menggunakan

### Saat Export
1. Klik tombol **"Export"** di halaman User Management
2. Tunggu proses export selesai
3. File akan otomatis terdownload
4. Notifikasi akan muncul dengan informasi password

### Saat Membuka File Export
1. Buka file Excel yang sudah di-download
2. File **dapat dibuka dan dilihat tanpa password**
3. Untuk **mengedit atau mengubah** file:
   - Klik **"Review" → "Unprotect Sheet"** di Excel
   - Masukkan password: `lms2024`
   - Sekarang file dapat diedit

## Proteksi yang Diterapkan

### 🛡️ Worksheet Protection
File Excel yang di-export memiliki proteksi worksheet dengan fitur:
- ❌ **Tidak bisa** edit/ubah data
- ❌ **Tidak bisa** format cells
- ❌ **Tidak bisa** insert/delete rows atau columns
- ❌ **Tidak bisa** sorting
- ✅ **Bisa** dibuka dan dilihat

### 🔒 Workbook Protection
Selain worksheet, workbook juga diproteksi:
- ❌ **Tidak bisa** tambah/hapus/rename sheet
- ❌ **Tidak bisa** resize atau move windows
- ✅ **Bisa** dibuka dan dilihat

## Cara Unprotect File

### Di Microsoft Excel:
1. Klik tab **"Review"**
2. Klik **"Unprotect Sheet"**
3. Masukkan password: `lms2024`
4. Klik **OK**

### Di LibreOffice Calc:
1. Menu **Tools → Protect Sheet**
2. Masukkan password: `lms2024`
3. Klik **OK**

### Di Google Sheets:
Google Sheets tidak mendukung password-protected Excel files secara penuh. Sebaiknya unprotect terlebih dahulu di Excel atau LibreOffice.

## Keamanan

- Password menggunakan enkripsi standard Excel
- Password tidak dapat di-recover jika lupa (harus menggunakan password yang benar)
- File dapat dibuka dan dilihat tanpa password
- Password hanya diperlukan untuk editing

## Troubleshooting

### File tidak bisa diedit
- Pastikan Anda sudah unprotect sheet dengan password `lms2024`
- Periksa apakah ada proteksi workbook yang masih aktif

### Lupa password
- Password default adalah `lms2024`
- Jika password diganti dan lupa, file tidak dapat di-unprotect
- Solusi: Export ulang file dari aplikasi

### File tidak ter-password
- Pastikan sudah menggunakan versi terbaru dari aplikasi
- Clear cache: `php artisan route:clear && php artisan config:clear`
- Export ulang file

## Informasi Teknis

- **Password**: `lms2024`
- **Metode**: Worksheet Protection + Workbook Protection
- **Format**: .xlsx (Excel 2007+)
- **Library**: PhpOffice\PhpSpreadsheet
- **Enkripsi**: Standard Excel Protection

---

**Catatan**: Password ini adalah password default untuk semua export. Jika ingin mengubah password, hubungi administrator sistem.
