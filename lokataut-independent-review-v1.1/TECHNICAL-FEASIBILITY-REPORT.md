# Laporan Kelayakan Teknis & Batasan Infrastruktur LokaTaut v1.1

> **Status Kelayakan:** `CONDITIONALLY FEASIBLE (WITH DEPLOYMENT FALLBACK)`  
> **Tanggal Evaluasi:** 31 Oktober 2026  
> **Auditor:** Senior Software Architect, Database Architect, & DevOps Engineer  
> **Target Infrastruktur:** WarnaHost Starter Unlimited (cPanel, LiteSpeed, CloudLinux 1 vCPU / 2 GB RAM, MySQL 8.0+)

---

## 1. Executive Summary Kelayakan Teknis

Evaluasi kelayakan teknis menyimpulkan bahwa pengembangan LokaTaut menggunakan **Laravel 13.x** dan **PHP 8.3+** di atas hosting **WarnaHost Starter Unlimited** berstatus **LAYAK SECARA BERSYARAT (*Conditionally Feasible*)**.

Kelebihan utama arsitektur LokaTaut adalah ketiadaan dependensi terhadap *in-memory stores* berat seperti Redis atau daemon persistent supervisor yang memerlukan akses *root*. Aplikasi dirancang murni berbasis Blade, MySQL, dan stateless HTTP request yang sangat kompatibel dengan lingkungan shared hosting.

Namun demikian, audit menemukan **3 kendala operasional infrastruktur** yang memerlukan mitigasi teknis sebelum fase deployment:
1. **Risiko Memori Composer di CloudLinux (REV-OPS-001 - P1):** Kapasitas memori fisik LVE 2 GB rentan terlampaui saat eksekusi `composer install` di server cPanel.
2. **Risiko Akumulasi Inode File Cache (REV-PERF-001 - P2):** Penggunaan `CACHE_STORE=file` untuk rate limiting ribuan request dapat menghabiskan kuota inode file hosting.
3. **Risiko Server-Side Caching LiteSpeed (REV-OPS-002 - P2):** Web server LiteSpeed dapat meng-cache response halaman safelink secara tidak sengaja tanpa header anti-cache yang tepat.

---

## 2. Analisis Kompatibilitas Stack & Ekstensi PHP

### 2.1 Persyaratan Runtime Laravel 13 vs Kemampuan WarnaHost
Laravel 13 menetapkan prasyarat lingkungan runtime PHP 8.3 atau lebih baru. Pada cPanel WarnaHost, versi PHP dikelola melalui fitur **MultiPHP Manager** dan **Select PHP Version (CloudLinux PHP Selector)**.

| Komponen Stack | Persyaratan Laravel 13 | Ketersediaan di WarnaHost | Status Kompatibilitas |
|---|---|---|:---:|
| **PHP Version** | PHP 8.3 s/d 8.5 | PHP 8.3 & 8.4 tersedia via cPanel Selector | **COMPATIBLE** |
| **Web Server** | Nginx / Apache / LiteSpeed | LiteSpeed Web Server Enterprise | **COMPATIBLE** |
| **Database** | MySQL 8.0+ / MariaDB 10.5+ | MySQL 8.0.x / MariaDB 10.6+ | **COMPATIBLE** |
| **Node.js** | Tidak diperlukan di production | N/A (Build asset dijalankan di mesin lokal) | **COMPATIBLE** |
| **Composer** | Composer v2.5+ | Tersedia di terminal SSH cPanel | **COMPATIBLE** |

### 2.2 Verifikasi Ekstensi PHP Wajib
Berdasarkan dokumentasi resmi Laravel 13, ekstensi berikut wajib aktif:
- `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `tokenizer`, `xml`.
- *Status:* Seluruh ekstensi ini merupakan modul inti standar (*compiled-in*) pada PHP Selector CloudLinux di WarnaHost.

---

## 3. Anggaran Sumber Daya (Resource Budgeting & Kapasitas Beban)

### 3.1 Profil Resource Paket WarnaHost Starter Unlimited
- **CPU Limit:** 100% dari 1 vCPU (CloudLinux LVE Speed limit).
- **RAM Limit:** 2048 MB (2 GB Physical Memory Limit).
- **IOPS Limit:** 1024 KB/s - 2048 KB/s.
- **Entry Processes (EP):** 20 - 30 concurrent processes.
- **Max Processes (NPROC):** 100 processes.
- **File Inodes:** Biasanya dibatasi 250.000 s/d 500.000 files pada paket unlimited.

### 3.2 Estimasi Beban Kerja & Alokasi Memori
- **Safelink View Request (`GET /s/{slug}`):**
  - Footprint memori per request: ~28 MB.
  - Waktu eksekusi rata-rata: 35–60 ms (dengan LiteSpeed PHP LSAPI).
  - Throughput teoritis pada 1 vCPU: ~30-40 request per detik tanpa bottleneck.
- **Proceed Redirect Request (`POST /sl/{token}/proceed`):**
  - Eksekusi verifikasi Turnstile (cURL HTTP call eksternal ke Cloudflare: ~120–250 ms).
  - Waktu eksekusi total: ~180–300 ms.
  - Throughput teoritis: ~15-20 request per detik per proses PHP yang tersedia.
- **Beban Puncak 50.000 Kunjungan/Hari:**
  - Rata-rata: ~0.58 request per detik.
  - Jam sibuk (*peak hour* 5x rata-rata): ~3-5 request per detik.
  - **Kesimpulan:** Paket 1 vCPU / 2 GB RAM **sangat mencukupi** beban trafik harian yang ditargetkan, asalkan tidak terjadi kebocoran memori atau query database N+1 yang berat.

---

## 4. Analisis Risiko Operasional & Prosedur Mitigasi

### 4.1 Risiko Crash Memori Composer di Shared Hosting (REV-OPS-001)

#### Masalah:
Proses `composer install --no-dev --optimize-autoloader` membutuhkan perhitungan *dependency tree* yang intensif. Composer dapat menggunakan memori puncak melebihi 1.2 GB hingga 2.2 GB selama proses resolusi paket Laravel 13. Di lingkungan CloudLinux, jika penggunaan memori mendekati batas LVE (2 GB), kernel CloudLinux akan langsung mengirim sinyal `SIGKILL` (*Out Of Memory Killer*), menyebabkan proses kompilasi mati tiba-tiba (*Killed*).

#### Prosedur Mitigasi: Fallback Deployment Workflow (Rekomendasi Wajib)
AI Coding Agent dan Administrator wajib mendokumentasikan dan memprioritaskan alur **Pre-Compiled Production Bundle**:

```
[ Local Dev / GitHub Actions ]
       |
       | 1. composer install --no-dev --optimize-autoloader
       | 2. npm run build (atau kompilasi aset css/js)
       | 3. zip -r lokataut-release-v1.x.zip app/ bootstrap/ config/ database/ public/ resources/ routes/ vendor/ artisan composer.json
       |
       v
[ Upload via cPanel / SCP / rsync ]
       |
       v
[ Server cPanel WarnaHost ]
       | 4. Unzip ke direktori root aplikasi
       | 5. php artisan migrate --force
       | 6. php artisan config:cache && php artisan route:cache && php artisan view:cache
```
*Dengan metode ini, server shared hosting sama sekali tidak perlu menjalankan `composer install` secara langsung, menghilangkan risiko OOM 100%.*

---

### 4.2 Risiko Kehabisan Inode File Cache (REV-PERF-001)

#### Masalah:
Dokumentasi menetapkan `CACHE_STORE=file`. Laravel menyimpan rate limiting (`throttle:60,1`) dalam file cache fisik berhierarki dua tingkat pada `storage/framework/cache/data/xx/yy/xxyy...`. Setiap IP unik menghasilkan minimal 1 file cache. Dalam 30 hari tanpa pembersihan rutin, puluhan ribu file cache kedaluwarsa dapat menumpuk, menyebabkan kuota *inode* cPanel habis (*Disk Full: No space left on device* meskipun sisa kapasitas GB masih besar).

#### Prosedur Mitigasi:
1. **Automated Prune Cronjob:**
   Daftarkan cronjob cPanel harian pada pukul 03:00 untuk membuang file cache yang usang:
   ```bash
   0 3 * * * find /home/username/lokataut/storage/framework/cache/data -type f -mtime +2 -delete
   ```
2. **Evaluasi Driver Database:**
   Jika batas inode WarnaHost di bawah 250.000 file, alihkan `CACHE_STORE=database` dengan membuat tabel `cache` standar Laravel (`php artisan cache:table`).

---

### 4.3 Pencegahan Server-Level Caching LiteSpeed (REV-OPS-002)

#### Masalah:
LiteSpeed Web Server Enterprise memiliki modul cache bawaan yang sangat agresif. Jika web server meng-cache rute `/s/{slug}` tanpa memeriksa cookie atau parameter dinamis, pengunjung B akan disajikan halaman HTML yang berisi `session_token` milik pengunjung A.

#### Prosedur Mitigasi:
1. **Penerapan Header Khusus pada Middleware:**
   Pada file `app/Http/Middleware/SafelinkSessionMiddleware.php`, sematkan header respon:
   ```php
   $response = $next($request);
   $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate, max-age=0');
   $response->headers->set('Pragma', 'no-cache');
   $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
   $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
   return $response;
   ```
2. **Konfigurasi `.htaccess` Root:**
   ```apache
   <IfModule LiteSpeed>
       RewriteEngine On
       RewriteRule ^(s|sl)/ - [E=Cache-Control:no-cache]
   </IfModule>
   ```

---

## 5. Daftar Item Belum Terverifikasi (Items NOT VERIFIED)

Sebelum kode pertama dideploy ke server WarnaHost, item-item teknis berikut wajib diverifikasi secara langsung oleh Product Owner / Administrator:

| Item Infrastruktur | Deskripsi Verifikasi | Metode Pengujian | Status Saat Ini |
|---|---|---|:---:|
| **CLI PHP Version vs Web PHP** | Memastikan perintah `php -v` di terminal SSH cPanel mengarah ke PHP 8.3+, bukan default PHP OS server (PHP 7.4 / 8.0). | Jalankan `which php` dan `php -v` via SSH. | `NOT VERIFIED` |
| **Batas Kuota Inode cPanel** | Mengetahui batas pasti kuota inode akun WarnaHost Starter Unlimited. | Cek statistik sidebar cPanel (*File Usage* / Inodes). | `NOT VERIFIED` |
| **Eksekusi Cronjob Background** | Memastikan cPanel WarnaHost mengizinkan cronjob memanggil `php -d memory_limit=512M /path/artisan schedule:run`. | Uji satu perintah cron dummy di cPanel. | `NOT VERIFIED` |
| **Mode Cloudflare SSL** | Memastikan SSL di Cloudflare disetel ke mode **Full (Strict)** dengan Origin Certificate terpasang di cPanel. | Cek tab SSL/TLS di dashboard Cloudflare. | `NOT VERIFIED` |

---
*Laporan kelayakan ini menjadi dasar teknis dalam memitigasi kendala hosting sebelum implementasi koding Phase 1.*

