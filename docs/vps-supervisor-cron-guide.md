# 🛠️ Guide Step-by-Step: Setup & Cek Supervisor + Cron di VPS

Panduan lengkap memeriksa dan men-setup **queue worker (supervisor)** serta **Laravel scheduler (cron)** di production server, step by step beserta cek keberhasilan di tiap langkah.

## Environment Target (sesuaikan bila berbeda)

| Item | Nilai |
|---|---|
| Server | Ubuntu + aaPanel, user SSH `oot` |
| Path aplikasi | `/var/www/koneksi` |
| PHP | `php` (apt PHP 8.3.x) |
| Web user | `www` (aaPanel) atau `www-data` (standar Debian) — dideteksi otomatis |
| Queue driver | `database` (tabel `jobs`) |
| App timezone | `Asia/Jakarta` |

## Kenapa Ini Penting

- **Supervisor** menjalankan `queue:work` → notifikasi (email/database/push), log aktivitas user, job analytics. Tanpa worker, semua job menumpuk di tabel `jobs`.
- **Cron** menjalankan `php artisan schedule:run` tiap menit → 7 task terjadwal di `routes/console.php`:
  - Reminder deadline tugas (**tiap jam**)
  - Auto-unblock user (harian 01:00)
  - **`certificates:generate` (harian 02:00)** — tanpa cron, sertifikat tidak pernah dibuat otomatis
  - Laporan analytics harian/mingguan/bulanan
  - Cleanup push subscription

> ⚠️ Contoh konfigurasi di `docs/deployment-guide.md` (path `/var/www/lms`) dan `README.md` (path `/www/wwwroot/lms`) memakai **path placeholder**. Untuk server ini path yang benar adalah **`/var/www/koneksi`**.

---

## Bagian A — Cek Kondisi Awal (read-only, aman dijalankan kapan pun)

### Step 1 — Login & masuk ke folder aplikasi

```bash
ssh oot@IP_VPS
cd /var/www/koneksi
php artisan --version
```

✅ **Lolos**: menampilkan `Laravel Framework 12.x`.
❌ **Gagal**: muncul error — jangan lanjut, perbaiki dulu aplikasi (cek `.env`, `php artisan optimize:clear`).

### Step 2 — Cek service supervisor

```bash
systemctl is-active supervisor
supervisorctl status
```

✅ **Lolos**: `active`, dan daftar program memuat `lms-worker:... RUNNING`.
❌ **Masalah**: `inactive` / `unknown`, atau `unix:///var/run/supervisor.sock no such file` → supervisor belum jalan/terinstall → lanjut **Bagian B (Step 7)**.

### Step 3 — Cek config & proses queue worker

```bash
ls -la /etc/supervisor/conf.d/
grep -rn "queue:work" /etc/supervisor/conf.d/
pgrep -af "queue:work"
```

✅ **Lolos**: ada file conf dengan `command=php /var/www/koneksi/artisan queue:work ...` **dan** `pgrep` menampilkan ≥1 proses.
❌ **Masalah**: tidak ada conf, path conf salah (`/var/www/lms` dsb), atau tidak ada proses → lanjut **Bagian B (Step 8–10)**.

### Step 4 — Cek service cron

```bash
systemctl is-active cron
```

✅ **Lolos**: `active`.
❌ **Masalah**: tidak aktif → `systemctl enable cron && systemctl start cron`.

### Step 5 — Cek crontab (apakah schedule:run sudah terpasang)

```bash
crontab -l
grep -rn "schedule:run" /etc/crontab /etc/cron.d/ /var/spool/cron/ 2>/dev/null
```

✅ **Lolos**: ada baris `* * * * * cd /var/www/koneksi && php artisan schedule:run >> /dev/null 2>&1`.
❌ **Masalah**: tidak ada, atau path-nya `/var/www/lms` / `/www/wwwroot/lms` (salah) → lanjut **Bagian C (Step 11)**.

### Step 6 — Cek scheduler Laravel, konfigurasi queue & backlog

```bash
php artisan schedule:list
grep -E "^(QUEUE_CONNECTION|CACHE_STORE)" .env
php artisan tinker --execute='echo "jobs pending: ".DB::table("jobs")->count().PHP_EOL."failed jobs : ".DB::table("failed_jobs")->count().PHP_EOL;'
```

✅ **Lolos**: `schedule:list` menampilkan 7 task + waktu "Next Due"; `QUEUE_CONNECTION=database`; jobs pending kecil (0–puluhan).
⚠️ **Catatan**:
- `QUEUE_CONNECTION=sync` → ganti ke `database` di `.env`, lalu `php artisan config:clear`.
- Jobs pending ratusan/ribuan → lihat **Step 15** (backlog dari masa worker mati).
- Timezone server ≠ Asia/Jakarta **bukan masalah** — Laravel memakai `APP_TIMEZONE` aplikasi.

---

## Bagian B — Setup Supervisor (Queue Worker)

> Kerjakan bila Step 2/3 menunjukkan masalah.

### Step 7 — Install supervisor (lewati bila sudah terinstall)

```bash
apt-get update && apt-get install -y supervisor
systemctl enable supervisor
systemctl start supervisor
systemctl is-active supervisor
```

✅ **Lolos**: output `active`.

### Step 8 — Tulis konfigurasi worker

```bash
cd /var/www/koneksi
WEB_USER="www-data"
id www &>/dev/null && WEB_USER="www"
echo "Web user terdeteksi: $WEB_USER"

cat > /etc/supervisor/conf.d/lms-worker.conf <<EOF
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/koneksi/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${WEB_USER}
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/koneksi/storage/logs/worker.log
stopwaitsecs=3600
EOF

cat /etc/supervisor/conf.d/lms-worker.conf
```

✅ **Lolos**: file terisi benar — path semua `/var/www/koneksi`, `user` = `www` atau `www-data`.

### Step 9 — Reload & start worker

```bash
supervisorctl reread
supervisorctl update
supervisorctl restart lms-worker:* 2>/dev/null || supervisorctl start lms-worker:*
```

### Step 10 — Verifikasi worker

```bash
sleep 3
supervisorctl status lms-worker:*
pgrep -af "queue:work"
tail -n 20 /var/www/koneksi/storage/logs/worker.log
```

✅ **Lolos**: `lms-worker_00` dan `lms-worker_01` berstatus **RUNNING**, `pgrep` menampilkan 2 proses.
❌ **BACKOFF / FATAL / tidak muncul**: baca `worker.log` — penyebab umum: path salah, `php` tidak terbaca user web, atau `storage/` tidak bisa ditulis. Perbaiki lalu `supervisorctl restart lms-worker:*`. Bila permission:
```bash
chown -R www:www /var/www/koneksi/storage    # sesuaikan user (www/www-data)
chmod -R 775 /var/www/koneksi/storage
supervisorctl restart lms-worker:*
```

---

## Bagian C — Setup Cron (Laravel Scheduler)

> Kerjakan bila Step 5 menunjukkan masalah.

### Step 11 — Pasang entri cron

```bash
( crontab -l 2>/dev/null | grep -vF "artisan schedule:run"; \
  echo '* * * * * cd /var/www/koneksi && php artisan schedule:run >> /dev/null 2>&1' ) | crontab -
```

Perintah ini **menghapus entri `schedule:run` lama** (termasuk yang path-nya salah) lalu memasang yang benar — idempoten, aman dijalankan berulang.

### Step 12 — Verifikasi crontab

```bash
crontab -l
```

✅ **Lolos**: tepat **satu** baris `* * * * * cd /var/www/koneksi && php artisan schedule:run >> /dev/null 2>&1`.

### Step 13 — Test scheduler manual

```bash
cd /var/www/koneksi && php artisan schedule:run
```

✅ **Lolos**: output `No scheduled commands are ready to run.` (bila belum waktunya) **atau** daftar task yang baru saja dijalankan. Task yang belum due memang dilewati — itu normal.

---

## Bagian D — Verifikasi Akhir & Maintenance

### Step 14 — Verifikasi gabungan

```bash
supervisorctl status lms-worker:*            # harus 2x RUNNING
pgrep -af "queue:work"                       # harus ≥2 proses
crontab -l | grep schedule:run               # 1 baris, path /var/www/koneksi
php artisan schedule:list                    # 7 task terdaftar
```

Semua ✅ → supervisor & cron **sudah oke**. Deploy berikutnya lewat `bash deploy.sh` tidak perlu setup ulang (deploy sudah menjalankan `queue:restart`, worker akan restart sendiri via `autorestart=true`).

### Step 15 — (Opsional) Bersihkan antrian jobs basi

Bila `jobs pending` ratusan/ribuan dari masa worker mati, semua notifikasi basi akan terkirim sekaligus saat worker hidup. Untuk membuangnya:

```bash
cd /var/www/koneksi
php artisan tinker --execute='DB::table("jobs")->truncate(); echo "antrian kosong";'
```

⚠️ `failed_jobs` **jangan** di-truncate tanpa dicek — itu catatan error yang perlu diperiksa (`php artisan queue:failed`).

### Step 16 — Perintah monitoring rutin

```bash
supervisorctl status                                       # cek worker
tail -f /var/www/koneksi/storage/logs/worker.log           # cek aktivitas job
php artisan schedule:list                                  # cek task terjadwal
php artisan tinker --execute='echo DB::table("jobs")->count();'   # cek backlog
php artisan queue:failed                                   # cek job gagal
tail -n 50 /var/www/koneksi/storage/logs/laravel.log       # cek error aplikasi
```

---

## Troubleshooting Cepat

| Gejala | Penyebab | Solusi |
|---|---|---|
| Worker `BACKOFF`/`FATAL` terus | Path salah / PHP tak terbaca / permission | Cek `worker.log` → Step 8–10 |
| Notifikasi tidak terkirim | Worker mati ATAU `QUEUE_CONNECTION=sync` | Step 8–10 + ganti `.env` ke `database` |
| Sertifikat tak dibuat otomatis | Cron tak ada / path salah | Step 11–12, lalu `php artisan schedule:run` manual |
| `schedule:run` jalan tapi task tak dieksekusi | Error scheduler — cek log | `php artisan schedule:list` + `storage/logs/laravel.log` |
| Permission ditolak saat worker menulis | User worker ≠ owner storage | `chown -R www:www /var/www/koneksi/storage` |
| `supervisorctl status` tak kenal `lms-worker` | Config belum di-reload | `supervisorctl reread && supervisorctl update` |
