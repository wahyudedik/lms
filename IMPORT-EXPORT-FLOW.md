# 📊 Alur Import & Export Users dengan Auto-Generate Password

## 🎯 Konsep Utama

Sistem ini dirancang agar:
1. **Import** → Admin tidak perlu memasukkan password, sistem **auto-generate**
2. **Export** → File Excel berisi password yang sudah di-generate
3. **Distribusi** → File Excel dikirim ke group, user bisa login dengan password dari file
4. **User Login** → User login dan mengganti password sendiri

---

## 📥 Alur Import Users

### Step 1: Persiapan File Import

1. Login sebagai **Admin**
2. Buka **User Management** → Klik **"Import"**
3. Download **Template** (tanpa kolom password)
4. Isi data user di template:
   ```
   | Name          | Email              | Role  | Phone        | ... |
   |---------------|--------------------|-------|--------------|-----|
   | Budi Santoso  | budi@example.com   | siswa | 081234567890 | ... |
   | Siti Nurhaliza| siti@example.com   | guru  | 081234567891 | ... |
   ```

### Step 2: Import File

1. Upload file Excel/CSV
2. Klik **"Import Users"**
3. Sistem akan:
   - Membaca data dari file
   - **Auto-generate password** untuk setiap user
   - Format password: `FirstName + Role + 4 digits random`
   - Contoh:
     - Budi Santoso (siswa) → `BudiSiswa1234`
     - Siti Nurhaliza (guru) → `SitiGuru5678`
   - Menyimpan password (hash) ke database
   - Menyimpan password (plain) untuk export

### Step 3: Verifikasi Import

1. Setelah import berhasil, cek halaman User Management
2. User baru akan muncul di list
3. Status: Active, Email Verified

---

## 📤 Alur Export Users dengan Password

### Step 1: Export Data

1. Di halaman **User Management**
2. (Opsional) Filter user yang ingin di-export:
   - Search by name/email
   - Filter by role
   - Filter by status
3. Klik tombol **"Export"** (hijau)
4. File Excel akan otomatis terdownload

### Step 2: Isi File Export

File Excel akan berisi kolom-kolom berikut:

| ID | Name | Email | **Password** | Role | Phone | ... |
|----|------|-------|--------------|------|-------|-----|
| 1 | Budi Santoso | budi@example.com | **BudiSiswa1234** | Siswa | 081... | ... |
| 2 | Siti Nurhaliza | siti@example.com | **SitiGuru5678** | Guru | 081... | ... |

**Kolom Password** berisi password asli yang di-generate saat import!

---

## 📧 Distribusi Password ke User

### Cara 1: Kirim File Excel ke Group

1. Setelah export, file Excel berisi semua data user + password
2. Kirim file ke **WhatsApp Group** / **Telegram Group** / **Email**
3. User bisa membuka file dan mencari email mereka
4. User bisa melihat password mereka di kolom "Password"

### Cara 2: Cetak dan Bagikan

1. Print file Excel
2. Bagikan kertas ke setiap user
3. User melihat kredensial login mereka

### Cara 3: Kirim Individual

1. Buka file Excel
2. Copy password masing-masing user
3. Kirim via WhatsApp/Email personal

---

## 🔐 User Login Pertama Kali

### Step 1: User Login

1. User membuka aplikasi LMS
2. Masuk ke halaman **Login**
3. Input:
   - **Email**: dari file Excel (contoh: `budi@example.com`)
   - **Password**: dari kolom Password di Excel (contoh: `BudiSiswa1234`)
4. Klik **Login**

### Step 2: User Ganti Password

1. Setelah login, user pergi ke **Profile** → **Edit Profile**
2. Scroll ke section **"Update Password"**
3. Input:
   - **Current Password**: Password dari Excel
   - **New Password**: Password baru yang diinginkan
   - **Confirm Password**: Ulang password baru
4. Klik **"Update Password"**
5. Password berhasil diganti!

---

## 🔄 Alur Lengkap (End-to-End)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. ADMIN: Persiapan Data                                     │
│    - Download template import (tanpa password)               │
│    - Isi data user (name, email, role, dll)                  │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. ADMIN: Import Users                                        │
│    - Upload file Excel                                        │
│    - Sistem auto-generate password                           │
│    - Password format: FirstName + Role + 4 digits            │
│    - Simpan ke database (hash + plain)                       │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. ADMIN: Export Users                                        │
│    - Klik Export di User Management                          │
│    - File Excel berisi kolom Password                        │
│    - Password adalah hasil auto-generate dari import         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. ADMIN: Distribusi Password                                │
│    - Kirim file Excel ke group WhatsApp/Telegram            │
│    - Atau cetak dan bagikan ke user                         │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. USER: Terima Kredensial                                   │
│    - Buka file Excel                                          │
│    - Cari nama/email sendiri                                  │
│    - Catat password dari kolom Password                      │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. USER: Login Pertama Kali                                  │
│    - Masukkan email dan password dari Excel                  │
│    - Berhasil login                                           │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. USER: Ganti Password                                       │
│    - Pergi ke Profile → Update Password                      │
│    - Ganti dengan password sendiri                           │
│    - Password lama dari Excel tidak bisa digunakan lagi      │
└─────────────────────────────────────────────────────────────┘
```

---

## 📝 Contoh Kasus Nyata

### Skenario: Sekolah Import 100 Siswa Baru

**1. Admin (Bu Ani) menyiapkan data:**
```excel
Name             | Email                  | Role
Budi Santoso     | budi.santoso@smp.sch   | siswa
Ani Rahmawati    | ani.rahmawati@smp.sch  | siswa
...
```

**2. Admin import file:**
- Sistem generate password otomatis:
  - Budi Santoso → `BudiSiswa3421`
  - Ani Rahmawati → `AniSiswa7856`

**3. Admin export file untuk distribusi:**
- File berisi kolom Password
- `budi.santoso@smp.sch` → Password: `BudiSiswa3421`

**4. Admin kirim ke group WhatsApp Kelas:**
"Silakan cek file Excel terlampir untuk username dan password login LMS"

**5. Budi membuka file:**
- Cari baris dengan emailnya
- Password: `BudiSiswa3421`

**6. Budi login:**
- Email: `budi.santoso@smp.sch`
- Password: `BudiSiswa3421`
- ✅ Berhasil login

**7. Budi ganti password:**
- Current Password: `BudiSiswa3421`
- New Password: `RahasiaBudi123!`
- ✅ Password berhasil diganti

**8. Login berikutnya:**
- Password baru: `RahasiaBudi123!`
- Password lama (`BudiSiswa3421`) tidak bisa digunakan

---

## 🛡️ Keamanan

### Yang Perlu Diperhatikan:

1. **File Excel berisi password sensitif**
   - Jangan kirim via email biasa
   - Gunakan WhatsApp/Telegram dengan group tertutup
   - Atau kirim individual ke masing-masing user

2. **Instruksikan user untuk segera ganti password**
   - Password dari Excel hanya untuk login pertama
   - User wajib ganti dengan password sendiri

3. **Hapus file Excel setelah distribusi**
   - Admin hapus file dari komputer
   - User hapus file setelah login dan ganti password

### Best Practice:

✅ Kirim file via channel aman (WhatsApp group tertutup)
✅ Instruksikan user untuk ganti password setelah login
✅ Hapus file setelah semua user berhasil login
✅ Set policy: password harus diganti dalam 7 hari
❌ Jangan kirim file via email publik
❌ Jangan simpan file di shared folder umum

---

## 💡 Tips

### Untuk Admin:

1. **Export setelah import**
   - Langsung export setelah import berhasil
   - Jangan tunggu lama karena bisa lupa

2. **Backup file export**
   - Simpan 1 copy di tempat aman
   - Untuk jaga-jaga ada user yang lupa password

3. **Verifikasi data sebelum kirim**
   - Cek apakah semua user ada di file export
   - Pastikan format password benar

### Untuk User:

1. **Ganti password segera**
   - Login pertama kali, langsung ganti password
   - Gunakan password yang kuat dan mudah diingat

2. **Jangan share password**
   - Password adalah rahasia pribadi
   - Jangan share ke teman

3. **Catat password baru**
   - Simpan di tempat aman (notebook, password manager)
   - Jangan tulis di tempat yang bisa dilihat orang

---

## 🔧 Technical Details

### Database Schema

```sql
users table:
- id
- name
- email
- password (hashed)
- plain_password (plain text) ← BARU!
- role
- ...
```

### Password Generation Logic

```php
// Saat Import
$firstName = explode(' ', $user->name)[0];
$role = $user->role;
$plainPassword = $firstName . ucfirst($role) . rand(1000, 9999);

// Simpan:
$user->password = Hash::make($plainPassword);  // untuk autentikasi
$user->plain_password = $plainPassword;         // untuk export
```

### Export Logic

```php
// Saat Export
$password = $user->plain_password;  // ambil dari database

// Jika user lama (belum ada plain_password), generate
if (!$password) {
    $firstName = explode(' ', $user->name)[0];
    $password = $firstName . ucfirst($user->role) . rand(1000, 9999);
}

return $password;  // tampilkan di Excel
```

---

## ❓ FAQ

**Q: Bagaimana kalau user lupa password setelah ganti?**
A: Admin bisa reset password via fitur "Update Password" di halaman Edit User, atau user bisa gunakan fitur "Forgot Password" (jika diimplementasikan).

**Q: Apakah password dari Excel masih bisa digunakan setelah user ganti password?**
A: Tidak, setelah user ganti password, password lama dari Excel tidak bisa digunakan lagi.

**Q: Bagaimana kalau ada user yang tidak ganti password?**
A: User masih bisa login dengan password dari Excel. Namun sebaiknya admin menginstruksikan untuk ganti password.

**Q: Apakah aman menyimpan plain_password di database?**
A: Untuk keamanan maksimal, sebaiknya plain_password dihapus setelah user berhasil login pertama kali. Atau enkripsi field ini.

**Q: Bagaimana kalau file Excel hilang?**
A: Admin bisa export ulang dari database, password masih tersimpan di field `plain_password`.

---

**Dokumentasi dibuat: 20 Oktober 2025**
