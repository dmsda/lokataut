# Deployment & Operations Guide — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-13 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Senior DevOps Engineer & System Administrator |
| **Target Pembaca** | AI Coding Agent, DevOps, Webmaster |
| **Tujuan** | Memberikan panduan deployment aplikasi Laravel 13 ke Shared Hosting WarnaHost Starter Unlimited, konfigurasi DNS Cloudflare, setup SSL, serta pemeliharaan operasional. |

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
│ Versi PHP Minimal    │ PHP 8.3+ (Laravel 13 Compatible)     │
│ Versi Database       │ MySQL 8.0+ / MariaDB 10.5+           │
│ Akses Shell          │ Terminal cPanel / SSH                │
└──────────────────────┴──────────────────────────────────────┘
```

---

## 2. Arsitektur Struktur Direktori Aman (Security-First Directory Layout)

> ### 🛑 ATURAN KEAMANAN: SOURCE CODE DILARANG BERADA DI DALAM `public_html`!
> Meletakkan seluruh folder proyek Laravel di dalam `public_html` berisiko mengekspos file konfigurasi rahasia `.env`, kode sumber, dan log privat ke publik.

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
│   ├── .env                         # AMAN! Terisolasi dari web root
│   └── artisan
│
└── public_html/                     # DOCUMENT ROOT DOMAIN lokataut.com
    ├── index.php                    # Memuat ../lokataut/vendor/autoload.php
    ├── .htaccess                    # LiteSpeed / Apache rules
    ├── css/                         # Asset statis publik
    ├── js/                          # Asset statis publik
    └── robots.txt
```

---

## 3. Langkah-demi-Langkah Deployment Produksi

### Langkah 1: Pengaturan Versi PHP di cPanel
1. Login ke cPanel WarnaHost.
2. Cari menu **Software** -> **Select PHP Version**.
3. Tetapkan domain `lokataut.com` ke **PHP 8.3**.
4. Di tab **Extensions**, pastikan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `bcmath`, dan `tokenizer` aktif.

### Langkah 2: Pembuatan Basis Data MySQL
1. Buka menu **Databases** -> **MySQL Database Wizard** di cPanel.
2. Buat database: misal `cpuser_lokataut`.
3. Buat user database: misal `cpuser_lokauser` dengan password kuat (minimal 16 karakter acak).
4. Berikan hak akses penuh (**ALL PRIVILEGES**).

### Langkah 3: Menyiapkan Composer Lokal & Unggah Kode
Buka **Terminal cPanel** dan jalankan:
```bash
cd ~
mkdir -p bin
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Masuk ke folder proyek
cd /home/cpanelusername/lokataut
cp .env.example .env
php -d memory_limit=-1 ~/bin/composer install --no-dev --optimize-autoloader
php artisan key:generate
```

### Langkah 4: Migrasi Database & Seeder
```bash
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=SettingsSeeder --force
```

### Langkah 5: Konfigurasi `public_html/index.php`
Edit file `/home/cpanelusername/public_html/index.php`:
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Arahkan ke folder aplikasi di luar public_html
require __DIR__.'/../lokataut/vendor/autoload.php';

$app = require_once __DIR__.'/../lokataut/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### Langkah 6: Atur Permissions & Cache Produksi
```bash
cd /home/cpanelusername/lokataut
chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Konfigurasi Cron Job Terjadwal cPanel

Buka menu **Advanced** -> **Cron Jobs** di cPanel, masukkan jadwal per menit (`* * * * *`):
```bash
/opt/alt/php83/usr/bin/php -d register_argc_argv=On /home/cpanelusername/lokataut/artisan schedule:run >> /dev/null 2>&1
```
Cron ini menjalankan tugas:
1. `lokataut:prune-sessions` (Setiap jam)
2. `lokataut:aggregate-stats` (Setiap hari pukul 00:05 WIB)
3. `lokataut:prune-visit-logs` (Setiap minggu)

