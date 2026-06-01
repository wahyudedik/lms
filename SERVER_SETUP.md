# 📋 Server Setup Guide - PHP Extensions

Panduan lengkap installasi PHP extensions yang dibutuhkan oleh Laravel LMS.

---

## ⚠️ FIX URGENT: PHP Extension Mismatch (BT Panel / 宝塔面板)

### 🔍 Penjelasan Masalah

Kesalahan ini terjadi karena **extension PHP 8.4 dimuat ke PHP 8.3**, menyebabkan **API version mismatch**. Error yang muncul:

```
Module "opcache" was compiled with module API=20240924, PHP compiled with module API=20230831
```

**Kenapa bisa terjadi?**
Di panel BT (宝塔面板), ketika Anda install PHP 8.3 dan PHP 8.4 bersamaan, kadang configuration file (`php.ini`) atau path `extension_dir` bisa tertukar atau tertinggal dari versi lain. Akibatnya PHP 8.3 mencoba memuat `.so` extension yang dikompilasi untuk PHP 8.4.

**Extension yang biasanya bermasalah:**
- `opcache.so` — dari PHP 8.4, dimuat ke PHP 8.3
- `zip.so`, `imagick.so` — path tidak ditemukan atau salah versi
- `fileinfo.so`, `redis.so`, `exif.so` — API mismatch warnings

---

### 🖥️ Fix via BT Panel (Prioritas Utama)

#### Metode 1: Reset Extension dari Panel

1. **Login** ke BT Panel (宝塔面板) di browser Anda
2. Buka menu **"Software Store"** → cari **PHP 8.3**
3. Klik **"Settings"** (设置) atau **"Manage"**
4. Buka tab **"Extensions"** (扩展管理 / PHP扩展)
5. **Disable SEMUA** extension yang error:
   - ☐ opcache
   - ☐ zip
   - ☐ imagick
   - ☐ fileinfo
   - ☐ redis
   - ☐ exif
6. **Restart PHP 8.3** dari panel
7. **Enable ulang** extension yang dibutuhkan (pastikan yang versi PHP 8.3):
   - ☑ pdo_mysql
   - ☑ curl
   - ☑ gd
   - ☑ mbstring
   - ☑ xml
   - ☑ zip
   - ☑ bcmath
   - ☑ intl
   - ☑ fileinfo
   - ☑ exif
   - ☑ openssl
   - ☑ json
8. **Restart PHP 8.3** lagi dari panel

#### Metode 2: Install Ulang PHP 8.3 (Paling Cepat & Bersih)

1. **Login** ke BT Panel
2. Buka **"Software Store"** → cari **PHP 8.3**
3. Klik **"Uninstall"** (卸载) PHP 8.3
4. **Tunggu** sampai selesai
5. **Install ulang** PHP 8.3 dari Software Store
6. Setelah install, buka **"Settings"** → **"Extensions"**
7. **Enable** semua extension yang dibutuhkan (lihat daftar di atas)
8. **Restart** PHP-FPM

> ⚡ **Metode 2 direkomendasikan** karena membersihkan semua file config yang salah secara otomatis.

---

### 🔧 Fix Manual via Command Line (Alternatif)

Jika Anda lebih nyaman via terminal, ikuti langkah berikut:

#### 1. Cek konfigurasi PHP yang aktif

```bash
# Cek versi PHP yang aktif
php --version

# Cek php.ini yang digunakan
php --ini
# Catat "Loaded Configuration File" path-nya, contoh:
# /www/server/php/83/etc/php.ini
```

#### 2. Cek `extension_dir`

```bash
php -i | grep extension_dir
# Pastikan hasilnya pointing ke PHP 8.3, BUKAN PHP 8.4
# Benar: /www/server/php/83/lib/php/extensions/...
# Salah: /www/server/php/84/lib/php/extensions/...
```

#### 3. Edit php.ini

```bash
# Backup dulu
cp /www/server/php/83/etc/php.ini /www/server/php/83/etc/php.ini.bak

# Edit php.ini
nano /www/server/php/83/etc/php.ini
```

Cari dan perbaiki baris-baris berikut:

```ini
; ✅ Pastikan extension_dir pointing ke PHP 8.3
extension_dir = "/www/server/php/83/lib/php/extensions/no-debug-non-zts-20230831/"

; ✅ Komentari atau hapus baris extension yang path-nya salah (ke PHP 8.4)
; extension=/www/server/php/84/lib/php/extensions/.../opcache.so

; ✅ Pastikan semua extension yang dibutuhkan aktif dan path-nya benar
extension=pdo_mysql.so
extension=curl.so
extension=gd.so
extension=mbstring.so
extension=xml.so
extension=zip.so
extension=bcmath.so
extension=intl.so
extension=fileinfo.so
extension=exif.so
extension=openssl.so
```

> 📌 **Tips:** Path `no-debug-non-zts-20230831` adalah module API untuk PHP 8.3. Untuk PHP 8.4, angkanya `no-debug-non-zts-20240924`. Jangan campur aduk!

#### 4. Restart PHP-FPM

```bash
# Restart PHP-FPM 8.3
/etc/init.d/php-fpm-8.3 restart

# Atau menggunakan systemctl
systemctl restart php-fpm

# Atau restart dari BT Panel CLI
bt restart
```

#### 5. Verifikasi

```bash
# Cek tidak ada warning
php -m 2>&1 | head -20

# Cek semua extension aktif
php -m | grep -iE "pdo_mysql|curl|gd|mbstring|xml|zip|bcmath|intl|fileinfo|exif|openssl|json|opcache"
```

---

### 📋 Extensions yang Wajib Aktif untuk Laravel LMS

| Extension    | Keterangan                                    | Wajib? |
|--------------|-----------------------------------------------|--------|
| `pdo_mysql`  | Koneksi database MySQL                        | ✅     |
| `curl`       | HTTP requests (notifications, API)            | ✅     |
| `gd`         | Manipulasi gambar, export spreadsheet         | ✅     |
| `mbstring`   | Multibyte string handling                     | ✅     |
| `xml`        | XML parsing (spreadsheet import/export)       | ✅     |
| `zip`        | File compression, export Excel                | ✅     |
| `bcmath`     | Matematika presisi tinggi                     | ✅     |
| `intl`       | Internasionalisasi & locale handling          | ✅     |
| `fileinfo`   | Deteksi tipe file upload                      | ✅     |
| `exif`       | Baca metadata gambar (EXIF)                   | ✅     |
| `openssl`    | Enkripsi, JWT, SSL                            | ✅     |
| `json`       | JSON handling                                 | ✅     |
| `opcache`    | PHP opcode caching (performa)                 | ⚡     |
| `redis`      | Cache & queue driver (jika pakai Redis)       | ⚡     |
| `imagick`    | Advanced image processing                     | 🔧     |

> ✅ = Wajib untuk aplikasi berjalan | ⚡ = Sangat disarankan untuk performa | 🔧 = Opsional

---

### ✅ Checklist Verifikasi Setelah Fix

Jalankan perintah ini **tanpa ada warning/error**:

```bash
# 1. Cek versi PHP benar
php --version
# Harus: PHP 8.3.x

# 2. Tidak ada API mismatch warning
php -m 2>&1
# Tidak boleh ada pesan "was compiled with module API=..."

# 3. Semua extension wajib aktif
php -m | grep -iE "pdo_mysql|curl|gd|mbstring|xml|zip|bcmath|intl|fileinfo|exif|openssl|json"

# 4. Extension dir benar
php -i | grep extension_dir
# Harus: path ke PHP 8.3

# 5. Jalankan Laravel artisan
php artisan --version
php artisan config:clear
php artisan cache:clear

# 6. Test aplikasi
curl -I http://localhost/
# Harus returned HTTP 200 atau redirect
```

### 🚨 Jika Masih Error

Jika setelah fix di atas masih muncul error, kemungkinan:

1. **Ada multiple php.ini** — Cek semua php.ini yang diload:
   ```bash
   php --ini
   # "Additional .ini files loaded" bisa menunjuk ke file lain
   # Cek juga file-file tersebut
   ```

2. **PHP-FPM belum restart** — Restart ulang:
   ```bash
   /etc/init.d/php-fpm-8.3 restart
   ```

3. **Ada extension `.so` fisik yang salah** — Hapus file .so lama dari PHP 8.4:
   ```bash
   # Cari file .so yang mungkin salah
   find /www/server/php/ -name "opcache.so" 2>/dev/null
   find /www/server/php/ -name "zip.so" 2>/dev/null
   # Pastikan yang di-load hanya dari path PHP 8.3
   ```

4. **Solusi paling ampuh**: Uninstall PHP 8.3 dari BT Panel, restart panel, lalu install ulang PHP 8.3.

---

## 🔧 Extensions yang Dibutuhkan

| Extension   | Fungsi                                        |
|-------------|-----------------------------------------------|
| `pdo_mysql` | Koneksi database MySQL                        |
| `curl`      | HTTP requests (web push notifications)        |
| `gd`        | Manipulasi gambar & export spreadsheet        |
| `mbstring`  | Multibyte string handling                     |
| `xml`       | XML parsing (spreadsheet import/export)       |
| `zip`       | File compression                              |
| `bcmath`    | Matematika presisi tinggi                     |
| `intl`      | Internasionalisasi & locale handling          |

---

## 🐧 Ubuntu / Debian (apt)

### PHP 8.1
```bash
sudo apt-get update
sudo apt-get install -y \
    php8.1-mysql \
    php8.1-curl \
    php8.1-gd \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-zip \
    php8.1-bcmath \
    php8.1-intl
```

### PHP 8.2
```bash
sudo apt-get update
sudo apt-get install -y \
    php8.2-mysql \
    php8.2-curl \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-intl
```

### PHP 8.3
```bash
sudo apt-get update
sudo apt-get install -y \
    php8.3-mysql \
    php8.3-curl \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-intl
```

### PHP 8.4
```bash
sudo apt-get update
sudo apt-get install -y \
    php8.4-mysql \
    php8.4-curl \
    php8.4-gd \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-zip \
    php8.4-bcmath \
    php8.4-intl
```

### Setelah Install (Ubuntu/Debian)
```bash
# Restart PHP-FPM (ganti versi sesuai yang dipakai)
sudo systemctl restart php8.3-fpm

# Atau restart Apache jika pakai mod_php
sudo systemctl restart apache2

# Atau restart Nginx + PHP-FPM
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm

# Verifikasi semua extension terinstall
php -m | grep -iE "pdo_mysql|curl|gd|mbstring|xml|zip|bcmath|intl"
```

---

## 🎩 CentOS / RHEL / AlmaLinux / Rocky Linux (yum/dnf)

### Menggunakan `dnf` (CentOS 8+, AlmaLinux, Rocky Linux)
```bash
sudo dnf update -y
sudo dnf install -y \
    php-mysqlnd \
    php-curl \
    php-gd \
    php-mbstring \
    php-xml \
    php-zip \
    php-bcmath \
    php-intl
```

### Menggunakan `yum` (CentOS 7)
```bash
# Pastikan repo EPEL dan Remi sudah aktif
sudo yum install -y epel-release
sudo yum install -y https://rpms.remirepo.net/enterprise/remi-release-7.rpm

# Aktifkan PHP 8.x module (ganti sesuai versi yang diinginkan)
sudo yum-config-manager --enable remi-php83

sudo yum install -y \
    php-mysqlnd \
    php-curl \
    php-gd \
    php-mbstring \
    php-xml \
    php-zip \
    php-bcmath \
    php-intl
```

### Menggunakan Remi Repository (CentOS 7/8/9, RHEL)
```bash
# Install EPEL & Remi
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E '%{rhel}').rpm

# Lihat available PHP streams
sudo dnf module list php

# Reset dan enable PHP 8.3 (atau versi yang diinginkan)
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.3 -y

# Install extensions
sudo dnf install -y \
    php-mysqlnd \
    php-curl \
    php-gd \
    php-mbstring \
    php-xml \
    php-zip \
    php-bcmath \
    php-intl
```

### Setelah Install (CentOS/RHEL)
```bash
# Restart PHP-FPM
sudo systemctl restart php-fpm

# Atau restart Apache
sudo systemctl restart httpd

# Verifikasi
php -m | grep -iE "pdo_mysql|curl|gd|mbstring|xml|zip|bcmath|intl"
```

---

## 🖥️ DirectAdmin / Custom Panel

### DirectAdmin (CustomBuild)
```bash
cd /usr/local/directadmin/custombuild
./build update
./build php php_default_options="--with-pdo_mysql --enable-curl --with-gd --enable-mbstring --with-xml --with-zlib --enable-bcmath --with-intl"
```

Atau edit `options.conf`:
```bash
cd /usr/local/directadmin/custombuild
nano options.conf
# Pastikan php_ver_target=8.3

# Build ulang PHP
./build php
```

### VestaCP
```bash
# VestaCP biasanya sudah include beberapa extension
# Untuk install tambahan:
sudo apt-get install -y php-fpm php-mysql php-curl php-gd php-mbstring php-xml php-zip php-bcmath php-intl
sudo systemctl restart php-fpm
```

### aaPanel / BT Panel
```bash
# Melalui panel web:
# 1. Login ke aaPanel
# 2. Go to App Store > PHP 8.3 (atau versi yang dipakai)
# 3. Click "Set" > "Install extensions"
# 4. Centang: curl, gd, mbstring, xml, zip, bcmath, intl, pdo_mysql

# Atau melalui terminal:
# Install via aaPanel's built-in manager
bt
# Pilih nomor PHP version > Install extensions
```

### CloudPanel
```bash
# CloudPanel menggunakan PHP-FPM, install extension via apt:
sudo apt-get install -y php8.3-mysql php8.3-curl php8.3-gd php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl
sudo systemctl restart php8.3-fpm
```

---

## 🐳 Docker

### Dockerfile
```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        curl \
        gd \
        mbstring \
        xml \
        zip \
        bcmath \
        intl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
```

### docker-compose.yml
```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: lms
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "3306:3306"
```

---

## ✅ Verifikasi Instalasi

Jalankan perintah berikut untuk memverifikasi semua extension sudah terinstall:

```bash
# Cek semua extension sekaligus
php -m | grep -iE "pdo_mysql|curl|gd|mbstring|xml|zip|bcmath|intl"

# Atau cek satu per satu
php -m | grep pdo_mysql
php -m | grep curl
php -m | grep gd
php -m | grep mbstring
php -m | grep xml
php -m | grep zip
php -m | grep bcmath
php -m | grep intl

# Atau gunakan phpinfo untuk detail lengkap
php -i | grep "Configure Command"

# Jalankan doctor Laravel untuk cek semua requirement
php artisan doctor
```

### Expected Output
Jika semua extension terinstall, Anda harus melihat:
```
bcmath
curl
gd
intl
mbstring
pdo_mysql
xml
zip
```

---

## 🔥 Troubleshooting

### "could not find driver" untuk MySQL
```bash
# Pastikan php-mysqlnd terinstall
sudo apt-get install -y php8.3-mysql   # Ubuntu/Debian
sudo dnf install -y php-mysqlnd         # CentOS/RHEL

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### "ext-curl / ext-gd is missing" saat Composer Install
```bash
# Install extension yang missing
sudo apt-get install -y php8.3-curl php8.3-gd

# Atau sementara, jalankan composer dengan flag ini:
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# ⚠️ Flag --ignore-platform-reqs hanya untuk sementara!
# Pastikan extension terinstall sesegera mungkin.
```

### Composer "lock file is not up to date"
```bash
# Hapus lock file dan vendor, lalu install ulang
rm -rf vendor composer.lock
composer install --no-dev --optimize-autoloader
```

### PHP-FPM tidak restart otomatis
```bash
# Cek status PHP-FPM
sudo systemctl status php8.3-fpm

# Restart manual
sudo systemctl restart php8.3-fpm

# Jika ada multiple PHP versions, pastikan yang benar:
sudo systemctl status php8.1-fpm php8.2-fpm php8.3-fpm
```

### Extension terinstall tapi tidak terdeteksi
```bash
# Cek apakah ada multiple PHP versions
which php
php --version

# Cek php.ini yang digunakan
php --ini

# Pastikan extension di-load dari php.ini yang benar
# Edit php.ini dan pastikan baris extension tidak dikomentari:
# extension=pdo_mysql
# extension=curl
# extension=gd
```

---

## 📝 Catatan Penting

1. **Selalu restart PHP-FPM** setelah install extension baru
2. **Jangan gunakan `--ignore-platform-reqs`** sebagai solusi permanente
3. **Cek versi PHP** sebelum install package (nama package berbeda per versi)
4. **Gunakan repo resmi** untuk package PHP (Remi untuk CentOS, Ondrej untuk Ubuntu)
5. **Backup database** sebelum menjalankan `deploy.sh` di production
