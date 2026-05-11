# 🎓 Laravel LMS - Learning Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

**Platform Pembelajaran Digital Lengkap dengan CBT, AI Assistant, PWA, dan Multi-Tenant Support**

[Demo](#) • [Dokumentasi](#-dokumentasi-lengkap) • [Lapor Bug](https://github.com/your-repo/lms/issues)

</div>

---

## 📑 Daftar Isi

- [📖 Tentang Aplikasi](#-tentang-aplikasi)
- [✨ Fitur Lengkap](#-fitur-lengkap)
- [🛠️ Teknologi Stack](#️-teknologi-stack)
- [📋 Persyaratan Sistem](#-persyaratan-sistem)
- [🚀 Setup Development](#-setup-development)
- [🌐 Setup Production di VPS dengan aaPanel](#-setup-production-di-vps-dengan-aapanel)
- [⚙️ Konfigurasi Environment](#️-konfigurasi-environment)
- [🔧 Maintenance & Monitoring](#-maintenance--monitoring)
- [🧾 Backup & Restore](#-backup--restore)
- [🧰 Troubleshooting](#-troubleshooting)
- [📚 Dokumentasi API](#-dokumentasi-api)
- [🤝 Kontribusi](#-kontribusi)
- [📄 License](#-license)

---

## 📖 Tentang Aplikasi

**Laravel LMS** adalah platform Learning Management System (LMS) yang komprehensif dan modern, dirancang khusus untuk institusi pendidikan di Indonesia. Sistem ini menyediakan solusi lengkap untuk pembelajaran digital dengan dukungan Computer-Based Test (CBT), manajemen kursus, forum diskusi, AI assistant, dan analytics yang powerful.

### 🎯 Tujuan Utama

- Memfasilitasi pembelajaran digital yang efektif dan efisien
- Menyediakan sistem ujian online (CBT) dengan anti-cheat yang robust
- Memberikan analytics dan insights untuk meningkatkan kualitas pembelajaran
- Mendukung pembelajaran offline melalui Progressive Web App (PWA)
- Menyediakan AI assistant 24/7 untuk membantu siswa belajar

### 👥 Target Pengguna

- **Institusi Pendidikan**: Sekolah, universitas, dan lembaga pelatihan
- **Administrator**: Pengelola sistem dan data
- **Guru/Dosen**: Pembuat konten dan pengelola ujian
- **Siswa/Mahasiswa**: Peserta pembelajaran dan ujian

### 🔑 Role System & Equivalensi

Sistem mendukung 5 role dengan dua pasang role yang ekuivalen secara permission:

| Role | Terminologi | Ekuivalen Dengan | URL Prefix | Dashboard |
|------|-------------|-----------------|------------|-----------|
| `admin` | Administrator | — | `/admin/*` | `/admin/dashboard` |
| `guru` | Guru (SMA/SMK) | — | `/guru/*` | `/guru/dashboard` |
| `dosen` | Dosen (Perguruan Tinggi) | **sama dengan `guru`** | `/dosen/*` | `/dosen/dashboard` |
| `siswa` | Siswa (SMA/SMK) | — | `/siswa/*` | `/siswa/dashboard` |
| `mahasiswa` | Mahasiswa (Perguruan Tinggi) | **sama dengan `siswa`** | `/mahasiswa/*` | `/mahasiswa/dashboard` |

**Penting untuk Administrator:**
- Role `dosen` memiliki **permission identik** dengan `guru` — dapat membuat kursus, ujian, materi, dan mengelola enrollment
- Role `mahasiswa` memiliki **permission identik** dengan `siswa` — dapat mengakses kursus, mengikuti ujian, melihat sertifikat
- Perbedaan hanya pada URL prefix dan terminologi, **bukan** pada hak akses
- Institusi pendidikan tinggi sebaiknya menggunakan `dosen`/`mahasiswa`; sekolah menggunakan `guru`/`siswa`

### 🌟 Keunggulan

- ✅ **Multi-Tenant**: Mendukung multiple sekolah dengan branding terpisah
- ✅ **Anti-Cheat System**: Deteksi kecurangan dengan auto-blocking
- ✅ **Offline Support**: PWA dengan IndexedDB untuk akses tanpa internet
- ✅ **AI-Powered**: ChatGPT integration untuk learning assistant
- ✅ **Modern UI/UX**: Responsive design dengan Tailwind CSS
- ✅ **Scalable**: Arsitektur yang dapat di-scale untuk ribuan pengguna
- ✅ **Open Source**: Kode terbuka dan dapat dikustomisasi

---

## ✨ Fitur Lengkap

### 🎓 Manajemen Pengguna & Role-Based Access

- **Multi-Role System**: Admin, Guru, Dosen, Siswa, dan Mahasiswa dengan permission terpisah
- **Authentication**: Login, registrasi, email verification, password reset
- **Profile Management**: Update profil, foto profil, change password
- **User Management**: CRUD users dengan bulk import/export Excel
- **Activity Tracking**: Log aktivitas pengguna untuk audit trail
- **Session Management**: Multiple device login dengan session tracking

### 🏫 Manajemen Kursus & Materi

- **Course Management**: Create, edit, delete, publish/unpublish courses
- **Course Categories**: Hierarchical categories dengan icon dan warna
- **Material Types**: 
  - 📄 PDF documents
  - 📊 PowerPoint presentations
  - 🎥 Video files (MP4, WebM)
  - 🔗 YouTube embeds
  - 📝 Rich text content (Markdown support)
- **Enrollment System**: 
  - Enrollment via unique course code
  - Manual enrollment by instructor
  - Enrollment approval workflow
- **Material Comments**: Discussion threads pada setiap materi
- **Progress Tracking**: Track student progress per course
- **Course Analytics**: View statistics, completion rates, engagement

### 📝 Computer-Based Test (CBT)

#### Tipe Soal (4 Jenis)

1. **Multiple Choice (Single Answer)** - Auto grading
   - Pilihan ganda dengan satu jawaban benar
   - Randomize options per attempt
   - Image support untuk soal dan opsi

2. **Multiple Choice (Multiple Answers)** - Auto grading
   - Pilihan ganda dengan multiple jawaban benar
   - Partial scoring support
   - Minimum correct answers configuration

3. **Matching Questions** - Auto grading
   - Menjodohkan pasangan items
   - Drag & drop interface
   - Randomize pairs per attempt

4. **Essay Questions** - Manual grading
   - Long-form text answers
   - Rich text editor untuk siswa
   - Rubric-based grading untuk guru
   - Feedback dan komentar

#### Fitur Ujian

- **Exam Scheduling**: Set start time, end time, duration
- **Multiple Attempts**: Configure max attempts per student
- **Question Randomization**: Random order per attempt
- **Time Limit**: Server-side timer dengan auto-submit
- **Passing Grade**: Set minimum score untuk lulus
- **Grade Visibility**: Show/hide grades immediately
- **Guest Access**: Token-based access tanpa registrasi
- **Exam Templates**: Save exam as template untuk reuse

#### Anti-Cheat System 🛡️

- **Fullscreen Enforcement**: Force fullscreen mode
- **Tab Switch Detection**: Detect dan log tab switching
- **Copy-Paste Prevention**: Disable copy-paste dalam exam
- **Right-Click Disable**: Prevent inspect element
- **Violation Logging**: Track semua suspicious activities
- **Auto-Blocking**: Automatic login block setelah threshold violations
- **Incident Dashboard**: Admin panel untuk review cheating incidents
- **Manual Override**: Admin dapat reset block status
- **Proctoring Logs**: Detailed logs untuk investigation

### 📚 Question Bank System

- **Centralized Repository**: Bank soal yang dapat digunakan ulang
- **Categories & Tags**: Organize questions dengan categories dan tags
- **Difficulty Levels**: Easy, Medium, Hard, Expert
- **Question Statistics**: Usage count, success rate, average score
- **Import/Export**: 
  - Excel (.xlsx, .xls)
  - PDF (with formatting)
  - JSON (structured data)
  - CSV (bulk import)
- **Template Export**: Download template untuk bulk import
- **Clone to Exam**: Quick add questions dari bank ke exam
- **Random Selection**: Auto-select questions berdasarkan criteria
- **Sharing**: Guru dapat share bank soal ke guru lain (shared + verified)
- **Version Control**: Track changes pada questions

### 🎯 Grading & Reporting

- **Auto-Grading**: Instant grading untuk MCQ dan Matching
- **Manual Grading**: Interface untuk grading essay questions
- **Grade Book**: Comprehensive grade management
- **Transcript**: Student transcript per course
- **Export Options**:
  - Excel dengan statistics
  - PDF dengan formatting
  - CSV untuk data processing
- **Grade Analytics**: Distribution, trends, comparisons
- **Feedback System**: Detailed feedback untuk students

### 💬 Forum Diskusi

- **Forum Categories**: Multiple categories dengan icon dan warna
- **Thread Management**: Create, edit, delete, pin, lock threads
- **Nested Replies**: Multi-level reply system
- **Like System**: Like threads dan replies (AJAX-powered)
- **Best Answer**: Mark solution/best answer
- **Search & Filter**: Full-text search dengan filters
- **Notifications**: Real-time notifications untuk replies
- **Moderation**: Admin dan Guru dapat moderate forum
- **Rich Text Editor**: Markdown support dengan preview

### 📊 Analytics & Reporting

#### Admin Analytics
- User growth trends (daily, weekly, monthly)
- Course popularity dan enrollment statistics
- Exam performance overview
- System usage metrics
- Revenue tracking (jika ada payment)
- 15+ interactive charts dengan Chart.js

#### Guru Analytics
- Student performance per course
- Exam completion rates
- Grade distribution analysis
- Question difficulty analysis
- Time-on-task metrics
- Individual student progress

#### Siswa Analytics
- Personal performance trends
- Course comparison
- Pass/fail ratio
- Time spent learning
- Strengths & weaknesses analysis
- Achievement badges

### 🔔 Notification System

- **Real-time Notifications**: Database-driven notifications
- **Notification Bell**: Alpine.js powered notification center
- **Auto-refresh**: Update setiap 30 detik
- **Notification Types**:
  - 📚 New material published
  - 📝 Exam scheduled
  - ✅ Exam graded
  - 💬 Forum reply
  - 🎓 Certificate issued
  - ⚠️ Cheating incident detected
- **Mark as Read**: Individual dan bulk mark as read
- **Notification History**: View all past notifications

### 🎨 Multi-Tenant & Branding

- **Multiple Schools**: Support multiple institutions
- **Custom Themes**: 20+ customizable color fields per school
- **Logo & Favicon**: Upload custom branding assets
- **Landing Pages**: Dynamic landing page per school
- **Landing Page Toggle**: Admin dapat activate/deactivate landing page
- **Single Active School**: Only one school landing page active at a time
- **Predefined Palettes**: 6 professional color schemes
- **SEO Optimization**: Meta tags, Open Graph, structured data
- **Custom Domain**: Support untuk custom domain per school

### 🎓 Certificate System

- **Auto-Generation**: Generate certificates upon course completion
- **4 Professional Templates**:
  - Classic: Traditional design dengan decorative elements
  - Modern: Gradient design dengan colorful elements
  - Elegant: Gold-themed formal certificate
  - Minimalist: Clean design dengan bold typography
- **Custom Branding**: Logo, signature, institution name
- **Color Customization**: Primary, secondary, accent colors
- **QR Code Verification**: Public verification via QR code
- **Certificate Number**: Unique certificate numbers
- **PDF Export**: High-quality PDF generation
- **Email Delivery**: Auto-send via email (optional)
- **Revocation System**: Revoke certificates jika diperlukan
- **Certificate Gallery**: Student certificate collection

### 🔌 Progressive Web App (PWA)

- **Offline Exam Access**: Download exam untuk offline use
- **IndexedDB Storage**: Local storage untuk exam data
- **Background Sync**: Auto-sync saat online kembali
- **Service Worker**: Cache assets untuk fast loading
- **Install Prompt**: Install sebagai app di desktop/mobile
- **Offline Indicator**: Visual indicator untuk offline status
- **Auto-save**: Save answers locally setiap 30 detik
- **Conflict Resolution**: Handle conflicts saat sync

### 🤖 AI Assistant (ChatGPT Integration)

- **24/7 Learning Assistant**: Always available untuk help
- **Context-Aware**: Understand course context
- **Natural Conversation**: Chat interface dengan history
- **Multiple AI Models**: Support GPT-4, GPT-3.5 Turbo, dll
- **Conversation History**: Save dan resume conversations
- **Cost Control**: Token usage tracking dan limits
- **Usage Analytics**: Monitor AI usage dan costs
- **Admin Configuration**: Configure API key, model, parameters
- **Floating Widget**: Accessible dari semua pages
- **Smart Responses**: Tidak memberikan jawaban langsung untuk exam

### ⚙️ Admin Panel

- **Dashboard**: Overview statistics dan quick actions
- **User Management**: CRUD users dengan bulk operations
- **Course Management**: Approve, edit, delete courses
- **Exam Management**: Monitor exams dan attempts
- **Grading Capabilities**: Grade assignments, submissions, and essay questions (same as Guru/Dosen)
- **Reports & Export**: Export grades to Excel/PDF, generate student transcripts
- **Settings System**: Centralized settings dengan caching
- **Database Backup**: One-click backup dan restore
- **System Logs**: View application logs
- **Cheating Incident Center**: Monitor dan manage violations
- **Email Templates**: Customize email notifications
- **System Health**: Monitor queue, cache, storage
- **Activity Logs**: Audit trail untuk admin actions

### 💻 Modern UI/UX

- **Responsive Design**: Mobile-first approach
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight reactive components
- **SweetAlert2**: Beautiful alerts dan confirmations
- **Font Awesome**: 1000+ icons
- **Loading States**: Skeleton loaders dan spinners
- **Empty States**: Helpful empty state illustrations
- **Form Validation**: Real-time client & server validation
- **Toast Notifications**: Non-intrusive notifications
- **Dark Mode Ready**: Prepared untuk dark mode implementation

---

## 🛠️ Teknologi Stack

### Backend

| Teknologi | Versi | Deskripsi |
|-----------|-------|-----------|
| **Laravel** | 11.x | PHP framework untuk web applications |
| **PHP** | 8.2+ | Server-side programming language |
| **MySQL** | 8.0+ | Relational database management system |
| **Redis** | 7.x | In-memory data structure store (optional) |

### Frontend

| Teknologi | Versi | Deskripsi |
|-----------|-------|-----------|
| **Tailwind CSS** | 3.x | Utility-first CSS framework |
| **Alpine.js** | 3.x | Lightweight JavaScript framework |
| **Vite** | 5.x | Frontend build tool |
| **Chart.js** | 4.x | JavaScript charting library |
| **SweetAlert2** | 11.x | Beautiful popup boxes |
| **Font Awesome** | 6.5.1 | Icon library |

### Key PHP Packages

```json
{
  "laravel/breeze": "^2.3",           // Authentication scaffolding
  "spatie/laravel-medialibrary": "^11.0",  // Media management
  "maatwebsite/excel": "^3.1",        // Excel import/export
  "barryvdh/laravel-dompdf": "^3.1",  // PDF generation
  "erusev/parsedown": "^1.7",         // Markdown parsing
  "jenssegers/agent": "^2.6"          // User agent detection
}
```

### Development Tools

| Tool | Deskripsi |
|------|-----------|
| **Laravel Pint** | Code style fixer (PSR-12) |
| **Larastan** | Static analysis tool |
| **Pest PHP** | Testing framework |
| **Laravel Sail** | Docker development environment |
| **Laravel Pail** | Real-time log viewer |

---

## 📋 Persyaratan Sistem

### Development Environment

#### Minimum Requirements

- **OS**: Windows 10/11, macOS 12+, Ubuntu 20.04+
- **PHP**: 8.2 atau lebih tinggi
- **Composer**: 2.x
- **Node.js**: 18.x atau lebih tinggi (20 LTS recommended)
- **NPM**: 9.x atau lebih tinggi
- **Database**: MySQL 8.0+ atau SQLite 3.x
- **RAM**: 4GB minimum (8GB recommended)
- **Storage**: 5GB free space

#### PHP Extensions Required

```bash
php-cli
php-common
php-mysql (atau php-sqlite3)
php-zip
php-gd
php-mbstring
php-curl
php-xml
php-bcmath
php-intl
php-readline
php-tokenizer
```

### Production Environment

#### Minimum Requirements

- **OS**: Ubuntu 20.04/22.04/24.04 LTS (recommended)
- **Web Server**: Nginx 1.18+ atau Apache 2.4+
- **PHP**: 8.2+ dengan PHP-FPM
- **Database**: MySQL 8.0+ atau MariaDB 10.6+
- **RAM**: 2GB minimum (4GB+ recommended untuk >100 users)
- **Storage**: 20GB minimum (SSD recommended)
- **SSL Certificate**: Required untuk production
- **Domain**: Recommended untuk proper SSL setup

#### Recommended Production Specs

- **CPU**: 2+ cores
- **RAM**: 8GB+
- **Storage**: 50GB+ SSD
- **Bandwidth**: 100Mbps+
- **Backup**: Automated daily backups

---

## 🚀 Setup Development

### Metode 1: Setup Manual (Recommended)

#### 1. Install Prerequisites

**Windows:**
```powershell
# Install PHP 8.2+ (via XAMPP, Laragon, atau manual)
# Download dari: https://windows.php.net/download/

# Install Composer
# Download dari: https://getcomposer.org/download/

# Install Node.js 20 LTS
# Download dari: https://nodejs.org/

# Install Git
# Download dari: https://git-scm.com/download/win
```

**macOS:**
```bash
# Install Homebrew jika belum ada
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP 8.2
brew install php@8.2

# Install Composer
brew install composer

# Install Node.js
brew install node@20

# Install MySQL
brew install mysql
```

**Linux (Ubuntu/Debian):**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 dan extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
    php8.2-xml php8.2-bcmath php8.2-intl php8.2-readline

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install MySQL
sudo apt install -y mysql-server
```

#### 2. Clone Repository

```bash
# Clone project
git clone https://github.com/your-username/lms.git
cd lms

# Atau jika sudah download ZIP
unzip lms.zip
cd lms
```

#### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 4. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Configure Database

**Option A: MySQL (Recommended untuk production-like development)**

```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE lms_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON lms_dev.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_dev
DB_USERNAME=lms_user
DB_PASSWORD=your_password
```

**Option B: SQLite (Quickest untuk development)**

```bash
# Buat database file
touch database/database.sqlite
```

Edit `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
# Atau gunakan relative path
DB_DATABASE=database/database.sqlite
```

#### 6. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed database dengan data contoh
php artisan db:seed

# Atau jalankan sekaligus
php artisan migrate:fresh --seed
```

#### 7. Create Storage Symlink

```bash
# Windows: Run as Administrator
php artisan storage:link

# macOS/Linux
php artisan storage:link
```

#### 8. Start Development Servers

**Option A: Menggunakan 3 Terminal Terpisah**

Terminal 1 - Laravel Server:
```bash
php artisan serve
# Server akan berjalan di http://localhost:8000
```

Terminal 2 - Queue Worker:
```bash
php artisan queue:work
# Atau dengan auto-reload saat code berubah:
php artisan queue:listen
```

Terminal 3 - Vite Dev Server:
```bash
npm run dev
# Vite akan berjalan di http://localhost:5173
```

**Option B: Menggunakan Composer Script (Recommended)**

```bash
# Install concurrently globally (sekali saja)
npm install -g concurrently

# Jalankan semua services sekaligus
composer dev
```

Ini akan menjalankan:
- Laravel server di `http://localhost:8000`
- Queue worker dengan auto-reload
- Vite dev server dengan HMR

#### 9. Access Application

Buka browser dan akses: `http://localhost:8000`

**Default Login Credentials:**

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Guru | guru@example.com | password |
| Siswa | siswa@example.com | password |

### Metode 2: Setup dengan Laravel Sail (Docker)

Laravel Sail menyediakan Docker environment yang sudah dikonfigurasi.

#### 1. Install Docker Desktop

- **Windows/macOS**: Download dari [docker.com](https://www.docker.com/products/docker-desktop)
- **Linux**: Install Docker Engine dan Docker Compose

#### 2. Setup Project

```bash
# Clone repository
git clone https://github.com/your-username/lms.git
cd lms

# Install dependencies via Docker
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

# Copy environment
cp .env.example .env

# Start Sail
./vendor/bin/sail up -d

# Generate key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate --seed

# Install npm dependencies
./vendor/bin/sail npm install

# Start Vite
./vendor/bin/sail npm run dev
```

#### 3. Access Application

Aplikasi akan tersedia di: `http://localhost`

**Sail Commands:**
```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run composer
./vendor/bin/sail composer [command]

# Run npm
./vendor/bin/sail npm [command]

# Access MySQL
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs
```

### Development Tips

#### 1. Code Style & Quality

```bash
# Format code dengan Laravel Pint
./vendor/bin/pint

# Run static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
# Atau dengan Pest
./vendor/bin/pest
```

#### 2. Clear Caches

```bash
# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

#### 3. Generate IDE Helper

```bash
# Generate helper files untuk IDE autocomplete
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

#### 4. Watch Logs

```bash
# Real-time log viewer dengan Laravel Pail
php artisan pail

# Atau tail log file
tail -f storage/logs/laravel.log
```

#### 5. Database Management

```bash
# Create new migration
php artisan make:migration create_table_name

# Create model dengan migration dan factory
php artisan make:model ModelName -mf

# Rollback last migration
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh --seed
```

---

## 🌐 Setup Production di VPS dengan aaPanel

aaPanel adalah control panel gratis yang memudahkan management server. Panduan ini akan memandu Anda setup Laravel LMS di VPS dengan aaPanel step-by-step.

### Persiapan VPS

#### Spesifikasi VPS yang Direkomendasikan

| Provider | Spesifikasi | Harga (estimasi) |
|----------|-------------|------------------|
| **DigitalOcean** | 2 CPU, 4GB RAM, 80GB SSD | $24/bulan |
| **Vultr** | 2 CPU, 4GB RAM, 80GB SSD | $24/bulan |
| **Linode** | 2 CPU, 4GB RAM, 80GB SSD | $24/bulan |
| **AWS Lightsail** | 2 CPU, 4GB RAM, 80GB SSD | $24/bulan |
| **Contabo** | 4 CPU, 8GB RAM, 200GB SSD | $8/bulan |

#### OS yang Didukung

- Ubuntu 20.04 LTS ✅ (Recommended)
- Ubuntu 22.04 LTS ✅ (Recommended)
- Ubuntu 24.04 LTS ✅
- Debian 10/11/12 ✅
- CentOS 7/8 ⚠️ (Not recommended, EOL)

### Step 1: Persiapan Server

#### 1.1 Login ke VPS via SSH

```bash
# Ganti dengan IP VPS Anda
ssh root@your_vps_ip

# Atau jika menggunakan SSH key
ssh -i /path/to/private_key root@your_vps_ip
```

#### 1.2 Update System

```bash
# Update package list
apt update && apt upgrade -y

# Install basic tools
apt install -y curl wget git unzip software-properties-common
```

#### 1.3 Setup Firewall (UFW)

```bash
# Install UFW jika belum ada
apt install -y ufw

# Allow SSH (PENTING! Jangan sampai terkunci)
ufw allow 22/tcp

# Allow HTTP dan HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Allow aaPanel port
ufw allow 8888/tcp

# Enable firewall
ufw enable

# Check status
ufw status
```

### Step 2: Install aaPanel

#### 2.1 Install aaPanel

```bash
# Install aaPanel (Ubuntu/Debian)
wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh && sudo bash install.sh aapanel
```

Proses instalasi akan memakan waktu 5-10 menit. Setelah selesai, Anda akan melihat output seperti ini:

```
==================================================================
Congratulations! Installed successfully!
==================================================================
aaPanel Internet Address: http://your_vps_ip:8888/xxxxxxxx
aaPanel Internal Address: http://your_internal_ip:8888/xxxxxxxx
username: xxxxxxxx
password: xxxxxxxx
==================================================================
```

**⚠️ PENTING: Simpan informasi login ini!**

#### 2.2 Login ke aaPanel

1. Buka browser dan akses: `http://your_vps_ip:8888/xxxxxxxx`
2. Login dengan username dan password yang diberikan
3. Anda akan diminta install LNMP/LAMP stack

### Step 3: Install LNMP Stack di aaPanel

#### 3.1 Pilih Software Stack

Setelah login pertama kali, pilih **LNMP** (Nginx):

- **Nginx**: 1.22+ (Recommended)
- **MySQL**: 8.0+ (Pilih 8.0.35 atau lebih baru)
- **PHP**: 8.2 atau 8.3 (Pilih 8.3 untuk performa terbaik)
- **phpMyAdmin**: Latest (Optional, untuk management database)

Klik **One-click Install** dan tunggu proses instalasi (15-30 menit).

#### 3.2 Install PHP Extensions

Setelah PHP terinstall, install extensions yang diperlukan:

1. Klik **App Store** di sidebar
2. Cari **PHP 8.3** (atau versi yang Anda install)
3. Klik **Settings**
4. Pilih tab **Install Extensions**
5. Install extensions berikut:

**Required Extensions:**
- ✅ opcache (untuk performa)
- ✅ redis (untuk caching)
- ✅ imagemagick (untuk image processing)
- ✅ exif (untuk image metadata)
- ✅ intl (untuk internationalization)
- ✅ bcmath (untuk calculations)
- ✅ zip (untuk compression)
- ✅ gd (untuk image manipulation)
- ✅ mbstring (sudah terinstall by default)
- ✅ curl (sudah terinstall by default)
- ✅ xml (sudah terinstall by default)
- ✅ mysql (sudah terinstall by default)

#### 3.3 Configure PHP Settings

1. Masih di PHP Settings, pilih tab **Configuration File**
2. Edit `php.ini` dan ubah:

```ini
memory_limit = 512M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
```

3. Klik **Save** dan **Restart PHP**

### Step 4: Setup Database

#### 4.1 Create Database via aaPanel

1. Klik **Database** di sidebar
2. Klik **Add Database**
3. Isi form:
   - **Database Name**: `lms_production`
   - **Username**: `lms_user`
   - **Password**: Generate strong password (simpan!)
   - **Access Permission**: `localhost` (recommended)
4. Klik **Submit**

#### 4.2 Optimize MySQL Configuration

1. Klik **App Store** → **MySQL 8.0** → **Settings**
2. Pilih tab **Performance Tuning**
3. Pilih preset sesuai RAM:
   - 2GB RAM: Select **Small**
   - 4GB RAM: Select **Medium**
   - 8GB+ RAM: Select **Large**
4. Klik **Save** dan **Restart MySQL**

### Step 5: Setup Website di aaPanel

#### 5.1 Add Website

1. Klik **Website** di sidebar
2. Klik **Add Site**
3. Isi form:
   - **Domain**: `yourdomain.com` (dan `www.yourdomain.com`)
   - **Root Directory**: `/www/wwwroot/lms`
   - **PHP Version**: PHP 8.3
   - **Database**: Select database yang sudah dibuat
   - **Create FTP**: No (tidak perlu)
4. Klik **Submit**

#### 5.2 Configure Website Settings

1. Klik nama domain di list website
2. Pilih tab **Site Directory**
3. Set **Running Directory** ke: `/public`
4. Enable **Prevent Cross-site Access**
5. Klik **Save**

#### 5.3 Configure Nginx

1. Masih di website settings, pilih tab **Config File**
2. Replace isi file dengan konfigurasi Laravel:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    root /www/wwwroot/lms/public;
    index index.php index.html;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Logging
    access_log /www/wwwroot/lms/storage/logs/nginx-access.log;
    error_log /www/wwwroot/lms/storage/logs/nginx-error.log;
    
    # Charset
    charset utf-8;
    
    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/tmp/php-cgi-83.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # PHP settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 300;
    }
    
    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Deny access to specific files
    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt { 
        access_log off; 
        log_not_found off; 
    }
}
```

3. Klik **Save**

### Step 6: Deploy Laravel Application

#### 6.1 Upload Code ke Server

**Option A: Via Git (Recommended)**

```bash
# SSH ke server
ssh root@your_vps_ip

# Navigate ke web root
cd /www/wwwroot

# Remove default lms folder jika ada
rm -rf lms

# Clone repository
git clone https://github.com/your-username/lms.git lms

# Set ownership
chown -R www:www lms
cd lms
```

**Option B: Via FTP/SFTP**

1. Gunakan FileZilla atau WinSCP
2. Connect ke server dengan SFTP
3. Upload semua file ke `/www/wwwroot/lms`
4. Set ownership via SSH:
```bash
chown -R www:www /www/wwwroot/lms
```

#### 6.2 Install Composer Dependencies

```bash
cd /www/wwwroot/lms

# Install Composer jika belum ada
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install dependencies (production)
composer install --optimize-autoloader --no-dev
```

#### 6.3 Install Node.js & Build Assets

```bash
# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Verify installation
node -v
npm -v

# Install dependencies dan build
cd /www/wwwroot/lms
npm ci
npm run build
```

#### 6.4 Setup Environment

```bash
cd /www/wwwroot/lms

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env file
nano .env
```

Edit `.env` dengan konfigurasi production:

```env
APP_NAME="Laravel LMS"
APP_ENV=production
APP_KEY=base64:xxxxx  # Sudah di-generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_production
DB_USERNAME=lms_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# AI Assistant (Optional)
OPENAI_API_KEY=your_openai_key
AI_MODEL=gpt-4o-mini

# Certificate
CERTIFICATE_INSTITUTION_NAME="Your Institution"
CERTIFICATE_DIRECTOR_NAME="Director Name"

# PWA
PWA_ENABLED=true
```

Save dengan `Ctrl+O`, `Enter`, `Ctrl+X`

#### 6.5 Run Migrations & Seeders

```bash
cd /www/wwwroot/lms

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Create storage symlink
php artisan storage:link
```

#### 6.6 Set Permissions

```bash
cd /www/wwwroot/lms

# Set ownership
chown -R www:www /www/wwwroot/lms

# Set directory permissions
find /www/wwwroot/lms -type d -exec chmod 755 {} \;

# Set file permissions
find /www/wwwroot/lms -type f -exec chmod 644 {} \;

# Set writable directories
chmod -R 775 /www/wwwroot/lms/storage
chmod -R 775 /www/wwwroot/lms/bootstrap/cache

# Set executable for artisan
chmod +x /www/wwwroot/lms/artisan
```

#### 6.7 Optimize Laravel

```bash
cd /www/wwwroot/lms

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 7: Setup SSL Certificate (Let's Encrypt)

#### 7.1 Install SSL via aaPanel

1. Kembali ke aaPanel dashboard
2. Klik **Website** → Pilih domain Anda
3. Pilih tab **SSL**
4. Pilih **Let's Encrypt**
5. Centang domain dan www subdomain
6. Klik **Apply**
7. Tunggu proses verifikasi (1-2 menit)
8. Enable **Force HTTPS**

#### 7.2 Verify SSL

Akses website Anda di browser: `https://yourdomain.com`

Pastikan:
- ✅ Padlock icon muncul di address bar
- ✅ Certificate valid
- ✅ HTTP redirect ke HTTPS

### Step 8: Setup Queue Worker

Queue worker diperlukan untuk menjalankan background jobs seperti sending emails, processing imports, dll.

#### 8.1 Create Supervisor Configuration

```bash
# Install Supervisor
apt install -y supervisor

# Create configuration file
nano /etc/supervisor/conf.d/lms-worker.conf
```

Isi dengan:

```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /www/wwwroot/lms/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/lms/storage/logs/worker.log
stopwaitsecs=3600
```

Save dengan `Ctrl+O`, `Enter`, `Ctrl+X`

#### 8.2 Start Supervisor

```bash
# Reload Supervisor configuration
supervisorctl reread
supervisorctl update

# Start worker
supervisorctl start lms-worker:*

# Check status
supervisorctl status

# Output should show:
# lms-worker:lms-worker_00    RUNNING   pid 12345, uptime 0:00:05
# lms-worker:lms-worker_01    RUNNING   pid 12346, uptime 0:00:05
```

#### 8.3 Supervisor Management Commands

```bash
# Start all workers
supervisorctl start lms-worker:*

# Stop all workers
supervisorctl stop lms-worker:*

# Restart all workers
supervisorctl restart lms-worker:*

# View logs
tail -f /www/wwwroot/lms/storage/logs/worker.log

# Reload configuration after changes
supervisorctl reread
supervisorctl update
```

### Step 9: Setup Scheduler (Cron Jobs)

Laravel scheduler diperlukan untuk menjalankan scheduled tasks seperti analytics reports, cleanup, dll.

#### 9.1 Setup Cron via aaPanel

1. Klik **Cron** di sidebar aaPanel
2. Klik **Add Cron**
3. Isi form:
   - **Task Type**: Shell Script
   - **Task Name**: Laravel Scheduler
   - **Execution Cycle**: Every minute (N Minutes → 1)
   - **Script Content**:
   ```bash
   cd /www/wwwroot/lms && php artisan schedule:run >> /dev/null 2>&1
   ```
4. Klik **Submit**

#### 9.2 Verify Cron is Running

```bash
# Check cron logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log

# Atau check via artisan
cd /www/wwwroot/lms
php artisan schedule:list
```

Output akan menampilkan scheduled tasks:

```
0 8 * * * ............... App\Console\Commands\SendDailyAnalyticsReport
0 8 * * 1 ............... App\Console\Commands\SendWeeklyAnalyticsReport
0 8 1 * * ............... App\Console\Commands\SendMonthlyAnalyticsReport
```

### Step 10: Setup Redis (Optional - Recommended)

Redis akan meningkatkan performa caching dan queue processing.

#### 10.1 Install Redis via aaPanel

1. Klik **App Store**
2. Search **Redis**
3. Klik **Install**
4. Tunggu instalasi selesai

#### 10.2 Install PHP Redis Extension

1. Klik **App Store** → **PHP 8.3** → **Settings**
2. Tab **Install Extensions**
3. Install **redis** extension
4. Restart PHP

#### 10.3 Configure Laravel to Use Redis

Edit `.env`:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Clear cache dan restart:

```bash
cd /www/wwwroot/lms
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Restart queue workers
supervisorctl restart lms-worker:*
```

### Step 11: Setup Monitoring & Logging

#### 11.1 Setup Log Rotation

```bash
# Create logrotate configuration
nano /etc/logrotate.d/lms
```

Isi dengan:

```
/www/wwwroot/lms/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www www
    sharedscripts
    postrotate
        supervisorctl restart lms-worker:* > /dev/null 2>&1 || true
    endscript
}
```

#### 11.2 Setup Monitoring via aaPanel

1. Klik **Monitor** di sidebar
2. Enable monitoring untuk:
   - CPU Usage
   - Memory Usage
   - Disk Usage
   - Network Traffic
3. Set alert thresholds
4. Configure email notifications

#### 11.3 Setup Application Monitoring

Install Laravel Telescope untuk debugging (optional):

```bash
cd /www/wwwroot/lms
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access Telescope di: `https://yourdomain.com/telescope`

**⚠️ PENTING**: Protect Telescope di production dengan authentication!

Edit `app/Providers/TelescopeServiceProvider.php`:

```php
protected function gate()
{
    Gate::define('viewTelescope', function ($user) {
        return in_array($user->email, [
            'admin@example.com',
        ]);
    });
}
```

### Step 12: Security Hardening

#### 12.1 Disable Directory Listing

Sudah di-handle oleh Nginx config di atas.

#### 12.2 Hide Server Information

Edit Nginx config:

```bash
nano /www/server/nginx/conf/nginx.conf
```

Tambahkan di dalam `http` block:

```nginx
server_tokens off;
```

Restart Nginx:

```bash
systemctl restart nginx
```

#### 12.3 Setup Fail2Ban

```bash
# Install Fail2Ban
apt install -y fail2ban

# Create local configuration
cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit configuration
nano /etc/fail2ban/jail.local
```

Uncomment dan edit:

```ini
[sshd]
enabled = true
port = 22
maxretry = 3
bantime = 3600

[nginx-http-auth]
enabled = true
port = http,https
maxretry = 5
```

Start Fail2Ban:

```bash
systemctl enable fail2ban
systemctl start fail2ban
systemctl status fail2ban
```

#### 12.4 Regular Security Updates

```bash
# Setup automatic security updates
apt install -y unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades
```

### Step 13: Performance Optimization

#### 13.1 Enable OPcache

Edit PHP configuration:

1. aaPanel → **App Store** → **PHP 8.3** → **Settings**
2. Tab **Configuration File**
3. Find OPcache section dan set:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

4. Save dan Restart PHP

#### 13.2 Enable Gzip Compression

Edit Nginx config:

```bash
nano /www/server/nginx/conf/nginx.conf
```

Tambahkan di `http` block:

```nginx
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
```

Restart Nginx:

```bash
systemctl restart nginx
```

#### 13.3 Setup Browser Caching

Sudah di-handle di Nginx config Laravel di atas (Step 5.3).

### Step 14: Final Checks & Testing

#### 14.1 Test Website

1. ✅ Akses `https://yourdomain.com`
2. ✅ Login dengan default credentials
3. ✅ Test upload file (course material)
4. ✅ Test create exam
5. ✅ Test take exam
6. ✅ Test forum
7. ✅ Test notifications
8. ✅ Test AI assistant (jika enabled)

#### 14.2 Check Queue Worker

```bash
# Check supervisor status
supervisorctl status

# Check worker logs
tail -f /www/wwwroot/lms/storage/logs/worker.log

# Test queue by triggering email
cd /www/wwwroot/lms
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

#### 14.3 Check Scheduler

```bash
# List scheduled tasks
cd /www/wwwroot/lms
php artisan schedule:list

# Run scheduler manually untuk test
php artisan schedule:run
```

#### 14.4 Check Logs

```bash
# Application logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log

# Nginx access logs
tail -f /www/wwwroot/lms/storage/logs/nginx-access.log

# Nginx error logs
tail -f /www/wwwroot/lms/storage/logs/nginx-error.log
```

#### 14.5 Performance Testing

```bash
# Install Apache Bench
apt install -y apache2-utils

# Test homepage
ab -n 100 -c 10 https://yourdomain.com/

# Check response time (should be < 500ms)
```

### Step 15: Post-Deployment Tasks

#### 15.1 Change Default Passwords

Login sebagai admin dan ubah password default:

1. Login ke `https://yourdomain.com/login`
2. Email: `admin@example.com`, Password: `password`
3. Klik profile → Change Password
4. Set strong password

Ulangi untuk akun guru dan siswa, atau hapus akun demo.

#### 15.2 Configure Email Settings

1. Login sebagai Admin
2. Go to **Settings** → **Email Configuration**
3. Test email sending
4. Configure email templates

#### 15.3 Configure AI Assistant (Optional)

1. Login sebagai Admin
2. Go to **Settings** → **AI Assistant**
3. Enter OpenAI API Key
4. Select model (gpt-4o-mini recommended)
5. Test connection
6. Enable AI assistant

#### 15.4 Setup Backup Schedule

1. aaPanel → **Cron**
2. Add new cron:
   - **Task Type**: Backup Database
   - **Database**: lms_production
   - **Execution Cycle**: Daily at 2 AM
   - **Backup Path**: `/www/backup/database/`
   - **Keep**: 7 days

3. Add file backup cron:
   - **Task Type**: Backup Website
   - **Website**: yourdomain.com
   - **Execution Cycle**: Weekly (Sunday 3 AM)
   - **Backup Path**: `/www/backup/website/`
   - **Keep**: 4 weeks

#### 15.5 Setup External Backup (Recommended)

Backup ke external storage untuk disaster recovery:

```bash
# Install rclone untuk backup ke cloud
curl https://rclone.org/install.sh | bash

# Configure rclone (Google Drive, Dropbox, S3, dll)
rclone config

# Create backup script
nano /root/backup-to-cloud.sh
```

Isi script:

```bash
#!/bin/bash

# Variables
BACKUP_DIR="/www/backup"
REMOTE_NAME="your-remote-name"
REMOTE_PATH="lms-backups"
DATE=$(date +%Y-%m-%d)

# Sync backups to cloud
rclone sync $BACKUP_DIR $REMOTE_NAME:$REMOTE_PATH --log-file=/var/log/rclone-backup.log

# Delete old local backups (older than 7 days)
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed at $DATE"
```

Make executable dan add to cron:

```bash
chmod +x /root/backup-to-cloud.sh

# Add to crontab
crontab -e

# Add line:
0 4 * * * /root/backup-to-cloud.sh
```

### 🎉 Deployment Complete!

Aplikasi Laravel LMS Anda sekarang sudah live di production!

**Access URLs:**
- **Website**: https://yourdomain.com
- **aaPanel**: http://your_vps_ip:8888
- **phpMyAdmin**: http://your_vps_ip:888 (via aaPanel)

**Default Credentials:**
- **Admin**: admin@example.com / password (UBAH SEGERA!)
- **Guru**: guru@example.com / password
- **Siswa**: siswa@example.com / password

**Important Files & Directories:**
- Application: `/www/wwwroot/lms`
- Logs: `/www/wwwroot/lms/storage/logs`
- Backups: `/www/backup`
- Nginx Config: `/www/server/panel/vhost/nginx/`
- PHP Config: `/www/server/php/83/etc/php.ini`

---

## ⚙️ Konfigurasi Environment

### Environment Variables Lengkap

Berikut adalah penjelasan lengkap untuk setiap environment variable yang tersedia:

#### Application Settings

```env
# Nama aplikasi (ditampilkan di UI)
APP_NAME="Laravel LMS"

# Environment: local, staging, production
APP_ENV=production

# Application key (generate dengan: php artisan key:generate)
APP_KEY=base64:xxxxx

# Debug mode (HARUS false di production!)
APP_DEBUG=false

# URL aplikasi (dengan https:// di production)
APP_URL=https://yourdomain.com

# Localization
APP_LOCALE=id                    # Bahasa default (id = Indonesia)
APP_FALLBACK_LOCALE=id           # Fallback language
APP_FAKER_LOCALE=id_ID           # Locale untuk fake data
APP_TIMEZONE=Asia/Jakarta        # Timezone (PENTING untuk exam scheduling)

# Maintenance mode
APP_MAINTENANCE_DRIVER=file      # Driver untuk maintenance mode
```

#### Database Configuration

```env
# Database driver: mysql, sqlite, pgsql
DB_CONNECTION=mysql

# MySQL Configuration
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_production
DB_USERNAME=lms_user
DB_PASSWORD=your_strong_password

# SQLite Configuration (alternative)
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

#### Session Configuration

```env
# Session driver: file, cookie, database, redis
SESSION_DRIVER=database          # Recommended untuk production
SESSION_LIFETIME=120             # Session lifetime dalam menit
SESSION_ENCRYPT=false            # Encrypt session data
SESSION_PATH=/                   # Cookie path
SESSION_DOMAIN=null              # Cookie domain
SESSION_SECURE_COOKIE=true       # HTTPS only (true untuk production)
SESSION_HTTP_ONLY=true           # Prevent JavaScript access
SESSION_SAME_SITE=lax            # CSRF protection: lax, strict, none
```

#### Cache & Queue Configuration

```env
# Cache driver: file, database, redis, memcached
CACHE_STORE=database             # atau redis untuk performa lebih baik

# Queue driver: sync, database, redis, sqs
QUEUE_CONNECTION=database        # atau redis untuk performa lebih baik

# Broadcast driver: log, redis, pusher
BROADCAST_CONNECTION=log

# Filesystem disk: local, public, s3
FILESYSTEM_DISK=local
```

#### Redis Configuration (Optional)

```env
REDIS_CLIENT=phpredis            # atau predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null              # Set jika Redis memerlukan password
REDIS_PORT=6379
REDIS_DB=0                       # Database number (0-15)

# Redis untuk cache
REDIS_CACHE_DB=1

# Redis untuk queue
REDIS_QUEUE_CONNECTION=default
REDIS_QUEUE=default
```

#### Mail Configuration

```env
# Mail driver: smtp, sendmail, mailgun, ses, postmark
MAIL_MAILER=smtp

# SMTP Configuration
MAIL_HOST=smtp.gmail.com         # Gmail SMTP
MAIL_PORT=587                    # 587 untuk TLS, 465 untuk SSL
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password  # Gunakan App Password untuk Gmail
MAIL_ENCRYPTION=tls              # tls atau ssl
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Alternative: Mailgun
# MAIL_MAILER=mailgun
# MAILGUN_DOMAIN=your-domain.com
# MAILGUN_SECRET=your-mailgun-key

# Alternative: AWS SES
# MAIL_MAILER=ses
# AWS_ACCESS_KEY_ID=your-key
# AWS_SECRET_ACCESS_KEY=your-secret
# AWS_DEFAULT_REGION=us-east-1
```

#### AI Assistant Configuration

```env
# OpenAI API Key (dapatkan di: https://platform.openai.com/api-keys)
OPENAI_API_KEY=sk-xxxxx

# AI Model: gpt-4, gpt-4-turbo, gpt-4o, gpt-4o-mini, gpt-3.5-turbo
AI_MODEL=gpt-4o-mini             # Recommended (murah dan cepat)

# Maximum tokens per response
AI_MAX_TOKENS=1024               # 1024 = ~750 words

# Temperature (0.0 - 2.0): Lower = more focused, Higher = more creative
AI_TEMPERATURE=0.7               # 0.7 = balanced
```

#### Certificate System Configuration

```env
# Certificate template: default, modern, elegant, minimalist
CERTIFICATE_TEMPLATE=default

# Institution information
CERTIFICATE_INSTITUTION_NAME="Your Institution Name"
CERTIFICATE_LOGO_PATH=           # Path ke logo (optional)
CERTIFICATE_DIRECTOR_NAME="Director Name"

# Certificate colors (hex codes)
CERTIFICATE_PRIMARY_COLOR=#3b82f6
CERTIFICATE_SECONDARY_COLOR=#8b5cf6
CERTIFICATE_ACCENT_COLOR=#ec4899
CERTIFICATE_TEXT_COLOR=#1e293b

# Certificate features
CERTIFICATE_AUTO_GENERATE=true   # Auto-generate saat course completed
CERTIFICATE_NOTIFY_STUDENT=true  # Send notification
CERTIFICATE_QR_CODE=true         # Include QR code untuk verification
CERTIFICATE_AUTO_SAVE=true       # Save PDF ke storage
CERTIFICATE_EMAIL_ENABLED=false  # Email certificate ke student

# Certificate storage
CERTIFICATE_STORAGE_DISK=public  # public atau s3
```

#### PWA Configuration

```env
# Enable/disable PWA features
PWA_ENABLED=true
```

#### AWS Services (Optional)

```env
# AWS credentials untuk S3 storage atau SES email
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=ap-southeast-1  # Singapore region
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false

# S3 Configuration
AWS_URL=                         # Custom S3 URL (optional)
AWS_ENDPOINT=                    # Custom endpoint (optional)
```

#### Logging Configuration

```env
# Log channel: stack, single, daily, slack, syslog, errorlog
LOG_CHANNEL=stack

# Log level: debug, info, notice, warning, error, critical, alert, emergency
LOG_LEVEL=error                  # error untuk production, debug untuk development

# Deprecation log channel
LOG_DEPRECATIONS_CHANNEL=null

# Stack channels
LOG_STACK=single                 # single, daily
```

#### Third-Party Services

```env
# Slack notifications (optional)
SLACK_BOT_USER_OAUTH_TOKEN=
SLACK_BOT_USER_DEFAULT_CHANNEL=

# Postmark (alternative email service)
POSTMARK_TOKEN=

# Resend (alternative email service)
RESEND_KEY=
```

#### Vite (Frontend Build)

```env
# Vite app name (untuk manifest)
VITE_APP_NAME="${APP_NAME}"
```

### Environment Presets

#### Development Environment

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

MAIL_MAILER=log
```

#### Staging Environment

```env
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.yourdomain.com

LOG_LEVEL=debug

DB_CONNECTION=mysql
# ... database config

CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

MAIL_MAILER=smtp
# ... mail config
```

#### Production Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_LEVEL=error

DB_CONNECTION=mysql
# ... database config

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
# ... mail config
```

---

## 🔧 Maintenance & Monitoring

### Daily Maintenance Tasks

#### 1. Monitor Queue Workers

```bash
# Check supervisor status
supervisorctl status

# View worker logs
tail -f /www/wwwroot/lms/storage/logs/worker.log

# Restart workers jika ada masalah
supervisorctl restart lms-worker:*
```

#### 2. Monitor Application Logs

```bash
# View Laravel logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log

# View Nginx error logs
tail -f /www/wwwroot/lms/storage/logs/nginx-error.log

# Search for errors
grep "ERROR" /www/wwwroot/lms/storage/logs/laravel.log
```

#### 3. Check Disk Space

```bash
# Check disk usage
df -h

# Check specific directory
du -sh /www/wwwroot/lms/storage/*

# Clean old logs jika perlu
find /www/wwwroot/lms/storage/logs -name "*.log" -mtime +30 -delete
```

#### 4. Monitor Database

```bash
# Check MySQL status
systemctl status mysql

# Check database size
mysql -u lms_user -p -e "SELECT table_schema AS 'Database', 
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' 
    FROM information_schema.TABLES 
    WHERE table_schema = 'lms_production' 
    GROUP BY table_schema;"

# Optimize tables
cd /www/wwwroot/lms
php artisan db:optimize
```

### Weekly Maintenance Tasks

#### 1. Clear Old Sessions

```bash
cd /www/wwwroot/lms
php artisan session:gc
```

#### 2. Clear Failed Jobs

```bash
cd /www/wwwroot/lms
php artisan queue:flush
```

#### 3. Optimize Database

```bash
cd /www/wwwroot/lms
php artisan db:optimize

# Atau via MySQL
mysql -u lms_user -p lms_production -e "OPTIMIZE TABLE users, courses, exams, exam_attempts;"
```

#### 4. Review Security Logs

```bash
# Check Fail2Ban logs
fail2ban-client status

# Check SSH login attempts
grep "Failed password" /var/log/auth.log | tail -20

# Check Nginx access logs untuk suspicious activity
grep "404" /www/wwwroot/lms/storage/logs/nginx-access.log | tail -20
```

### Monthly Maintenance Tasks

#### 1. Update System Packages

```bash
# Update system
apt update && apt upgrade -y

# Update aaPanel
bt update
```

#### 2. Review and Archive Old Data

```bash
cd /www/wwwroot/lms

# Archive old exam attempts (older than 1 year)
php artisan tinker
>>> ExamAttempt::where('created_at', '<', now()->subYear())->delete();

# Archive old notifications
>>> Notification::where('created_at', '<', now()->subMonths(6))->delete();
```

#### 3. Review SSL Certificate

```bash
# Check certificate expiry
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com 2>/dev/null | openssl x509 -noout -dates

# Let's Encrypt auto-renews, tapi verify:
certbot certificates
```

#### 4. Performance Audit

```bash
# Run performance tests
ab -n 1000 -c 50 https://yourdomain.com/

# Check slow queries
mysql -u lms_user -p -e "SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 10;"
```

### Monitoring Tools

#### 1. Setup Laravel Horizon (untuk Redis Queue)

Jika menggunakan Redis untuk queue:

```bash
cd /www/wwwroot/lms
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

Update supervisor config untuk Horizon:

```bash
nano /etc/supervisor/conf.d/lms-horizon.conf
```

```ini
[program:lms-horizon]
process_name=%(program_name)s
command=php /www/wwwroot/lms/artisan horizon
autostart=true
autorestart=true
user=www
redirect_stderr=true
stdout_logfile=/www/wwwroot/lms/storage/logs/horizon.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start lms-horizon
```

Access Horizon: `https://yourdomain.com/horizon`

#### 2. Setup Uptime Monitoring

Gunakan service gratis seperti:

- **UptimeRobot**: https://uptimerobot.com (Free, 50 monitors)
- **Pingdom**: https://pingdom.com (Free trial)
- **StatusCake**: https://statuscake.com (Free tier)

Setup monitoring untuk:
- Homepage: `https://yourdomain.com`
- Login page: `https://yourdomain.com/login`
- API health: `https://yourdomain.com/api/health` (jika ada)

#### 3. Setup Error Tracking

Install Sentry untuk error tracking:

```bash
cd /www/wwwroot/lms
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=your-sentry-dsn
```

Edit `.env`:

```env
SENTRY_LARAVEL_DSN=your-sentry-dsn
SENTRY_TRACES_SAMPLE_RATE=0.2
```

#### 4. Setup Performance Monitoring

Install Laravel Debugbar (development only):

```bash
composer require barryvdh/laravel-debugbar --dev
```

Atau gunakan New Relic / Datadog untuk production monitoring.

### Health Check Endpoints

Create health check endpoint untuk monitoring:

```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'services' => [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => Cache::has('health_check') ? 'ok' : 'error',
            'queue' => Queue::size() !== null ? 'ok' : 'error',
        ]
    ]);
});
```

### Automated Monitoring Script

Create monitoring script:

```bash
nano /root/monitor-lms.sh
```

```bash
#!/bin/bash

# Variables
APP_DIR="/www/wwwroot/lms"
LOG_FILE="/var/log/lms-monitor.log"
ALERT_EMAIL="admin@yourdomain.com"

# Function to log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> $LOG_FILE
}

# Check if website is up
if ! curl -f -s https://yourdomain.com > /dev/null; then
    log "ERROR: Website is down!"
    echo "Website is down!" | mail -s "LMS Alert: Website Down" $ALERT_EMAIL
fi

# Check queue workers
if ! supervisorctl status lms-worker:* | grep -q "RUNNING"; then
    log "ERROR: Queue workers not running!"
    supervisorctl restart lms-worker:*
    echo "Queue workers restarted" | mail -s "LMS Alert: Queue Workers Restarted" $ALERT_EMAIL
fi

# Check disk space
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    log "WARNING: Disk usage is ${DISK_USAGE}%"
    echo "Disk usage is ${DISK_USAGE}%" | mail -s "LMS Alert: High Disk Usage" $ALERT_EMAIL
fi

# Check MySQL
if ! systemctl is-active --quiet mysql; then
    log "ERROR: MySQL is not running!"
    systemctl start mysql
    echo "MySQL restarted" | mail -s "LMS Alert: MySQL Restarted" $ALERT_EMAIL
fi

log "Health check completed"
```

Make executable dan add to cron:

```bash
chmod +x /root/monitor-lms.sh

# Add to crontab (run every 5 minutes)
crontab -e
*/5 * * * * /root/monitor-lms.sh
```

---

## 🧾 Backup & Restore

### Automated Backup Strategy

#### 1. Database Backup

**Via aaPanel (Recommended):**

1. aaPanel → **Cron**
2. Add Cron:
   - Type: **Backup Database**
   - Database: `lms_production`
   - Cycle: **Daily at 2:00 AM**
   - Keep: **7 days**
   - Path: `/www/backup/database/`

**Via Command Line:**

```bash
# Create backup script
nano /root/backup-database.sh
```

```bash
#!/bin/bash

# Variables
DB_NAME="lms_production"
DB_USER="lms_user"
DB_PASS="your_password"
BACKUP_DIR="/www/backup/database"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="lms_db_${DATE}.sql.gz"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$FILENAME

# Delete backups older than 7 days
find $BACKUP_DIR -name "lms_db_*.sql.gz" -mtime +7 -delete

echo "Database backup completed: $FILENAME"
```

```bash
chmod +x /root/backup-database.sh

# Add to crontab
crontab -e
0 2 * * * /root/backup-database.sh
```

#### 2. Files Backup

**Via aaPanel:**

1. aaPanel → **Cron**
2. Add Cron:
   - Type: **Backup Website**
   - Website: `yourdomain.com`
   - Cycle: **Weekly (Sunday 3:00 AM)**
   - Keep: **4 weeks**
   - Path: `/www/backup/website/`

**Via Command Line:**

```bash
# Create backup script
nano /root/backup-files.sh
```

```bash
#!/bin/bash

# Variables
APP_DIR="/www/wwwroot/lms"
BACKUP_DIR="/www/backup/website"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="lms_files_${DATE}.tar.gz"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup files (exclude cache and logs)
tar -czf $BACKUP_DIR/$FILENAME \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='node_modules' \
    --exclude='.git' \
    $APP_DIR

# Delete backups older than 30 days
find $BACKUP_DIR -name "lms_files_*.tar.gz" -mtime +30 -delete

echo "Files backup completed: $FILENAME"
```

```bash
chmod +x /root/backup-files.sh

# Add to crontab (weekly on Sunday at 3 AM)
crontab -e
0 3 * * 0 /root/backup-files.sh
```

#### 3. Backup to Cloud Storage

**Setup Rclone:**

```bash
# Install rclone
curl https://rclone.org/install.sh | bash

# Configure rclone
rclone config

# Follow prompts untuk setup:
# - Google Drive
# - Dropbox
# - AWS S3
# - Backblaze B2
# - dll
```

**Create cloud backup script:**

```bash
nano /root/backup-to-cloud.sh
```

```bash
#!/bin/bash

# Variables
LOCAL_BACKUP="/www/backup"
REMOTE_NAME="gdrive"  # Nama remote dari rclone config
REMOTE_PATH="lms-backups"
LOG_FILE="/var/log/cloud-backup.log"

# Sync to cloud
rclone sync $LOCAL_BACKUP $REMOTE_NAME:$REMOTE_PATH \
    --log-file=$LOG_FILE \
    --log-level INFO \
    --transfers 4 \
    --checkers 8

# Send notification
if [ $? -eq 0 ]; then
    echo "Cloud backup completed successfully at $(date)" >> $LOG_FILE
else
    echo "Cloud backup failed at $(date)" >> $LOG_FILE
    echo "Cloud backup failed!" | mail -s "LMS Alert: Backup Failed" admin@yourdomain.com
fi
```

```bash
chmod +x /root/backup-to-cloud.sh

# Add to crontab (daily at 4 AM)
crontab -e
0 4 * * * /root/backup-to-cloud.sh
```

### Manual Backup

#### Database Backup

```bash
# Backup database
mysqldump -u lms_user -p lms_production > lms_backup_$(date +%Y%m%d).sql

# Backup dengan compression
mysqldump -u lms_user -p lms_production | gzip > lms_backup_$(date +%Y%m%d).sql.gz

# Backup specific tables
mysqldump -u lms_user -p lms_production users courses exams > lms_partial_backup.sql
```

#### Files Backup

```bash
# Backup entire application
tar -czf lms_backup_$(date +%Y%m%d).tar.gz /www/wwwroot/lms

# Backup only storage directory
tar -czf lms_storage_$(date +%Y%m%d).tar.gz /www/wwwroot/lms/storage

# Backup only uploads
tar -czf lms_uploads_$(date +%Y%m%d).tar.gz /www/wwwroot/lms/storage/app/public
```

### Restore from Backup

#### Restore Database

```bash
# Restore from .sql file
mysql -u lms_user -p lms_production < lms_backup_20250101.sql

# Restore from .sql.gz file
gunzip < lms_backup_20250101.sql.gz | mysql -u lms_user -p lms_production

# Restore via aaPanel
# 1. aaPanel → Database → Select database
# 2. Click "Import"
# 3. Upload .sql file
# 4. Click "Import"
```

#### Restore Files

```bash
# Stop services
supervisorctl stop lms-worker:*

# Backup current files (just in case)
mv /www/wwwroot/lms /www/wwwroot/lms_old

# Extract backup
tar -xzf lms_backup_20250101.tar.gz -C /www/wwwroot/

# Set permissions
chown -R www:www /www/wwwroot/lms
chmod -R 755 /www/wwwroot/lms
chmod -R 775 /www/wwwroot/lms/storage
chmod -R 775 /www/wwwroot/lms/bootstrap/cache

# Clear caches
cd /www/wwwroot/lms
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart services
supervisorctl start lms-worker:*
```

#### Full Disaster Recovery

```bash
# 1. Setup fresh server dengan aaPanel (ikuti Step 1-5 di Setup Production)

# 2. Restore database
mysql -u lms_user -p lms_production < lms_backup_latest.sql

# 3. Restore files
tar -xzf lms_backup_latest.tar.gz -C /www/wwwroot/

# 4. Set permissions
chown -R www:www /www/wwwroot/lms
chmod -R 755 /www/wwwroot/lms
chmod -R 775 /www/wwwroot/lms/storage
chmod -R 775 /www/wwwroot/lms/bootstrap/cache

# 5. Update .env jika perlu
nano /www/wwwroot/lms/.env

# 6. Clear caches
cd /www/wwwroot/lms
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:cache
php artisan config:cache

# 7. Setup queue worker dan scheduler (ikuti Step 8-9)

# 8. Test website
curl -I https://yourdomain.com
```

### Backup Best Practices

1. **3-2-1 Rule**: 
   - 3 copies of data
   - 2 different media types
   - 1 offsite backup

2. **Test Restores**: Test restore process setiap bulan

3. **Automate**: Gunakan automated backups, jangan manual

4. **Monitor**: Setup alerts untuk backup failures

5. **Document**: Dokumentasikan restore procedures

6. **Encrypt**: Encrypt backups yang berisi data sensitif

7. **Retention**: Keep daily backups for 7 days, weekly for 4 weeks, monthly for 12 months

---

## 🧰 Troubleshooting

### Common Issues & Solutions

#### 1. 500 Internal Server Error

**Symptoms:**
- White screen atau "500 Internal Server Error"
- Tidak ada error message yang ditampilkan

**Solutions:**

```bash
# Check Laravel logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log

# Check Nginx error logs
tail -f /www/wwwroot/lms/storage/logs/nginx-error.log

# Clear all caches
cd /www/wwwroot/lms
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Check permissions
chown -R www:www /www/wwwroot/lms
chmod -R 775 /www/wwwroot/lms/storage
chmod -R 775 /www/wwwroot/lms/bootstrap/cache

# Regenerate autoload files
composer dump-autoload

# Check PHP errors
php artisan tinker
>>> echo "PHP is working";
```

#### 2. Database Connection Error

**Symptoms:**
- "SQLSTATE[HY000] [2002] Connection refused"
- "Access denied for user"

**Solutions:**

```bash
# Check MySQL is running
systemctl status mysql

# Start MySQL if stopped
systemctl start mysql

# Test database connection
mysql -u lms_user -p lms_production

# Verify .env database credentials
cat /www/wwwroot/lms/.env | grep DB_

# Clear config cache
cd /www/wwwroot/lms
php artisan config:clear

# Test connection via artisan
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 3. Queue Jobs Not Processing

**Symptoms:**
- Emails tidak terkirim
- Background tasks tidak berjalan
- Jobs stuck di queue

**Solutions:**

```bash
# Check supervisor status
supervisorctl status

# Restart queue workers
supervisorctl restart lms-worker:*

# Check worker logs
tail -f /www/wwwroot/lms/storage/logs/worker.log

# Check failed jobs
cd /www/wwwroot/lms
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush

# Test queue manually
php artisan queue:work --once
```

#### 4. Scheduler Not Running

**Symptoms:**
- Scheduled tasks tidak berjalan
- Analytics reports tidak terkirim
- Cleanup tasks tidak jalan

**Solutions:**

```bash
# Check cron is configured
crontab -l -u www

# Add cron if missing
crontab -e -u www
* * * * * cd /www/wwwroot/lms && php artisan schedule:run >> /dev/null 2>&1

# List scheduled tasks
cd /www/wwwroot/lms
php artisan schedule:list

# Run scheduler manually untuk test
php artisan schedule:run

# Check scheduler logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log | grep "schedule"
```

#### 5. File Upload Errors

**Symptoms:**
- "The file exceeds the maximum upload size"
- Upload gagal tanpa error message
- Files tidak tersimpan

**Solutions:**

```bash
# Check PHP upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Update PHP settings via aaPanel
# aaPanel → App Store → PHP 8.3 → Settings → Configuration File
# Edit php.ini:
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300

# Restart PHP-FPM
systemctl restart php-fpm-83

# Check Nginx client_max_body_size
grep client_max_body_size /www/server/nginx/conf/nginx.conf

# Add to nginx.conf if missing:
client_max_body_size 100M;

# Restart Nginx
systemctl restart nginx

# Check storage permissions
ls -la /www/wwwroot/lms/storage/app/public
chmod -R 775 /www/wwwroot/lms/storage
```

#### 6. SSL Certificate Issues

**Symptoms:**
- "Your connection is not private"
- Certificate expired
- Mixed content warnings

**Solutions:**

```bash
# Check certificate status
openssl s_client -connect yourdomain.com:443 -servername yourdomain.com 2>/dev/null | openssl x509 -noout -dates

# Renew Let's Encrypt certificate
certbot renew

# Force renew
certbot renew --force-renewal

# Check certificate via aaPanel
# aaPanel → Website → Select domain → SSL → Check expiry

# Fix mixed content (HTTP resources on HTTPS page)
# Edit .env:
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true

# Clear cache
cd /www/wwwroot/lms
php artisan config:clear
php artisan config:cache
```

#### 7. Performance Issues

**Symptoms:**
- Slow page load times
- High server load
- Timeout errors

**Solutions:**

```bash
# Enable OPcache
# aaPanel → PHP 8.3 → Settings → Configuration File
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000

# Use Redis for cache and queue
# Edit .env:
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Clear and rebuild caches
cd /www/wwwroot/lms
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize database
php artisan db:optimize
mysql -u lms_user -p lms_production -e "OPTIMIZE TABLE users, courses, exams, exam_attempts;"

# Check slow queries
mysql -u lms_user -p -e "SHOW VARIABLES LIKE 'slow_query_log';"

# Enable slow query log
mysql -u lms_user -p -e "SET GLOBAL slow_query_log = 'ON';"
mysql -u lms_user -p -e "SET GLOBAL long_query_time = 2;"

# Monitor server resources
htop
iotop
```

#### 8. Session Issues

**Symptoms:**
- Logged out randomly
- "CSRF token mismatch"
- Session data lost

**Solutions:**

```bash
# Clear sessions
cd /www/wwwroot/lms
php artisan session:gc

# Check session driver
cat .env | grep SESSION_DRIVER

# Use database for sessions (recommended)
# Edit .env:
SESSION_DRIVER=database

# Clear config
php artisan config:clear
php artisan config:cache

# Check session table exists
mysql -u lms_user -p lms_production -e "SHOW TABLES LIKE 'sessions';"

# Create session table if missing
php artisan session:table
php artisan migrate

# Check session lifetime
cat .env | grep SESSION_LIFETIME

# Increase if needed (in minutes)
SESSION_LIFETIME=120
```

#### 9. Email Not Sending

**Symptoms:**
- Emails tidak terkirim
- "Connection refused" error
- "Authentication failed" error

**Solutions:**

```bash
# Test email configuration
cd /www/wwwroot/lms
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Check mail logs
tail -f /www/wwwroot/lms/storage/logs/laravel.log | grep "mail"

# Verify SMTP credentials
cat .env | grep MAIL_

# For Gmail, use App Password:
# 1. Enable 2FA di Google Account
# 2. Generate App Password: https://myaccount.google.com/apppasswords
# 3. Use App Password di .env

# Test SMTP connection
telnet smtp.gmail.com 587

# Check queue for mail jobs
php artisan queue:failed
php artisan queue:retry all

# Clear config
php artisan config:clear
php artisan config:cache
```

#### 10. PWA/Offline Mode Issues

**Symptoms:**
- Service worker tidak register
- Offline mode tidak bekerja
- Cache tidak update

**Solutions:**

```bash
# Rebuild assets
cd /www/wwwroot/lms
npm run build

# Clear browser cache
# Chrome: Ctrl+Shift+Delete → Clear cache

# Check service worker registration
# Chrome DevTools → Application → Service Workers

# Unregister old service worker
# Chrome DevTools → Application → Service Workers → Unregister

# Check manifest.json
curl https://yourdomain.com/manifest.json

# Verify HTTPS (PWA requires HTTPS)
curl -I https://yourdomain.com

# Check console for errors
# Chrome DevTools → Console
```

#### 11. AI Assistant Not Working

**Symptoms:**
- AI responses tidak muncul
- "API key invalid" error
- Timeout errors

**Solutions:**

```bash
# Verify API key
cat .env | grep OPENAI_API_KEY

# Test API key
cd /www/wwwroot/lms
php artisan tinker
>>> $client = new \GuzzleHttp\Client();
>>> $response = $client->get('https://api.openai.com/v1/models', [
    'headers' => ['Authorization' => 'Bearer ' . env('OPENAI_API_KEY')]
]);
>>> echo $response->getStatusCode();

# Check AI settings in admin panel
# Login → Settings → AI Assistant

# Check queue for AI jobs
php artisan queue:failed

# Increase timeout
# Edit config/services.php:
'openai' => [
    'timeout' => 60,
],

# Clear config
php artisan config:clear
php artisan config:cache
```

#### 12. Permission Denied Errors

**Symptoms:**
- "Permission denied" saat upload
- "Unable to write to storage"
- "Failed to create directory"

**Solutions:**

```bash
# Fix ownership
chown -R www:www /www/wwwroot/lms

# Fix permissions
chmod -R 755 /www/wwwroot/lms
chmod -R 775 /www/wwwroot/lms/storage
chmod -R 775 /www/wwwroot/lms/bootstrap/cache

# Check SELinux (jika enabled)
getenforce

# Disable SELinux temporarily
setenforce 0

# Or set proper SELinux context
chcon -R -t httpd_sys_rw_content_t /www/wwwroot/lms/storage
chcon -R -t httpd_sys_rw_content_t /www/wwwroot/lms/bootstrap/cache
```

### Debug Mode

**⚠️ JANGAN enable debug mode di production!**

Untuk troubleshooting, enable debug mode sementara:

```bash
# Edit .env
nano /www/wwwroot/lms/.env

# Change:
APP_DEBUG=true
LOG_LEVEL=debug

# Clear config
php artisan config:clear

# Reproduce error dan check logs

# DISABLE debug mode setelah selesai:
APP_DEBUG=false
LOG_LEVEL=error

# Clear config
php artisan config:cache
```

### Getting Help

Jika masalah masih belum resolved:

1. **Check Logs**:
   - Laravel: `/www/wwwroot/lms/storage/logs/laravel.log`
   - Nginx: `/www/wwwroot/lms/storage/logs/nginx-error.log`
   - PHP-FPM: `/www/server/php/83/var/log/php-fpm.log`
   - MySQL: `/var/log/mysql/error.log`

2. **Search Documentation**: Check Laravel docs di https://laravel.com/docs

3. **Community**: Post di Laravel forum atau Stack Overflow

4. **GitHub Issues**: Report bug di repository

---

## 📚 Dokumentasi API

### Authentication

Semua API endpoints memerlukan authentication via Laravel Sanctum.

#### Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Response:
```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "siswa"
  }
}
```

#### Authenticated Requests

```http
GET /api/user
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxx
```

### Endpoints

#### Courses

```http
# List courses
GET /api/courses

# Get course detail
GET /api/courses/{id}

# Enroll in course
POST /api/courses/{id}/enroll
{
  "enrollment_code": "ABC123"
}

# Get course materials
GET /api/courses/{id}/materials
```

#### Exams

```http
# List exams
GET /api/exams

# Get exam detail
GET /api/exams/{id}

# Start exam attempt
POST /api/exams/{id}/start

# Submit answer
POST /api/exam-attempts/{attemptId}/answers
{
  "question_id": 1,
  "answer": "A"
}

# Submit exam
POST /api/exam-attempts/{attemptId}/submit
```

#### Forum

```http
# List threads
GET /api/forum/threads

# Create thread
POST /api/forum/threads
{
  "category_id": 1,
  "title": "Thread title",
  "content": "Thread content"
}

# Reply to thread
POST /api/forum/threads/{id}/replies
{
  "content": "Reply content"
}

# Like thread
POST /api/forum/threads/{id}/like
```

### Rate Limiting

API endpoints dibatasi:
- **Authenticated**: 60 requests per minute
- **Guest**: 10 requests per minute

### Error Responses

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

HTTP Status Codes:
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Server Error

---

## 🤝 Kontribusi

Kami menerima kontribusi dari komunitas! Berikut cara berkontribusi:

### Development Setup

1. Fork repository
2. Clone fork Anda
3. Create feature branch: `git checkout -b feature/amazing-feature`
4. Commit changes: `git commit -m 'Add amazing feature'`
5. Push to branch: `git push origin feature/amazing-feature`
6. Open Pull Request

### Coding Standards

- Follow PSR-12 coding standard
- Run Laravel Pint: `./vendor/bin/pint`
- Run static analysis: `./vendor/bin/phpstan analyse`
- Write tests untuk new features
- Update documentation

### Pull Request Guidelines

- Describe changes clearly
- Include screenshots untuk UI changes
- Update README jika perlu
- Ensure all tests pass
- Keep PR focused (one feature per PR)

### Reporting Bugs

Gunakan GitHub Issues dengan template:

```markdown
**Describe the bug**
A clear description of the bug.

**To Reproduce**
Steps to reproduce:
1. Go to '...'
2. Click on '...'
3. See error

**Expected behavior**
What you expected to happen.

**Screenshots**
If applicable, add screenshots.

**Environment:**
- OS: [e.g. Ubuntu 22.04]
- PHP Version: [e.g. 8.3]
- Laravel Version: [e.g. 11.0]
- Browser: [e.g. Chrome 120]
```

### Feature Requests

Gunakan GitHub Issues dengan label "enhancement":

```markdown
**Is your feature request related to a problem?**
A clear description of the problem.

**Describe the solution you'd like**
A clear description of what you want to happen.

**Describe alternatives you've considered**
Alternative solutions or features.

**Additional context**
Any other context or screenshots.
```

---

## 📄 License

Proprietary - All rights reserved

Copyright (c) 2025 Laravel LMS

Unauthorized copying, modification, distribution, or use of this software, via any medium, is strictly prohibited without explicit permission from the copyright holder.

For licensing inquiries, please contact: license@yourdomain.com

---

## 🙏 Acknowledgments

### Technologies Used

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Chart.js](https://www.chartjs.org) - JavaScript charting library
- [SweetAlert2](https://sweetalert2.github.io) - Beautiful popup boxes
- [Font Awesome](https://fontawesome.com) - Icon library
- [DomPDF](https://github.com/dompdf/dompdf) - PDF generation
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) - Media management
- [Laravel Excel](https://laravel-excel.com) - Excel import/export

### Contributors

Terima kasih kepada semua kontributor yang telah membantu mengembangkan Laravel LMS!

<!-- ALL-CONTRIBUTORS-LIST:START -->
<!-- Add contributors here -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

---

## 📞 Support

Butuh bantuan? Hubungi kami:

- **Email**: support@yourdomain.com
- **Documentation**: https://docs.yourdomain.com
- **GitHub Issues**: https://github.com/your-username/lms/issues
- **Discord**: https://discord.gg/your-invite

---

## 🗺️ Roadmap

### Version 2.0 (Q2 2025)

- [ ] Mobile app (React Native)
- [ ] Video conferencing integration (Zoom/Google Meet)
- [ ] Advanced analytics dengan ML
- [ ] Gamification system
- [ ] Multi-language support
- [ ] Payment gateway integration
- [ ] LTI integration untuk LMS lain

### Version 2.1 (Q3 2025)

- [ ] Live streaming classes
- [ ] Whiteboard collaboration
- [ ] Assignment submission system
- [ ] Plagiarism detection
- [ ] Advanced proctoring (webcam, screen recording)
- [ ] Mobile app offline mode

### Version 3.0 (Q4 2025)

- [ ] AI-powered content generation
- [ ] Adaptive learning paths
- [ ] VR/AR support
- [ ] Blockchain certificates
- [ ] Advanced reporting & BI
- [ ] Multi-tenant SaaS version

---

<div align="center">

**Made with ❤️ in Indonesia**

⭐ Star us on GitHub — it motivates us a lot!

[Website](https://yourdomain.com) • [Documentation](https://docs.yourdomain.com) • [Demo](https://demo.yourdomain.com)

</div>
