# GJS — Go Journal System

Platform pengelolaan jurnal ilmiah berbasis web yang terbuka, aman, dan memenuhi standar internasional.

**Stack:** Laravel 13 · Livewire v4 · Filament v5 · MySQL 8 · Vite 8 · Tailwind CSS v4

---

## Daftar Isi

- [Kebutuhan Server](#kebutuhan-server)
- [Instalasi Lokal (Development)](#instalasi-lokal-development)
- [Deploy ke Server (Production)](#deploy-ke-server-production)
- [Konfigurasi Nginx](#konfigurasi-nginx)
- [Queue Worker (Supervisor)](#queue-worker-supervisor)
- [Cron Scheduler](#cron-scheduler)
- [Update Kode](#update-kode)
- [Troubleshooting](#troubleshooting)

---

## Kebutuhan Server

| Komponen | Versi Minimum |
|---|---|
| PHP | 8.3+ |
| MySQL | 8.0+ (atau MariaDB 10.6+) |
| Node.js | **20.19+** atau 22.12+ |
| Composer | 2.x |
| Nginx | 1.18+ |
| OS | Ubuntu 22.04 LTS / 24.04 LTS |

**RAM minimum:** 1 GB &nbsp;|&nbsp; **Rekomendasi:** 2–4 GB  
**Storage minimum:** 20 GB SSD

---

## Instalasi Lokal (Development)

```bash
# 1. Clone repositori
git clone https://github.com/yourorg/gjs.git
cd gjs

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies & build asset
npm install
npm run dev   # atau: npm run build

# 4. Salin file environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_HOST=127.0.0.1
# DB_DATABASE=gjs
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Buat symlink storage
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

Akses di: `http://127.0.0.1:8000`  
Admin panel: `http://127.0.0.1:8000/admin`

---

## Deploy ke Server (Production)

### Langkah 1 — Persiapkan Server

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 + ekstensi yang dibutuhkan
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-cli \
  php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl \
  php8.3-redis php8.3-fileinfo php8.3-tokenizer

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS (WAJIB v20+, Vite 8 tidak support v18)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs

# Verifikasi versi
node --version    # pastikan v20.x.x
npm --version
php --version     # pastikan 8.3.x
composer --version

# Install Supervisor (untuk queue)
sudo apt install -y supervisor

# Install Redis (opsional tapi disarankan)
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

### Langkah 2 — Setup Database

```bash
sudo mysql -u root -p

# Di dalam MySQL:
CREATE DATABASE gjs_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gjs_user'@'localhost' IDENTIFIED BY 'password_kuat_disini';
GRANT ALL PRIVILEGES ON gjs_prod.* TO 'gjs_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 3 — Upload Kode

```bash
# Opsi A: via Git
git clone https://github.com/yourorg/gjs.git /home/wwwroot/namadomain.id
cd /home/wwwroot/namadomain.id

# Opsi B: via scp dari lokal
scp -r /path/lokal/gjs/ user@server:/home/wwwroot/namadomain.id/
```

### Langkah 4 — Install Dependencies & Build

```bash
cd /home/wwwroot/namadomain.id

# PHP dependencies (tanpa dev tools)
composer install --no-dev --optimize-autoloader

# Node dependencies & build asset Vite
npm ci
npm run build
```

> **Pastikan `npm run build` berhasil** — ini menghasilkan folder `public/build/` yang wajib ada.  
> Error `ViteManifestNotFoundException` artinya langkah ini belum dijalankan.

### Langkah 5 — Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
nano .env
```

Ubah nilai berikut di `.env`:

```env
APP_NAME="Go Journal System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://namadomain.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=gjs_prod
DB_USERNAME=gjs_user
DB_PASSWORD=password_kuat_disini

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.namadomain.id
MAIL_PORT=587
MAIL_USERNAME=noreply@namadomain.id
MAIL_PASSWORD=password_mail
MAIL_FROM_ADDRESS=noreply@namadomain.id
MAIL_FROM_NAME="${APP_NAME}"
```

### Langkah 6 — Migrasi & Storage

```bash
php artisan migrate --force
php artisan storage:link
```

### Langkah 7 — Permission Folder

```bash
sudo chown -R www-data:www-data /home/wwwroot/namadomain.id
sudo chmod -R 755 /home/wwwroot/namadomain.id
sudo chmod -R 775 /home/wwwroot/namadomain.id/storage
sudo chmod -R 775 /home/wwwroot/namadomain.id/bootstrap/cache
```

### Langkah 8 — Optimize untuk Produksi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
```

---

## Konfigurasi Nginx

Buat file konfigurasi baru:

```bash
sudo nano /etc/nginx/sites-available/namadomain.id
```

Isi dengan:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name namadomain.id www.namadomain.id;
    root /home/wwwroot/namadomain.id/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;
    client_max_body_size 25M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/namadomain.id /etc/nginx/sites-enabled/
sudo nginx -t          # pastikan tidak ada error
sudo systemctl reload nginx
```

### Install SSL (HTTPS)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d namadomain.id -d www.namadomain.id
```

Certbot akan otomatis update konfigurasi Nginx untuk HTTPS.

---

## Queue Worker (Supervisor)

```bash
sudo nano /etc/supervisor/conf.d/gjs-worker.conf
```

```ini
[program:gjs-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/wwwroot/namadomain.id/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=/home/wwwroot/namadomain.id
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/home/wwwroot/namadomain.id/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gjs-worker:*
sudo supervisorctl status   # pastikan RUNNING
```

---

## Cron Scheduler

```bash
sudo crontab -e -u www-data
```

Tambahkan baris berikut:

```
* * * * * cd /home/wwwroot/namadomain.id && php artisan schedule:run >> /dev/null 2>&1
```

---

## Update Kode

Setiap kali ada update dari repositori:

```bash
cd /home/wwwroot/namadomain.id

git pull

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force
php artisan optimize:clear
php artisan optimize

sudo supervisorctl restart gjs-worker:*
```

---

## Troubleshooting

### `ViteManifestNotFoundException` — manifest.json tidak ditemukan

Asset belum di-build. Jalankan:
```bash
npm ci && npm run build
```

### `ReferenceError: CustomEvent is not defined` saat `npm run build`

Node.js versi terlalu lama (v18). Upgrade ke v20:
```bash
sudo apt remove -y nodejs
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs
node --version   # pastikan v20.x.x
rm -rf node_modules
npm ci && npm run build
```

### Error 500 setelah deploy

```bash
# Cek log error
tail -f storage/logs/laravel.log

# Pastikan permission benar
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan optimize:clear
```

### Halaman tampil kosong / CSS tidak muncul

```bash
# Pastikan symlink storage ada
php artisan storage:link

# Pastikan build berhasil
ls public/build/   # harus ada manifest.json dan file asset
```

### Queue tidak jalan

```bash
sudo supervisorctl status
sudo supervisorctl restart gjs-worker:*
tail -f storage/logs/worker.log
```

### Tidak bisa login setelah deploy

```bash
php artisan config:clear
php artisan cache:clear
php artisan session:table   # jika pakai session database
```

---

## Lisensi

Hak cipta © 2026 Gudangsoft. Seluruh hak dilindungi.
