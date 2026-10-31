# Deployment & Operations Guide — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-13 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Senior DevOps Engineer & System Administrator |
| **Target Pembaca** | AI Coding Agent, DevOps, Webmaster |
| **Tujuan** | Memberikan panduan langkah demi langkah deployment aplikasi Laravel 11 ke Shared Hosting WarnaHost Starter Unlimited, konfigurasi DNS Cloudflare, setup SSL, serta pemeliharaan operasional. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Kebutuhan infrastruktur dan batas resource shared hosting |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Setup database MySQL dan artisan migration |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Variabel `.env` dan struktur folder aplikasi |
| [AGENTS.md](../../AGENTS.md) | Larangan mengekspos folder internal Laravel ke public |

---

## 1. Prasyarat Lingkungan Hosting (WarnaHost Starter Unlimited)

```
┌─────────────────────────────────────────────────────────────┐
│             Spesifikasi Server Target Produksi              │
├──────────────────────┬──────────────────────────────────────┤
│ Provider Hosting     │ WarnaHost (PT Warna Data Multimedia) │
│ Paket Layanan        │ Starter Unlimited                    │
│ Resource Allocation  │ 1 CPU Core, 2 GB RAM, NVMe SSD       │
│ Control Panel        │ cPanel                               │
│ Web Server           │ LiteSpeed Web Server                 │
│ Versi PHP Minimal    │ PHP 8.3 (dengan ekstensi lengkap)    │
│ Versi Database       │ MySQL 8.0 / MariaDB 10.5+            │
│ Akses Shell          │ Terminal cPanel / SSH                │
│ Ekstensi PHP Wajib   │ BCMath, Ctype, cURL, DOM, Fileinfo,  │
│                      │ JSON, Mbstring, OpenSSL, PCRE, PDO,  │
│                      │ PDO_MySQL, Tokenizer, XML            │
└──────────────────────┴──────────────────────────────────────┘
```

---

## 2. Arsitektur Struktur Direktori Aman (Security-First Directory Layout)

> ### 🛑 BAHAYA BESAR: JANGAN TARUH SOURCE CODE DI DALAM `public_html`!
> Meletakkan seluruh folder proyek Laravel langsung di dalam `public_html` berpotensi mengekspos file konfigurasi rahasia `.env`, kode sumber, dan log privat ke publik melalui browser.

Struktur penempatan file yang **WAJIB** diterapkan di cPanel:

```
/home/cpanelusername/
│
├── bin/
│   └── composer                     # Composer binary lokal
│
├── lokataut/                        # FOLDER APLIKASI UTAMA (DILUAR public_html!)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                         # AMAN! Tidak dapat diakses via web
│   └── artisan
│
└── public_html/                     # DOCUMENT ROOT DOMAIN lokataut.com
    ├── index.php                    # Dimodifikasi mengarah ke ../lokataut/
    ├── .htaccess                    # LiteSpeed / Apache rules
    ├── css/                         # Asset statis publik
    ├── js/                          # Asset statis publik
    └── robots.txt
```

---

## 3. Prosedur Deployment Langkah-demi-Langkah (Step-by-Step)

### Langkah 1: Pengaturan Versi PHP di cPanel
1. Login ke cPanel WarnaHost.
2. Cari menu **Software** -> **Select PHP Version** (atau **MultiPHP Manager**).
3. Pilih domain `lokataut.com` dan tetapkan ke **PHP 8.3**.
4. Di tab **Extensions**, pastikan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, dan `bcmath` telah dicentang aktif.

### Langkah 2: Pembuatan Basis Data MySQL
1. Buka menu **Databases** -> **MySQL Database Wizard** di cPanel.
2. Buat database baru: misal `cpuser_lokataut`.
3. Buat user database baru: misal `cpuser_lokauser` dengan password kuat (minimal 16 karakter acak).
4. Berikan hak akses penuh (**ALL PRIVILEGES**) kepada user tersebut ke database `cpuser_lokataut`.

### Langkah 3: Menyiapkan Composer Lokal (Jika Belum Tersedia)
Buka menu **Advanced** -> **Terminal** di cPanel dan jalankan perintah:
```bash
cd ~
mkdir -p bin
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
composer --version
```

### Langkah 4: Unggah & Instalasi Kode Sumber
1. Clone repository atau unggah arsip zip proyek ke `/home/cpanelusername/lokataut`.
2. Masuk ke direktori aplikasi via Terminal:
```bash
cd /home/cpanelusername/lokataut
```
3. Salin file environment dan atur permissions:
```bash
cp .env.example .env
```
4. Jalankan instalasi dependensi tanpa dev packages:
```bash
php -d memory_limit=-1 ~/bin/composer install --no-dev --optimize-autoloader
```
5. Generate application encryption key:
```bash
php artisan key:generate
```

### Langkah 5: Konfigurasi File `.env`
Buka file `/home/cpanelusername/lokataut/.env` dan sesuaikan parameter berikut:
```env
APP_NAME=LokaTaut
APP_ENV=production
APP_KEY=base64:... (otomatis dari key:generate)
APP_DEBUG=false
APP_URL=https://lokataut.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpuser_lokataut
DB_USERNAME=cpuser_lokauser
DB_PASSWORD=PasswordKuatAnda

SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=daily
LOG_LEVEL=warning

TURNSTILE_SITE_KEY=0x4AAAAAA...
TURNSTILE_SECRET_KEY=0x4AAAAAA...
TURNSTILE_ENABLED=true
```

### Langkah 6: Jalankan Migrasi & Seeder Database
```bash
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=SettingsSeeder --force
```

### Langkah 7: Hubungkan Public Assets ke `public_html`
1. Pindahkan isi folder `/home/cpanelusername/lokataut/public/*` ke dalam `/home/cpanelusername/public_html/`.
2. Edit file `/home/cpanelusername/public_html/index.php` dan sesuaikan path autoload serta bootstrap Laravel:
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tentukan path ke folder aplikasi di luar public_html
require __DIR__.'/../lokataut/vendor/autoload.php';

$app = require_once __DIR__.'/../lokataut/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### Langkah 8: Atur Hak Akses Folder (Permissions)
Pastikan web server dapat menulis ke folder storage dan cache:
```bash
cd /home/cpanelusername/lokataut
chmod -R 775 storage bootstrap/cache
```

### Langkah 9: Optimasi Cache Laravel untuk Produksi
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Konfigurasi Cloudflare DNS, SSL & Turnstile

1. **DNS Management:** Tambahkan record A pada Cloudflare yang mengarah ke IP shared hosting WarnaHost dengan status **Proxied (Awan Jingga)**:
   - `A @ -> [IP_WARNAHOST]`
   - `CNAME www -> lokataut.com`
2. **Pengaturan SSL/TLS:** Tetapkan mode SSL ke **Full (Strict)**. Terbitkan sertifikat Cloudflare Origin Certificate (berlaku 15 tahun) dan pasang di menu **SSL/TLS Manager** cPanel.
3. **Turnstile Widget:** Daftarkan hostname `lokataut.com` pada dashboard Cloudflare Turnstile, ambil *Site Key* dan *Secret Key*, lalu masukkan ke `.env`.

---

## 5. Konfigurasi Cron Job Terjadwal (cPanel Scheduled Tasks)

Agar fitur pembersihan sesi kedaluwarsa dan agregasi statistik harian berjalan otomatis, tambahkan entri Cron Job di cPanel:

1. Buka menu **Advanced** -> **Cron Jobs** di cPanel.
2. Pada bagian *Common Settings*, pilih **Once Per Minute (`* * * * *`)** (atau interval minimal yang diizinkan hosting).
3. Masukkan perintah berikut:
```bash
/opt/alt/php83/usr/bin/php -d register_argc_argv=On /home/cpanelusername/lokataut/artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Prosedur Pemulihan Bencana & Cadangan (Backup & Disaster Recovery)

1. **Cadangan Harian Otomatis:**
   - Database MySQL di-backup melalui cPanel Backup Wizard atau command mysqldump terjadwal:
```bash
mysqldump -u cpuser_lokauser -p'PasswordKuat' cpuser_lokataut | gzip > /home/cpanelusername/backups/db-$(date +%F).sql.gz
```
2. **Prosedur Rollback saat Deployment Gagal:**
   - Bersihkan cache: `php artisan optimize:clear`.
   - Rollback migrasi database: `php artisan migrate:rollback --step=1`.
   - Kembalikan backup database dari arsip `.sql.gz` terbaru.

