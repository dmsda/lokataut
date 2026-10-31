# Architecture Decision Records (ADR) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-15 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Lead Software Architect |
| **Target Pembaca** | Product Owner, AI Coding Agent, Future Maintainers |
| **Tujuan** | Mencatat keputusan-keputusan arsitektural dan teknis yang fundamental, konteks masalah, opsi yang dipertimbangkan, alasan pemilihan, serta konsekuensi yang timbul. |

---

## Log Keputusan Arsitektur

```
┌────────────────────────────────────────────────────────────────────────┐
│                        ADR Registry Summary                            │
├─────────┬──────────────────────────────────────────────┬───────────────┤
│ ADR ID  │ Judul Keputusan                              │ Status        │
├─────────┼──────────────────────────────────────────────┼───────────────┤
│ ADR-001 │ Adopsi Modular Monolith Berbasis Laravel 11  │ APPROVED      │
│ ADR-002 │ Penggunaan MySQL 8.0 sebagai RDBMS Tunggal   │ APPROVED      │
│ ADR-003 │ Blade + Native CSS + Vanilla JS (No SPA)     │ APPROVED      │
│ ADR-004 │ Penolakan AdSense & Pemilihan Adsterra       │ APPROVED      │
│ ADR-005 │ Tabel Khusus `visitor_sessions` untuk Safelink│ APPROVED     │
│ ADR-006 │ Cloudflare Turnstile untuk Mitigasi Bot      │ APPROVED      │
│ ADR-007 │ Validasi Countdown Wajib di Server-Side      │ APPROVED      │
│ ADR-008 │ Enkripsi Simetris AES-256 Destination URL    │ APPROVED      │
│ ADR-009 │ Pemilihan Shared Hosting WarnaHost Starter   │ APPROVED      │
│ ADR-010 │ Retensi Data Mentah 90 Hari & Agregasi Harian│ APPROVED      │
└─────────┴──────────────────────────────────────────────┴───────────────┘
```

---

### ADR-001: Adopsi Modular Monolith Berbasis Laravel 11

- **Konteks:** LokaTaut adalah platform safelink yang dikembangkan secara custom untuk satu orang pemilik (single owner) guna mengelola link unduhan dari jaringan website pribadinya.
- **Masalah:** Memilih arsitektur sistem yang cepat dikembangkan, mudah dipelihara oleh AI Coding Agent, berkinerja tinggi, dan hemat biaya operasional.
- **Opsi yang Dipertimbangkan:**
  1. *Microservices* (Go + Node.js + Python): Biaya tinggi, overhead deployment shared hosting sangat sulit.
  2. *Custom Framework PHP Ringan* (Slim / Lumen): Fitur built-in minim, butuh banyak coding manual untuk auth, validasi, dan ORM.
  3. *Modular Monolith Laravel 11*: Fitur lengkap (Auth, Eloquent, Migration, Testing, Security, Blade), dokumentasi ekstensif, ramah AI Coding Agent.
- **Keputusan:** Mengadopsi **Laravel 11.x (PHP 8.3+)** dalam arsitektur **Modular Monolith**.
- **Konsekuensi:**
  - *Positif:* Waktu pengembangan cepat, ekosistem security bawaan Laravel matang, deployment cPanel sederhana.
  - *Mitigasi:* Mengoptimalkan konfigurasi caching (`config:cache`, `route:cache`) dan memangkas dependensi vendor agar memori server tetap di bawah 2 GB RAM.

---

### ADR-002: Penggunaan MySQL 8.0 sebagai RDBMS Tunggal

- **Konteks:** Diperlukan media persistensi data untuk link, sesi kunjungan, analitik trafik, dan pengaturan sistem.
- **Masalah:** Memilih basis data yang didukung penuh oleh cPanel shared hosting tanpa lisensi tambahan.
- **Opsi yang Dipertimbangkan:**
  1. *PostgreSQL*: Dukungan cPanel shared hosting sering kali terbatas atau memerlukan paket tambahan.
  2. *MongoDB / NoSQL*: Membutuhkan daemon server terpisah yang tidak diizinkan pada shared hosting.
  3. *MySQL 8.0 (InnoDB)*: Standar baku shared hosting WarnaHost, mendukung indexing komposit, kueri JSON, dan integrasi mulus dengan Eloquent.
- **Keputusan:** Menggunakan **MySQL 8.0+** dengan engine storage InnoDB.
- **Konsekuensi:**
  - *Positif:* Kompatibilitas 100% dengan WarnaHost cPanel, backup via `mysqldump` sangat mudah.
  - *Mitigasi:* Tabel dengan pertumbuhan cepat (`visits`) wajib menerapkan indexing presisi dan strategi pruning 90 hari.

---

### ADR-003: Blade Templating + Native CSS + Vanilla JS (No SPA)

- **Konteks:** Frontend terdiri dari dua area: antarmuka publik safelink dan panel admin dashboard.
- **Masalah:** Menentukan arsitektur frontend yang memprioritaskan skor Core Web Vitals, waktu muat ultra-cepat, dan kemudahan deployment tanpa build step kompleks di server produksi.
- **Opsi yang Dipertimbangkan:**
  1. *Single Page Application (React / Vue / Next.js)*: Menambah payload JavaScript (> 200 KB), memperlambat First Contentful Paint, memicu masalah rendering iklan pihak ketiga.
  2. *Tailwind CSS dengan Node Build Step di Server*: Shared hosting tidak memiliki akses root dan eksekusi NPM sering timeout / OOM.
  3. *Server-Side Rendered Blade + Native CSS + Vanilla JS*: Waktu muat mendekati instan, payload CSS < 20 KB, JS < 15 KB, tidak butuh compiler di shared hosting.
- **Keputusan:** Menggunakan **Laravel Blade, Semantic HTML5, Native CSS3 (CSS Variables), dan Vanilla JS**.
- **Konsekuensi:**
  - *Positif:* Skor Lighthouse tinggi, performa mobile optimal, bebas dari breaking changes tooling frontend.
  - *Mitigasi:* Struktur CSS diatur rapi berbasis token desain di file `safelink.css` dan `admin.css`.

---

### ADR-004: Penolakan AdSense & Pemilihan Adsterra sebagai Ad Network Primer

- **Konteks:** Pendapatan platform berasal dari penempatan iklan di halaman safelink/countdown.
- **Masalah:** Google AdSense Program Policies secara ketat melarang safelink karena kategori *thin content* dan *forced clicks*, dengan ancaman permanent account ban.
- **Opsi yang Dipertimbangkan:**
  1. *Tetap Memaksakan Google AdSense*: Risiko tinggi penutupan akun permanen.
  2. *Adsterra*: Mendukung safelink secara resmi, persetujuan instan, ambang payout rendah ($5), variasi format iklan beragam (Social Bar, Banner 300x250, Popunder).
  3. *Monetag / PropellerAds*: Alternatif solid untuk trafik internasional.
- **Keputusan:** **Menolak keras penggunaan Google AdSense** pada safelink dan menetapkan **Adsterra** sebagai mitra periklanan primer (dengan Monetag sebagai alternatif sekunder).
- **Konsekuensi:**
  - *Positif:* Bebas dari risiko pemblokiran Google, pendapatan 100% milik pemilik platform, payout cepat.
  - *Mitigasi:* Memilih format iklan yang tidak merusak pengalaman pengguna (Social Bar & Clean Banners, hindari popunder berlebihan).

---

### ADR-005: Pemisahan Penyimpanan Sesi Pengunjung ke Database `visitor_sessions`

- **Konteks:** Validasi token dan countdown safelink membutuhkan penyimpanan state pengunjung yang aman.
- **Masalah:** File session Laravel default dibuat untuk pengguna web terautentikasi. Menyimpan puluhan ribu sesi pengunjung anonim di folder `storage/framework/sessions` akan menghabiskan kuota *inodes* hosting.
- **Opsi yang Dipertimbangkan:**
  1. *Laravel Session File Driver*: Menghasilkan jutaan file kecil di disk shared hosting, memicu limit inodes cPanel.
  2. *Redis In-Memory Session*: Tidak tersedia di Shared Hosting WarnaHost Starter.
  3. *Dedicated MySQL Table `visitor_sessions`*: Terstruktur, mudah diindeks, cepat di-query, dan sangat mudah dibersihkan secara massal via single query SQL `DELETE WHERE expires_at < NOW()`.
- **Keputusan:** Menggunakan tabel basis data khusus **`visitor_sessions`** untuk alur safelink pengunjung.
- **Konsekuensi:**
  - *Positif:* Inodes shared hosting aman, validasi token berkecepatan tinggi, pembersihan sesi kedaluwarsa sangat efisien.
  - *Mitigasi:* Menjalankan artisan command `lokataut:prune-sessions` setiap jam via Cron cPanel.

---

### ADR-006: Cloudflare Turnstile untuk Mitigasi Bot

- **Konteks:** Safelink rentan diserang bot crawler yang mengonsumsi bandwidth tanpa melihat iklan.
- **Masalah:** Memilih captcha yang tidak mengganggu pengunjung manusia (*frictionless UX*) dan gratis tanpa batas kuota.
- **Opsi yang Dipertimbangkan:**
  1. *Google reCAPTCHA v2 (Checkbox/Image)*: Menyebalkan bagi pengunjung di smartphone, gambar puzzle lambat dimuat.
  2. *Google reCAPTCHA v3*: Memerlukan analisis skor yang rumit dan rentan false-positive.
  3. *Cloudflare Turnstile*: Gratis tanpa batas request, privasi terjaga, memverifikasi browser secara cerdas dalam hitungan milidetik tanpa puzzle gambar.
- **Keputusan:** Mengintegrasikan **Cloudflare Turnstile** sebelum countdown timer dimulai.
- **Konsekuensi:**
  - *Positif:* Tingkat konversi pengunjung tinggi, bot tersaring efektif, integrasi API via backend Laravel sangat bersih.
  - *Mitigasi:* Menyediakan toggle bypass di `.env` (`TURNSTILE_ENABLED=false`) untuk memudahkan pengujian automated test di localhost.

---

### ADR-007: Validasi Countdown Wajib di Server-Side

- **Konteks:** Pengunjung sering mencoba mempercepat atau melewati hitung mundur timer dengan memodifikasi variabel JavaScript di console browser.
- **Masalah:** Menjamin bahwa redirect hanya terjadi jika pengunjung benar-benar telah menunggu durasi countdown penuh.
- **Opsi yang Dipertimbangkan:**
  1. *Validasi Murni Client-Side*: Rentan di-bypass dengan perintah `timer = 0; submit();`.
  2. *Enkripsi Parameter di Client*: Tetap bisa didekripsi jika kuncinya ada di browser.
  3. *Server-Side Timestamp Delta Check*: Waktu resmi dicatat di tabel `visitor_sessions.countdown_started_at` saat Turnstile disetujui. Saat request proceed masuk, server menghitung `now() - countdown_started_at >= countdown_seconds`.
- **Keputusan:** Menerapkan **Server-Side Timestamp Delta Enforcement**.
- **Konsekuensi:**
  - *Positif:* Perlindungan anti-bypass 100% kebal manipulasi JavaScript client-side.
  - *Mitigasi:* Jam server hosting wajib disinkronkan via NTP (standar cPanel WarnaHost).

---

### ADR-008: Enkripsi Simetris AES-256 Destination URL

- **Konteks:** Database administrator menyimpan URL tujuan asli file unduhan.
- **Masalah:** Menjaga kerahasiaan tautan asli jika database terekspos cadangannya dan mencegah kebocoran link sebelum safelink selesai.
- **Opsi yang Dipertimbangkan:**
  1. *Plaintext URL*: Mudah dibaca jika terjadi kebocoran file database dump.
  2. *Hashing (SHA-256)*: Tidak bisa didekripsi kembali (one-way), tidak bisa digunakan untuk redirect.
  3. *Symmetric Encryption AES-256-CBC via Laravel Crypt*: Standar enkripsi industri kuat, otomatis menggunakan `APP_KEY`, terintegrasi rapi dengan Eloquent Attribute Cast.
- **Keputusan:** Mengenkripsi kolom `destination_url` menggunakan **AES-256-CBC**.
- **Konsekuensi:**
  - *Positif:* URL tujuan terlindungi secara kriptografis baik di database maupun di memory dump.
  - *Mitigasi:* `APP_KEY` wajib di-backup secara aman; kehilangan key membuat seluruh data link tidak dapat didekripsi.

---

### ADR-009: Pemilihan Shared Hosting WarnaHost Starter Unlimited

- **Konteks:** Menentukan target infrastruktur penyedia hosting dengan anggaran efisien (< Rp 100.000 / bulan).
- **Masalah:** Memastikan aplikasi berjalan stabil pada alokasi resource 1 vCPU Core dan 2 GB RAM dengan traffic harian 10.000–50.000 kunjungan.
- **Opsi yang Dipertimbangkan:**
  1. *Cloud VPS (DigitalOcean / Linode)*: Biaya bulanan lebih tinggi ($6-$12/bulan) dan butuh pemeliharaan OS manual.
  2. *Shared Hosting WarnaHost Starter Unlimited*: Biaya ~Rp 79.000/bulan, penyimpanan NVMe unlimited, cPanel lengkap, SSL gratis, dukungan PHP 8.3+.
- **Keputusan:** Memilih **Shared Hosting WarnaHost Starter Unlimited** dengan proteksi edge network Cloudflare.
- **Konsekuensi:**
  - *Positif:* Biaya sangat ekonomis, pengelolaan praktis via cPanel, performa NVMe cepat.
  - *Mitigasi:* Kode aplikasi dirancang bebas dari kebutuhan akses root dan beban database ditekan lewat query indexing yang presisi.

---

### ADR-010: Retensi Data Mentah 90 Hari & Agregasi Harian

- **Konteks:** Pada volume 50.000 kunjungan/hari, tabel `visits` menghasilkan ~3 juta baris baru setiap bulan.
- **Masalah:** Mencegah basis data kehabisan ruang disk atau mengalami perlambatan kueri kueri (*slow queries*) di shared hosting.
- **Opsi yang Dipertimbangkan:**
  1. *Simpan Semua Data Mentah Selamanya*: Disk database akan membengkak > 15 GB dalam setahun, memperlambat kueri.
  2. *Tidak Menyimpan Data Mentah Sama Sekali*: Kehilangan data audit trail untuk analisis anomali bot.
  3. *Penyimpanan Hybrid (90 Hari Mentah + Agregasi Permanen)*: Data mentah disimpan 90 hari untuk investigasi insiden, lalu dibersihkan secara terjadwal setelah dirangkum ke tabel `daily_stats`.
- **Keputusan:** Menerapkan **Retensi 90 Hari untuk `visits` dan Agregasi Harian ke `daily_stats`**.
- **Konsekuensi:**
  - *Positif:* Ukuran database stabil di kisaran 1.5 - 3 GB, grafik dashboard dimuat dalam hitungan milidetik.
  - *Mitigasi:* Artisan command `lokataut:aggregate-stats` dijalankan setiap malam pukul 00:05 WIB.

