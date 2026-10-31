# Architecture Decision Records (ADR) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-15 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Lead Software Architect |
| **Target Pembaca** | Product Owner, AI Coding Agent, Future Maintainers |
| **Tujuan** | Mencatat keputusan-keputusan arsitektural dan teknis yang fundamental, konteks masalah, opsi yang dipertimbangkan, alasan pemilihan, serta konsekuensi yang timbul. |

---

## Log Keputusan Arsitektur (ADR Summary)

> **Catatan Status:** Seluruh keputusan di bawah ini berstatus **`PROPOSED`** atau **`UNDER REVIEW`** sampai disetujui secara resmi oleh Product Owner melalui Approval Registry di `README.md`.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        ADR Registry Summary                            │
├─────────┬──────────────────────────────────────────────┬───────────────┤
│ ADR ID  │ Judul Keputusan                              │ Status        │
├─────────┼──────────────────────────────────────────────┼───────────────┤
│ ADR-001 │ Adopsi Modular Monolith Berbasis Laravel 13  │ PROPOSED      │
│ ADR-002 │ Penggunaan MySQL 8.0 sebagai RDBMS Tunggal   │ PROPOSED      │
│ ADR-003 │ Blade + Native CSS + Vanilla JS (No SPA)     │ PROPOSED      │
│ ADR-004 │ Penolakan AdSense & Kandidat Adsterra        │ PROPOSED      │
│ ADR-005 │ Tabel Khusus visitor_sessions & UA Hash      │ PROPOSED      │
│ ADR-006 │ Cloudflare Turnstile untuk Mitigasi Bot      │ PROPOSED      │
│ ADR-007 │ Validasi Countdown Server & Atomic Token     │ PROPOSED      │
│ ADR-008 │ Enkripsi Simetris AES-256 Destination URL    │ PROPOSED      │
│ ADR-009 │ Target Hosting WarnaHost Starter Unlimited   │ PROPOSED      │
│ ADR-010 │ Retensi Data 90 Hari & Keyed HMAC IP Hash    │ PROPOSED      │
└─────────┴──────────────────────────────────────────────┴───────────────┘
```

---

### ADR-001: Adopsi Modular Monolith Berbasis Laravel 13.x

- **Konteks:** Menentukan framework backend untuk platform LokaTaut per Oktober 2026.
- **Masalah:** Laravel 11 telah mencapai akhir masa dukungan keamanan (EOL) pada 12 Maret 2026. Mengembangkan proyek baru di atas framework EOL berisiko tinggi. Laravel 12 hanya menerima patch keamanan hingga Februari 2027.
- **Opsi yang Dipertimbangkan:**
  1. *Tetap Menggunakan Laravel 11*: Ditolak (EOL, risiko keamanan).
  2. *Laravel 12*: Ditolak (Masa dukungan keamanan hanya tersisa ~4 bulan).
  3. *Laravel 13.x (PHP 8.3+)*: Dirilis 17 Maret 2026, didukung bug fixes hingga Q3 2027 dan security fixes hingga 17 Maret 2028.
- **Keputusan:** Mengadopsi **Laravel 13.x (PHP 8.3+)** dalam arsitektur **Modular Monolith**.
- **Konsekuensi:**
  - *Positif:* Menjamin keamanan dan stabilitas proyek selama 2 tahun ke depan; kompatibel dengan shared hosting yang mendukung PHP 8.3+.
  - *Mitigasi:* Verifikasi kompatibilitas runtime PHP 8.3 CLI di cPanel WarnaHost sebelum instalasi.
- **Status:** `PROPOSED (PO Approved in Concept)`.

---

### ADR-002: Penggunaan MySQL 8.0 sebagai RDBMS Tunggal

- **Konteks:** Media persistensi basis data relasional.
- **Keputusan:** Menggunakan **MySQL 8.0+** dengan storage engine InnoDB.
- **Rasional:** Kompatibilitas 100% dengan cPanel shared hosting WarnaHost, mendukung index komposit dan kueri JSON, serta terintegrasi mulus dengan Eloquent ORM.
- **Status:** `PROPOSED`.

---

### ADR-003: Blade Templating + Native CSS + Vanilla JS (No SPA)

- **Konteks:** Menentukan arsitektur antarmuka frontend.
- **Keputusan:** Menggunakan **Laravel Blade Views, Semantic HTML5, Native CSS3 (CSS Variables), dan Vanilla JS**.
- **Rasional:** Menolak SPA (React/Vue) untuk menjaga skor Core Web Vitals (LCP < 1.2s), memangkas payload aset (< 20 KB), dan menghindari ketergantungan compiler NPM berat di server shared hosting produksi.
- **Status:** `PROPOSED`.

---

### ADR-004: Penolakan AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan

- **Konteks:** Model monetisasi periklanan safelink.
- **Keputusan:** **Menolak mutlak Google AdSense** pada halaman safelink dan menetapkan **Adsterra** sebagai `CANDIDATE AD NETWORK (PROPOSED)`.
- **Rasional:** AdSense melarang safelink karena kategori *thin content* dan *forced clicks*. Adsterra mengizinkan safelink selama tidak menerapkan trafik insentif dan memberikan jarak aman (min 30px) dari tombol unduhan. Arsitektur kode iklan dibuat agnostik.
- **Status:** `PROPOSED`.

---

### ADR-005: Tabel Khusus `visitor_sessions` untuk Safelink

- **Konteks:** Pengelolaan state dan token pengunjung safelink.
- **Keputusan:** Memisahkan sesi pengunjung dari file session Laravel default ke dalam tabel MySQL **`visitor_sessions`** dengan kombinasi **Token Kriptografis (64 hex) + User-Agent Hash**.
- **Rasional:** Menghindari kepenuhan kuota *inodes* shared hosting akibat puluhan ribu file sesi anonim, serta mencegah false-positive penolakan pada pengguna mobile yang mengalami rotasi IP seluler.
- **Status:** `PROPOSED`.

---

### ADR-006: Cloudflare Turnstile untuk Mitigasi Bot

- **Konteks:** Perlindungan dari bot scraper dan perayap otomatis.
- **Keputusan:** Mengintegrasikan **Cloudflare Turnstile** sebelum countdown timer dimulai.
- **Rasional:** Verifikasi bot tanpa puzzle gambar yang mengganggu pengguna smartphone, gratis tanpa batas request kuota, dan mudah di-mock saat automated feature test (`Http::fake()`).
- **Status:** `PROPOSED`.

---

### ADR-007: Validasi Countdown Server-Side & Transisi Token Atomic

- **Konteks:** Pencegahan kecurangan bypass timer JavaScript di browser.
- **Keputusan:** Validasi countdown dievaluasi berdasarkan selisih waktu server (`now() - countdown_started_at >= countdown_seconds`) dan update token dieksekusi secara atomic:
  ```sql
  UPDATE visitor_sessions SET completed = 1 WHERE token = ? AND completed = 0;
  ```
- **Rasional:** Kebal manipulasi JavaScript console dan mencegah serangan replay / race condition dari dua request konkuren.
- **Status:** `PROPOSED`.

---

### ADR-008: Enkripsi Simetris AES-256 Destination URL

- **Konteks:** Penyimpanan URL tujuan unduhan asli di database.
- **Keputusan:** Mengenkripsi kolom `destination_url` menggunakan algoritma simetris **AES-256-CBC** melalui fitur Eloquent Attribute Cast.
- **Rasional:** Melindungi kerahasiaan URL tujuan saat berada di basis data dan backup dump. URL didekripsi tepat sebelum respon HTTP 302 diserahkan.
- **Status:** `PROPOSED`.

---

### ADR-009: Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited

- **Konteks:** Pemilihan hosting dengan anggaran hemat (< Rp 100.000 / bulan).
- **Keputusan:** Menetapkan WarnaHost Starter Unlimited (1 vCPU Core, 2 GB RAM, NVMe SSD) sebagai target dengan status **`TARGET HOSTING (NEEDS PRE-DEPLOYMENT VERIFICATION)`**.
- **Rasional:** Biaya ekonomis (~Rp 79.000/bulan) dan performa NVMe mencukupi untuk modul monolith yang dioptimasi. Struktur direktori wajib diletakkan secara aman **di luar `public_html`**.
- **Status:** `PROPOSED`.

---

### ADR-010: Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC

- **Konteks:** Batasan disk shared hosting dan kepatuhan privasi data pengunjung.
- **Keputusan:** Data mentah `visits` disimpan maksimal **90 hari** sebelum dibersihkan via cron mingguan, setelah diringkas permanen ke `daily_stats`. Alamat IP pengunjung disamarkan menggunakan **Keyed HMAC-SHA256**.
- **Rasional:** Menjaga ukuran basis data stabil di bawah 3 GB dan menghormati privasi pengunjung tanpa menyimpan alamat IP mentah di log analitik.
- **Status:** `PROPOSED`.

