# 🚀 Quick Start - Laravel LMS MCP Server

## ✅ Status: Ready to Use!

Semua dependencies sudah terinstall dan test passed.

## 📋 Langkah Cepat Setup

### 1. Buka File Konfigurasi Cursor

Tekan `Win + R`, ketik:
```
%APPDATA%\Cursor\User\globalStorage\rooveterinaryinc.roo-cline\settings
```

### 2. Edit atau Buat File `cline_mcp_settings.json`

Copy paste konfigurasi ini:

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

### 3. Restart Cursor

Tutup dan buka lagi Cursor.

### 4. Test di Chat

Buka Cursor dan coba tanyakan ke AI:

```
"Apakah MCP server laravel-lms tersedia?"
```

atau

```
"Tampilkan semua routes Laravel yang ada"
```

## 🎯 Contoh Penggunaan Langsung

### Membuat Model Baru
```
"Buatkan model Course dengan migration dan controller"
```

### Check Database
```
"Berapa jumlah user di database?"
```

### List Routes
```
"Tampilkan semua routes"
```

### Run Tests
```
"Jalankan semua test"
```

### Baca Kode
```
"Baca model User dan jelaskan relationshipnya"
```

## 🛠️ Tools yang Tersedia

| Tool | Deskripsi | Contoh |
|------|-----------|--------|
| `run_artisan` | Jalankan artisan command | "Jalankan migrate" |
| `read_model` | Baca file model | "Baca model User" |
| `read_controller` | Baca controller | "Baca UserController" |
| `list_routes` | List semua routes | "Tampilkan routes" |
| `read_migration` | Baca migration | "Baca migration users" |
| `read_config` | Baca config | "Tampilkan config database" |
| `run_test` | Jalankan tests | "Run test" |
| `create_model` | Buat model baru | "Buat model Course" |
| `database_query` | Query database | "Count users" |
| `read_env` | Baca .env | "Tampilkan env" |

## 📚 Dokumentasi Lengkap

- **INSTALL.md** - Panduan instalasi detail
- **README.md** - Dokumentasi lengkap
- **examples.md** - Contoh penggunaan advanced
- **test-server.js** - Script untuk test server

## 🧪 Test Server

Untuk memastikan server berfungsi:

```bash
npm test
```

Semua test harus passed (✅).

## ⚠️ Troubleshooting

### MCP tidak terdeteksi
1. Pastikan path di config benar
2. Restart Cursor
3. Check Node.js: `node --version`

### Error saat jalankan tool
1. Pastikan di root Laravel
2. Run `composer install`
3. Check database connection

## 💡 Tips Pro

1. **Gunakan bahasa natural** - AI mengerti bahasa Indonesia
2. **Combine multiple tasks** - "Buat model Course, controllernya, dan run migration"
3. **Ask for explanations** - "Explain code ini dan suggest improvements"
4. **Debug together** - "Ada error di UserController, tolong fix"

## 🎉 Siap Digunakan!

Server sudah ready. Restart Cursor dan mulai coding dengan AI assistant yang powerful!

---

**Need Help?**
- Check INSTALL.md untuk detail
- See examples.md untuk use cases
- Run `npm test` untuk verify setup

