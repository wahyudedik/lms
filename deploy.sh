#!/bin/bash

# =============================================================================
# Deploy Script - Laravel LMS
# =============================================================================
# Gunakan script ini untuk deploy update di VPS
# Usage: bash deploy.sh

# Pertama kali, beri permission executable
# chmod +x deploy.sh

# Setiap ada update, cukup jalankan:
# ./deploy.sh
# =============================================================================

set -e

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Fungsi helper
info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# =============================================================================
# Konfigurasi - Sesuaikan dengan VPS kamu
# =============================================================================
APP_DIR=$(dirname "$(realpath "$0")")
PHP_BIN="php"
COMPOSER_BIN="composer"
NPM_BIN="npm"
GIT_BRANCH="main"

# Izinkan Composer berjalan sebagai root tanpa warning
export COMPOSER_ALLOW_SUPERUSER=1

# =============================================================================
# Mulai Deploy
# =============================================================================
echo ""
echo "============================================="
echo "  🚀 Deploy Laravel LMS"
echo "============================================="
echo ""

cd "$APP_DIR" || error "Gagal masuk ke direktori aplikasi: $APP_DIR"
info "Direktori: $APP_DIR"
info "Branch: $GIT_BRANCH"
echo ""

# 1. Aktifkan Maintenance Mode
info "Mengaktifkan maintenance mode..."
$PHP_BIN artisan down --refresh=15 --retry=60 || true

# 2. Pull perubahan terbaru dari Git
info "Pulling perubahan terbaru dari git..."
git pull origin "$GIT_BRANCH" || error "Gagal pull dari git"

# 3. Install/update dependencies PHP
info "Menginstall dependencies PHP (production)..."
$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction

# 4. Install/update dependencies Node.js & build assets
info "Menginstall dependencies Node.js..."
$NPM_BIN ci

info "Building assets (Vite)..."
$NPM_BIN run build

# 5. Jalankan migrasi database
info "Menjalankan migrasi database..."
$PHP_BIN artisan migrate --force

# 6. Optimasi Laravel
info "Mengoptimasi aplikasi..."
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# 7. Clear cache lama
info "Membersihkan cache lama..."
$PHP_BIN artisan cache:clear

# 8. Link storage (jika belum)
info "Memastikan storage link..."
$PHP_BIN artisan storage:link 2>/dev/null || true

# 9. Restart queue worker
info "Merestart queue worker..."
$PHP_BIN artisan queue:restart

# 10. Set permission yang benar
info "Mengatur permission..."
chmod -R 775 storage bootstrap/cache

# Deteksi user web server (www-data untuk Ubuntu/Debian, www untuk aaPanel/BT Panel)
WEB_USER="www-data"
if id "www" &>/dev/null; then
    WEB_USER="www"
fi
chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || warn "Gagal chown. Pastikan permission sudah benar secara manual."

# 11. Nonaktifkan Maintenance Mode
info "Menonaktifkan maintenance mode..."
$PHP_BIN artisan up

# =============================================================================
# Selesai
# =============================================================================
echo ""
echo "============================================="
echo -e "  ${GREEN}✅ Deploy selesai!${NC}"
echo "============================================="
echo ""
info "Jangan lupa cek:"
echo "  - Website bisa diakses"
echo "  - Queue worker berjalan (supervisord)"
echo "  - Log error: storage/logs/laravel.log"
echo ""
