# 🚀 Laravel LMS - Ubuntu VPS Deployment Guide

## 📋 Complete Step-by-Step Installation Guide

This guide covers deploying Laravel LMS on Ubuntu 20.04/22.04 VPS from scratch.

---

## ⚙️ System Requirements

- Ubuntu 20.04 or 22.04 LTS
- Minimum 2GB RAM
- 20GB Storage
- Root or sudo access

---

## 🔧 Step 1: Initial Server Setup (5 minutes)

### **1.1 Update System**
```bash
sudo apt update && sudo apt upgrade -y
```

### **1.2 Install Essential Tools**
```bash
sudo apt install -y software-properties-common curl wget git unzip
```

### **1.3 Configure Firewall**
```bash
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

---

## 🐘 Step 2: Install PHP 8.4 (5 minutes)

### **2.1 Add PHP Repository**
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

### **2.2 Install PHP & Extensions**
```bash
sudo apt install -y php8.4 \
    php8.4-fpm \
    php8.4-cli \
    php8.4-common \
    php8.4-mysql \
    php8.4-xml \
    php8.4-mbstring \
    php8.4-curl \
    php8.4-zip \
    php8.4-gd \
    php8.4-bcmath \
    php8.4-intl \
    php8.4-dom \
    php8.4-tokenizer
```

### **2.3 Verify Installation**
```bash
php -v
# Should show: PHP 8.4.x
```

---

## 🗄️ Step 3: Install MySQL 8.0 (5 minutes)

### **3.1 Install MySQL Server**
```bash
sudo apt install -y mysql-server
```

### **3.2 Secure MySQL**
```bash
sudo mysql_secure_installation
```

Answer the prompts:
- Remove anonymous users? **Yes**
- Disallow root login remotely? **Yes**
- Remove test database? **Yes**
- Reload privilege tables? **Yes**

### **3.3 Create Database & User**
```bash
sudo mysql
```

Run these SQL commands:
```sql
CREATE DATABASE lms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON lms_db.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📦 Step 4: Install Composer (3 minutes)

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify
composer --version
```

---

## 🌐 Step 5: Install Nginx (5 minutes)

### **5.1 Install Nginx**
```bash
sudo apt install -y nginx
```

### **5.2 Configure Nginx for Laravel**
```bash
sudo nano /etc/nginx/sites-available/lms
```

Paste this configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your_domain.com www.your_domain.com;
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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### **5.3 Enable Site**
```bash
sudo ln -s /etc/nginx/sites-available/lms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🎯 Step 6: Deploy Laravel LMS (10 minutes)

### **6.1 Clone Repository**
```bash
cd /var/www
sudo git clone https://github.com/YOUR_USERNAME/lms.git
cd lms
```

*OR upload your project files via SFTP*

### **6.2 Install Dependencies**
```bash
sudo composer install --optimize-autoloader --no-dev
```

### **6.3 Set Permissions**
```bash
sudo chown -R www-data:www-data /var/www/lms
sudo chmod -R 755 /var/www/lms
sudo chmod -R 775 /var/www/lms/storage
sudo chmod -R 775 /var/www/lms/bootstrap/cache
```

### **6.4 Configure Environment**
```bash
sudo cp .env.example .env
sudo nano .env
```

Update these values:
```env
APP_NAME="Laravel LMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your_domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_db
DB_USERNAME=lms_user
DB_PASSWORD=your_strong_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your_domain.com
MAIL_FROM_NAME="${APP_NAME}"

QUEUE_CONNECTION=database

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### **6.5 Generate Application Key**
```bash
sudo php artisan key:generate
```

### **6.6 Run Migrations & Seeders**
```bash
sudo php artisan migrate --force
sudo php artisan db:seed --force
```

### **6.7 Build Frontend Assets**
```bash
# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Build assets
npm install
npm run build
```

### **6.8 Optimize Laravel**
```bash
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

---

## 🔐 Step 7: Install SSL Certificate (5 minutes)

### **7.1 Install Certbot**
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### **7.2 Obtain SSL Certificate**
```bash
sudo certbot --nginx -d your_domain.com -d www.your_domain.com
```

Follow the prompts:
- Enter email address
- Agree to Terms of Service
- Choose redirect HTTP to HTTPS: **Yes**

### **7.3 Auto-Renewal**
```bash
sudo certbot renew --dry-run
```

---

## ⏰ Step 8: Configure Laravel Scheduler (3 minutes)

### **8.1 Open Crontab**
```bash
sudo crontab -e
```

### **8.2 Add Laravel Scheduler**
```cron
* * * * * cd /var/www/lms && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🚀 Step 9: Configure Queue Worker (5 minutes)

### **9.1 Create Systemd Service**
```bash
sudo nano /etc/systemd/system/laravel-queue.service
```

Paste:
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/lms
ExecStart=/usr/bin/php /var/www/lms/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

### **9.2 Enable & Start Service**
```bash
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
sudo systemctl status laravel-queue
```

---

## ✅ Step 10: Final Configuration (5 minutes)

### **10.1 Create Storage Link**
```bash
cd /var/www/lms
sudo php artisan storage:link
```

### **10.2 Set Final Permissions**
```bash
sudo chown -R www-data:www-data /var/www/lms
sudo find /var/www/lms -type f -exec chmod 644 {} \;
sudo find /var/www/lms -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/lms/storage
sudo chmod -R 775 /var/www/lms/bootstrap/cache
```

### **10.3 Restart Services**
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
sudo systemctl restart laravel-queue
```

---

## 🎉 Step 11: Access Your LMS!

Visit: `https://your_domain.com`

**Default Login Credentials:**
- **Admin:** admin@example.com / password
- **Guru:** guru@example.com / password
- **Siswa:** siswa@example.com / password

⚠️ **IMPORTANT: Change default passwords immediately!**

---

## 🔧 Maintenance Commands

### **Update Application**
```bash
cd /var/www/lms
sudo git pull origin main
sudo composer install --optimize-autoloader --no-dev
npm install && npm run build
sudo php artisan migrate --force
sudo php artisan config:clear
sudo php artisan cache:clear
sudo php artisan route:clear
sudo php artisan view:clear
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo systemctl restart laravel-queue
```

### **View Logs**
```bash
# Laravel logs
sudo tail -f /var/www/lms/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log

# Queue worker logs
sudo journalctl -u laravel-queue -f
```

### **Database Backup**
```bash
# Manual backup
sudo mysqldump -u lms_user -p lms_db > backup-$(date +%Y%m%d).sql

# Automated daily backup (add to crontab)
0 2 * * * mysqldump -u lms_user -pYOUR_PASSWORD lms_db | gzip > /backups/lms-$(date +\%Y\%m\%d).sql.gz
```

---

## 🐛 Troubleshooting

### **Permission Issues**
```bash
sudo chown -R www-data:www-data /var/www/lms
sudo chmod -R 755 /var/www/lms
sudo chmod -R 775 /var/www/lms/storage
sudo chmod -R 775 /var/www/lms/bootstrap/cache
```

### **500 Internal Server Error**
```bash
# Check Laravel logs
sudo tail -50 /var/www/lms/storage/logs/laravel.log

# Check Nginx logs
sudo tail -50 /var/log/nginx/error.log

# Ensure .env is configured
cat /var/www/lms/.env

# Clear all caches
cd /var/www/lms
sudo php artisan config:clear
sudo php artisan cache:clear
sudo php artisan route:clear
sudo php artisan view:clear
```

### **Database Connection Error**
```bash
# Test database connection
mysql -u lms_user -p lms_db

# Check .env database credentials
cat /var/www/lms/.env | grep DB_
```

### **Queue Not Processing**
```bash
# Check queue worker status
sudo systemctl status laravel-queue

# Restart queue worker
sudo systemctl restart laravel-queue

# View queue jobs
cd /var/www/lms
sudo php artisan queue:work --once
```

---

## 📊 Performance Optimization

### **Enable OPcache**
```bash
sudo nano /etc/php/8.4/fpm/php.ini
```

Find and update:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.4-fpm
```

### **Configure MySQL**
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add:
```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 512M
innodb_log_file_size = 128M
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

---

## 🔐 Security Hardening

### **Disable Directory Listing**
Already configured in Nginx config above.

### **Hide PHP Version**
```bash
sudo nano /etc/php/8.4/fpm/php.ini
```

Set:
```ini
expose_php = Off
```

### **Install Fail2Ban**
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 📈 Monitoring

### **Install Monitoring Tools (Optional)**
```bash
# htop for process monitoring
sudo apt install -y htop

# netdata for comprehensive monitoring
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

---

## ✅ Deployment Checklist

- [ ] System updated
- [ ] PHP 8.4 installed
- [ ] MySQL installed & configured
- [ ] Composer installed
- [ ] Nginx installed & configured
- [ ] Project deployed
- [ ] Dependencies installed
- [ ] Environment configured
- [ ] Database migrated & seeded
- [ ] Frontend assets built
- [ ] SSL certificate installed
- [ ] Scheduler configured
- [ ] Queue worker configured
- [ ] Permissions set correctly
- [ ] Application tested
- [ ] Default passwords changed
- [ ] Backup system configured

---

## 🎯 Post-Deployment

1. Change all default passwords
2. Configure email settings
3. Test all features
4. Set up regular backups
5. Monitor logs for errors
6. Configure monitoring
7. Document custom changes

---

## 📞 Support

For issues or questions:
- Check `/var/www/lms/storage/logs/laravel.log`
- Review Nginx error logs
- Check queue worker status
- Verify file permissions

---

**Deployment Time:** ~60 minutes
**Difficulty:** Intermediate
**Status:** Production Ready ✅

**Last Updated:** October 22, 2025

