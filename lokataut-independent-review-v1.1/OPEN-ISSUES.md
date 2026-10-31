# Register Isu Terbuka & Keputusan Tertunda LokaTaut v1.1

> **Status Dokumen:** `ACTIVE OPEN ISSUES REGISTER`  
> **Tanggal Audit:** 31 Oktober 2026  
> **Auditor:** Multi-disciplinary Independent Audit Team  
> **Tujuan:** Mendokumentasikan seluruh ketidakpastian teknis, dependensi eksternal, dan keputusan desain yang membutuhkan persetujuan formal Product Owner.

---

## 1. Ikhtisar Isu Terbuka

Berikut adalah register seluruh isu terbuka (*open issues*) yang teridentifikasi selama audit independen LokaTaut v1.1:

```
+----------------------------------------------------------------------------------------------------------+
| ISSUE ID  | TOPIK KEPUTUSAN             | DAMPAK ARSITEKTUR / SISTEM  | PENGAMBIL KEPUTUSAN | STATUS     |
+-----------+-----------------------------+-----------------------------+---------------------+------------+
| ISSUE-001 | Fitur Ganti Password Admin  | Ketiadaan rotasi kredensial | Product Owner       | OPEN       |
| ISSUE-002 | Hashing Token Sesi Safelink | Defense-in-depth DB leak    | Lead Architect / PO | OPEN       |
| ISSUE-003 | Verifikasi Akun Adsterra    | Monetisasi tertunda/berubah | Product Owner       | PENDING PO |
| ISSUE-004 | Verifikasi PHP 8.3 CLI Host | Gagal eksekusi Artisan CLI  | DevOps / PO         | UNVERIFIED |
| ISSUE-005 | Prosedur Fallback Vendor    | Crash memori saat deploy    | DevOps / Lead Arch  | OPEN       |
| ISSUE-006 | Switch Darurat Turnstile    | Risiko safelink lumpuh total| Lead Architect / PO | OPEN       |
| ISSUE-007 | Rekonsiliasi RTM vs DOC-12  | 17 test case QA hilang      | QA Lead / Lead Arch | OPEN       |
| ISSUE-008 | Header Anti-Cache LiteSpeed | Kebocoran token sesi        | DevOps / Lead Arch  | OPEN       |
| ISSUE-009 | Formal Sign-Off ADR 001-010 | Dokumen berstatus PROPOSED  | Product Owner       | PENDING PO |
+----------------------------------------------------------------------------------------------------------+
```

---

## 2. Rincian Isu Terbuka & Opsi Keputusan

### ISSUE-001: Penambahan Fitur Ganti Kata Sandi Admin (`FR-AUTH-004`)
- **Deskripsi:** Dokumen saat ini tidak menyediakan antarmuka atau rute untuk mengganti kata sandi administrator tunggal.
- **Dampak:** Kredensial hasil seeder awal berisiko tetap digunakan di produksi atau admin harus mengedit hash Bcrypt manual di phpMyAdmin.
- **Opsi yang Dipertimbangkan:**
  - *Opsi A (Direkomendasikan):* Tambahkan `FR-AUTH-004`, rute `GET /admin/profile` dan `PUT /admin/profile/password`, Form Request validasi ketat, dan controller terkait.
  - *Opsi B:* Tanpa UI, admin mengganti password hanya via command artisan custom di terminal cPanel (`php artisan admin:reset-password`).
- **Rekomendasi Auditor:** **Opsi A** (Wajib ada antarmuka web yang aman).
- **Pengambil Keputusan:** Product Owner (Dimas Aditya).
- **Status:** `OPEN`

---

### ISSUE-002: Penyimpanan Token Sesi: Plaintext vs Hashed Token
- **Deskripsi:** `visitor_sessions.token` saat ini didesain menyimpan raw hex 64-karakter secara plaintext.
- **Dampak:** Jika file database dump bocor, penyerang dapat langsung mem-bypass alur safelink yang sedang aktif.
- **Opsi yang Dipertimbangkan:**
  - *Opsi A (Direkomendasikan):* Simpan `token_hash = sha256($rawToken)` di basis data. Raw token hanya dipegang client browser.
  - *Opsi B:* Tetap gunakan plaintext token dengan asumsi TTL 30 menit sudah cukup mitigasi.
- **Rekomendasi Auditor:** **Opsi A** (Memenuhi standar OWASP ASVS Section 3.2).
- **Pengambil Keputusan:** Lead Architect & Product Owner.
- **Status:** `OPEN`

---

### ISSUE-003: Verifikasi Akun Adsterra & Penetapan Slot Iklan
- **Deskripsi:** Format slot iklan yang didefinisikan di `DOC-10` didasarkan pada asumsi Adsterra menyetujui domain `lokataut.com` tanpa kendala.
- **Dampak:** Jika domain ditolak atau tipe iklan tertentu (seperti popunder) dinilai terlalu mengganggu pengunjung, skema slot iklan harus didesain ulang.
- **Opsi yang Dipertimbangkan:**
  - *Opsi A (Direkomendasikan):* Daftarkan domain ke Adsterra sekarang untuk verifikasi awal; siapkan fallback ke Monetag jika ada kendala.
  - *Opsi B:* Menunda pendaftaran hingga Phase 4 saat situs sudah jadi.
- **Rekomendasi Auditor:** **Opsi A** (Verifikasi lebih awal mencegah perombakan layout view di akhir).
- **Pengambil Keputusan:** Product Owner.
- **Status:** `PENDING PO VERIFICATION`

---

### ISSUE-004: Verifikasi CLI PHP 8.3 di SSH Shared Hosting
- **Deskripsi:** Seringkali di cPanel shared hosting, PHP web telah disetel ke 8.3 via MultiPHP, namun terminal SSH bawaan (`/usr/bin/php`) masih menunjuk ke PHP sistem (misal PHP 7.4 atau PHP 8.1).
- **Dampak:** Perintah `php artisan` Laravel 13 akan langsung error (*syntax error / unsupported PHP version*) saat dijalankan di terminal SSH.
- **Opsi Tindakan:**
  - Jalankan `which php` dan `php -v` di SSH WarnaHost.
  - Jika belum 8.3, buat alias di `~/.bashrc`: `alias php='/usr/local/bin/ea-php83'`.
- **Rekomendasi Auditor:** Lakukan pengecekan sebelum Phase 1 dimulai.
- **Pengambil Keputusan:** DevOps Engineer / Product Owner.
- **Status:** `NOT VERIFIED`

---

### ISSUE-005: Penanganan Risiko Kehabisan Memori Composer (CloudLinux LVE)
- **Deskripsi:** Paket WarnaHost Starter (2 GB RAM) berisiko mematikan proses `composer install` secara paksa (*Killed*).
- **Dampak:** Gagal deploy pada lingkungan hosting produksi.
- **Opsi yang Dipertimbangkan:**
  - *Opsi A (Direkomendasikan):* Tambahkan prosedur rilis berbasis ZIP/Tarball yang telah memuat folder `vendor/` hasil build lokal/CI (Pre-compiled bundle).
  - *Opsi B:* Tetap mengandalkan live `composer install` via SSH dengan flag `--no-dev --optimize-autoloader`.
- **Rekomendasi Auditor:** **Opsi A** sebagai prosedur rilis standar di `DOC-13`.
- **Pengambil Keputusan:** Lead DevOps Architect.
- **Status:** `OPEN`

---

### ISSUE-006: Desain Switch Darurat API Cloudflare Turnstile
- **Deskripsi:** Jika API Cloudflare mengalami gangguan global, verifikasi captcha safelink akan gagal terus-menerus.
- **Dampak:** Pengunjung tidak dapat mengunduh file, memicu keluhan pengguna dan kerugian tayangan iklan.
- **Opsi yang Dipertimbangkan:**
  - *Opsi A (Direkomendasikan):* Tambahkan setting `turnstile_mode` (`enabled`, `disabled`, `emergency_bypass`) pada tabel `settings` dan UI dashboard admin dengan audit logging ketat.
  - *Opsi B:* Mengubah variabel `.env` di server saat terjadi insiden (membutuhkan akses cPanel).
- **Rekomendasi Auditor:** **Opsi A** (Bisa dikendalikan admin dalam hitungan detik via UI web).
- **Pengambil Keputusan:** Lead Architect & Product Owner.
- **Status:** `OPEN`

---

### ISSUE-007: Sinkronisasi Fisik Matriks RTM dan Registry DOC-12
- **Deskripsi:** Sebanyak 17 test ID di RTM tidak memiliki definisi tabel di DOC-12, dan 9 test ID di DOC-12 belum terdaftar di RTM.
- **Dampak:** AI QA Agent tidak memiliki acuan test cases lengkap saat menguji acceptance criteria.
- **Opsi Tindakan:** Tuliskan tabel pengujian untuk 17 test case tersebut di `DOC-12` dan masukkan 9 test case ke RTM.
- **Pengambil Keputusan:** QA Lead.
- **Status:** `OPEN`

---

### ISSUE-008: Header Anti-Cache Web Server LiteSpeed
- **Deskripsi:** Caching agresif LiteSpeed dapat menyebabkan session token milik pengunjung tertentu tersaji ke pengunjung lain.
- **Dampak:** Tabrakan sesi (*session collision*) dan kegagalan countdown.
- **Opsi Tindakan:** Terapkan header `X-LiteSpeed-Cache-Control: no-cache` pada middleware safelink dan rule `.htaccess`.
- **Pengambil Keputusan:** Lead Architect & DevOps.
- **Status:** `OPEN`

---

### ISSUE-009: Persetujuan Formal Dokumen Keputusan Arsitektur (ADR-001 s/d ADR-010)
- **Deskripsi:** Seluruh 10 dokumen ADR pada `09-decisions/15-architecture-decisions.md` saat ini masih berstatus `PROPOSED / UNDER REVIEW`.
- **Dampak:** Berdasarkan direktif `AGENTS.md`, AI Coding Agent dilarang mengimplementasikan modul yang dokumen rujukannya belum disetujui formal oleh Product Owner.
- **Opsi Tindakan:** Product Owner meninjau dan mengubah status menjadi `APPROVED` di dokumen terkait.
- **Pengambil Keputusan:** Product Owner (Dimas Aditya).
- **Status:** `PENDING PO APPROVAL`

---
*Register isu terbuka ini akan diperbarui secara berkala seiring keputusan yang diambil oleh Product Owner.*

