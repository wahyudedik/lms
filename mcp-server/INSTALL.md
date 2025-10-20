# Panduan Instalasi MCP Server Laravel LMS

## Langkah 1: Install Dependencies (✅ Selesai)

Dependencies sudah terinstall.

## Langkah 2: Konfigurasi di Cursor

### Lokasi File Konfigurasi Cursor

File konfigurasi Cursor ada di:
```
%APPDATA%\Cursor\User\globalStorage\rooveterinaryinc.roo-cline\settings\cline_mcp_settings.json
```

Atau bisa juga di:
```
C:\Users\[USERNAME]\AppData\Roaming\Cursor\User\globalStorage\rooveterinaryinc.roo-cline\settings\cline_mcp_settings.json
```

### Cara Menambahkan Konfigurasi

1. Buka File Explorer
2. Paste path di atas ke address bar
3. Buat file `cline_mcp_settings.json` jika belum ada
4. Copy konfigurasi berikut:

```json
{
  "mcpServers": {
    "laravel-lms": {
      "command": "node",
      "args": ["E:\\PROJEK  LARAVEL\\lms\\mcp-server\\index.js"]
    }
  }
}
```

**⚠️ PENTING:** Jika file sudah ada dan sudah berisi konfigurasi lain, tambahkan konfigurasi laravel-lms ke dalam object `mcpServers` yang sudah ada.

### Cara Alternatif: Melalui Cursor Settings

1. Buka Cursor
2. Tekan `Ctrl + Shift + P` untuk membuka Command Palette
3. Ketik "MCP" dan cari setting untuk MCP
4. Tambahkan konfigurasi server

## Langkah 3: Test MCP Server

### Test Manual

Untuk test apakah server berjalan dengan baik, jalankan:

```bash
node index.js
```

Server akan menampilkan:
```
Laravel LMS MCP Server running on stdio
```

Tekan `Ctrl + C` untuk stop.

### Test di Cursor

1. Restart Cursor setelah menambahkan konfigurasi
2. Buka chat dengan AI
3. Coba tanyakan: "Apakah MCP server laravel-lms tersedia?"
4. AI akan mendeteksi tools yang tersedia

## Contoh Penggunaan

Setelah MCP server terkonfigurasi, Anda bisa menggunakan perintah seperti:

### 1. List Routes
```
"Tampilkan semua routes yang ada di aplikasi Laravel"
```

### 2. Baca Model
```
"Baca isi file model User"
```

### 3. Jalankan Artisan
```
"Jalankan php artisan migrate"
```

### 4. Buat Model Baru
```
"Buatkan model Course dengan migration dan controller"
```

### 5. Run Tests
```
"Jalankan semua test"
```

### 6. Database Query
```
"Berapa jumlah user yang ada di database?"
```

## Tools yang Tersedia

1. ✅ **run_artisan** - Menjalankan perintah Artisan
2. ✅ **read_model** - Membaca model
3. ✅ **read_controller** - Membaca controller
4. ✅ **list_routes** - List semua routes
5. ✅ **read_migration** - Membaca migration
6. ✅ **read_config** - Membaca config
7. ✅ **run_test** - Menjalankan tests
8. ✅ **create_model** - Membuat model baru
9. ✅ **database_query** - Query database via tinker
10. ✅ **read_env** - Membaca .env (dengan masking)

## Troubleshooting

### MCP Server tidak terdeteksi

1. Pastikan path sudah benar (gunakan double backslash `\\` di Windows)
2. Restart Cursor setelah menambahkan konfigurasi
3. Check apakah Node.js terinstall: `node --version`

### Error saat menjalankan tool

1. Pastikan Anda berada di root directory Laravel
2. Pastikan composer dependencies terinstall: `composer install`
3. Pastikan `.env` file ada dan database terkonfigurasi

### Permission Error

Pastikan Node.js memiliki permission untuk mengakses folder project.

## Tips

- Gunakan bahasa natural untuk berkomunikasi dengan AI
- AI akan secara otomatis memilih tool yang tepat
- Anda bisa meminta AI untuk melakukan multiple actions sekaligus
- MCP server bekerja dengan artisan commands, jadi pastikan Laravel environment sudah siap

## Support

Jika ada masalah, check:
1. Node.js version: `node --version` (minimal v18)
2. Laravel version: `php artisan --version`
3. Composer packages: `composer install`
4. File permissions

---

**Status Instalasi: ✅ Dependencies Installed**
**Next Step: Konfigurasi di Cursor Settings**

