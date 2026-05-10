# Sistem Sertifikat

Panduan konfigurasi dan pengelolaan sertifikat otomatis untuk siswa yang menyelesaikan kursus.

## Cara Kerja

1. Siswa menyelesaikan semua materi dan tugas di kursus
2. Status enrollment berubah menjadi **Completed**
3. Sistem otomatis generate sertifikat (jika auto-generate aktif)
4. Siswa mendapat notifikasi bahwa sertifikat tersedia
5. Siswa bisa download PDF atau lihat online
6. Sertifikat bisa diverifikasi publik via URL atau QR code

## Template Tersedia

| Template | Gaya | Cocok Untuk |
|----------|------|-------------|
| **Classic** (Default) | Formal, navy + gold, centered | Sertifikat umum |
| **Modern** | Clean, left-aligned, accent biru | Kursus teknologi |
| **Elegant** | Premium, gold-themed, serif | Institusi formal |
| **Minimalist** | Swiss design, bold typography | Profesional |

## Konfigurasi

### Via Admin Panel
Buka **Platform > Sertifikat** untuk:
- Pilih template aktif
- Preview setiap template
- Set informasi institusi
- Konfigurasi warna
- Reset ke default

### Via Environment (.env)

```env
CERTIFICATE_TEMPLATE=default
CERTIFICATE_INSTITUTION_NAME="Nama Institusi"
CERTIFICATE_DIRECTOR_NAME="Nama Direktur"
CERTIFICATE_PRIMARY_COLOR=#3b82f6
CERTIFICATE_SECONDARY_COLOR=#8b5cf6
CERTIFICATE_AUTO_GENERATE=true
CERTIFICATE_NOTIFY_STUDENT=true
CERTIFICATE_QR_CODE=true
```

## Informasi di Sertifikat

Setiap sertifikat menampilkan:
- Nama institusi (dari config)
- Nama siswa
- Judul kursus
- Nilai/Grade (A, B, C, D, F)
- Skor akhir (persentase)
- Tanggal selesai
- Tanggal terbit
- Durasi kursus (jika tersedia)
- Tanda tangan pengajar dan direktur
- Nomor sertifikat unik
- URL verifikasi

## Grading

Nilai otomatis dihitung berdasarkan progress enrollment:

| Skor | Grade | Label |
|------|-------|-------|
| 90-100% | A | Excellent |
| 80-89% | B | Very Good |
| 70-79% | C | Good |
| 60-69% | D | Satisfactory |
| 0-59% | F | Needs Improvement |

## Verifikasi Publik

Setiap sertifikat memiliki:
- **Nomor unik** — Format: `CERT-2026-XXXXXXXX`
- **URL verifikasi** — Bisa diakses publik tanpa login
- **QR Code** — Scan untuk verifikasi cepat (jika diaktifkan)

URL verifikasi: `https://yourdomain.com/verify-certificate/{nomor}`

## Auto-Generate

Jika `CERTIFICATE_AUTO_GENERATE=true`:
- Sertifikat dibuat otomatis saat enrollment status = completed
- Tidak perlu aksi manual dari admin
- Siswa langsung dapat notifikasi

Jika dinonaktifkan, admin bisa generate manual dari halaman manajemen sertifikat.

## Generate Sertifikat yang Terlewat

Jika ada enrollment yang sudah completed tapi belum punya sertifikat:

```bash
php artisan tinker
>>> app(App\Services\CertificateService::class)->generateMissing()
```

## Revoke Sertifikat

Admin bisa mencabut sertifikat jika ditemukan kecurangan:
1. Buka halaman manajemen sertifikat
2. Pilih sertifikat yang ingin dicabut
3. Masukkan alasan pencabutan
4. Sertifikat tidak lagi valid saat diverifikasi

## Format PDF

- Ukuran: A4 Landscape
- DPI: 150
- Font: Georgia (Classic/Elegant), Helvetica (Modern/Minimalist)
- Kompatibel dengan DomPDF (tanpa gradient text, tanpa emoji)
