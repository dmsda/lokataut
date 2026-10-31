# Architecture Decision Records (ADR) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-15 |
| **Versi** | 1.2.1 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Lead Software Architect |
| **Target Pembaca** | Product Owner, AI Coding Agent, Future Maintainers |
| **Tujuan** | Mencatat keputusan-keputusan arsitektural dan teknis yang fundamental, konteks masalah, opsi yang dipertimbangkan, alasan pemilihan, serta konsekuensi yang timbul. |

---

## Log Keputusan Arsitektur (ADR Summary)

> **Catatan Status:** Seluruh keputusan di bawah ini berstatus **`PROPOSED`** atau **`UNDER REVIEW`** sampai disetujui secara resmi oleh Product Owner melalui Approval Registry di `README.md`. Sesuai aturan operasional `AGENTS.md`, AI Agent dilarang menyetujui ADR atas nama Product Owner.

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
│ ADR-011 │ Penyimpanan Hashed Session Token (SHA-256)   │ PROPOSED      │
│ ADR-012 │ Admin Password Change & Profile Management   │ PROPOSED      │
│ ADR-013 │ Fallback Pre-Built Vendor Deployment         │ PROPOSED      │
│ ADR-014 │ Penangguhan Popunder & Sterilisasi Tombol    │ PROPOSED      │
│ ADR-015 │ Kebijakan Hapus Data & Retensi Analitik      │ PROPOSED      │
│ ADR-016 │ Mandatori ANALYTICS_SALT Tanpa Fallback      │ PROPOSED      │
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
- **Keputusan:** Data mentah `visits` disimpan maksimal **90 hari** sebelum dibersihkan via cron mingguan, setelah diringkas permanen ke `daily_stats`. Alamat IP pengunjung disamarkan menggunakan **Keyed HMAC-SHA256** dengan salt terpisah `ANALYTICS_SALT`.
- **Rasional:** Menjaga ukuran basis data stabil di bawah 3 GB dan menghormati privasi pengunjung tanpa menyimpan alamat IP mentah di log analitik. Memisahkan salt dari `APP_KEY` menjamin rotasi kunci enkripsi tidak merusak analitik historis.
- **Status:** `PROPOSED`.

---

### ADR-011: Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (`token_hash`)

- **Konteks:** Menentukan format persistensi token sesi safelink di tabel `visitor_sessions`.
- **Masalah:** Menyimpan token plaintext membuka celah di mana kebocoran dump SQL atau file backup memungkinkan penyerang membajak sesi safelink aktif untuk mem-bypass Turnstile dan countdown.
- **Keputusan:** Mengadopsi prinsip *Defense-in-Depth* (OWASP ASVS v4.0 Section 3.2.4). Kolom pada database diubah menjadi `token_hash VARCHAR(64)`, yang menyimpan nilai `hash('sha256', $rawToken)`. Nilai `$rawToken` (256-bit CSPRNG) hanya dipegang oleh client browser. Kueri transisi atomik dieksekusi dengan:
  ```sql
  UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0;
  ```
- **Konsekuensi:**
  - *Positif:* Sekalipun database terekspos, penyerang tidak dapat merekonstruksi token mentah untuk bypass safelink.
  - *Mitigasi:* Controller melakukan satu kali hashing `hash('sha256', $rawToken)` saat memvalidasi request verifikasi dan proceed.
- **Status:** `PROPOSED`.

---

### ADR-012: Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (`FR-AUTH-004`)

- **Konteks:** Manajemen kredensial administrator tunggal pada sistem produksi.
- **Masalah:** Dokumentasi sebelumnya tidak menyediakan UI/rute untuk mengganti kata sandi admin, memaksa admin mengedit hash Bcrypt manual di phpMyAdmin/Tinker.
- **Keputusan:** Menambahkan `FR-AUTH-004`, rute `GET /admin/profile` dan `PUT /admin/profile/password`, Form Request `UpdatePasswordRequest`, serta controller terkait.
- **Rasional:** Mematuhi OWASP ASVS v4.0 Section 2.1 (Credential Management). Administrator wajib dapat merotasi kata sandi secara aman dengan verifikasi kata sandi saat ini (`current_password`), validasi kompleksitas kata sandi baru (12+ karakter), dan regenerasi session ID.
- **Status:** `PROPOSED`.

---

### ADR-013: Fallback Pre-Built Vendor Deployment untuk Shared Hosting

- **Konteks:** Menangani potensi kegagalan kompilasi dependensi Laravel 13 di shared hosting WarnaHost.
- **Masalah:** Paket WarnaHost Starter dibatasi alokasi memori fisik CloudLinux LVE 2 GB. Proses live `composer install` berisiko dimatikan paksa (*Killed/SIGKILL*) oleh sistem operasi.
- **Keputusan:** Menyediakan prosedur deployment cadangan (*Fallback Pre-Built Vendor Deployment*), di mana dependensi `vendor/` dikompilasi pada lingkungan lokal/CI yang kompatibel dengan PHP 8.3 target, dikemas dalam arsip ZIP, dan diunggah langsung ke root cPanel.
- **Rasional:** Mencegah downtime deployment. Dokumen secara realistis mencatat bahwa metode ini mengeliminasi risiko OOM Composer tetapi menuntut verifikasi ketat kecocokan versi PHP dan ekstensi build environment dengan server hosting.
- **Status:** `PROPOSED`.

---

### ADR-014: Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink

- **Konteks:** Mitigasi risiko sanksi Invalid Traffic (IVT) dan penahanan saldo dari jaringan periklanan Adsterra/Monetag.
- **Masalah:** Mengikat event klik popunder atau interaksi langsung pada tombol proceed memicu deteksi fraud oleh sistem GeoEdge Adsterra atas pelanggaran *Forced/Incentivized Clicks*.
- **Keputusan:**
  1. Menangguhkan (*suspend*) implementasi format popunder pada MVP. Evaluasi popunder baru boleh dibuka pasca-MVP setelah akun Adsterra resmi terverifikasi dan ada konfirmasi kebijakan tertulis.
  2. Mensterilkan tombol navigasi unduhan (`#proceed-btn`) secara mutlak dari skrip pihak ketiga.
  3. Memfokuskan monetisasi pada banner display terlabel jelas (`"Advertisement / Iklan Sponsor"`) dengan jarak aman minimal 30px dari elemen aksi.
- **Status:** `PROPOSED`.

---

### ADR-015: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik

- **Konteks:** Menentukan konsekuensi penghapusan tautan terhadap entitas relasional (`visitor_sessions`, `visits`, `daily_stats`).
- **Masalah:** Jika tautan dihapus permanen dengan `ON DELETE CASCADE` di seluruh tabel, data metrik analitik historis akan hilang tanpa alasan. Sebaliknya, jika `ON DELETE RESTRICT`, admin tidak bisa membersihkan tautan sampah dari basis data.
- **Keputusan:**
  1. Operasi penghapusan default di panel admin adalah **Soft Delete** (`deleted_at = NOW()`). Tautan dinonaktifkan untuk publik, namun seluruh relasi data tetap utuh. Agregasi harian menyertakan link soft-deleted via `Link::withTrashed()`.
  2. Operasi **Hard Delete (Force Delete)** hanya diizinkan dari Trash:
     - `visitor_sessions`: `ON DELETE CASCADE` (sesi sementara kedaluwarsa dibersihkan).
     - `visits`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, data analitik agregat global seperti total trafik/negara tetap tersimpan).
     - `daily_stats`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`), dengan kolom `link_slug` snapshot yang tersimpan saat agregasi memastikan data rekap historis tetap terbaca pada grafik performa.
- **Konsekuensi:** Data historis terlindungi dari kehilangan tidak disengaja, agregasi harian tidak rusak, dan integritas basis data terjamin.
- **Status:** `PROPOSED`.

---

### ADR-016: Pemisahan Mandatori Salt Analitik (`ANALYTICS_SALT`) Tanpa Fallback ke `APP_KEY`

- **Konteks:** Mematuhi prinsip pemisahan kunci kriptografis (*Cryptographic Key Separation*) menurut rekomendasi NIST SP 800-57 Part 1.
- **Masalah:** Menggunakan `APP_KEY` sebagai salt atau menyediakan fallback implisit `config('services.analytics.salt', config('app.key'))` mengikat siklus hidup analitik historis ke kunci enkripsi sesi. Jika `APP_KEY` dirotasi pada insiden darurat, seluruh hash IP pengunjung lama akan rusak. Sebaliknya, jika salt analitik kosong di production, IP akan di-hash tanpa salt.
- **Keputusan:**
  1. Menghapus secara ketat fallback `ANALYTICS_SALT` ke `APP_KEY`.
  2. Menjadwalkan `ANALYTICS_SALT` sebagai variabel wajib di `.env` produksi. Jika tidak dikonfigurasi saat `APP_ENV=production`, sistem melempar `RuntimeException` dan menolak boot/logging.
  3. Di lingkungan lokal/testing, sistem menggunakan fixed testing salt string untuk memfasilitasi automated testing.
  4. Mendokumentasikan secara transparan bahwa rotasi `ANALYTICS_SALT` memutus kontinuitas unique visitor historis (IP sama pasca-rotasi menjadi unique visitor baru), namun log masa lalu tetap utuh.
- **Status:** `PROPOSED`.

