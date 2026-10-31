# Architecture Decision Records (ADR) Inventory — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-INV-01 |
| **Versi Dokumen** | 1.0 |
| **Status Dokumen** | UNDER REVIEW (Menunggu Keputusan Product Owner atas DEC-012) |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Panduan & Status Tata Kelola ADR

Sesuai aturan operasional repository [AGENTS.md](../AGENTS.md) dan ketetapan tata kelola pada [README.md](./README.md):
- **Otoritas Tunggal:** Hanya Product Owner (Dimas Aditya) yang memiliki kewenangan menyetujui, menolak, atau meminta revisi terhadap setiap ADR.
- **Status Formal ADR:** Seluruh 17 ADR saat ini berstatus **`PROPOSED / UNDER REVIEW`**.
- **Pemisahan Evaluasi:** Evaluasi tim peninjau teknis (`TECHNICALLY SOUND`, `SOUND WITH CONDITIONS`, `BLOCKED BY MISSING EVIDENCE`) adalah **rekomendasi profesional berbasis bukti**, bukan persetujuan otomatis. Persetujuan definitif menunggu penandatanganan lembar [DEC-012-APPROVAL-GOVERNANCE.md](./DEC-012-APPROVAL-GOVERNANCE.md).

---

## 2. Tabel Ringkasan Inventaris (Master ADR Registry)

| ADR ID | Judul Keputusan | Disiplin Utama | Status Formal | Evaluasi Peninjau | DEC Terkait | Kebutuhan Terkait | Test Case Kunci |
|:---:|---|---|:---:|:---:|:---:|---|---|
| **ADR-001** | Adopsi Modular Monolith Berbasis Laravel 13.x | Software Architecture | `PROPOSED` | `SOUND WITH CONDITIONS` | DEC-001 | NFR-ARCH-001, NFR-ARCH-002 | TEST-DEPLOY-001, TEST-AUTH-001 |
| **ADR-002** | Penggunaan MySQL 8.0 sebagai RDBMS Tunggal | Database Architecture | `PROPOSED` | `BLOCKED BY MISSING EVIDENCE` | DEC-002 | NFR-ARCH-003, NFR-DATA-001 | TEST-DEPLOY-001, TEST-ANA-004 |
| **ADR-003** | Blade + Native CSS + Vanilla JS (No SPA) | Frontend / UI Architecture | `PROPOSED` | `TECHNICALLY SOUND` | DEC-003 | NFR-PERF-001, NFR-PERF-002 | TEST-PERF-001, TEST-SAFE-001 |
| **ADR-004** | Penolakan AdSense & Kandidat Adsterra | Monetization / Compliance | `PROPOSED` | `SOUND WITH CONDITIONS` | DEC-004 | FR-ADS-001 s/d FR-ADS-004 | TEST-ADS-001 s/d TEST-ADS-005 |
| **ADR-005** | Tabel Khusus `visitor_sessions` & UA Hash | Security / Performance | `PROPOSED` | `TECHNICALLY SOUND` | DEC-005 | FR-SAFE-001, NFR-SEC-002 | TEST-SAFE-001, TEST-SAFE-007 |
| **ADR-006** | Cloudflare Turnstile untuk Mitigasi Bot | Security / Bot Mitigation | `PROPOSED` | `SOUND WITH CONDITIONS` | DEC-006 | FR-SAFE-002, NFR-SEC-003 | TEST-SAFE-004, TEST-SAFE-009, TEST-SAFE-010 |
| **ADR-007** | Validasi Countdown Server & Atomic Token | Security / Anti-Cheat | `PROPOSED` | `TECHNICALLY SOUND` | DEC-007 | FR-SAFE-003, FR-SAFE-004 | TEST-SAFE-005, TEST-SAFE-006, TEST-SAFE-007 |
| **ADR-008** | Enkripsi Simetris AES-256 Destination URL | Application Security | `PROPOSED` | `TECHNICALLY SOUND` | DEC-008 | FR-LINK-001, NFR-SEC-004 | TEST-LINK-001, TEST-SEC-002 |
| **ADR-009** | Target Hosting WarnaHost Starter Unlimited | DevOps / Infrastructure | `PROPOSED` | `BLOCKED BY MISSING EVIDENCE` | DEC-009 | NFR-HOST-001 s/d NFR-HOST-004 | TEST-DEPLOY-001 s/d TEST-DEPLOY-004 |
| **ADR-010** | Retensi Data 90 Hari & Keyed HMAC IP Hash | Data Privacy / Analytics | `PROPOSED` | `TECHNICALLY SOUND` | DEC-010 | FR-ANA-001, NFR-DATA-002 | TEST-ANA-001, TEST-ANA-007 |
| **ADR-011** | Penyimpanan Hashed Session Token (SHA-256) | Application Security | `PROPOSED` | `TECHNICALLY SOUND` | DEC-011 | FR-SAFE-001, NFR-SEC-001 | TEST-SAFE-001, TEST-SEC-001 |
| **ADR-012** | Admin Password Change & Profile Management | Security / Auth | `PROPOSED` | `TECHNICALLY SOUND` | DEC-012 (Scope) | FR-AUTH-004, NFR-SEC-005 | TEST-AUTH-006, TEST-AUTH-007 |
| **ADR-013** | Fallback Pre-Built Vendor Deployment | DevOps / Deployment | `PROPOSED` | `SOUND WITH CONDITIONS` | DEC-013 | NFR-HOST-002, NFR-DEPL-001 | TEST-DEPLOY-002, TEST-DEPLOY-003 |
| **ADR-014** | Penangguhan Popunder & Sterilisasi Tombol | Monetization / Anti-IVT | `PROPOSED` | `TECHNICALLY SOUND` | DEC-014 | FR-ADS-005, NFR-MON-001 | TEST-ADS-003, TEST-ADS-004 |
| **ADR-015** | Kebijakan Hapus Data & Retensi Analitik | Database Architecture | `PROPOSED` | `TECHNICALLY SOUND` | DEC-015 | FR-LINK-006, FR-LINK-007 | TEST-LINK-006, TEST-LINK-007 |
| **ADR-016** | Mandatori ANALYTICS_SALT Tanpa Fallback | Security / Cryptography | `PROPOSED` | `TECHNICALLY SOUND` | DEC-016 (Scope) | NFR-SEC-006, NFR-PRIV-001 | TEST-ANA-002, TEST-SEC-003 |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot Identitas | Database Architecture | `PROPOSED` | `SOUND WITH CONDITIONS` | DEC-016 (Core) | FR-ANA-003, NFR-DATA-003 | TEST-ANA-004, TEST-LINK-007 |

---

## 3. Inventaris Detail 17 ADR

---

### ADR-001: Adopsi Modular Monolith Berbasis Laravel 13.x

1. **ADR ID & Title:** `ADR-001 — Adopsi Modular Monolith Berbasis Laravel 13.x`
2. **Status:** `PROPOSED (PO Approved in Concept)`
3. **Konteks & Pernyataan Masalah:**
   Per Oktober 2026, Laravel 11 telah mencapai status End-of-Life (EOL) pada 12 Maret 2026. Memulai proyek baru di atas framework EOL menghadapkan sistem pada kerentanan zero-day yang tidak ditambal. Laravel 12 hanya memiliki sisa jendela dukungan keamanan hingga Februari 2027 (~4 bulan). Proyek membutuhkan framework modern yang memiliki masa dukungan jangka panjang, ekosistem matur, dan kompatibel dengan lingkungan shared hosting.
4. **Keputusan:**
   Mengadopsi **Laravel 13.x (PHP 8.3+)** dengan pola arsitektur **Modular Monolith** (`app/Modules/Auth`, `app/Modules/Links`, `app/Modules/Safelink`, `app/Modules/Monetization`, `app/Modules/Analytics`).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Laravel 11.x:* Ditolak karena telah EOL (tidak ada patch keamanan resmi).
   - *Laravel 12.x:* Ditolak karena sisa masa patch keamanan terlalu pendek (< 4 bulan).
   - *Microservices (Node.js / Go):* Ditolak karena overhead operasional berlebihan dan tidak didukung cPanel shared hosting tanpa root.
   - *Microframework (Slim / Lumen):* Ditolak karena Lumen telah abandonware dan Slim memerlukan perakitan manual ORM, migrasi, validasi, dan autentikasi.
6. **Konsekuensi Positif:**
   - Masa dukungan keamanan terjamin hingga 17 Maret 2028.
   - Fitur bawaan lengkap: Eloquent, Form Requests, Blade, Rate Limiter, Scheduler.
   - Batas modul internal yang rapi mencegah kopling kode tanpa overhead jaringan RPC.
7. **Konsekuensi Negatif & Trade-offs:**
   - Ukuran direktori `vendor/` Laravel 13 relatif besar (~65-85 MB).
   - Mengharuskan runtime target menyediakan PHP 8.3+ baik pada Web Server (LiteSpeed) maupun CLI (Terminal/Cron).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Perbedaan versi PHP antara web handler dan CLI cPanel.
   - *Mitigasi:* Verifikasi eksplisit path binary PHP (`/usr/local/bin/ea-php83`) pada crontab cPanel (lihat [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md)).
9. **Kaitannya dengan Keputusan PO:** `DEC-001 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `NFR-ARCH-001`, `NFR-ARCH-002`, `NFR-MAINT-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001`, `TEST-AUTH-001`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Verifikasi eksekusi perintah pre-flight `php -v` pada CLI WarnaHost sebelum scaffolding repositori.

---

### ADR-002: Penggunaan MySQL 8.0 sebagai RDBMS Tunggal

1. **ADR ID & Title:** `ADR-002 — Penggunaan MySQL 8.0 sebagai RDBMS Tunggal`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Sistem memerlukan sistem basis data relasional yang andal, berkinerja tinggi, mendukung transaksi ACID, memiliki integritas foreign key kuat, dan secara native tersedia pada cPanel shared hosting tanpa konfigurasi daemon tambahan.
4. **Keputusan:**
   Menggunakan **MySQL 8.0+** dengan storage engine **InnoDB** dan charset `utf8mb4_unicode_ci` sebagai RDBMS tunggal.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *SQLite:* Ditolak karena konkurensi write rendah (database lock pada trafik lonjakan) dan keterbatasan fitur concurrency lock.
   - *PostgreSQL:* Ditolak karena jarang tersedia secara default pada paket shared hosting cPanel di Indonesia.
   - *Redis (untuk Sesi/Cache):* Ditolak karena paket shared hosting tidak menyediakan persistent Redis daemon tanpa akses root.
6. **Konsekuensi Positif:**
   - Dukungan penuh native InnoDB transactions, JSON columns, CTE (Common Table Expressions), dan index komposit.
   - Kompatibilitas 100% dengan driver `mysql` Laravel dan tools cPanel (phpMyAdmin).
7. **Konsekuensi Negatif & Trade-offs:**
   - Seluruh beban write (sesi safelink, pencatatan kunjungan) bertumpu pada MySQL disk I/O.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Server shared hosting WarnaHost mungkin menjalankan **MariaDB 10.5+ / 10.11 LTS** alih-alih Oracle MySQL 8.0 resmi.
   - *Mitigasi:* Menjalankan perintah `mysql -V` pada terminal cPanel. Sintaks SQL DDL di [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md) dirancang kompatibel lintas MySQL 8.0 dan MariaDB 10.5+.
9. **Kaitannya dengan Keputusan PO:** `DEC-002 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `NFR-ARCH-003`, `NFR-DATA-001`, `NFR-HOST-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001`, `TEST-ANA-004`
12. **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**  
    *Penjelasan:* Membutuhkan eksekusi perintah `mysql -V` atau `SELECT VERSION();` di cPanel WarnaHost untuk memastikan varian dan versi database aktual.

---

### ADR-003: Blade Templating + Native CSS + Vanilla JS (No SPA)

1. **ADR ID & Title:** `ADR-003 — Blade Templating + Native CSS + Vanilla JS (No SPA)`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Halaman safelink publik diakses oleh pengunjung dari beragam spesifikasi perangkat seluler (low-end smartphone) dan koneksi internet yang bervariasi. Halaman safelink harus memiliki performa ekstrem (LCP < 1.2 detik, payload aset < 20 KB) untuk memaksimalkan retensi tayangan iklan dan skor Web Vitals.
4. **Keputusan:**
   Membangun seluruh antarmuka (baik panel admin maupun halaman publik) menggunakan **Laravel Blade Components, Semantic HTML5, Native CSS3 (CSS Custom Properties), dan Vanilla JavaScript (ES6+)**. Menolak framework frontend berbasis JavaScript runtime / SPA.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Single Page Application (React / Vue / Svelte):* Ditolak karena payload JavaScript besar (> 150 KB), rentan layout shift, dan memerlukan Node.js runtime untuk SSR atau build step berat.
   - *Tailwind CSS / Bootstrap di Public View:* Ditolak karena menambah ukuran CSS yang tidak diperlukan; Native CSS mencukupi (< 8 KB).
   - *Inertia.js / Livewire:* Ditolak untuk halaman publik safelink guna menjamin zero network dependency saat eksekusi countdown timer.
6. **Konsekuensi Positif:**
   - Payload CSS publik < 8 KB, Vanilla JS < 5 KB.
   - Zero compilation step di server produksi (tidak memerlukan `npm run build` di cPanel).
   - Rendering server-side instan via Blade engine.
7. **Konsekuensi Negatif & Trade-offs:**
   - Pengembang menulis CSS native secara disiplin tanpa utility classes generator.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Inkonsistensi style antarkomponen Blade.
   - *Mitigasi:* Dokumentasi design system terpusat pada [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) dengan variabel CSS seragam (`--primary`, `--bg-surface`, dll.).
9. **Kaitannya dengan Keputusan PO:** `DEC-003 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `NFR-PERF-001`, `NFR-PERF-002`, `NFR-PERF-003`
11. **Test Case Terkait:** `TEST-PERF-001`, `TEST-PERF-002`, `TEST-SAFE-001`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-004: Penolakan Google AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan

1. **ADR ID & Title:** `ADR-004 — Penolakan Google AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Model bisnis LokaTaut bertumpu pada monetisasi tayangan iklan pada alur safelink. Google AdSense secara ketat melarang halaman safelink/countdown karena dikategorikan sebagai *Thin Content*, *Forced Waiting*, dan *Unnatural Navigation*, yang berujung pada penutupan akun permanen. Sistem memerlukan jaringan periklanan yang memperbolehkan safelink dan aman dari sanksi invalid traffic.
4. **Keputusan:**
   1. **Menolak mutlak integrasi Google AdSense** pada halaman safelink.
   2. Menetapkan **Adsterra** sebagai `CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT & DOMAIN VERIFICATION)`.
   3. Merancang arsitektur slot iklan secara modular dan agnostik (`ad_slots` table) sehingga kompatibel dengan jaringan alternatif (Monetag/PropellerAds).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Google AdSense:* Ditolak mutlak karena melanggar Google Publisher Policies (risiko penalti akun).
   - *Direct Advertiser Insertion:* Ditolak untuk fase awal karena belum memiliki audiens untuk penjualan iklan langsung.
6. **Konsekuensi Positif:**
   - Melindungi akun AdSense pengelola dari pemblokiran.
   - Memberikan fleksibilitas pergantian vendor iklan tanpa mengubah struktur database atau kode aplikasi.
7. **Konsekuensi Negatif & Trade-offs:**
   - eCPM jaringan pihak ketiga fluktuatif dan bergantung pada kualitas trafik geografis (Tier 1 vs Tier 3).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Penolakan domain atau penangguhan akun oleh Adsterra jika tombol navigasi terlalu dekat dengan iklan.
   - *Mitigasi:* Penegakan batas aman visual (jarak vertikal min 30px) dan sterilisasi tombol unduhan (lihat ADR-014).
9. **Kaitannya dengan Keputusan PO:** `DEC-004 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-ADS-001` s/d `FR-ADS-005`, `NFR-MON-001`
11. **Test Case Terkait:** `TEST-ADS-001` s/d `TEST-ADS-005`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Domain dan akun Adsterra wajib diverifikasi secara resmi sebelum menempatkan tag iklan riil di lingkungan produksi.

---

### ADR-005: Tabel Khusus `visitor_sessions` & User-Agent Hash

1. **ADR ID & Title:** `ADR-005 — Tabel Khusus visitor_sessions & UA Hash`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Penyimpanan sesi bawaan Laravel berbasis file (`file` session driver) akan menciptakan puluhan ribu file teks kecil di shared hosting jika dikunjungi ribuan trafik safelink per hari. Hal ini dengan cepat menghabiskan kuota *inodes* shared hosting dan memicu disk failure. Selain itu, mengikat sesi semata-mata pada IP publik menimbulkan *false positive* kegagalan bagi pengguna jaringan seluler (CGNAT) yang IP-nya berganti saat transisi menara BTS.
4. **Keputusan:**
   Memisahkan sesi safelink anonim ke dalam tabel database MySQL khusus **`visitor_sessions`** dengan identitas sesi berupa **Token Acak Kriptografis (256-bit CSPRNG)** dan **User-Agent Hash**. Sesi safelink dibersihkan secara otomatis via scheduled task harian.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Laravel File Session Driver:* Ditolak karena risiko kehabisan kuota *inodes* hosting.
   - *Sesi Terikat IP Ketat:* Ditolak karena membatalkan sesi pengguna seluler yang mengalami rotasi alamat IP dinamis saat countdown berjalan.
   - *Client-Side JWT Cookie:* Ditolak karena mengekspos state dan mempersulit pembatalan atomic sekali pakai.
6. **Konsekuensi Positif:**
   - Inodes hosting aman dari penumpukan file sementara.
   - Toleran terhadap rotasi IP seluler tanpa mengorbankan keamanan.
   - Sesi dapat dibersihkan secara terukur via scheduled query SQL.
7. **Konsekuensi Negatif & Trade-offs:**
   - Menambah volume baris tabel MySQL (dikelola dengan TTL retensi sesi aktif 30 menit).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Pertumbuhan tabel `visitor_sessions` jika trafik harian melonjak.
   - *Mitigasi:* Scheduler membersihkan sesi berumur > 24 jam setiap 1 jam (`DELETE FROM visitor_sessions WHERE created_at < NOW() - INTERVAL 24 HOUR`).
9. **Kaitannya dengan Keputusan PO:** `DEC-005 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-SAFE-001`, `NFR-SEC-002`, `NFR-PERF-003`
11. **Test Case Terkait:** `TEST-SAFE-001`, `TEST-SAFE-007`, `TEST-SAFE-011`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-006: Cloudflare Turnstile untuk Mitigasi Bot

1. **ADR ID & Title:** `ADR-006 — Cloudflare Turnstile untuk Mitigasi Bot`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Platform safelink menjadi target utama bot scraper otomatis dan crawler unduhan yang membebani server serta mendegradasi reputasi trafik iklan. Sistem membutuhkan perlindungan bot yang efektif, tidak mengganggu pengguna manusia, dan memiliki model harga terjangkau.
4. **Keputusan:**
   Mengintegrasikan **Cloudflare Turnstile** pada tahap awal halaman safelink sebelum countdown timer dapat diinisiasi. Verifikasi token Turnstile dieksekusi secara server-side via endpoint Cloudflare `siteverify`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Google reCAPTCHA v2 (Checkbox/Puzzle):* Ditolak karena pengalaman pengguna buruk pada ponsel pintar (puzzle gambar lambat) dan isu privasi Google.
   - *Google reCAPTCHA v3:* Ditolak karena sistem skoring probabilitas sering memblokir pengguna seluler sah di Indonesia tanpa opsi verifikasi ulang yang ramah.
   - *Custom Math/Text Captcha:* Ditolak karena mudah ditembus oleh OCR dan bot berbasis LLM modern.
6. **Konsekuensi Positif:**
   - Pengalaman pengguna tanpa hambatan (*invisible/smart challenge* tanpa puzzle gambar).
   - Gratis tanpa batas kuota request per bulan untuk kebutuhan operasional LokaTaut.
   - Mudah di-mock dalam automated testing menggunakan `Http::fake()`.
7. **Konsekuensi Negatif & Trade-offs:**
   - Ketergantungan jaringan eksternal ke server Cloudflare saat verifikasi token.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Timeout jaringan atau kegagalan API Cloudflare memblokir 100% alur safelink.
   - *Mitigasi:* Penerapan cURL timeout 3-5 detik dengan penanganan HTTP 503 yang ramah (`TEST-SAFE-010`) dan sakelar darurat bypass di konfigurasi jika Cloudflare mengalami outage global.
9. **Kaitannya dengan Keputusan PO:** `DEC-006 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-SAFE-002`, `NFR-SEC-003`
11. **Test Case Terkait:** `TEST-SAFE-004`, `TEST-SAFE-009`, `TEST-SAFE-010`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Timeout HTTP client ke API Turnstile dibatasi maksimal 5 detik dan mekanisme penanganan fallback outage terdokumentasi.

---

### ADR-007: Validasi Countdown Server-Side & Transisi Token Atomic

1. **ADR ID & Title:** `ADR-007 — Validasi Countdown Server-Side & Transisi Token Atomic`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Pengunjung teknis sering memanipulasi variabel timer JavaScript di browser console untuk melewati waktu tunggu safelink. Selain itu, penyerang dapat mengirimkan request unduhan konkuren secara paralel untuk memanfaatkan *race condition*.
4. **Keputusan:**
   1. Validasi waktu countdown dievaluasi sepenuhnya di **server-side** dengan membandingkan timestamp server: `(NOW() - countdown_started_at) >= countdown_seconds`.
   2. Transisi status token sesi di basis data dieksekusi secara **atomic** menggunakan kueri bersyarat tunggal:
      ```sql
      UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0;
      ```
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Client-Side Timer Validation:* Ditolak keras karena mudah di-bypass dengan mengubah nilai variabel JavaScript di devtools.
   - *SELECT Then UPDATE (Non-Atomic):* Ditolak karena membuka celah *race condition* pada dua request konkuren yang tiba pada milidetik yang sama.
6. **Konsekuensi Positif:**
   - Kebal terhadap manipulasi timer dari sisi client (insiden bypass langsung ditolak dengan HTTP 422).
   - Menjamin token sesi hanya dapat digunakan satu kali (*single-use token*), mencegah serangan replay.
7. **Konsekuensi Negatif & Trade-offs:**
   - Membutuhkan sinkronisasi waktu clock server yang akurat (NTP).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Drift waktu server pada shared hosting.
   - *Mitigasi:* Server WarnaHost menggunakan sinkronisasi NTP otomatis cPanel; toleransi clock drift tidak mempengaruhi perbandingan selisih waktu internal database (`TIMESTAMPDIFF`).
9. **Kaitannya dengan Keputusan PO:** `DEC-007 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-SAFE-003`, `FR-SAFE-004`, `NFR-SEC-001`
11. **Test Case Terkait:** `TEST-SAFE-005`, `TEST-SAFE-006`, `TEST-SAFE-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-008: Enkripsi Simetris AES-256 Destination URL

1. **ADR ID & Title:** `ADR-008 — Enkripsi Simetris AES-256 Destination URL`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   URL tujuan unduhan asli merupakan aset bisnis sensitif. Jika basis data terekspos akibat kebocoran file SQL backup atau eksfiltrasi sql injection, seluruh URL tujuan asli tidak boleh terbaca dalam bentuk teks polos (*plaintext*).
4. **Keputusan:**
   Mengenkripsi kolom `destination_url` di basis data menggunakan algoritma simetris **AES-256-CBC** melalui fitur Eloquent Attribute Casting (`'encrypted'`). URL asli didekripsi di memori server sesaat sebelum header HTTP 302 diserahkan kepada browser.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Plaintext Storage:* Ditolak karena membuka seluruh URL tujuan jika basis data bocor.
   - *Hashing Satu Arah (SHA-256/Bcrypt):* Ditolak karena sistem harus mampu mengarahkan pengguna ke URL tujuan asli (hashing tidak dapat didekripsi).
   - *Client-Side Decryption Key:* Ditolak karena kunci enkripsi akan terekspos di browser pengunjung.
6. **Konsekuensi Positif:**
   - Perlindungan kerahasiaan data tingkat tinggi (*confidentiality at rest*).
   - Terintegrasi transparan dengan model Eloquent tanpa kueri kriptografi manual.
7. **Konsekuensi Negatif & Trade-offs:**
   - Tidak dapat melakukan pencarian substring langsung via `WHERE destination_url LIKE '%query%'` di SQL (pencarian dilakukan via filter slug atau judul).
   - Sedikit overhead CPU untuk enkripsi/dekripsi AES (dapat diabaikan, < 0.2 ms per operasi).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Kehilangan `APP_KEY` menyebabkan seluruh URL tujuan tidak dapat didekripsi permanen.
   - *Mitigasi:* Prosedur operasional mewajibkan pencadangan aman file `.env` di luar repositori git; `php artisan key:generate` dilarang dijalankan ulang pasca-instalasi awal.
9. **Kaitannya dengan Keputusan PO:** `DEC-008 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-LINK-001`, `NFR-SEC-004`
11. **Test Case Terkait:** `TEST-LINK-001`, `TEST-SEC-002`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-009: Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited

1. **ADR ID & Title:** `ADR-009 — Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Kebutuhan operasional LokaTaut pada fase awal menuntut infrastruktur yang hemat biaya (< Rp 100.000 / bulan), memiliki stabilitas jaringan lokal Indonesia (IIX), serta mendukung konfigurasi web server LiteSpeed.
4. **Keputusan:**
   Menetapkan **WarnaHost Starter Unlimited (1 vCPU Core, 2 GB RAM fisik CloudLinux LVE, NVMe SSD)** sebagai target infrastruktur dengan status **`TARGET HOSTING (NEEDS PRE-DEPLOYMENT VERIFICATION)`**. Seluruh kode Laravel diletakkan di luar root publik (`public_html`).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Cloud VPS (DigitalOcean / AWS / Linode):* Ditolak karena biaya lebih tinggi ($6-$12/bulan) dan memerlukan manajemen sistem operasi/keamanan mandiri (maintenance overhead).
   - *Serverless (AWS Lambda / Vercel):* Ditolak karena batasan koneksi basis data MySQL relasional dan biaya bandwidth tak terprediksi.
6. **Konsekuensi Positif:**
   - Biaya infrastruktur sangat terjangkau (~Rp 79.000/bulan).
   - Web server LiteSpeed Enterprise mendukung header caching native (`X-LiteSpeed-Cache-Control`).
   - cPanel menyederhanakan manajemen domain, SSL Let's Encrypt, dan backup.
7. **Konsekuensi Negatif & Trade-offs:**
   - Keterbatasan sumber daya komputasi fisik (1 CPU, 2 GB RAM) yang diawasi ketat oleh CloudLinux LVE.
   - Tidak ada akses root, tidak mendukung Docker, dan tidak ada persistent background daemon tanpa crontab.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* OOM (Out-of-Memory) saat menjalankan artisan migration atau composer di server.
   - *Mitigasi:* Menerapkan strategi pre-built vendor deployment (ADR-013) dan pembersihan retensi berkala (ADR-010).
9. **Kaitannya dengan Keputusan PO:** `DEC-009 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `NFR-HOST-001` s/d `NFR-HOST-004`, `NFR-DEPL-001`
11. **Test Case Terkait:** `TEST-DEPLOY-001` s/d `TEST-DEPLOY-004`
12. **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**  
    *Penjelasan:* Membutuhkan eksekusi 7 perintah pre-flight verifikasi terminal cPanel pada akun hosting riil pengguna (lihat [README.md](./README.md#langkah-1-eksekusi-verifikasi-hosting-pre-flight-checks)).

---

### ADR-010: Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC

1. **ADR ID & Title:** `ADR-010 — Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Penyimpanan log kunjungan mentah secara tanpa batas akan menghabiskan kuota disk MySQL shared hosting. Selain itu, penyimpanan alamat IP mentah pengunjung melanggar prinsip kepatuhan privasi (GDPR / UU PDP).
4. **Keputusan:**
   1. Data mentah `visits` disimpan dengan batas retensi maksimal **90 hari**. Scheduler mingguan membersihkan baris `visits` yang lebih tua dari 90 hari setelah diringkas permanen ke `daily_stats`.
   2. Alamat IP pengunjung disamarkan secara satu arah menggunakan **Keyed HMAC-SHA256** dengan salt khusus `ANALYTICS_SALT`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Retensi Data Selamanya:* Ditolak karena memicu pembengkakan basis data (> 10 GB) dan melanggar prinsip *data minimization*.
   - *Penyimpanan IP Plaintext:* Ditolak karena risiko pelanggaran hukum privasi dan kebocoran data pribadi.
   - *Penggunaan IP Truncation Sederhana (contoh: 192.168.1.0):* Ditolak karena menyulitkan penghitungan metrik *unique visitor* harian yang akurat.
6. **Konsekuensi Positif:**
   - Ukuran tabel `visits` terjaga stabil di bawah 3 GB.
   - Privasi pengunjung terlindungi 100% tanpa menyimpan data identitas pribadi (PII).
   - Mempertahankan akurasi penghitungan *unique visitor* harian berbasis hash IP samaran.
7. **Konsekuensi Negatif & Trade-offs:**
   - Analisis forensik log kunjungan individual di atas 90 hari tidak tersedia (data hanya tersisa dalam agregat harian).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Cron pembersihan retensi gagal berjalan, menyebabkan disk penuh.
   - *Mitigasi:* Scheduler dilengkapi log audit pada `security_logs` dan health check metrik disk di dashboard admin.
9. **Kaitannya dengan Keputusan PO:** `DEC-010 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-ANA-001`, `NFR-DATA-002`, `NFR-PRIV-001`
11. **Test Case Terkait:** `TEST-ANA-001`, `TEST-ANA-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-011: Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (`token_hash`)

1. **ADR ID & Title:** `ADR-011 — Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (token_hash)`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Menyimpan token sesi safelink dalam bentuk plaintext di database membuka celah serius: jika file database backup bocor, penyerang dapat membaca token aktif dan langsung memanggil endpoint `/proceed` untuk membajak unduhan tanpa melewati Turnstile dan countdown.
4. **Keputusan:**
   Menerapkan standar **OWASP ASVS v4.0 Section 3.2.4**. Kolom pada tabel `visitor_sessions` diubah menjadi **`token_hash VARCHAR(64)`** yang menyimpan nilai `hash('sha256', $rawToken)`. Nilai mentah `$rawToken` (256-bit CSPRNG) hanya dipegang oleh browser client.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Plaintext Token Storage:* Ditolak karena risiko eksfiltrasi backup SQL dan pembajakan sesi safelink aktif.
   - *Bcrypt Hashing:* Ditolak karena kalkulasi Bcrypt sengaja lambat (menghabiskan CPU pada trafik ribuan sesi/menit). SHA-256 kriptografis cepat dan kebal collision.
6. **Konsekuensi Positif:**
   - Sekalipun database terekspos, penyerang tidak dapat merekonstruksi token mentah untuk mem-bypass safelink.
   - Kueri pencarian hash SHA-256 sangat cepat dan terindeks secara optimal (`INDEX token_hash`).
7. **Konsekuensi Negatif & Trade-offs:**
   - Controller melakukan satu kali hashing `hash('sha256', $rawToken)` pada setiap verifikasi dan proceed request.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Perbedaan encoding token antara client dan server.
   - *Mitigasi:* Token di-generate menggunakan format string heksadesimal baku (`bin2hex(random_bytes(32))`) 64 karakter alfanumerik.
9. **Kaitannya dengan Keputusan PO:** `DEC-011 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-SAFE-001`, `NFR-SEC-001`
11. **Test Case Terkait:** `TEST-SAFE-001`, `TEST-SEC-001`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-012: Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (`FR-AUTH-004`)

1. **ADR ID & Title:** `ADR-012 — Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (FR-AUTH-004)`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Pada spesifikasi v1.0, tidak ada rute maupun antarmuka bagi administrator untuk mengganti kata sandi. Mengganti kata sandi dengan mengedit hash manual di phpMyAdmin atau tinker sangat berbahaya dan rawan human error pada server produksi.
4. **Keputusan:**
   Menambahkan fitur **`FR-AUTH-004`**, rute aman `GET /admin/profile` dan `PUT /admin/profile/password`, Form Request `UpdatePasswordRequest`, serta pencatatan insiden rotasi kredensial pada `security_logs`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Reset Password via Email (Forgot Password):* Ditolak karena sistem bertipe private single-admin; konfigurasi SMTP mail server di shared hosting sering tidak stabil dan rentan masuk spam.
   - *Edit Manual di Database:* Ditolak keras karena menyalahi standar keamanan aplikasi web dan rentan salah ketik Bcrypt cost factor.
6. **Konsekuensi Positif:**
   - Mematuhi standar OWASP ASVS v4.0 Section 2.1 (Credential Management).
   - Validasi ketat kata sandi saat ini (`current_password`), panjang minimal 12 karakter, dan otomatis merotasi session ID login.
7. **Konsekuensi Negatif & Trade-offs:**
   - Menambah 2 rute dan 1 view Blade baru di panel admin.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Brute force terhadap endpoint penggantian kata sandi jika sesi admin ditinggalkan aktif.
   - *Mitigasi:* Pembatasan rate limiting 5 percobaan per menit dan audit log otomatis saat terjadi kegagalan verifikasi kata sandi lama (`TEST-AUTH-007`).
9. **Kaitannya dengan Keputusan PO:** `DEC-012 (Scope: Security Baseline)`
10. **Kebutuhan Terkait:** `FR-AUTH-004`, `NFR-SEC-005`
11. **Test Case Terkait:** `TEST-AUTH-006`, `TEST-AUTH-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-013: Fallback Pre-Built Vendor Deployment untuk Shared Hosting

1. **ADR ID & Title:** `ADR-013 — Fallback Pre-Built Vendor Deployment untuk Shared Hosting`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Paket shared hosting WarnaHost Starter dibatasi memori fisik 2 GB. Menjalankan perintah `composer install` secara langsung di terminal hosting dapat memicu penghentian paksa proses (*SIGKILL / Out of Memory*) oleh CloudLinux LVE, yang menyebabkan situs mengalami kegagalan fatal saat deployment.
4. **Keputusan:**
   Menyediakan prosedur deployment fallback resmi: kompilasi direktori `vendor/` dan dependensi dijalankan di lingkungan lokal/CI yang identik dengan PHP 8.3 target, dikemas dalam arsip ZIP, diunggah via cPanel File Manager/FTP, lalu diekstrak ke server.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Live Composer Install di Server:* Dipertahankan sebagai opsi sekunder jika memori mencukupi, namun dilarang menjadi satu-satunya jalur deployment.
   - *Git Push to Deploy dengan Webhook:* Ditolak karena shared hosting tidak menyediakan hook trigger yang andal tanpa daemon supervisor.
6. **Konsekuensi Positif:**
   - Menghilangkan risiko OOM / timeout saat proses deployment di hosting.
   - Waktu deployment menjadi sangat cepat (cukup ekstrak arsip dan jalankan `php artisan migrate --force`).
7. **Konsekuensi Negatif & Trade-offs:**
   - Ukuran file arsip rilis membesar (~25-35 MB terkompresi).
   - Menuntut kecocokan ABI ekstensi PHP antara mesin build lokal dan server hosting.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Ketidakcocokan ekstensi PHP (misal: ekstensi `fileinfo` atau `sodium` hilang di server hosting).
   - *Mitigasi:* Verifikasi pre-flight CLI `php -m` sebelum deployment; eksekusi `mysqldump` sebelum migrasi basis data dijalankan.
9. **Kaitannya dengan Keputusan PO:** `DEC-013 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `NFR-HOST-002`, `NFR-DEPL-001`
11. **Test Case Terkait:** `TEST-DEPLOY-002`, `TEST-DEPLOY-003`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Mesin build lokal wajib menggunakan PHP versi minor dan ekstensi yang identik dengan runtime target cPanel.

---

### ADR-014: Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink

1. **ADR ID & Title:** `ADR-014 — Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Jaringan periklanan (Adsterra/Monetag) menerapkan sistem deteksi kecurangan GeoEdge yang sangat ketat. Menempelkan event klik popunder pada tombol aksi safelink atau meletakkan iklan bertumpuk dengan tombol proceed diklasifikasikan sebagai *Incentivized / Accidental Clicks*, yang mengakibatkan saldo tertahan (*revenue withheld*) atau pemblokiran domain.
4. **Keputusan:**
   1. **Menangguhkan format iklan popunder** pada rilis MVP.
   2. Mensterilkan tombol navigasi unduhan (`#proceed-btn`) secara mutlak dari skrip pelacak atau pemicu klik pihak ketiga.
   3. Menerapkan jarak aman minimal 30px (WCAG 2.5.5) antara tombol navigasi dan banner iklan sponsor.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Memasang Popunder pada MVP:* Ditolak karena risiko tinggi saldo iklan dibekukan sebelum reputasi akun terjalin.
   - *Overlap Iklan di Atas Tombol Proceed:* Ditolak keras karena melanggar etika UX dan kebijakan anti-IVT periklanan.
6. **Konsekuensi Positif:**
   - Menjamin keamanan akun jaringan iklan dan integritas pencairan pendapatan.
   - Meningkatkan kepercayaan pengguna dan menurunkan bounce rate akibat spam tab baru.
7. **Konsekuensi Negatif & Trade-offs:**
   - Potensi pendapatan awal lebih konservatif dibandingkan safelink agresif berbasis popunder ganda.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Skrip iklan eksternal menyuntikkan event listener global pada tag `<a>` atau `<button>`.
   - *Mitigasi:* Event isolation pada `#proceed-btn` via `e.stopPropagation()` dan audit elemen DOM berkala.
9. **Kaitannya dengan Keputusan PO:** `DEC-014 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-ADS-005`, `NFR-MON-001`
11. **Test Case Terkait:** `TEST-ADS-003`, `TEST-ADS-004`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-015: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik

1. **ADR ID & Title:** `ADR-015 — Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Jika operasi penghapusan tautan menerapkan `ON DELETE CASCADE` di seluruh basis data, data metrik analitik historis akan terhapus permanen saat tautan dihapus. Sebaliknya, jika menerapkan `ON DELETE RESTRICT`, administrator tidak dapat membersihkan tautan sampah dari basis data.
4. **Keputusan:**
   1. Operasi penghapusan default di panel admin adalah **Soft Delete** (`deleted_at = NOW()`). Tautan dinonaktifkan untuk publik, namun seluruh relasi data tetap utuh. Agregasi harian menyertakan link soft-deleted via `Link::withTrashed()`.
   2. Operasi **Hard Delete (Force Delete)** hanya diizinkan dari Trash:
      - `visitor_sessions`: `ON DELETE CASCADE` (sesi sementara dibersihkan).
      - `visits`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, snapshot identitas dipertahankan).
      - `daily_stats`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, snapshot `link_slug` dan `aggregate_key` tetap terbaca).
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Hard Delete Instan Tanpa Trash:* Ditolak karena rentan kesalahan manusia yang tidak dapat dipulihkan.
   - *Hapus Cascade Menyeluruh:* Ditolak karena merusak laporan statistik pendapatan dan analitik masa lalu.
6. **Konsekuensi Positif:**
   - Melindungi data historis dari kehilangan tidak disengaja.
   - Data analitik historis tetap utuh dan grafik performa jangka panjang tetap akurat.
7. **Konsekuensi Negatif & Trade-offs:**
   - Mengharuskan penanganan relasi bernilai `NULL` pada Eloquent query di sisi aplikasi.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Error `Trying to get property 'name' of non-object` saat link telah di-hard delete.
   - *Mitigasi:* Penggunaan helper Eloquent `optional($visit->link)->title ?? $visit->link_slug_snapshot` dan relasi null-safe.
9. **Kaitannya dengan Keputusan PO:** `DEC-015 (APPROVED 2026-10-31)`
10. **Kebutuhan Terkait:** `FR-LINK-006`, `FR-LINK-007`, `NFR-DATA-001`
11. **Test Case Terkait:** `TEST-LINK-006`, `TEST-LINK-007`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-016: Pemisahan Mandatori Salt Analitik (`ANALYTICS_SALT`) Tanpa Fallback ke `APP_KEY`

1. **ADR ID & Title:** `ADR-016 — Pemisahan Mandatori Salt Analitik (ANALYTICS_SALT) Tanpa Fallback ke APP_KEY`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Menurut rekomendasi NIST SP 800-57 Part 1, kunci kriptografis yang berbeda harus digunakan untuk tujuan yang berbeda (*Key Separation*). Menggunakan `APP_KEY` sebagai salt analitik atau mengizinkan fallback `config('services.analytics.salt', config('app.key'))` mengikat siklus hidup analitik historis ke kunci enkripsi sesi. Jika terjadi insiden keamanan dan `APP_KEY` dirotasi, seluruh riwayat IP hash pengunjung masa lalu akan rusak. Sebaliknya, jika salt analitik kosong di production, alamat IP akan di-hash tanpa salt.
4. **Keputusan:**
   1. Menghapus secara ketat kode fallback `ANALYTICS_SALT` ke `APP_KEY`.
   2. Menjadwalkan `ANALYTICS_SALT` sebagai variabel wajib di file `.env` produksi. Jika tidak dikonfigurasi saat `APP_ENV=production`, sistem melempar `RuntimeException` saat inisialisasi boot.
   3. Di lingkungan lokal/testing, sistem menggunakan fixed testing salt string untuk memfasilitasi automated testing.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Fallback Implisit ke APP_KEY:* Ditolak keras karena melanggar standar NIST SP 800-57 dan merusak analitik saat rotasi kunci enkripsi.
   - *Hash IP Tanpa Salt:* Ditolak karena rentan serangan *rainbow table attack* terhadap alamat IPv4 publik.
6. **Konsekuensi Positif:**
   - Kepatuhan penuh terhadap standar pemisahan kunci NIST SP 800-57.
   - Rotasi `APP_KEY` darurat tidak mengganggu data analitik historis.
7. **Konsekuensi Negatif & Trade-offs:**
   - Kegagalan konfigurasi `.env` produksi akan menghentikan boot aplikasi (fail-fast design).
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Administrator lupa menambahkan `ANALYTICS_SALT` saat instalasi pertama kali.
   - *Mitigasi:* Template `.env.example` menyertakan key ini secara eksplisit dan verifikasi dilakukan pada skrip pre-flight deployment.
9. **Kaitannya dengan Keputusan PO:** `DEC-016 (Scope: Cryptographic Governance)`
10. **Kebutuhan Terkait:** `NFR-SEC-006`, `NFR-PRIV-001`
11. **Test Case Terkait:** `TEST-ANA-002`, `TEST-SEC-003`
12. **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

---

### ADR-017: Dedicated Aggregate Key NOT NULL dan Snapshot Identitas Tautan Kunjungan

1. **ADR ID & Title:** `ADR-017 — Dedicated Aggregate Key NOT NULL dan Snapshot Identitas Tautan Kunjungan`
2. **Status:** `PROPOSED`
3. **Konteks & Pernyataan Masalah:**
   Pada MySQL 8.0/InnoDB, constraint `UNIQUE KEY (link_id, date)` tidak membatasi baris ganda ketika `link_id` bernilai `NULL` karena aturan standar SQL di mana perbandingan `NULL = NULL` bernilai false. Jika tautan di-hard delete sebelum agregasi tengah malam dijalankan, foreign key `link_id` pada tabel `visits` menjadi `NULL`, menyebabkan agregasi kehilangan informasi slug asli dan menduplikasi baris saat cron job di-retry.
4. **Keputusan:**
   1. Menambahkan kolom `aggregate_key VARCHAR(100) NOT NULL` pada tabel `daily_stats` dengan format `'link:' . $linkIdSnapshot` atau partisi trafik sistem.
   2. Menerapkan constraint unik definitif `UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)` di mana kedua kolom berstatus `NOT NULL`.
   3. Menambahkan kolom snapshot immutable `link_id_snapshot BIGINT UNSIGNED NOT NULL` dan `link_slug_snapshot VARCHAR(50) NOT NULL` pada tabel `visits`.
   4. Command `AggregateDailyStatsCommand` mengeksekusi upsert idempoten via `DailyStat::updateOrCreate(['aggregate_key' => $aggregateKey, 'date' => $targetDate], ...)`.
5. **Alternatif yang Dipertimbangkan & Alasan Penolakan:**
   - *Membiarkan Unique Key (link_id, date) dengan Nullable:* Ditolak karena MySQL mengizinkan baris duplikat tak terbatas saat `link_id` bernilai NULL.
   - *Sentinel Value link_id = 0:* Ditolak karena melanggar constraint foreign key ke tabel `links`.
   - *Aggregation Lock Table:* Ditolak karena menambah overhead locking tanpa menyelesaikan masalah integritas data.
6. **Konsekuensi Positif:**
   - Menghilangkan anomali duplikasi baris agregasi pada MySQL 100%.
   - Eksekusi retry nightly command aman secara matematis (idempotent).
   - Tautan yang di-hard delete tetap terlacak performanya tanpa kehilangan slug historis.
7. **Konsekuensi Negatif & Trade-offs:**
   - Penambahan 2 kolom snapshot pada tabel `visits` (+59 byte/baris) dan 1 kolom kunci pada `daily_stats` (+24 byte/baris), yang termitigasi oleh retensi data 90 hari.
8. **Risiko Teknis & Mitigasi:**
   - *Risiko:* Perbedaan agregasi trafik tak bertuan (*unassigned traffic*).
   - *Mitigasi:* Pemisahan taksonomi unassigned traffic (lihat [DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md)).
9. **Kaitannya dengan Keputusan PO:** `DEC-016 (Core Technical Analysis)`
10. **Kebutuhan Terkait:** `FR-ANA-003`, `NFR-DATA-003`
11. **Test Case Terkait:** `TEST-ANA-004`, `TEST-LINK-007`
12. **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**  
    *Kondisi:* Menunggu persetujuan definitif Product Owner atas rekomendasi penyajian UI dan taksonomi partisi pada [DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md).
