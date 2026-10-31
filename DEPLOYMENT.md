# Panduan Deployment LokaTaut — WarnaHost Shared Hosting (cPanel)

Dokumen ini menjelaskan prosedur instalasi dan peluncuran sistem **LokaTaut** pada lingkungan *Shared Hosting* cPanel WarnaHost (Paket Starter Unlimited) dengan LiteSpeed Web Server, PHP 8.3+, dan basis data MySQL.

---

## 1. Spesifikasi Target Infrastruktur

| Komponen | Spesifikasi / Konfigurasi |
|---|---|
| **Penyedia Hosting** | WarnaHost Shared Hosting (cPanel) |
| **Alokasi Sumber Daya** | 1 Core vCPU, 2 GB RAM, Unlimited NVMe Storage |
| **Web Server** | LiteSpeed Web Server (LSWS) |
| **Versi PHP** | PHP 8.3.x (ea-php83 atau alt-php83) |
| **Ekstensi PHP Wajib** | `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml` |
| **Engine Basis Data** | MySQL 8.0+ / MariaDB 10.5+ (InnoDB) |
| **Edge Network** | Cloudflare CDN & DNS (SSL Mode: Full Strict) |

---

## 2. Struktur Pemisahan Direktori (Keamanan Mutlak)

Untuk menjaga keamanan kode sumber dan file konfigurasi rahasia (`.env`), file inti Laravel **TIDAK BOLEH** ditaruh langsung di dalam `public_html`. Gunakan pemisahan arsitektur direktori berikut:

```
/home/username/
├── lokataut/                         <-- Direktori Inti Laravel (Di luar web root)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                         <-- Konfigurasi Rahasia (Aman dari peramban)
│   └── artisan
│
└── public_html/                      <-- Web Root Publik (Hanya aset publik)
    ├── css/
    ├── js/
    ├── .htaccess                    <-- Rewrite Engine LiteSpeed
    ├── index.php                    <-- Entry point yang dimodifikasi
    └── robots.txt
```

---

## 3. Langkah-Langkah Deployment Bertahap

### Langkah 1: Persiapan PHP di cPanel
1. Masuk ke cPanel WarnaHost Anda.
2. Cari menu **Select PHP Version** atau **MultiPHP Manager**.
3. Pilih domain `lokataut.com` dan atur versi PHP ke **PHP 8.3**.
4. Masuk ke tab **Extensions** dan pastikan ekstensi berikut telah dicentang/aktif:
   - `pdo_mysql`
   - `openssl`
   - `mbstring`
   - `tokenizer`
   - `xml`
   - `curl`
   - `bcmath`

---

### Langkah 2: Pembuatan Basis Data MySQL
1. Buka menu **MySQL Database Wizard** di cPanel.
2. Buat database baru: misal `username_lokataut`.
3. Buat user database baru: misal `username_lokauser` dengan kata sandi yang kuat.
4. Berikan hak akses penuh (**ALL PRIVILEGES**) kepada user tersebut ke database yang baru dibuat.
5. Catat nama database, user, dan kata sandi untuk konfigurasi `.env`.

---

### Langkah 3: Mengunggah Berkas Proyek

#### Opsi A: Menggunakan Git via Terminal cPanel (Direkomendasikan)
Jika fitur SSH / Terminal aktif di cPanel Anda:
```bash
cd ~
git clone https://github.com/dmsda/lokataut.git lokataut
cd lokataut
composer install --no-dev --optimize-autoloader
```

#### Opsi B: Mengunggah via File Manager / ZIP
1. Buat arsip `.zip` dari lokal proyek (tanpa folder `vendor`, `node_modules`, dan file `.env`).
2. Di File Manager cPanel, buat folder `/home/username/lokataut` dan unggah file ZIP ke sana, lalu ekstrak.
3. Jalankan `composer install --no-dev --optimize-autoloader` melalui Terminal cPanel.

---

### Langkah 4: Memindahkan Isi Folder `public/` ke `public_html/`
1. Pindahkan seluruh isi dari `/home/username/lokataut/public/` ke direktori `/home/username/public_html/`.
2. Buka file `/home/username/public_html/index.php` menggunakan Code Editor cPanel.
3. Sesuaikan path autoloader dan bootstrap:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tentukan apakah aplikasi sedang dalam mode pemeliharaan
if (file_exists($maintenance = __DIR__.'/../lokataut/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Daftarkan Composer Autoloader
require __DIR__.'/../lokataut/vendor/autoload.php';

// Bootstrap Laravel Application
$app = require_once __DIR__.'/../lokataut/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

---

### Langkah 5: Konfigurasi File `.env` Produksi
Buat atau sunting file `/home/username/lokataut/.env`:

```env
APP_NAME=LokaTaut
APP_ENV=production
APP_KEY=base64:GENERATE_KEY_ANDA_DI_SINI
APP_DEBUG=false
APP_URL=https://lokataut.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_lokataut
DB_USERNAME=username_lokauser
DB_PASSWORD=KataSandiMySQLKuatAnda

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Garam Pseudonimisasi IP Pengunjung (Wajib unik & minimal 32 karakter acak)
ANALYTICS_SALT=kombinasi_string_acak_kriptografis_minimal_32_karakter_unik

# Cloudflare Turnstile API Keys (Dapatkan dari Cloudflare Dashboard)
TURNSTILE_SITE_KEY=0x4AAAAAA...
TURNSTILE_SECRET_KEY=0x4AAAAAA...
```

> **PERINGATAN KEAMANAN:**  
> - `APP_DEBUG` **WAJIB** bernilai `false`.
> - `ANALYTICS_SALT` **DILARANG** diubah setelah data berjalan agar riwayat pseudonimisasi IP tetap konsisten dan idempoten.

---

### Langkah 6: Eksekusi Migrasi & Database Seeder
Jalankan perintah berikut melalui Terminal cPanel atau SSH:

```bash
cd /home/username/lokataut
php artisan migrate --force --seed
```

Perintah ini akan menyusun seluruh 11 tabel basis data, membuat akun administrator tunggal, konfigurasi bawaan, slot iklan sponsor, dan artikel dasar.

---

### Langkah 7: Pengaturan Cron Job cPanel (Wajib)
Agregasi statistik harian dan pembersihan sesi sementara memerlukan Cron Job Laravel:

1. Di cPanel, buka menu **Cron Jobs**.
2. Pada bagian *Common Settings*, pilih **Once Per Minute (`* * * * *`)**.
3. Masukkan perintah berikut:
   ```bash
   /usr/local/bin/php /home/username/lokataut/artisan schedule:run >> /dev/null 2>&1
   ```
4. Klik **Add New Cron Job**.

---

### Langkah 8: Optimasi Cache Laravel
Jalankan perintah optimasi untuk mempercepat respon LiteSpeed:

```bash
cd /home/username/lokataut
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Konfigurasi Cloudflare Edge Network

Untuk melindungi domain dari serangan DDoS dan serangan bot liar:

1. **DNS Management:**
   - Arahkan A Record `@` dan `www` ke alamat IP server WarnaHost dengan status **Proxied (Awan Jingga)**.
2. **SSL / TLS Encryption Mode:**
   - Atur mode enkripsi SSL ke **Full (Strict)**.
   - Pasang sertifikat SSL gratis (cPanel AutoSSL / Let's Encrypt) di server WarnaHost.
3. **Turnstile Configuration:**
   - Buat widget Cloudflare Turnstile baru untuk domain `lokataut.com`.
   - Salin *Site Key* dan *Secret Key* ke file `.env`.
4. **Security Level:**
   - Atur Security Level ke *Medium* di Cloudflare Dashboard.

---

## 5. Verifikasi Pasca-Deployment

Periksa poin-poin berikut segera setelah instalasi:
- [ ] Buka `https://lokataut.com/` — pastikan halaman publik tampil dengan tema default tanpa error CSS/JS.
- [ ] Buka `https://lokataut.com/login` — masuk dengan akun admin dan lakukan **ganti password** di menu profil.
- [ ] Buat sebuah shortlink pengujian di menu *Tautan Baru*.
- [ ] Buka halaman safelink tautan tersebut di peramban terpisah (Incognito), periksa Turnstile, countdown timer, dan pengalihan ke tujuan.
- [ ] Periksa menu *Security Logs* di admin panel untuk memastikan aktivitas tercatat dengan benar.

---
*Dokumen ini merupakan panduan resmi deployment repository LokaTaut. Versi 1.2.2.*
