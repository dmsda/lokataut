# Deployment & Operations Guide — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-13 |
| **Versi** | 1.2.2 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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

## 1. Prasyarat Lingkungan Hosting & Matriks Verifikasi

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        Spesifikasi Server Target Produksi                              │
├──────────────────────┬──────────────────────────────────────┬──────────────────────────┤
│ Komponen             │ Target Spesifikasi                   │ Status Verifikasi        │
├──────────────────────┼──────────────────────────────────────┼──────────────────────────┤
│ Provider Hosting     │ WarnaHost (PT Warna Data Multimedia) │ PROVIDER CLAIM VERIFIED  │
│ Paket Layanan        │ Starter Unlimited                    │ PROVIDER CLAIM VERIFIED  │
│ Resource Allocation  │ 1 CPU Core, 2 GB RAM, NVMe SSD       │ PROVIDER CLAIM VERIFIED  │
│ Control Panel        │ cPanel                               │ PROVIDER CLAIM VERIFIED  │
│ Web Server           │ LiteSpeed Web Server                 │ PROVIDER CLAIM VERIFIED  │
│ Versi PHP Web        │ PHP 8.3+ via Select PHP Version      │ NOT VERIFIED (Belum uji) │
│ Versi PHP CLI        │ PHP 8.3+ via SSH Terminal            │ NOT VERIFIED (Belum uji) │
│ Ekstensi PHP Wajib   │ pdo_mysql, mbstring, openssl, curl.. │ NOT VERIFIED (Belum uji) │
│ Composer CLI         │ Composer 2.x terinstal di shell      │ NOT VERIFIED (Belum uji) │
│ Versi Database       │ MySQL 8.0+ / MariaDB 10.5+           │ NOT VERIFIED (Belum uji) │
│ Akses Shell SSH      │ Terminal cPanel / OpenSSH Port 22/222│ NOT VERIFIED (Belum uji) │
└──────────────────────┴──────────────────────────────────────┴──────────────────────────┘
```

> **KLASIFIKASI BUKTI VERIFIKASI:**
> - `PROVIDER CLAIM VERIFIED`: Berdasarkan spesifikasi paket resmi WarnaHost Starter di website/brosur penyedia.
> - `ACCOUNT ENVIRONMENT VERIFIED`: Berdasarkan pengujian riil yang dieksekusi pada akun cPanel aktif.
> - `NOT VERIFIED`: Belum diuji secara langsung pada akun pengguna aktual. (Dilarang mengklaim fitur lingkungan aktual sudah berhasil sebelum pengujian dilakukan).

### 1.1 Daftar Perintah Pre-Flight Verification (Jalankan via cPanel Terminal / SSH)

Sebelum memulai deployment rilis awal, administrator/DevOps wajib menjalankan perintah verifikasi berikut di cPanel Terminal/SSH dan mencatat hasilnya:

```bash
# 1. Cek versi PHP CLI aktif
php -v

# 2. Cek alokasi memori CLI
php -r "echo 'Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;"

# 3. Cek seluruh ekstensi PHP yang dibutuhkan Laravel 13
php -m | grep -E 'pdo_mysql|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|curl|fileinfo|zip'

# 4. Cek ketersediaan Composer
composer --version || which composer

# 5. Cek versi Database Client & Konektivitas
mysql --version

# 6. Cek struktur direktori awal dan document root
ls -la ~/public_html

# 7. Cek kuota Inode akun cPanel
df -i ~
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

### Langkah 3: Menyiapkan Kode & Dependensi (Dua Opsi Alur)

Terdapat 2 opsi alur deployment yang didukung:
- **Opsi A (Standard SSH Deployment):** Mengunduh dependensi via terminal SSH cPanel.
- **Opsi B (Fallback Pre-Built Vendor Deployment - Direkomendasikan jika OOM):** Membangun dependensi di lingkungan lokal/CI dan mengunggah paket siap rilis.

#### Opsi A: Standard SSH Deployment (Jika Memori cPanel Mencukupi)
```bash
cd ~
mkdir -p bin
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

cd /home/cpanelusername/lokataut
cp .env.example .env
php -d memory_limit=1024M ~/bin/composer install --no-dev --optimize-autoloader

# PENTING: Jalankan key:generate HANYA SATU KALI saat instalasi awal pertama kali!
# JANGAN PERNAH menjalankan key:generate pada rilis berkala karena akan membatalkan
# seluruh enkripsi destination_url pada basis data yang sudah ada!
php artisan key:generate
```

#### Opsi B: Fallback Pre-Built Vendor Deployment (Mitigasi CloudLinux LVE Memory Limit)
Pada paket shared hosting WarnaHost Starter (2 GB RAM fisik LVE), eksekusi live `composer install` berisiko dimatikan paksa (*Killed/SIGKILL*) saat kompilasi pohon dependensi Laravel 13. Untuk memitigasi kendala ini, gunakan prosedur rilis pre-built:

1. **Build Dependensi di Lingkungan Lokal / CI:**
   - Lingkungan lokal **WAJIB** menggunakan versi PHP yang cocok dengan server produksi (PHP 8.3.x) dan ekstensi yang identik.
   - Jalankan kompilasi teroptimasi di mesin lokal:
     ```bash
     composer install --no-dev --optimize-autoloader --no-interaction
     ```
2. **Validasi File Composer Lock & Platform Compatibility:**
   - Pastikan `composer.lock` sinkron 100% dan tidak menggunakan flag bypass berbahaya.
3. **Packaging Archive Rilis:**
   - Kemas aplikasi (termasuk folder `vendor/` dan aset build) ke dalam arsip:
     ```bash
     zip -r lokataut-release-v1.2.zip \
         app/ bootstrap/ config/ database/ public/ resources/ routes/ vendor/ artisan composer.json composer.lock
     ```
4. **Unggah & Ekstrak di Server cPanel:**
   - Unggah `lokataut-release-v1.2.zip` melalui cPanel File Manager atau SCP/rsync ke `/home/cpanelusername/`.
   - Ekstrak ke direktori `/home/cpanelusername/lokataut/`.
5. **Evaluasi Risiko Deployment Opsi B:**
   > ⚠️ **CATATAN REALISTIS RESIKO:** Metode pre-built ini mengeliminasi lonjakan konsumsi memori Composer di server cPanel, namun **tidak menghilangkan seluruh risiko deployment**. Risiko baru yang tetap harus diawasi mencakup:
   > - *PHP Version / ABI Mismatch:* Jika binary vendor dikompilasi pada PHP 8.4 lokal sementara server menjalankan PHP 8.3, error fatal syntax/fungsi dapat terjadi.
   > - *Missing Extensions:* Vendor mungkin bergantung pada ekstensi PHP lokal yang tidak aktif di cPanel.
   > - *File Permissions:* File arsip yang diekstrak via cPanel wajib diperiksa kembali hak aksesnya (`chmod -R 775 storage bootstrap/cache`).

---

### Langkah 4: Pencadangan Database & Prosedur Rollback Cepat (Disaster Recovery)

> ⚠️ **BATASAN KEAMANAN DATABASE ROLLBACK:**  
> Menjalankan `php artisan migrate:rollback` **TIDAK SELALU AMAN** dan tidak dapat mengembalikan data jika migrasi baru melakukan `DROP COLUMN`, `RENAME COLUMN`, atau transformasi data parsial.  
> **Jaring pengaman mutlak:** Wajib membuat snapshot dump database via `mysqldump` sebelum mengeksekusi rilis kode baru atau migrasi database.

1. **Backup Database & Kode Sebelum Rilis:**
   ```bash
   cd /home/cpanelusername
   # 1. Snapshot database penuh sebelum migrasi
   mysqldump -u cpuser_lokauser -p cpuser_lokataut > db-backup-predeploy-$(date +%F_%H%M%S).sql

   # 2. Backup folder aplikasi rilis sebelumnya
   cp -R lokataut lokataut-backup-previous
   ```

2. **Langkah Pemulihan Bencana (Rollback Procedure):**
   Jika terjadi crash sistem atau kegagalan fatal pasca rilis:
   ```bash
   cd /home/cpanelusername
   # Kembalikan kode aplikasi stabil
   mv lokataut lokataut-failed-$(date +%F_%H%M%S)
   mv lokataut-backup-previous lokataut

   # Restore penuh basis data dari snapshot jika sempat terjadi perubahan skema/data
   mysql -u cpuser_lokauser -p cpuser_lokataut < db-backup-predeploy-*.sql

   cd /home/cpanelusername/lokataut
   php artisan optimize:clear
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```

---

### Langkah 5: Migrasi Database & Seeder
```bash
cd /home/cpanelusername/lokataut
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=SettingsSeeder --force
```

### Langkah 6: Konfigurasi `public_html/index.php` & `.htaccess` LiteSpeed
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

Tambahkan directive proteksi bypass cache LiteSpeed pada `/home/cpanelusername/public_html/.htaccess`:
```apache
<IfModule LiteSpeed>
    # Cegah caching pada halaman dinamis safelink ber-token
    RewriteRule ^(s|sl)/ - [E=Cache-Control:no-cache]
</IfModule>
```

### Langkah 7: Atur Permissions & Cache Produksi
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
Cron ini menjalankan tugas terjadwal internal:
1. `lokataut:prune-sessions` (Setiap jam pada menit 15: membersihkan sesi kedaluwarsa > 24 jam)
2. `lokataut:aggregate-daily` (Setiap hari pukul 00:05 WIB: agregasi visits ke daily_stats via `Link::withTrashed()`)
3. `lokataut:prune-visits` (Setiap hari pukul 02:00 WIB: membersihkan data mentah visits > 90 hari)
4. `lokataut:prune-cache` (Setiap minggu pada Minggu 03:00 WIB: membersihkan file cache usang di `storage/framework/cache/data` untuk menjaga batas inode cPanel)

