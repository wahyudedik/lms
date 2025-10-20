# Laravel LMS MCP Server

Server Model Context Protocol (MCP) untuk aplikasi Laravel LMS yang memungkinkan AI berinteraksi dengan aplikasi Laravel Anda.

## Fitur

MCP server ini menyediakan tools untuk:

1. **run_artisan** - Menjalankan perintah Artisan Laravel
2. **read_model** - Membaca file model Laravel
3. **read_controller** - Membaca file controller Laravel
4. **list_routes** - Menampilkan semua route yang terdaftar
5. **read_migration** - Membaca file migration
6. **read_config** - Membaca file konfigurasi
7. **run_test** - Menjalankan test menggunakan Pest/PHPUnit
8. **create_model** - Membuat model baru dengan migration dan controller
9. **database_query** - Menjalankan query database via tinker
10. **read_env** - Membaca konfigurasi environment (dengan masking data sensitif)

## Instalasi

### 1. Install dependencies

```bash
cd mcp-server
npm install
```

### 2. Konfigurasi di Cursor/Claude Desktop

Tambahkan konfigurasi berikut ke file config Cursor atau Claude Desktop:

**Untuk Windows (`%APPDATA%\Cursor\User\globalStorage\storage.json` atau `%APPDATA%\Claude\claude_desktop_config.json`):**

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

**Untuk Mac/Linux:**

```json
{
  "mcpServers": {
    "laravel-lms": {
      "command": "node",
      "args": ["/path/to/your/project/mcp-server/index.js"]
    }
  }
}
```

### 3. Restart Cursor/Claude Desktop

Setelah menambahkan konfigurasi, restart aplikasi Cursor atau Claude Desktop.

## Cara Menggunakan

Setelah MCP server terhubung, Anda dapat menggunakan tools melalui AI assistant:

### Contoh Penggunaan

**Menjalankan Artisan Command:**
```
"Jalankan migration menggunakan tool run_artisan dengan command 'migrate'"
```

**Membaca Model:**
```
"Baca model User menggunakan tool read_model"
```

**Membuat Model Baru:**
```
"Buat model Course dengan migration dan controller menggunakan tool create_model"
```

**Melihat Routes:**
```
"Tampilkan semua routes menggunakan tool list_routes"
```

**Menjalankan Test:**
```
"Jalankan semua test menggunakan tool run_test"
```

**Query Database:**
```
"Tampilkan jumlah user menggunakan tool database_query dengan query 'User::count()'"
```

## Struktur Project

```
mcp-server/
├── index.js          # MCP server implementation
├── package.json      # Node.js dependencies
└── README.md         # Dokumentasi
```

## Requirements

- Node.js >= 18.0.0
- PHP >= 8.1
- Laravel 11.x
- Composer dependencies terinstall

## Troubleshooting

### Server tidak terdeteksi
- Pastikan path ke `index.js` sudah benar
- Pastikan Node.js sudah terinstall dan di PATH
- Restart Cursor/Claude Desktop

### Error saat menjalankan command
- Pastikan Anda berada di root directory Laravel
- Pastikan dependencies Composer sudah terinstall
- Check permission file dan direktori

### Permission denied
Di Linux/Mac, tambahkan execute permission:
```bash
chmod +x mcp-server/index.js
```

## Development

Untuk development mode dengan auto-reload:

```bash
npm run dev
```

## Lisensi

MIT

