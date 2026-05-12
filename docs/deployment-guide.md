# Panduan Deployment

Panduan lengkap untuk deploy dan update aplikasi Laravel LMS di VPS (Virtual Private Server).

## Persyaratan Server

- Ubuntu 20.04/22.04/24.04 LTS
- PHP 8.2+ dengan ekstensi: mysql, zip, gd, mbstring, curl, xml, bcmath, intl
- Composer 2.x
- Node.js 18+ (20 LTS direkomendasikan)
- MySQL 8.0+
- Nginx atau Apache
- SSL Certificate (Let's Encrypt)
- Minimum 2GB RAM (4GB+ direkomendasikan)

## Setup Awal di VPS

### 1. Clone Repository

```bash
cd /var/www
git clone <repository-url> lms
cd lms
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan:
- `APP_URL` — domain kamu
- `DB_*` — kredensial database
- `MAIL_*` — konfigurasi email
- `AI_*` — API key AI (opsional)

### 4. Setup Database

```bash
php artisan migrate --force
php artisan db:seed
php artisan storage:link
```

### 5. Optimasi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Permission

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Deploy Update

Gunakan script `deploy.sh` yang sudah tersedia di root project:

```bash
chmod +x deploy.sh
./deploy.sh
```

Script ini otomatis melakukan:
1. Aktifkan maintenance mode
2. Reset perubahan lokal (`git checkout -- .` & `git clean -fd`) lalu pull dari git
3. Install dependencies PHP dan Node.js
4. Build assets (Vite)
5. Jalankan migrasi database
6. Cache config, route, view, event
7. Restart queue worker
8. Set permission
9. Nonaktifkan maintenance mode

## Konfigurasi Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;
    root /var/www/lms/public;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

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

## Queue Worker (Supervisor)

Buat file `/etc/supervisor/conf.d/lms-worker.conf`:

```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms/storage/logs/worker.log
stopwaitsecs=3600
```

Kemudian:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

## Scheduler (Cron)

Tambahkan ke crontab (`crontab -e`):

```
* * * * * cd /var/www/lms && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler menjalankan:
- Reminder deadline tugas (setiap jam)
- Auto-unblock user (harian)
- Cleanup push subscriptions

## Troubleshooting

### Halaman Blank / Error 500
```bash
php artisan optimize:clear
chmod -R 775 storage bootstrap/cache
```

### Queue Tidak Jalan
```bash
sudo supervisorctl status
sudo supervisorctl restart lms-worker:*
```

### Asset Tidak Muncul
```bash
npm run build
php artisan storage:link
```
