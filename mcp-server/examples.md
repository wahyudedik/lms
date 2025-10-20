# Contoh Penggunaan Laravel LMS MCP Server

Dokumen ini berisi contoh-contoh penggunaan MCP server untuk berbagai skenario.

## 1. Development Workflow

### Membuat Modul Course Lengkap

**Prompt untuk AI:**
```
Saya ingin membuat modul Course untuk LMS. Tolong:
1. Buat model Course dengan migration
2. Tambahkan field: title, description, instructor_id, status, published_at
3. Buat controller CourseController dengan CRUD
4. Tampilkan routes yang tersedia
```

**AI akan menggunakan tools:**
- `create_model` untuk membuat model dan migration
- `run_artisan` untuk generate controller
- `list_routes` untuk menampilkan routes

### Melihat Struktur Database

**Prompt:**
```
Tampilkan struktur table users dan berapa jumlah user yang ada
```

**AI akan:**
- Menggunakan `read_migration` untuk melihat struktur
- Menggunakan `database_query` untuk count users

## 2. Database Management

### Check Database Status

**Prompt:**
```
Apakah database sudah ter-migrate? Kalau belum, jalankan migration
```

### Seed Database

**Prompt:**
```
Jalankan database seeder untuk populate data testing
```

**Command yang dijalankan:**
```
run_artisan: "db:seed"
```

### Query Data

**Prompt:**
```
Berapa jumlah user yang registered hari ini?
```

**Query yang dijalankan:**
```
database_query: "User::whereDate('created_at', today())->count()"
```

## 3. Testing

### Menjalankan Specific Test

**Prompt:**
```
Jalankan test untuk UserTest
```

**Tool call:**
```
run_test: { test_filter: "UserTest" }
```

### Menjalankan Semua Tests

**Prompt:**
```
Jalankan semua test dan laporkan hasilnya
```

## 4. Code Review & Analysis

### Review Model

**Prompt:**
```
Baca model User dan jelaskan relationship yang ada
```

**AI akan:**
- Menggunakan `read_model` untuk membaca User.php
- Menganalisis relationships, fillable, casts, dll

### Review Controller

**Prompt:**
```
Baca UserController dan identifikasi potential security issues
```

### Check Configuration

**Prompt:**
```
Tampilkan konfigurasi database yang sedang digunakan
```

**Tool:**
```
read_config: { config_name: "database" }
```

## 5. Route Management

### List All Routes

**Prompt:**
```
Tampilkan semua routes dan kelompokkan berdasarkan method
```

### Check Specific Route

**Prompt:**
```
Apakah ada route untuk /api/courses?
```

## 6. Model Generation

### Model dengan Full Features

**Prompt:**
```
Buat model Enrollment dengan:
- Migration
- Controller
- Factory
- Resource
- Relationships ke User dan Course
```

**Tools yang digunakan:**
```
1. create_model: { name: "Enrollment", migration: true, controller: true }
2. run_artisan: "make:factory EnrollmentFactory"
3. run_artisan: "make:resource EnrollmentResource"
```

## 7. Environment Check

### Check Environment Configuration

**Prompt:**
```
Tampilkan konfigurasi environment untuk development
```

**Tool:**
```
read_env: {}
```

**Note:** Data sensitif seperti password akan di-mask

## 8. Migration Management

### Create New Migration

**Prompt:**
```
Buat migration untuk menambahkan kolom 'rating' ke table courses
```

**Command:**
```
run_artisan: "make:migration add_rating_to_courses_table"
```

### Read Migration

**Prompt:**
```
Tampilkan isi migration create_users_table
```

**Tool:**
```
read_migration: { migration_name: "create_users_table" }
```

## 9. Debugging

### Clear Cache

**Prompt:**
```
Clear semua cache Laravel
```

**Commands:**
```
run_artisan: "cache:clear"
run_artisan: "config:clear"
run_artisan: "view:clear"
run_artisan: "route:clear"
```

### Check Logs

**Prompt:**
```
Apakah ada error di log hari ini?
```

**Note:** Anda bisa extend MCP server untuk add tool `read_logs`

## 10. Advanced Usage

### Batch Operations

**Prompt:**
```
Saya ingin setup modul lengkap untuk Student Management:
1. Model Student dengan migration
2. Model Enrollment
3. Controller untuk keduanya
4. Run migration
5. Create factory dan seed sample data
6. Run test
```

**AI akan menjalankan sequence:**
1. `create_model` x2
2. `run_artisan` untuk controllers
3. `run_artisan` untuk factories
4. `run_artisan` untuk migration
5. `run_artisan` untuk seed
6. `run_test`

### Performance Analysis

**Prompt:**
```
Analyze performa query User dengan eager loading vs N+1 problem
```

**AI akan:**
- Membaca model
- Membaca controllers
- Memberikan saran optimasi
- Bisa test query via `database_query`

## Tips Penggunaan

### 1. Natural Language
Gunakan bahasa natural, AI akan mengerti:
```
❌ Jangan: "run_artisan dengan parameter make:model Course"
✅ Lakukan: "Buatkan model Course"
```

### 2. Context Aware
AI bisa mengingat context conversation:
```
User: "Buat model Course"
AI: *membuat model*
User: "Sekarang buat controllernya"
AI: *membuat CourseController*
```

### 3. Multiple Tools
Request complex tasks, AI akan chain tools:
```
"Setup complete CRUD untuk Product dengan migration, controller, dan test"
```

### 4. Error Handling
Jika ada error, AI akan handle dan suggest solution:
```
User: "Jalankan migration"
AI: *error database connection*
AI: "Ada error koneksi database. Cek konfigurasi di .env dan pastikan database server running"
```

## Security Notes

1. **Environment Variables**: Tool `read_env` otomatis mask sensitive data
2. **Database Queries**: Hati-hati dengan destructive queries
3. **File Access**: Server hanya bisa akses files di project directory
4. **Artisan Commands**: Avoid dangerous commands seperti `db:wipe` di production

## Limitations

1. Long-running commands mungkin timeout
2. Interactive commands tidak supported
3. Requires PHP dan Composer installed
4. Needs proper file permissions

---

**Pro Tip**: Combine MCP tools dengan filesystem MCP untuk full development experience!

