# Architecture Decision Records (ADR) Inventory — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-INV-01 |
| **Versi Dokumen** | 1.0.2 (Remediasi Pemetaan DEC & Penguatan Idempotensi) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Panduan & Status Tata Kelola ADR

Sesuai aturan operasional repository [AGENTS.md](../AGENTS.md) dan ketetapan tata kelola pada [README.md](./README.md):
- **Otoritas Tunggal:** Hanya Product Owner (Dimas Aditya) yang memiliki kewenangan menyetujui, menolak, atau meminta revisi terhadap setiap ADR.
- **Status Formal ADR:** Seluruh 17 ADR saat ini berstatus **`PROPOSED / UNDER REVIEW`**.
- **Pemisahan Keputusan Produk (DEC) vs Rekam Arsitektur (ADR):** Product Owner telah menyetujui 14 keputusan produk/kebijakan kanonikal pada [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md). Persetujuan produk tersebut **tidak secara otomatis mengesahkan** berkas arsitektur teknis. Setiap ADR memerlukan peninjauan dan otorisasi mandiri sesuai tata kelola [DEC-012-APPROVAL-GOVERNANCE.md](./DEC-012-APPROVAL-GOVERNANCE.md).
- **Relasi DEC yang Tepat:** Keputusan arsitektural yang tidak memiliki pasangan langsung pada 16 DEC kanonikal ditandai secara jujur sebagai `No dedicated DEC relationship (Architectural Baseline)`. DEC-012 adalah kerangka tata kelola umum, bukan pemetaan teknis untuk ADR tertentu.
- **Status Peninjau:** Predikat seperti `TECHNICALLY SOUND`, `SOUND WITH CONDITIONS`, dan `BLOCKED BY MISSING EVIDENCE` adalah **rekomendasi profesional tim peninjau**, bukan persetujuan resmi.

---

## 2. Tabel Ringkasan Inventaris (Master ADR Registry v1.0.2)

| ADR ID | Judul Keputusan Arsitektur | Disiplin Utama | Status Formal | Evaluasi Peninjau | Relasi DEC Kanonikal (v1.2.2) | Kebutuhan Terkait | Test Case Kunci (DOC-12) |
|:---:|---|---|:---:|:---:|---|---|---|
| **ADR-001** | Adopsi Modular Monolith Berbasis Laravel 13.x | Software Architecture | `PROPOSED` | `SOUND WITH CONDITIONS` | **DEC-001** (Approved: Laravel 13 & PHP 8.3+) | NFR-ARCH-001, NFR-ARCH-002 | TEST-DEPLOY-001, TEST-AUTH-001 |
| **ADR-002** | Penggunaan MySQL 8.0 sebagai RDBMS Tunggal | Database Architecture | `PROPOSED` | `BLOCKED BY MISSING EVIDENCE` | *No dedicated DEC relationship (Architectural Baseline)* | NFR-ARCH-003, NFR-DATA-001 | TEST-DEPLOY-001, TEST-ANA-004 |
| **ADR-003** | Blade + Native CSS + Vanilla JS (No SPA) | Frontend / UI Architecture | `PROPOSED` | `TECHNICALLY SOUND` | *No dedicated DEC relationship (Architectural Baseline)* | NFR-PERF-001, NFR-PERF-002 | TEST-PERF-001, TEST-SAFE-001 |
| **ADR-004** | Penolakan AdSense & Kandidat Adsterra | Monetization / Compliance | `PROPOSED` | `SOUND WITH CONDITIONS` | **DEC-002** (Approved: Adsterra Candidate) | FR-ADS-001 s/d FR-ADS-004 | TEST-ADS-001 s/d TEST-ADS-005 |
| **ADR-005** | Tabel Khusus `visitor_sessions` & UA Hash | Security / Performance | `PROPOSED` | `TECHNICALLY SOUND` | *No dedicated DEC relationship (Architectural Baseline)* | FR-SAFE-001, NFR-SEC-002 | TEST-SAFE-001, TEST-SAFE-007 |
| **ADR-006** | Cloudflare Turnstile untuk Mitigasi Bot | Security / Bot Mitigation | `PROPOSED` | `SOUND WITH CONDITIONS` | **DEC-005** (Approved: Turnstile Placement) & **DEC-011** (Bypass Switch) | FR-SAFE-002, NFR-SEC-003 | TEST-SAFE-004, TEST-SAFE-009, TEST-SAFE-010 |
| **ADR-007** | Validasi Countdown Server & Atomic Token | Security / Anti-Cheat | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-004** (Approved: Countdown 10s) | FR-SAFE-003, FR-SAFE-004 | TEST-SAFE-005, TEST-SAFE-006, TEST-SAFE-007 |
| **ADR-008** | Enkripsi Simetris AES-256 Destination URL | Application Security | `PROPOSED` | `TECHNICALLY SOUND` | *No dedicated DEC relationship (Architectural Baseline)* | FR-LINK-001, NFR-SEC-004 | TEST-LINK-001, TEST-SEC-002 |
| **ADR-009** | Target Hosting WarnaHost Starter Unlimited | DevOps / Infrastructure | `PROPOSED` | `BLOCKED BY MISSING EVIDENCE` | **DEC-003** (Approved: WarnaHost Verification) | NFR-HOST-001 s/d NFR-HOST-004 | TEST-DEPLOY-001 s/d TEST-DEPLOY-004 |
| **ADR-010** | Retensi Data 90 Hari & Keyed HMAC IP Hash | Data Privacy / Analytics | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-006** (Approved: Retensi 90 Hari) | FR-ANA-001, NFR-DATA-002 | TEST-ANA-001, TEST-ANA-007 |
| **ADR-011** | Penyimpanan Hashed Session Token (SHA-256) | Application Security | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-009** (Approved: Hashed Token) | FR-SAFE-001, NFR-SEC-001 | TEST-SAFE-001, TEST-SEC-001 |
| **ADR-012** | Admin Password Change & Profile Management | Security / Auth | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-008** (Approved: Ganti Password FR-AUTH-004) | FR-AUTH-004, NFR-SEC-005 | TEST-AUTH-006, TEST-AUTH-007 |
| **ADR-013** | Fallback Pre-Built Vendor Deployment | DevOps / Deployment | `PROPOSED` | `SOUND WITH CONDITIONS` | **DEC-010** (Approved: Fallback Vendor Deploy) | NFR-HOST-002, NFR-DEPL-001 | TEST-DEPLOY-002, TEST-DEPLOY-003 |
| **ADR-014** | Penangguhan Popunder & Sterilisasi Tombol | Monetization / Anti-IVT | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-007** (Approved: Suspend Popunder) | FR-ADS-005, NFR-MON-001 | TEST-ADS-003, TEST-ADS-004 |
| **ADR-015** | Kebijakan Hapus Data & Retensi Analitik | Database Architecture | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-013** (Approved: Soft/Hard Delete & Slug Snapshot) | FR-LINK-006, FR-LINK-007 | TEST-LINK-006, TEST-LINK-007 |
| **ADR-016** | Mandatori ANALYTICS_SALT Tanpa Fallback | Security / Cryptography | `PROPOSED` | `TECHNICALLY SOUND` | **DEC-014** (Approved: ANALYTICS_SALT Tanpa Fallback) | NFR-SEC-006, NFR-PRIV-001 | TEST-ANA-002, TEST-SEC-003 |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot Identitas | Database Architecture | `PROPOSED` | `SOUND WITH CONDITIONS` | **DEC-015** (Approved: Aggregate Key) & **DEC-016** (Under Review: Snapshot Identity) | FR-ANA-003, NFR-DATA-003 | TEST-ANA-004, TEST-LINK-007 |

---

## 3. Inventaris Detail 17 ADR

---

### ADR-001: Adopsi Modular Monolith Berbasis Laravel 13.x

1. **ADR ID & Title:** `ADR-001 — Adopsi Modular Monolith Berbasis Laravel 13.x`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Laravel 11 telah mencapai akhir masa dukungan keamanan (EOL) pada 12 Maret 2026. Mengembangkan proyek di atas framework EOL menghadapkan sistem pada risiko keamanan kritis. Laravel 12 hanya menerima patch keamanan hingga Februari 2027 (~4 bulan tersisa). Sistem membutuhkan framework yang aman dengan masa dukungan panjang dan kompatibel dengan lingkungan shared hosting.
4. **Keputusan:**
   Mengadopsi **Laravel 13.x (PHP 8.3+)** dengan pola **Modular Monolith** (`app/Modules/*`).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Laravel 11.x:* Ditolak karena EOL (tidak ada patch keamanan).
   - *Laravel 12.x:* Ditolak karena jendela dukungan keamanan terlalu sempit.
   - *Microservices (Go/Node.js):* Ditolak karena overhead operasional dan tidak didukung cPanel shared hosting tanpa root.
   - *Slim / Lumen:* Ditolak karena Lumen abandonware dan Slim membutuhkan perakitan ORM manual.
6. **Konsekuensi Positif:**
   - Menjamin keamanan dan stabilitas hingga 17 Maret 2028 (24 bulan dukungan aktif).
   - Ekosistem lengkap: Eloquent, Form Requests, Blade, Throttling, Scheduler.
7. **Konsekuensi Negatif & Trade-offs:**
   - Direktori `vendor/` relatif besar (~65-85 MB).
   - Membutuhkan keselarasan PHP 8.3+ pada CLI cPanel.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Perbedaan versi PHP antara web handler dan CLI cPanel.
   - *Mitigasi:* Mengatur path binary PHP absolut `/usr/local/bin/ea-php83` pada crontab (lihat [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md)).
9. **Kaitannya dengan Keputusan PO:** `DEC-001 (APPROVED 2026-10-31: Migrasi Framework ke Laravel 13.x)`
10. **Kebutuhan Terkait:** `NFR-ARCH-001`, `NFR-ARCH-002`, `NFR-MAINT-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001`, `TEST-AUTH-001`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Verifikasi eksekusi perintah pre-flight `php -v` pada CLI WarnaHost sebelum scaffolding proyek.

---

### ADR-002: Penggunaan MySQL 8.0 sebagai RDBMS Tunggal

1. **ADR ID & Title:** `ADR-002 — Penggunaan MySQL 8.0 sebagai RDBMS Tunggal`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Sistem memerlukan RDBMS yang stabil, mendukung transaksi ACID, foreign keys, index B-Tree efisien, dan tersedia secara native pada cPanel shared hosting tanpa daemon tambahan.
4. **Keputusan:**
   Menggunakan **MySQL 8.0+** dengan storage engine **InnoDB** dan charset `utf8mb4_unicode_ci`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *SQLite:* Ditolak karena konkurensi write rendah (database lock saat traffic spike).
   - *PostgreSQL:* Ditolak karena jarang tersedia di shared hosting cPanel Indonesia.
   - *Redis:* Ditolak karena shared hosting tidak menyediakan persistent Redis daemon tanpa root.
6. **Konsekuensi Positif:**
   - 100% native di cPanel; mendukung index komposit, transaksi InnoDB, dan query JSON.
7. **Konsekuensi Negatif & Trade-offs:**
   - Seluruh beban write safelink dan logging bertumpu pada MySQL disk I/O.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Hosting menjalankan MariaDB 10.5+ alih-alih Oracle MySQL 8.0.
   - *Mitigasi:* Skema DDL pada [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md) dirancang 100% netral lintas MySQL 8 dan MariaDB 10.5+.
9. **Kaitannya dengan Keputusan PO:** *No dedicated DEC relationship (Architectural Baseline)*
10. **Kebutuhan Terkait:** `NFR-ARCH-003`, `NFR-DATA-001`, `NFR-HOST-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001`, `TEST-ANA-004`
12. **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**  
    *Penjelasan:* Menunggu eksekusi perintah `mysql -V` di cPanel WarnaHost untuk memastikan varian dan versi database aktual.

---

### ADR-003: Blade Templating + Native CSS + Vanilla JS (No SPA)

1. **ADR ID & Title:** `ADR-003 — Blade Templating + Native CSS + Vanilla JS (No SPA)`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Halaman safelink publik diakses oleh beragam perangkat ponsel dan koneksi internet bervariasi. Halaman harus memuat instan (LCP < 1.2s, aset transfer < 20 KB) untuk mengoptimasi retensi tayangan iklan.
4. **Keputusan:**
   Menggunakan **Laravel Blade Views, Semantic HTML5, Native CSS3 (CSS Variables), dan Vanilla JS (ES6+)**. Menolak framework frontend berbasis JavaScript runtime / SPA.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *SPA (React / Vue):* Ditolak karena payload JavaScript besar (> 150 KB) dan membutuhkan Node.js runtime/build step berat.
   - *Tailwind CSS di Publik:* Ditolak untuk halaman publik agar ukuran CSS minimal (< 8 KB).
   - *Livewire / Inertia:* Ditolak di halaman safelink agar timer berjalan secara lokal tanpa latensi network.
6. **Konsekuensi Positif:**
   - Payload CSS publik < 8 KB, Vanilla JS < 5 KB.
   - Zero compilation step di server produksi shared hosting (tanpa `npm run build`).
7. **Konsekuensi Negatif & Trade-offs:**
   - Pengembang menulis CSS secara disiplin tanpa generator utility classes.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Inkonsistensi style antarkomponen.
   - *Mitigasi:* Design system terpusat pada [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) dengan CSS Custom Properties.
9. **Kaitannya dengan Keputusan PO:** *No dedicated DEC relationship (Architectural Baseline)*
10. **Kebutuhan Terkait:** `NFR-PERF-001`, `NFR-PERF-002`, `NFR-PERF-003`
11. **Test Case Terkait:** `TEST-PERF-001`, `TEST-PERF-002`, `TEST-SAFE-001`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-004: Penolakan Google AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan

1. **ADR ID & Title:** `ADR-004 — Penolakan Google AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Model monetisasi safelink membutuhkan jaringan periklanan yang memperbolehkan format halaman utilitas/countdown. Google AdSense secara ketat melarang halaman safelink (*Thin Content* & *Forced Waiting*), yang berakibat penutupan akun permanen.
4. **Keputusan:**
   1. **Menolak mutlak Google AdSense** pada alur safelink.
   2. Menetapkan **Adsterra** sebagai `CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT & DOMAIN VERIFICATION)`.
   3. Menerapkan slot iklan yang modular dan agnostik (`ad_slots` table) untuk fleksibilitas perpindahan vendor (Monetag/PropellerAds).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Google AdSense:* Ditolak keras demi menghindari penalti akun Google Publisher.
   - *Direct Ads Only:* Ditolak pada tahap awal karena belum memiliki audiens untuk penawaran iklan langsung.
6. **Konsekuensi Positif:**
   - Menghindarkan akun utama pemilik dari sanksi AdSense.
   - Arsitektur slot iklan agnostik siap memuat vendor periklanan apapun.
7. **Konsekuensi Negatif & Trade-offs:**
   - eCPM jaringan iklan pihak ketiga fluktuatif bergantung pada kualitas trafik geografis.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Penolakan domain atau penangguhan akun oleh Adsterra.
   - *Mitigasi:* Penegakan batas aman visual (jarak vertikal min 30px sebagai internal UI safeguard) dan sterilisasi tombol unduhan (lihat ADR-014).
9. **Kaitannya dengan Keputusan PO:** `DEC-002 (APPROVED 2026-10-31: Status Kandidat Jaringan Iklan Adsterra)`
10. **Kebutuhan Terkait:** `FR-ADS-001` s/d `FR-ADS-005`, `NFR-MON-001`
11. **Test Case Terkait:** `TEST-ADS-001` s/d `TEST-ADS-005`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Domain dan akun Adsterra wajib diverifikasi resmi sebelum deployment tag iklan produksi; penempatan iklan tidak boleh menyerupai tombol navigasi unduhan.

---

### ADR-005: Tabel Khusus `visitor_sessions` & User-Agent Hash

1. **ADR ID & Title:** `ADR-005 — Tabel Khusus visitor_sessions & UA Hash`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Driver session berbasis file (`storage/framework/sessions`) membuat puluhan ribu file kecil di shared hosting, menghabiskan kuota *inodes* secara cepat. Mengikat sesi hanya pada alamat IP menyebabkan *session drop* pada jaringan seluler (CGNAT) yang sering merotasi IP publik saat countdown berjalan.
4. **Keputusan:**
   Memisahkan sesi safelink ke tabel MySQL khusus **`visitor_sessions`** dengan identitas **Token Kriptografis (256-bit CSPRNG)** dan **User-Agent Hash**.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Laravel File Session Driver:* Ditolak karena risiko kehabisan kuota inodes hosting.
   - *Sesi Terikat IP Ketat:* Ditolak karena memicu penolakan valid pada pengguna ponsel seluler yang mengalami rotasi IP wajar.
   - *Client-Side JWT:* Ditolak karena mempersulit mekanisme pembatalan atomic sekali pakai (*single-use token*).
6. **Konsekuensi Positif:**
   - Inodes hosting aman; toleran terhadap rotasi IP seluler tanpa mengorbankan keamanan.
7. **Konsekuensi Negatif & Trade-offs:**
   - Menambah baris tabel MySQL (dibersihkan via TTL 24 jam oleh scheduler).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Pertumbuhan tabel `visitor_sessions`.
   - *Mitigasi:* Scheduler membersihkan sesi berumur > 24 jam setiap jam.
9. **Kaitannya dengan Keputusan PO:** *No dedicated DEC relationship (Architectural Baseline)*
10. **Kebutuhan Terkait:** `FR-SAFE-001`, `NFR-SEC-002`, `NFR-PERF-003`
11. **Test Case Terkait:** `TEST-SAFE-001`, `TEST-SAFE-007`, `TEST-SAFE-011`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-006: Cloudflare Turnstile untuk Mitigasi Bot

1. **ADR ID & Title:** `ADR-006 — Cloudflare Turnstile untuk Mitigasi Bot`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Safelink menjadi target bot scraper dan crawler unduhan otomatis. Diperlukan perlindungan bot yang efektif, ramah pengguna ponsel, dan tidak menimbulkan biaya tinggi.
4. **Keputusan:**
   Mengintegrasikan **Cloudflare Turnstile** sebelum countdown timer dimulai. Verifikasi token dievaluasi secara server-side via endpoint Cloudflare `siteverify`. Dilengkapi sakelar darurat bypass (`turnstile_mode = emergency_bypass`).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Google reCAPTCHA v2:* Ditolak karena puzzle gambar menyulitkan pengguna ponsel.
   - *Google reCAPTCHA v3:* Ditolak karena false-positive pemblokiran IP seluler di Indonesia.
   - *Custom Math Captcha:* Ditolak karena mudah di-bypass oleh skrip OCR/AI.
6. **Konsekuensi Positif:**
   - UX tanpa hambatan (*smart challenge* tanpa teka-teki visual).
   - Paket gratis Cloudflare saat ini mengakomodasi volume safelink tanpa biaya per-request (tunduk pada Acceptable Use Policy Cloudflare).
   - Mudah di-mock dalam testing (`Http::fake()`).
7. **Konsekuensi Negatif & Trade-offs:**
   - Ketergantungan jaringan eksternal ke Cloudflare API.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Timeout jaringan atau pemadaman global Cloudflare.
   - *Mitigasi:* Timeout HTTP client dibatasi maksimal 5 detik (`Http::timeout(5)`), penanganan HTTP 503 yang ramah (`TEST-SAFE-010`), dan sakelar darurat `emergency_bypass` (DEC-011).
9. **Kaitannya dengan Keputusan PO:**  
   - `DEC-005 (APPROVED 2026-10-31: Titik Penempatan Cloudflare Turnstile Sebelum Countdown)`  
   - `DEC-011 (APPROVED 2026-10-31: Mode Darurat Turnstile emergency_bypass)`
10. **Kebutuhan Terkait:** `FR-SAFE-002`, `NFR-SEC-003`
11. **Test Case Terkait:** `TEST-SAFE-004`, `TEST-SAFE-009`, `TEST-SAFE-010`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Timeout cURL dibatasi 5 detik; klausul penggunaan gratis tunduk pada Terms of Service Cloudflare tanpa klaim kapasitas tak terbatas secara absolut.

---

### ADR-007: Validasi Countdown Server-Side & Transisi Token Atomic

1. **ADR ID & Title:** `ADR-007 — Validasi Countdown Server-Side & Transisi Token Atomic`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Pengunjung teknis dapat memanipulasi variabel timer JavaScript di browser console untuk melewati countdown. Penyerang juga dapat mengirimkan request konkuren paralel (*race condition*).
4. **Keputusan:**
   1. Validasi waktu countdown dievaluasi sepenuhnya di **server-side** dengan membandingkan selisih timestamp server: `(NOW() - countdown_started_at) >= countdown_seconds`. Durasi default ditetapkan **10 detik** (DEC-004).
   2. Transisi status token di basis data dieksekusi secara **atomic**:
      ```sql
      UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0;
      ```
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Client-Side Validation:* Ditolak keras karena mudah dimanipulasi via console browser.
   - *SELECT Then UPDATE:* Ditolak karena membuka celah race condition pada request konkuren.
6. **Konsekuensi Positif:**
   - Kebal manipulasi timer client (ditolak dengan HTTP 422).
   - Menjamin token hanya dapat digunakan satu kali (*single-use*), kebal race condition.
7. **Konsekuensi Negatif & Trade-offs:**
   - Menuntut akurasi sinkronisasi waktu server (NTP).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Clock drift server.
   - *Mitigasi:* Server WarnaHost menggunakan NTP otomatis cPanel; perbandingan selisih waktu internal database (`TIMESTAMPDIFF`) kebal perbedaan timezone.
9. **Kaitannya dengan Keputusan PO:** `DEC-004 (APPROVED 2026-10-31: Durasi Default Countdown Timer 10 Detik)`
10. **Kebutuhan Terkait:** `FR-SAFE-003`, `FR-SAFE-004`, `NFR-SEC-001`
11. **Test Case Terkait:** `TEST-SAFE-005`, `TEST-SAFE-006`, `TEST-SAFE-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-008: Enkripsi Simetris AES-256 Destination URL

1. **ADR ID & Title:** `ADR-008 — Enkripsi Simetris AES-256 Destination URL`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   URL tujuan asli adalah aset komersial sensitif. Jika file backup basis data SQL terekspos, URL asli tidak boleh terbaca dalam format teks polos (*plaintext*).
4. **Keputusan:**
   Mengenkripsi kolom `destination_url` di basis data menggunakan algoritma simetris **AES-256-CBC** melalui Eloquent Attribute Casting (`'encrypted'`). Dekripsi hanya dilakukan di memori RAM server sesaat sebelum header HTTP 302 diserahkan kepada pengunjung sah.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Plaintext Storage:* Ditolak karena mengekspos seluruh tautan rahasia jika backup bocor.
   - *One-Way Hashing:* Ditolak karena sistem harus mampu mengarahkan pengguna ke URL asli.
6. **Konsekuensi Positif:**
   - Menjamin *confidentiality at rest* sesuai prinsip keamanan modern.
   - Terintegrasi transparan dengan model Eloquent tanpa manipulasi kueri manual.
7. **Konsekuensi Negatif & Trade-offs:**
   - Tidak dapat melakukan pencarian `LIKE %query%` langsung pada kolom URL di SQL.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Kehilangan `APP_KEY` mematikan proses dekripsi permanen.
   - *Mitigasi:* Backup aman file `.env` di luar repositori; larangan menjalankan `key:generate` ulang pasca-instalasi awal.
9. **Kaitannya dengan Keputusan PO:** *No dedicated DEC relationship (Architectural Baseline)*
10. **Kebutuhan Terkait:** `FR-LINK-001`, `NFR-SEC-004`
11. **Test Case Terkait:** `TEST-LINK-001`, `TEST-SEC-002`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-009: Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited

1. **ADR ID & Title:** `ADR-009 — Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Kebutuhan operasional LokaTaut pada fase awal menuntut infrastruktur berbiaya hemat (< Rp 100.000 / bulan), memiliki konektivitas lokal Indonesia (IIX), dan mendukung LiteSpeed Web Server.
4. **Keputusan:**
   Menetapkan **WarnaHost Starter Unlimited (1 vCPU Core, 2 GB RAM fisik CloudLinux LVE, NVMe SSD)** sebagai target dengan status **`TARGET HOSTING (NEEDS PRE-DEPLOYMENT RUNTIME VERIFICATION)`**. Seluruh kode diletakkan di luar `public_html`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Cloud VPS:* Ditolak karena biaya lebih tinggi dan menuntut manajemen OS/keamanan mandiri.
   - *Serverless:* Ditolak karena kendala koneksi basis data relasional dan biaya tak terduga.
6. **Konsekuensi Positif:**
   - Biaya ekonomis (~Rp 79.000/bulan); web server LiteSpeed mendukung caching native.
7. **Konsekuensi Negatif & Trade-offs:**
   - Pembatasan memori fisik 2 GB diawasi ketat oleh CloudLinux LVE. Tidak ada akses root.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Memory exhaustion (OOM) saat migration atau kompilasi composer.
   - *Mitigasi:* Menjalankan verifikasi pre-flight CLI dan menyediakan prosedur pre-built vendor (ADR-013).
9. **Kaitannya dengan Keputusan PO:** `DEC-003 (APPROVED 2026-10-31: Verifikasi Runtime Hosting WarnaHost Starter)`
10. **Kebutuhan Terkait:** `NFR-HOST-001` s/d `NFR-HOST-004`, `NFR-DEPL-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001` s/d `TEST-DEPLOY-004`
12. **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**  
    *Penjelasan:* Menunggu eksekusi 7 perintah verifikasi terminal cPanel pada akun hosting riil pengguna (lihat [README.md](./README.md#5-protokol-pemeriksaan-pra-penerbangan-hosting-pre-flight-checks)).

---

### ADR-010: Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC

1. **ADR ID & Title:** `ADR-010 — Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Penyimpanan log kunjungan tanpa batas akan menghabiskan kuota disk shared hosting. Menyimpan alamat IP mentah melanggar prinsip privasi (UU PDP / GDPR).
4. **Keputusan:**
   1. Data mentah `visits` disimpan dengan batas retensi maksimal **90 hari** sebelum dibersihkan via scheduler, setelah diringkas permanen ke `daily_stats`.
   2. Alamat IP pengunjung disamarkan satu arah menggunakan **Keyed HMAC-SHA256** dengan salt khusus `ANALYTICS_SALT`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Retensi Data Selamanya:* Ditolak karena memicu pembengkakan disk (> 10 GB) dan melanggar prinsip *data minimization*.
   - *Penyimpanan IP Plaintext:* Ditolak keras karena risiko kebocoran data pribadi (PII).
6. **Konsekuensi Positif:**
   - Ukuran tabel `visits` terjaga stabil di kisaran ~420 MB (skenario normal dengan index B-Tree).
   - Menjamin privasi pengunjung tanpa menyimpan PII.
7. **Konsekuensi Negatif & Trade-offs:**
   - Log kunjungan mentah individual di atas 90 hari tidak dapat diaudit (hanya tersisa rekap agregat).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Scheduler retensi gagal berjalan atau menghapus data sebelum teragregasi.
   - *Mitigasi:* Script prune mewajibkan verifikasi status `completed` pada tabel `aggregation_checkpoints` sebelum menghapus baris mentah.
9. **Kaitannya dengan Keputusan PO:** `DEC-006 (APPROVED 2026-10-31: Kebijakan Retensi Data Kunjungan 90 Hari)`
10. **Kebutuhan Terkait:** `FR-ANA-001`, `NFR-DATA-002`, `NFR-PRIV-001`
11. **Test Case Terkait:** `TEST-ANA-001`, `TEST-ANA-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-011: Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (`token_hash`)

1. **ADR ID & Title:** `ADR-011 — Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (token_hash)`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Menyimpan token sesi plaintext di database membuka celah: jika file backup database bocor, penyerang dapat membaca token aktif dan langsung memanggil endpoint `/proceed` untuk membajak unduhan tanpa melewati Turnstile dan countdown.
4. **Keputusan:**
   Menerapkan standar **OWASP ASVS v4.0 Section 3.2.4**. Kolom pada `visitor_sessions` diubah menjadi **`token_hash VARCHAR(64)`** yang menyimpan nilai `hash('sha256', $rawToken)`. Nilai mentah `$rawToken` (256-bit CSPRNG) hanya dipegang oleh browser client.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Plaintext Token:* Ditolak karena risiko eksfiltrasi backup SQL dan pembajakan sesi safelink aktif.
   - *Bcrypt Hashing:* Ditolak karena kalkulasi lambat membebani CPU pada ribuan request/menit. SHA-256 kriptografis cepat dan kebal collision.
6. **Konsekuensi Positif:**
   - Sekalipun database terekspos, penyerang tidak dapat merekonstruksi token mentah untuk bypass.
   - Kueri hash SHA-256 terindeks secara optimal (`INDEX token_hash`).
7. **Konsekuensi Negatif & Trade-offs:**
   - Controller melakukan 1x hashing `hash('sha256', $rawToken)` pada setiap validasi.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Perbedaan encoding token.
   - *Mitigasi:* Token di-generate menggunakan heksadesimal baku (`bin2hex(random_bytes(32))`) 64 karakter alfanumerik.
9. **Kaitannya dengan Keputusan PO:** `DEC-009 (APPROVED 2026-10-31: Penyimpanan Hashed Session Token token_hash)`
10. **Kebutuhan Terkait:** `FR-SAFE-001`, `NFR-SEC-001`
11. **Test Case Terkait:** `TEST-SAFE-001`, `TEST-SEC-001`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-012: Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (`FR-AUTH-004`)

1. **ADR ID & Title:** `ADR-012 — Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (FR-AUTH-004)`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Dokumentasi awal v1.0 tidak menyediakan UI/rute bagi admin untuk mengganti kata sandi. Mengedit hash manual di phpMyAdmin atau tinker berbahaya dan rawan human error pada produksi.
4. **Keputusan:**
   Menambahkan fitur **`FR-AUTH-004`**, rute aman `GET /admin/profile` dan `PUT /admin/profile/password`, Form Request `UpdatePasswordRequest`, serta pencatatan insiden rotasi pada `security_logs`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Reset Password via Email:* Ditolak karena sistem bertipe private single-admin; konfigurasi SMTP mail server di shared hosting sering tidak stabil dan rentan masuk folder spam.
   - *Edit Manual di Database:* Ditolak keras karena menyalahi standar keamanan aplikasi web.
6. **Konsekuensi Positif:**
   - Mematuhi standar OWASP ASVS v4.0 Section 2.1 (Credential Management).
   - Validasi ketat kata sandi saat ini (`current_password`), panjang minimal 12 karakter, dan otomatis merotasi session ID login.
7. **Konsekuensi Negatif & Trade-offs:**
   - Menambah 2 rute dan 1 view Blade baru di panel admin.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Brute force terhadap endpoint penggantian kata sandi.
   - *Mitigasi:* Pembatasan rate limiting 5 percobaan/menit dan audit log otomatis pada `security_logs` (`TEST-AUTH-007`).
9. **Kaitannya dengan Keputusan PO:** `DEC-008 (APPROVED 2026-10-31: Penambahan Fitur Ganti Kata Sandi Admin FR-AUTH-004)`
10. **Kebutuhan Terkait:** `FR-AUTH-004`, `NFR-SEC-005`
11. **Test Case Terkait:** `TEST-AUTH-006`, `TEST-AUTH-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-013: Fallback Pre-Built Vendor Deployment untuk Shared Hosting

1. **ADR ID & Title:** `ADR-013 — Fallback Pre-Built Vendor Deployment untuk Shared Hosting`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Paket shared hosting WarnaHost Starter dibatasi memori 2 GB. Perintah `composer install` secara langsung di terminal hosting dapat memicu penghentian paksa proses (*SIGKILL / Out of Memory*) oleh CloudLinux LVE, meninggalkan direktori vendor korup.
4. **Keputusan:**
   Menyediakan prosedur deployment fallback resmi: direktori `vendor/` dan dependensi dikompilasi pada lingkungan lokal/CI yang identik dengan PHP 8.3 target, dikemas dalam arsip ZIP, diunggah via cPanel File Manager/FTP, lalu diekstrak ke server.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Live Composer di Server:* Dipertahankan sebagai opsi sekunder jika memori mencukupi, namun dilarang menjadi satu-satunya jalur deployment.
   - *Git Push Deploy:* Ditolak karena shared hosting tidak menyediakan webhook trigger andal tanpa daemon supervisor.
6. **Konsekuensi Positif:**
   - Menghilangkan risiko OOM / timeout saat proses deployment di hosting.
   - Waktu deployment cepat (< 2 menit untuk ekstraksi arsip).
7. **Konsekuensi Negatif & Trade-offs:**
   - Menuntut keselarasan versi PHP minor dan ekstensi antara mesin build lokal dan server hosting.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Ketidakcocokan ekstensi PHP native.
   - *Mitigasi:* Verifikasi pre-flight CLI `php -m` sebelum deployment; eksekusi `mysqldump` sebelum migrasi basis data dijalankan.
9. **Kaitannya dengan Keputusan PO:** `DEC-010 (APPROVED 2026-10-31: Prosedur Fallback Pre-Built Vendor Deployment)`
10. **Kebutuhan Terkait:** `NFR-HOST-002`, `NFR-DEPL-001`
11. **Test Case Terkait:** `TEST-DEPLOY-002`, `TEST-DEPLOY-003`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Mesin lokal/CI wajib menggunakan PHP versi minor dan ekstensi yang identik dengan target hosting cPanel.

---

### ADR-014: Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink

1. **ADR ID & Title:** `ADR-014 — Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Jaringan periklanan (Adsterra/Monetag) menerapkan sistem deteksi kecurangan GeoEdge otomatis. Mengikat klik popunder pada tombol safelink atau meletakkan iklan bertumpuk dengan tombol proceed diklasifikasikan sebagai *Incentivized / Accidental Clicks*, yang memicu penahanan saldo atau penutupan domain.
4. **Keputusan:**
   1. **Menangguhkan format iklan popunder** pada rilis MVP.
   2. Mensterilkan tombol navigasi unduhan (`#proceed-btn`) secara mutlak dari skrip pelacak atau event hijacking pihak ketiga.
   3. Menerapkan jarak aman minimal 30px sebagai **internal UI safeguard** untuk mengurangi risiko salah klik (*accidental clicks*).
5. **Klarifikasi Batasan & Kebijakan:**
   - Jarak 30px adalah **internal UI safeguard pengembang**, **BUKAN** persyaratan resmi tertulis dari Adsterra.
   - Jarak 30px **BUKAN** bukti otomatis kepatuhan WCAG 2.5.5 secara menyeluruh (WCAG 2.5.5 mengatur target size dan spacing, namun pemenuhan standar menuntut audit elemen sentuh komprehensif).
   - Penempatan iklan dilarang keras menyerupai tombol aksi navigasi unduhan (*no deceptive UI / dark patterns*).
   - Dilarang merekayasa klik paksa (*no forced clicks*).
6. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Memasang Popunder pada MVP:* Ditolak karena risiko tinggi saldo iklan dibekukan sebelum reputasi akun terjalin.
   - *Iklan Bertumpuk:* Ditolak keras karena melanggar etika UX dan memicu penalti IVT.
7. **Konsekuensi Positif:**
   - Menjamin keamanan akun jaringan iklan dan reputasi domain sejak hari pertama.
   - Pengalaman pengguna lebih bersih tanpa banjir tab baru yang tidak diinginkan.
8. **Konsekuensi Negatif & Trade-offs:**
   - Potensi pendapatan awal lebih konservatif.
9. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Skrip iklan eksternal menyuntikkan event listener global pada elemen DOM.
   - *Mitigasi:* Isolasi event `#proceed-btn` via `e.stopPropagation()` dan audit elemen DOM berkala.
10. **Kaitannya dengan Keputusan PO:** `DEC-007 (APPROVED 2026-10-31: Penangguhan Format Popunder & Isolasi Tombol Navigasi)`
11. **Kebutuhan Terkait:** `FR-ADS-005`, `NFR-MON-001`
12. **Test Case Terkait:** `TEST-ADS-003`, `TEST-ADS-004`
13. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-015: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik

1. **ADR ID & Title:** `ADR-015 — Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Penerapan `ON DELETE CASCADE` di seluruh basis data akan menghapus riwayat statistik saat link dihapus. Sebaliknya, `ON DELETE RESTRICT` mencegah admin membersihkan tautan sampah.
4. **Keputusan:**
   1. Default penghapusan adalah **Soft Delete** (`deleted_at = NOW()`). Tautan dinonaktifkan untuk publik, namun relasi analitik tetap utuh. Agregasi menyertakan link soft-deleted via `Link::withTrashed()`.
   2. **Hard Delete (Force Delete)** hanya diizinkan dari Trash:
      - `visitor_sessions`: `ON DELETE CASCADE`.
      - `visits`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, snapshot `link_id_snapshot` dan `link_slug_snapshot` dipertahankan).
      - `daily_stats`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, namun `aggregate_key` bernilai `link:{snapshot_id}` dan snapshot `link_slug` tetap terbaca).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Hard Delete Instan:* Ditolak karena rentan kesalahan manusia yang tidak dapat dipulihkan.
   - *Cascade Menyeluruh:* Ditolak karena merusak laporan tren analitik masa lalu.
6. **Konsekuensi Positif:**
   - Melindungi data historis dari kehilangan tidak disengaja. Laporan statistik tetap akurat.
7. **Konsekuensi Negatif & Trade-offs:**
   - Penanganan relasi bernilai `NULL` pada Eloquent query di sisi aplikasi.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Exception saat mengakses properti link yang telah di-hard delete.
   - *Mitigasi:* Null-safe helper `optional($visit->link)->title ?? $visit->link_slug_snapshot`.
9. **Kaitannya dengan Keputusan PO:** `DEC-013 (APPROVED 2026-10-31: Kebijakan Penghapusan Data Soft & Hard Delete dan Snapshot Slug)`
10. **Kebutuhan Terkait:** `FR-LINK-006`, `FR-LINK-007`, `NFR-DATA-001`
11. **Test Case Terkait:** `TEST-LINK-006`, `TEST-LINK-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-016: Pemisahan Mandatori Salt Analitik (`ANALYTICS_SALT`) Tanpa Fallback ke `APP_KEY`

1. **ADR ID & Title:** `ADR-016 — Pemisahan Mandatori Salt Analitik (ANALYTICS_SALT) Tanpa Fallback ke APP_KEY`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   NIST SP 800-57 Part 1 Bagian 5.3.4 menegaskan prinsip *Cryptographic Key Separation*. Menggunakan `APP_KEY` sebagai salt analitik atau menyediakan fallback implisit mengikat siklus hidup analitik ke kunci enkripsi sesi. Jika `APP_KEY` dirotasi pada insiden darurat, seluruh hash IP pengunjung masa lalu akan rusak.
4. **Keputusan:**
   1. Menghapus secara ketat kode fallback `ANALYTICS_SALT` ke `APP_KEY`.
   2. Menjadwalkan `ANALYTICS_SALT` sebagai variabel wajib di `.env` produksi. Jika tidak dikonfigurasi saat `APP_ENV=production`, sistem melempar `RuntimeException` (fail-fast).
   3. Di lingkungan lokal/testing, fixed testing salt string digunakan untuk memfasilitasi automated testing.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Fallback ke APP_KEY:* Ditolak keras karena merusak kontinuitas analitik saat rotasi kunci enkripsi.
   - *Hash IP Tanpa Salt:* Ditolak karena rentan serangan rainbow table attack.
6. **Konsekuensi Positif:**
   - Kepatuhan penuh terhadap NIST SP 800-57 Part 1. Rotasi `APP_KEY` tidak mengganggu data analitik historis.
7. **Konsekuensi Negatif & Trade-offs:**
   - Kegagalan konfigurasi `.env` menghentikan booting aplikasi di produksi.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Admin lupa menyetel `ANALYTICS_SALT`.
   - *Mitigasi:* Template `.env.example` menyertakan key ini secara eksplisit dan verifikasi dijalankan pada skrip pre-flight deployment.
9. **Kaitannya dengan Keputusan PO:** `DEC-014 (APPROVED 2026-10-31: Mandatori Pemisahan Kunci ANALYTICS_SALT Tanpa Fallback)`
10. **Kebutuhan Terkait:** `NFR-SEC-006`, `NFR-PRIV-001`
11. **Test Case Terkait:** `TEST-ANA-002`, `TEST-SEC-003`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-017: Dedicated Aggregate Key NOT NULL dan Snapshot Identitas Tautan Kunjungan

1. **ADR ID & Title:** `ADR-017 — Dedicated Aggregate Key NOT NULL dan Snapshot Identitas Tautan Kunjungan`
2. **Status:** `PROPOSED (Under Review)`
3. **Konteks & Pernyataan Masalah:**
   Pada MySQL 8.0/InnoDB, constraint `UNIQUE KEY (link_id, date)` tidak membatasi baris ganda ketika `link_id` bernilai `NULL` (`NULL != NULL`). Jika tautan di-hard delete sebelum agregasi nightly dijalankan, foreign key `link_id` pada tabel `visits` menjadi `NULL`, menyebabkan agregasi kehilangan informasi slug asli dan menduplikasi baris saat cron job di-retry.
4. **Keputusan:**
   1. **DEC-015 (Approved 2026-10-31):** Menambahkan kolom `aggregate_key VARCHAR(100) NOT NULL` pada tabel `daily_stats` dengan format `'link:' . $linkIdSnapshot` atau partisi trafik sistem. Menerapkan constraint unik definitif `UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)` di mana kedua kolom `NOT NULL`.
   2. **DEC-016 (Under Review):** Menambahkan kolom snapshot immutable `link_id_snapshot BIGINT UNSIGNED NOT NULL` dan `link_slug_snapshot VARCHAR(50) NOT NULL` pada tabel sumber `visits`.
   3. **Arsitektur Idempotensi Berbasis Watermark & Checkpoint (Comprehensive Architecture):**
      - Tidak mengandalkan fungsi individual secara terpisah (`updateOrCreate`, `chunkById`, atau `withoutOverlapping` bukan jaminan mandiri).
      - Menerapkan tabel `aggregation_checkpoints` dengan pelacakan `last_processed_visit_id` per target tanggal.
      - Membungkus mutasi batch per-chunk dalam `DB::transaction()` dengan `lockForUpdate()`.
      - Mencegah tumpang tindih penjadwalan via `withoutOverlapping(60)->runInBackground()`.
      - Memastikan kunjungan dari tautan yang telah di-hard delete tetap diagregasikan ke format `link:{link_id_snapshot}`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Unique Key Nullable:* Ditolak karena mengizinkan baris duplikat tak terbatas saat `link_id IS NULL`.
   - *Delta Increments Tanpa Checkpoint:* Ditolak karena jika proses crash di tengah batch, retry akan menghitung baris yang sama dua kali.
   - *Full Recalculate Tanpa Windowing:* Ditolak karena scan seluruh tabel `visits` menghabiskan memori RAM dan IOPS shared hosting.
6. **Konsekuensi Positif:**
   - Menghilangkan anomali duplikasi baris agregasi pada MySQL 100%.
   - Retry scheduler aman secara matematis tanpa risiko double-counting atau data loss.
   - Tautan yang di-hard delete tetap terlacak performanya menggunakan partisi `link:{snapshot_id}` tanpa kehilangan slug historis.
7. **Konsekuensi Negatif & Trade-offs:**
   - Penambahan 2 kolom snapshot pada tabel `visits` (+55 s/d 75 byte/baris pada physical storage) dan 1 kolom kunci pada `daily_stats`, yang termitigasi oleh jadwal retensi data 90 hari.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Race condition / overlapping cron execution.
   - *Mitigasi:* Penerapan mutlak `withoutOverlapping(60)` pada Laravel Scheduler dan `DB::transaction()` dengan row lock saat upsert.
9. **Kaitannya dengan Keputusan PO:**  
   - `DEC-015 (APPROVED 2026-10-31: Dedicated Aggregate Key NOT NULL pada daily_stats)`  
   - `DEC-016 (UNDER REVIEW: Snapshot Identitas Tautan Kunjungan pada visits)`
10. **Kebutuhan Terkait:** `FR-ANA-003`, `NFR-DATA-003`
11. **Test Case Terkait:** `TEST-ANA-004`, `TEST-LINK-007`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Menunggu persetujuan definitif Product Owner atas DEC-016 pada [DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md).

