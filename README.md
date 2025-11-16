# 🎓 Laravel LMS - Learning Management System

Sistem manajemen pembelajaran berbasis web yang lengkap dengan fitur Computer-Based Test (CBT), manajemen kursus, forum diskusi, dan analytics yang powerful.

## 📖 Tentang Aplikasi

Laravel LMS adalah platform pembelajaran digital yang dirancang untuk memudahkan proses belajar mengajar secara online. Aplikasi ini mendukung multi-role (Admin, Guru, Siswa) dengan fitur lengkap untuk manajemen kursus, ujian berbasis komputer, dan pelacakan progress siswa.

### Teknologi yang Digunakan

- **Backend:** Laravel 12.x, PHP 8.2+
- **Frontend:** Tailwind CSS, Alpine.js, Vite
- **Database:** MySQL 8.0 / SQLite (testing)
- **PWA:** Service Worker, IndexedDB, Offline Support
- **AI Integration:** OpenAI ChatGPT API

---

## ✨ Fitur Utama

### 🎓 Manajemen Pengguna & Role-Based Access
- Login & registrasi untuk Admin, Guru, dan Siswa
- Profil pengguna dengan foto
- Manajemen pengguna lengkap dengan import/export

### 🏫 Manajemen Kursus & Materi
- Buat, edit, dan kelola kursus/mata pelajaran
- Upload materi pembelajaran (PDF, PPT, Video, YouTube)
- Sistem enrollment dengan kode kelas
- Komentar dan diskusi materi

### 🧩 Computer-Based Test (CBT)
- **4 Tipe Soal:**
  - Pilihan ganda (MCQ Single) - Auto grading
  - Pilihan ganda kompleks (MCQ Multiple) - Auto grading
  - Menjodohkan (Matching) - Auto grading
  - Esai - Manual grading dengan feedback
- **Fitur Anti-Cheat:**
  - Mode layar penuh (fullscreen)
  - Deteksi perpindahan tab
  - Penguncian waktu (server-side)
  - Violation logging
  - Auto block login + incident tracking saat kecurangan terdeteksi
  - Admin dashboard untuk review & reset akses login
- Pengacakan soal & opsi per attempt
- Timer real-time dengan auto-submit
- Multiple attempts (dapat dikonfigurasi)
- Scheduled exams dengan start/end time
- Guest access via token (tanpa registrasi)

### 📚 Question Bank System
- Repository soal yang dapat digunakan kembali
- Kategori hierarkis dengan difficulty levels
- Import/Export (Excel, PDF, JSON, CSV)
- Clone soal ke exam
- Random selection support
- Statistics & usage tracking

### 🧾 Nilai & Laporan
- Rekap nilai otomatis
- Export ke Excel/PDF dengan statistik
- Transcript siswa per kursus
- Dashboard laporan untuk Guru
- Analytics dengan Chart.js

### 💬 Forum Diskusi
- Kategori dengan icon & warna
- Thread creation & management
- Nested replies support
- Like system (AJAX)
- Pin/Lock threads (Admin/Guru)
- Mark solution/best answer
- Search functionality

### 📊 Analytics & Reporting
- **Admin Analytics:** User trends, course popularity, exam performance
- **Guru Analytics:** Student performance, completion rates, grade distribution
- **Siswa Analytics:** Performance trend, course comparison, pass/fail ratio
- 15+ interactive charts dengan Chart.js
- Real-time filtering

### 💬 Notifikasi System
- Database notifications dengan Laravel
- Notification bell dengan Alpine.js
- Notifikasi untuk: materi baru, exam scheduled, hasil ujian
- Auto-refresh setiap 30 detik

### 🎨 Custom Themes & Landing Pages
- Multi-tenant branding per sekolah
- Custom themes dengan 20+ color fields
- Dynamic landing pages per sekolah
- Logo & favicon upload
- 6 predefined color palettes

### 🎓 Certificate System
- Auto-generate sertifikat PDF
- 4 template profesional (Classic, Modern, Elegant, Minimalist)
- Custom branding (logo, signature, colors)
- QR code verification
- Certificate revocation system

### 🔌 Offline Mode (PWA)
- Download exam untuk akses offline
- Auto-save dengan IndexedDB
- Background sync saat online
- PWA installation (desktop & mobile)
- Perfect untuk lab komputer dengan internet tidak stabil

### 🤖 AI Assistant (ChatGPT)
- 24/7 AI-powered learning assistant
- Context-aware course help
- Natural conversation interface
- Floating chat widget
- Multiple AI model support (GPT-4, GPT-3.5 Turbo)
- Cost control & usage analytics

### ⚙️ Admin Panel
- Settings system dengan caching
- Database backup & restore
- User/Course/Exam management
- System configuration
- Cheating Incident Center (monitoring, notifikasi, reset login)
- Built-in documentation system

### 💻 Modern UI/UX
- Tailwind CSS dengan responsive design
- SweetAlert2 untuk alerts & confirmations
- Alpine.js untuk interaktif components
- Font Awesome icons
- Loading states & empty states
- Form validation (client & server-side)

---

## 🛠️ Pembaruan Terbaru
- **Blokir user benar-benar efektif:** siswa yang terdeteksi curang otomatis dikeluarkan dan tidak bisa memulai attempt baru sampai admin melakukan reset.
- **Jadwal ujian sesuai timezone sekolah:** seluruh `start_time` / `end_time` kini disimpan di UTC dan otomatis dikonversi kembali ke zona waktu aplikasi saat ditampilkan, sehingga tidak ada lagi selisih jam.
- **Notifikasi lebih informatif:** fallback teks ditambahkan di komponen bell, jadi semua notifikasi—termasuk yang lama—selalu menampilkan judul dan detail aksinya.

---

## 🚀 Instalasi di VPS Ubuntu

### Persyaratan Sistem

- **OS:** Ubuntu 20.04 atau 22.04 LTS
- **RAM:** Minimum 2GB (disarankan 4GB)
- **Storage:** Minimum 20GB
- **Domain:** Untuk SSL certificate (opsional)

### Langkah 1: Update Sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### Langkah 2: Install PHP 8.2+ & Extensions

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml \
    php8.2-bcmath php8.2-intl php8.2-readline php8.2-tokenizer
```

### Langkah 3: Install Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Langkah 4: Install MySQL 8.0

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Buat database dan user:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE lms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON lms_db.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 5: Install Nginx

```bash
sudo apt install -y nginx
```

### Langkah 6: Clone & Setup Aplikasi

```bash
cd /var/www
sudo git clone https://github.com/your-repo/lms.git
sudo chown -R www-data:www-data lms
cd lms
```

Install dependencies:

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Langkah 7: Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Laravel LMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_db
DB_USERNAME=lms_user
DB_PASSWORD=your_strong_password
```

### Langkah 8: Setup Database

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### Langkah 9: Konfigurasi Nginx

Buat file konfigurasi:

```bash
sudo nano /etc/nginx/sites-available/lms
```

Isi dengan:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/lms/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan:

```bash
sudo ln -s /etc/nginx/sites-available/lms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Langkah 10: Install SSL dengan Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Langkah 11: Setup Queue Worker

Buat systemd service:

```bash
sudo nano /etc/systemd/system/lms-queue.service
```

Isi dengan:

```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/lms/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

Aktifkan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable lms-queue
sudo systemctl start lms-queue
```

### Langkah 12: Setup Scheduler (Cron)

```bash
sudo crontab -e -u www-data
```

Tambahkan:

```
* * * * * cd /var/www/lms && php artisan schedule:run >> /dev/null 2>&1
```

### Langkah 13: Optimasi & Security

Set permissions:

```bash
sudo chown -R www-data:www-data /var/www/lms
sudo chmod -R 755 /var/www/lms
sudo chmod -R 775 /var/www/lms/storage
sudo chmod -R 775 /var/www/lms/bootstrap/cache
```

Optimasi:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 14: Firewall Setup

```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

### ✅ Selesai!

Akses aplikasi di: `https://yourdomain.com`

**Default Login:**
- **Admin:** admin@example.com / password
- **Guru:** guru@example.com / password
- **Siswa:** siswa@example.com / password

---

## 📝 Catatan Penting

- Pastikan `storage/` dan `bootstrap/cache/` writable
- Setup backup database secara berkala
- Monitor queue worker dengan `sudo systemctl status lms-queue`
- Update aplikasi: `git pull && composer install && npm run build && php artisan migrate`

---

## 📚 Dokumentasi Lengkap

Dokumentasi lengkap tersedia di folder `docs/` atau akses melalui:
- Admin Panel → Documentation (jika sudah login)

---

## 📄 License

Proprietary - All rights reserved
