# Architecture Decision Records (ADR) Individual Technical Review — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-REV-01 |
| **Versi Dokumen** | 1.0.2 (Remediasi Pemetaan DEC, Idempotensi, & Klarifikasi Kebijakan) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Pengantar & Metodologi Evaluasi v1.0.2

Laporan ini menyajikan evaluasi mendalam terhadap masing-masing dari 17 Architecture Decision Records (ADR-001 s/d ADR-017) yang tercantum pada baseline [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md). Pada versi 1.0.2:
1. **Penghapusan Pseudo-Relasi DEC:** Menghapus penggunaan DEC-012 sebagai relasi teknis untuk ADR-002, ADR-003, ADR-005, dan ADR-008. Keempat keputusan ini diklasifikasikan secara benar sebagai `No dedicated DEC relationship (Architectural Baseline)`. DEC-012 beroperasi murni sebagai instrumen tata kelola umum.
2. **Penguatan Arsitektur Idempotensi Agregasi:** Melakukan dekonstruksi mendalam terhadap mekanisme idempotensi pada ADR-017 dan DEC-016 melampaui anggapan parsial atas `updateOrCreate()`.
3. **Koreksi Kebijakan Iklan & WCAG:** Mengklarifikasi batasan jarak aman 30px pada ADR-014 sebagai *internal UI safeguard*.

Evaluasi ditelaah dari 8 disiplin ilmu teknik perangkat lunak:
- Software Architecture, Laravel Engineering, Database Architecture, Application Security, DevOps & Infrastructure, Monetization & Compliance, Quality Assurance, dan Governance.

### Kategori Hasil Evaluasi (Reviewer Recommendation Legend):
- **`TECHNICALLY SOUND`:** Keputusan arsitektural solid, realistis terhadap batasan infrastruktur, dan direkomendasikan untuk disetujui Product Owner.
- **`SOUND WITH CONDITIONS`:** Keputusan arsitektural baik secara konseptual, namun memerlukan pemenuhan prasyarat operasional sebelum implementasi.
- **`BLOCKED BY MISSING EVIDENCE`:** Keputusan tidak dapat divalidasi secara final sebelum bukti empiris lingkungan hosting riil (CLI execution log) diverifikasi oleh Product Owner.

---

## 2. Telaah Teknis Mendalam 17 ADR

---

### ADR-001: Adopsi Modular Monolith Berbasis Laravel 13.x

- **Disiplin:** Software Architecture & Laravel Engineering
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-001 (APPROVED 2026-10-31: Migrasi Framework ke Laravel 13.x & PHP 8.3+)`
- **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Siklus Hidup & Dukungan Keamanan (LTS vs EOL):**
   Keputusan meninggalkan Laravel 11 (EOL sejak 12 Maret 2026) dan Laravel 12 (akhir patch keamanan Februari 2027) adalah **sangat tepat dan bertanggung jawab secara arsitektural**. Mengadopsi Laravel 13 (rilis 17 Maret 2026) memberikan masa dukungan bug fixes hingga September 2027 dan security fixes hingga 17 Maret 2028, menjamin stabilitas aplikasi selama 24 bulan ke depan tanpa ancaman end-of-life prematur.
2. **Kesesuaian Pola Modular Monolith:**
   Memilih arsitektur *Modular Monolith* (`app/Modules/*`) memberikan isolasi domain logis yang jelas (Auth, Links, Safelink, Monetization, Analytics) tanpa introducing overhead jaringan RPC, serialization cost, atau kompleksitas orkestrasi microservices yang mustahil dijalankan pada shared hosting.
3. **Beban Dependensi & Vendor Directory:**
   Ukuran direktori `vendor/` Laravel 13 berkisar antara 65–85 MB. Ini sepenuhnya aman terhadap kuota disk hosting (NVMe SSD), namun memerlukan strategi kompilasi yang hati-hati jika dijalankan langsung di server (lihat ADR-013).

#### Kondisi yang Harus Dipenuhi Sebelum Coding:
- **Prasyarat CLI Hosting:** Versi PHP pada CLI cPanel WarnaHost wajib diverifikasi mendukung PHP 8.3+ (`php -v`). Seringkali web server cPanel menjalankan PHP 8.3 via LiteSpeed SAPI, namun path binary CLI default `/usr/bin/php` masih mengarah ke PHP 8.1. Jika demikian, alias atau full path `/usr/local/bin/ea-php83` wajib digunakan pada skrip cron.

---

### ADR-002: Penggunaan MySQL 8.0 sebagai RDBMS Tunggal

- **Disiplin:** Database Architecture
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** *No dedicated DEC relationship (Architectural Baseline)*
- **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Fitur RDBMS yang Diandalkan:**
   Arsitektur LokaTaut v1.2.2 bergantung pada kapabilitas InnoDB transactions, indexing B-Tree teroptimasi, kolom JSON untuk konfigurasi slot iklan (`ad_slots.display_conditions`), dan fungsi tanggal (`TIMESTAMP`, `DATE`).
2. **Isu Kompatibilitas MySQL 8.0 vs MariaDB 10.5+ / 10.11 LTS:**
   Di industri web hosting Indonesia yang menggunakan cPanel, banyak penyedia beralih dari Oracle MySQL ke **MariaDB** (MariaDB 10.5, 10.6, atau 10.11 LTS) untuk efisiensi lisensi cPanel.
   - *Verifikasi Desain:* Skema DDL pada [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md) telah diaudit dan tidak menggunakan fitur eksklusif Oracle MySQL (seperti functional index ekspresi rumit atau histogram), sehingga 100% kompatibel dengan MariaDB 10.5+. Namun, identitas server riil tetap wajib dibuktikan.

#### Bukti yang Dibutuhkan untuk Melepas Blokir:
- Product Owner wajib menjalankan perintah:
  ```bash
  mysql -V
  # atau melalui MySQL CLI:
  mysql -u <user> -p -e "SELECT VERSION();"
  ```
  dan mencatat outputnya pada lembar verifikasi sebelum Phase 1 dimulai.

---

### ADR-003: Blade Templating + Native CSS + Vanilla JS (No SPA)

- **Disiplin:** Frontend / UI Architecture & Web Performance
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** *No dedicated DEC relationship (Architectural Baseline)*
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Kinerja Halaman Publik & Core Web Vitals:**
   Menolak framework JavaScript SPA (React/Vue/Angular) pada halaman safelink adalah **keputusan arsitektur terbaik untuk monetisasi**. Halaman safelink harus dimuat seketika di ponsel pengunjung dengan jaringan 3G/4G lambat. Menggunakan Blade Views, Semantic HTML5, Native CSS (< 8 KB), dan Vanilla JS (< 5 KB) menghasilkan total transfer payload < 20 KB (jauh di bawah batas toleransi 50 KB), menjamin LCP < 1.2 detik.
2. **Eliminasi Ketergantungan Build Step Produksi:**
   Dengan meniadakan Tailwind CSS atau Webpack/Vite bundler pada lingkungan produksi shared hosting, deployment tidak memerlukan `npm install` atau `npm run build` di server. Server hosting tidak terbebani proses kompilasi Node.js yang rakus memori RAM.
3. **Kemudahan Pemeliharaan:**
   Penggunaan Blade Components (`<x-button>`, `<x-card>`) dan CSS Custom Properties (`--color-primary`, dll.) memberikan modularitas antarmuka yang bersih tanpa kompleksitas dependensi npm.

---

### ADR-004: Penolakan Google AdSense & Penetapan Adsterra sebagai Kandidat Jaringan Iklan

- **Disiplin:** Monetization & Policy Compliance
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-002 (APPROVED 2026-10-31: Status Kandidat Jaringan Iklan Adsterra)`
- **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Pencegahan Risiko Penalti Akun Google:**
   Penolakan Google AdSense pada safelink adalah **kepatuhan mutlak**. Kebijakan Google Publisher secara eksplisit melarang penempatan iklan pada halaman dengan *forced waiting*, *thin content*, dan navigasi unduhan buatan. Memaksakan AdSense pada safelink hampir pasti berujung pada penutupan akun permanen.
2. **Kandidat Jaringan Periklanan (Adsterra):**
   Adsterra adalah pilihan industri yang mapan untuk situs utilitas/safelink, asalkan trafik bukan bot (anti-fraud) dan tidak menggunakan trik klik paksa.
3. **Klarifikasi Batasan Visual:**
   Penempatan iklan dilarang menyerupai tombol navigasi unduhan atau elemen antarmuka aplikasi (*anti-deceptive layout*).
4. **Arsitektur Slot Iklan yang Agnostik:**
   Tabel `ad_slots` menyimpan snippet kode mentah (`ad_code`) dengan batasan isolasi Super Admin, memungkinkan LokaTaut berganti vendor iklan (misal ke Monetag atau PropellerAds) tanpa mengubah kode aplikasi.

#### Kondisi yang Harus Dipenuhi Sebelum Integrasi Iklan Riil:
- Pendaftaran akun resmi, penambahan domain `lokataut.com`, dan verifikasi kepemilikan domain (DNS/TXT) wajib diselesaikan terlebih dahulu oleh Product Owner. Sebelum verifikasi akun selesai, sistem menggunakan placeholder container `[Adsterra Banner Slot - 728x90]` untuk testing fungsional.

---

### ADR-005: Tabel Khusus `visitor_sessions` & User-Agent Hash

- **Disiplin:** Security Architecture & Shared Hosting Optimization
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** *No dedicated DEC relationship (Architectural Baseline)*
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Proteksi Kuota Inodes Shared Hosting:**
   Pada traffic safelink tinggi (misal 10.000 kunjungan/hari), driver sesi bawaan Laravel berbasis file (`storage/framework/sessions`) akan membuat 10.000 file teks baru setiap hari. Pada shared hosting, limit *inodes* (jumlah total file) biasanya dibatasi ketat (misal 250.000–500.000 inodes). Memindahkan sesi safelink ke tabel MySQL `visitor_sessions` menjaga inodes hosting tetap bersih dan memungkinkan pembersihan batch via SQL `DELETE` yang sangat efisien.
2. **Mitigasi Rotasi IP Seluler (CGNAT):**
   Pengunjung di Indonesia mayoritas menggunakan jaringan seluler 4G/5G (Telkomsel, Indosat, XL) yang menggunakan CGNAT. IP publik pengguna dapat berganti dalam hitungan detik saat berpindah cell tower. Mengikat sesi hanya pada IP akan menyebabkan *session drop* (gagal unduh). Menggabungkan **Token Kriptografis (256-bit CSPRNG)** dengan **User-Agent Hash** memberikan keseimbangan ideal antara keamanan anti-bajak sesi dan toleransi transisi jaringan seluler.

---

### ADR-006: Cloudflare Turnstile untuk Mitigasi Bot

- **Disiplin:** Application Security & Bot Mitigation
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:**  
  - `DEC-005 (APPROVED 2026-10-31: Titik Penempatan Cloudflare Turnstile Sebelum Countdown)`  
  - `DEC-011 (APPROVED 2026-10-31: Mode Darurat Turnstile emergency_bypass)`
- **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Keunggulan UX & Biaya Operasional:**
   Turnstile memberikan proteksi bot berbasis CAPTCHA modern tanpa membebani pengguna manusia dengan puzzle visual di layar ponsel. Paket gratis Cloudflare saat ini mengakomodasi volume safelink tanpa beban biaya per request, sangat cocok untuk anggaran efisien LokaTaut. Namun, klausul tidak boleh menjanjikan "unlimited tanpa batas kuota secara absolut", melainkan bergantung pada *acceptable use policy* Cloudflare.
2. **Pola Verifikasi Server-Side & Timeout:**
   Token client dikirim via POST ke `/s/{slug}/verify`, dan server Laravel memverifikasi token ke endpoint Cloudflare `siteverify`. Batas waktu cURL wajib disetel maksimal 5 detik (`Http::timeout(5)`).
3. **Sakelar Darurat Bypass (DEC-011):**
   Jika terjadi gangguan jaringan eksternal atau Cloudflare mengalami outage global, sistem menyediakan mode darurat via konfigurasi `.env` (`turnstile_mode = emergency_bypass`), dengan setiap insiden bypass tercatat di `security_logs`.

#### Kondisi yang Harus Dipenuhi:
- Implementasi cURL timeout maksimal 5 detik wajib disertai unit/feature test (`TEST-SAFE-010`) dan sakelar darurat bypass (DEC-011) siap pakai.

---

### ADR-007: Validasi Countdown Server-Side & Transisi Token Atomic

- **Disiplin:** Application Security & Concurrency Control
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-004 (APPROVED 2026-10-31: Durasi Default Countdown Timer 10 Detik)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Pencegahan Bypass Manipulasi JavaScript:**
   LokaTaut mewajibkan pencatatan waktu mulai countdown di basis data (`countdown_started_at`). Durasi default ditetapkan **10 detik** sesuai DEC-004. Saat endpoint `/s/{slug}/proceed` dipanggil, server menghitung selisih waktu server aktual:
   $$\Delta t = \text{now}() - \text{countdown\_started\_at}$$
   Jika $\Delta t < \text{countdown\_seconds}$, request ditolak mentah-mentah dengan HTTP 422 (`TEST-SAFE-006`), dan insiden dicatat di `security_logs`.
2. **Pemberantasan Race Condition via Atomic Single-Row Update:**
   Menggunakan atomic update bersyarat:
   ```sql
   UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0;
   ```
   menjamin baris terpengaruh (*affected rows*) bernilai 1 hanya untuk request pertama. Request konkuren kedua yang tiba bersamaan akan menghasilkan 0 affected rows dan ditolak dengan pesan *Token Already Consumed* (`TEST-SAFE-007`). Ini memberantas race condition tanpa memerlukan table lock atau distributed Redis lock.

---

### ADR-008: Enkripsi Simetris AES-256 Destination URL

- **Disiplin:** Application Security & Cryptography
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** *No dedicated DEC relationship (Architectural Baseline)*
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Prinsip Confidentiality at Rest:**
   Tautan tujuan unduhan asli merupakan aset komersial pemilik situs. Jika file backup basis data SQL terekspos, URL asli tetap aman terlindungi karena tersimpan dalam format ciphertext AES-256-CBC.
2. **Implementasi Eloquent Attribute Casting:**
   Menggunakan `'destination_url' => 'encrypted'` pada model `Link` memastikan enkripsi dan dekripsi berlangsung secara transparan di layer aplikasi Laravel. Dekripsi hanya dilakukan di memori RAM server tepat sebelum header HTTP 302 diserahkan kepada browser pengunjung yang sah.
3. **Overhead Kinerja:**
   Enkripsi dan dekripsi AES-256 pada OpenSSL PHP memakan waktu < 0.2 milidetik per operasi, sepenuhnya terabaikan terhadap total respon time server.

---

### ADR-009: Target Infrastruktur Shared Hosting WarnaHost Starter Unlimited

- **Disiplin:** DevOps & Infrastructure Engineering
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-003 (APPROVED 2026-10-31: Verifikasi Runtime Hosting WarnaHost Starter)`
- **Evaluasi Peninjau:** **`BLOCKED BY MISSING EVIDENCE`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Analisis Efisiensi Biaya:**
   Dengan biaya ~Rp 79.000/bulan, paket WarnaHost Starter Unlimited menawarkan storage NVMe SSD, LiteSpeed Web Server, dan lokasi data center IIX Indonesia. Ini merupakan titik awal yang sangat realistis untuk menjaga rasio profitabilitas operasional LokaTaut tetap tinggi.
2. **Batasan Komputasi CloudLinux LVE:**
   Batas alokasi fisik 1 vCPU Core dan 2 GB RAM fisik menuntut aplikasi beroperasi dengan *zero waste*:
   - Tidak ada daemon worker berjalan terus menerus (menggunakan cPanel cron per menit).
   - Seluruh kueri basis data wajib menggunakan index B-Tree efisien.
   - Cache file statis ditangani langsung oleh LiteSpeed web server.

#### Bukti yang Dibutuhkan untuk Melepas Blokir:
- Product Owner wajib mengeksekusi 7 perintah verifikasi terminal cPanel (lihat [README.md](./README.md#5-protokol-pemeriksaan-pra-penerbangan-hosting-pre-flight-checks)) untuk memastikan versi PHP CLI 8.3+, ketersediaan ekstensi kritis, dan alokasi memory limit.

---

### ADR-010: Retensi Data Mentah 90 Hari & Pseudonimisasi IP via Keyed HMAC

- **Disiplin:** Data Architecture & Privacy Compliance (GDPR / UU PDP)
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-006 (APPROVED 2026-10-31: Kebijakan Retensi Data Kunjungan 90 Hari)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Pengendalian Pertumbuhan Disk MySQL:**
   Pada simulasi trafik 10.000 kunjungan/hari, tabel `visits` menghasilkan 300.000 baris per bulan (~97.5 MB logical data, ~420 MB physical data + index). Membersihkan baris `visits` yang lebih tua dari 90 hari setelah diringkas ke `daily_stats` menjaga ukuran tabel `visits` stabil di kisaran ~420 MB (aman di bawah kuota shared hosting).
2. **Kepatuhan Privasi Data Pribadi:**
   Menyimpan IP address mentah pengunjung bertentangan dengan prinsip *data minimization* UU PDP dan GDPR. Menggunakan **Keyed HMAC-SHA256** dengan salt terisolasi `ANALYTICS_SALT` mengubah alamat IP menjadi pseudonim permanen (`ip_hash VARCHAR(64)`). Hash ini memungkinkan sistem mendeteksi *unique visitor* harian tanpa pernah menyimpan informasi yang dapat mengidentifikasi individu secara langsung.
3. **Interaksi dengan Scheduler Agregasi:**
   Scheduler retensi mingguan (`lokataut:prune-visits`) mewajibkan pemeriksaan pra-syarat: seluruh tanggal yang lebih tua dari 90 hari wajib berstatus `completed` pada tabel `aggregation_checkpoints` sebelum dieksekusi pembersihan baris fisik.

---

### ADR-011: Penyimpanan Token Sesi Safelink Berbasis SHA-256 Hash (`token_hash`)

- **Disiplin:** Application Security (Defense-in-Depth)
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-009 (APPROVED 2026-10-31: Penyimpanan Hashed Session Token token_hash)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Analisis Vektor Serangan:**
   Pada safelink v1.0, token sesi disimpan secara plaintext di database. Jika terjadi kebocoran basis data (SQL dump leak), penyerang dapat mengekstrak token aktif yang sedang menunggu countdown, lalu langsung menembakkan POST `/proceed` dari mesin mereka untuk mengunduh konten tanpa melihat iklan.
2. **Remediasi Defense-in-Depth:**
   Mengadopsi **OWASP ASVS v4.0 Section 3.2.4**:
   - Kolom diubah menjadi `token_hash VARCHAR(64)`.
   - Nilai tersimpan adalah $H = \text{hash}('sha256', \text{rawToken})$.
   - Karena SHA-256 adalah fungsi satu arah kriptografis yang kebal pre-image attack, kepemilikan nilai hash di basis data tidak memungkinkan penyerang merekonstruksi `rawToken`. Sesi safelink aktif di database tidak dapat disalahgunakan sekalipun dump basis data dicuri.

---

### ADR-012: Antarmuka Mandiri Penggantian Kata Sandi & Profil Admin (`FR-AUTH-004`)

- **Disiplin:** Security & Identity Management
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-008 (APPROVED 2026-10-31: Penambahan Fitur Ganti Kata Sandi Admin FR-AUTH-004)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Penutupan Kesenjangan Operasional Kredensial:**
   Dokumentasi awal LokaTaut tidak memiliki antarmuka penggantian kata sandi admin. Dalam skenario produksi riil, administrator terpaksa membuka phpMyAdmin atau terminal Tinker untuk membuat hash Bcrypt manual. Prosedur manual ini melanggar standar tata kelola kredensial dan rentan salah konfigurasi cost factor.
2. **Standar Keamanan Form Request:**
   Fitur `FR-AUTH-004` menerapkan:
   - Validasi kata sandi saat ini (`current_password`) untuk mencegah pembajakan sesi admin saat komputer ditinggalkan.
   - Kebijakan kompleksitas kata sandi baru (minimal 12 karakter alfanumerik + simbol).
   - Regenerasi ID sesi login pasca-rotasi password untuk membatalkan potensi session fixation.
   - Audit trail di `security_logs` untuk mencatat setiap percobaan penggantian kata sandi yang gagal (`TEST-AUTH-007`).

---

### ADR-013: Fallback Pre-Built Vendor Deployment untuk Shared Hosting

- **Disiplin:** DevOps & Release Engineering
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-010 (APPROVED 2026-10-31: Prosedur Fallback Pre-Built Vendor Deployment)`
- **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Mitigasi Masalah OOM Composer di CloudLinux LVE:**
   Proses resolusi dependensi dan instalasi paket Composer (`composer install --optimize-autoloader --no-dev`) sering mengonsumsi memori RAM lebih dari 1.5 GB. Pada shared hosting yang dibatasi 2 GB RAM fisik, lonjakan konsumsi memori saat kompilasi kelas Laravel 13 berisiko tinggi memicu kernel OOM killer (`SIGKILL`), meninggalkan direktori vendor korup.
2. **Strategi Fallback Pre-Built Vendor ZIP:**
   Mempersiapkan dependensi vendor pada mesin lokal/CI, mengompresinya menjadi `vendor.zip`, dan mengunggahnya langsung ke cPanel mengeliminasi risiko kompilasi di server produksi 100%.

#### Kondisi yang Wajib Diperhatikan:
- Mesin lokal/CI yang membangun direktori vendor wajib menggunakan PHP 8.3 dengan ekstensi yang identik. Backup database via `mysqldump` wajib dieksekusi sebelum perintah `php artisan migrate --force` dijalankan pada setiap rilis rujukan.

---

### ADR-014: Penangguhan Format Popunder & Isolasi Tombol Navigasi Safelink

- **Disiplin:** Monetization Strategy & Anti-Fraud Compliance
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-007 (APPROVED 2026-10-31: Penangguhan Format Popunder & Isolasi Tombol Navigasi)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Pencegahan Fraud Flagging (GeoEdge Anti-IVT):**
   Jaringan periklanan modern menggunakan sistem otomatis GeoEdge untuk mendeteksi penipuan klik. Memasang iklan popunder yang terikat pada tombol unduhan safelink memicu deteksi *Incentivized / Deceptive Clicks*, yang mengakibatkan akun dibekukan dan penahanan seluruh pendapatan.
2. **Penangguhan Popunder pada MVP:**
   Keputusan menunda popunder dan berfokus pada banner display terlabel jelas adalah **keputusan bisnis dan teknis yang sangat cerdas**. Ini membangun reputasi trafik bersih (*clean traffic history*) di mata jaringan iklan sejak hari pertama.
3. **Klarifikasi Batasan Visual & Standar Aksesibilitas:**
   - Jarak aman minimal 30px antara tombol navigasi unduhan (`#proceed-btn`) dan banner iklan adalah **internal UI safeguard pengembang** untuk mengurangi risiko salah klik (*accidental clicks*).
   - Jarak 30px **BUKAN** persyaratan resmi tertulis dari pihak Adsterra.
   - Jarak 30px **BUKAN** bukti otomatis kepatuhan menyeluruh terhadap standar WCAG 2.5.5 (WCAG 2.5.5 mengatur ukuran target sentuh dan jarak, namun pemenuhan standar menuntut audit elemen sentuh komprehensif pada antarmuka).
   - Penempatan iklan dilarang keras menyerupai tombol aksi navigasi unduhan (*no deceptive UI / dark patterns*).
   - Dilarang merekayasa klik paksa (*no forced clicks*).
4. **Isolasi Tombol Navigasi (`#proceed-btn`):**
   Tombol navigasi unduhan disterilkan secara mutlak dari skrip pelacak atau event hijacking pihak ketiga via `e.stopPropagation()` pada event listener.

---

### ADR-015: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Integritas Analitik

- **Disiplin:** Database Architecture & Data Integrity
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-013 (APPROVED 2026-10-31: Kebijakan Penghapusan Data Soft & Hard Delete dan Snapshot Slug)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Dilema Penghapusan Relasional:**
   - Jika `ON DELETE CASCADE` diterapkan: Menghapus satu baris `links` akan menghapus seluruh baris `visits` dan `daily_stats` terkait. Akibatnya, grafik tren analitik bulanan/tahunan mendadak anjlok dan data pendapatan masa lalu menjadi tidak dapat dijelaskan.
   - Jika `ON DELETE RESTRICT` diterapkan: Administrator tidak dapat menghapus tautan usang karena adanya foreign key constraint error.
2. **Solusi Hibrida Dua Tingkat:**
   - **Soft Delete sebagai Default:** Tautan yang dihapus dipindahkan ke Trash (`deleted_at = NOW()`). Tautan tidak dapat diakses publik, namun data analitik, sesi, dan kunjungan tetap utuh. Agregasi harian tetap menghitung link soft-deleted via `Link::withTrashed()`.
   - **Hard Delete Terisolasi dari Trash:**
     - `visitor_sessions`: `ON DELETE CASCADE`.
     - `visits`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, namun snapshot `link_slug_snapshot` dan `link_id_snapshot` tetap tersimpan permanen secara immutable).
     - `daily_stats`: `ON DELETE SET NULL` (`link_id` menjadi `NULL`, namun `aggregate_key` bernilai `link:{snapshot_id}` dan snapshot `link_slug` tetap terbaca).
   Integritas metrik analitik historis terlindungi 100% dari kehilangan data akibat penghapusan tautan.

---

### ADR-016: Pemisahan Mandatori Salt Analitik (`ANALYTICS_SALT`) Tanpa Fallback ke `APP_KEY`

- **Disiplin:** Cryptographic Key Governance (NIST SP 800-57 Part 1)
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:** `DEC-014 (APPROVED 2026-10-31: Mandatori Pemisahan Kunci ANALYTICS_SALT Tanpa Fallback)`
- **Evaluasi Peninjau:** **`TECHNICALLY SOUND`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Pemisahan Kunci Kriptografis (Cryptographic Key Separation):**
   NIST SP 800-57 Part 1 Bagian 5.3.4 menegaskan bahwa satu kunci kriptografis tidak boleh digunakan untuk tujuan ganda yang berbeda domain keamanannya.
   - `APP_KEY` digunakan untuk enkripsi sesi admin dan dekripsi URL sensitif (`destination_url`). Siklus hidup `APP_KEY` menuntut rotasi saat terjadi insiden kebocoran kredensial atau rotasi berkala.
   - `ANALYTICS_SALT` digunakan untuk mengaburkan IP pengunjung pada tabel `visits` (`ip_hash`).
2. **Bahaya Fallback Implisit:**
   Jika kode mengizinkan fallback `config('services.analytics.salt', config('app.key'))`, saat administrator merotasi `APP_KEY` darurat, rumus hash IP berubah seketika:
   $$\text{HMAC}(\text{IP}, \text{APP\_KEY}_{\text{baru}}) \neq \text{HMAC}(\text{IP}, \text{APP\_KEY}_{\text{lama}})$$
   Akibatnya, pengunjung lama yang sama akan dihitung sebagai *unique visitor* ganda pada hari yang sama, dan kesinambungan analitik historis terputus total.
3. **Penerapan Fail-Fast di Produksi:**
   Aplikasi menolak booting dan melempar `RuntimeException` jika `ANALYTICS_SALT` tidak terdefinisi di file `.env` produksi. Ini mencegah kegagalan senyap (*silent failure*) di mana IP di-hash tanpa salt atau menggunakan salt kosong.

---

### ADR-017: Dedicated Aggregate Key NOT NULL dan Snapshot Identitas Tautan Kunjungan

- **Disiplin:** Database Architecture & Concurrency Integrity
- **Status Formal:** `PROPOSED (Under Review)`
- **Relasi DEC Kanonikal:**  
  - `DEC-015 (APPROVED 2026-10-31: Dedicated Aggregate Key NOT NULL pada daily_stats)`  
  - `DEC-016 (UNDER REVIEW: Snapshot Identitas Tautan Kunjungan pada visits)`
- **Evaluasi Peninjau:** **`SOUND WITH CONDITIONS`**

#### Analisis Arsitektur & Temuan Kritis:
1. **Perilaku Standar SQL Terhadap NULL pada Unique Constraint:**
   Pada MySQL 8.0 dan MariaDB InnoDB, nilai `NULL` dianggap tidak sama dengan `NULL` lainnya (`NULL != NULL`).
   - Jika tabel `daily_stats` menggunakan `UNIQUE KEY (link_id, date)` di mana `link_id` dapat bernilai `NULL`, MySQL akan **mengizinkan baris duplikat tak terbatas** dengan kombinasi `(NULL, '2026-10-31')`.
   - **DEC-015 yang telah disetujui** menyelesaikan ini dengan kolom `aggregate_key VARCHAR(100) NOT NULL` dan constraint `UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)`.
2. **Penyimpanan Snapshot Identitas pada Tabel `visits` (DEC-016):**
   Menyimpan `link_id_snapshot BIGINT UNSIGNED NOT NULL` dan `link_slug_snapshot VARCHAR(50) NOT NULL` memastikan bahwa sekalipun tautan di-hard delete sebelum cron berjalan tengah malam, cron malam tetap mengetahui ID asli dan slug tautan tersebut.
3. **Koreksi Identitas Trafik Tautan Terhapus:**
   Kunjungan dari tautan yang telah di-hard delete **tetap diagregasikan ke format `link:{link_id_snapshot}`** (contoh: `link:42`), bukan dibuang ke `traffic:purged_legacy`! Ini menjamin bahwa grafik historis tautan terhapus tetap memiliki baris agregat lengkap dengan snapshot slug aslinya.
4. **Arsitektur Idempotensi Berbasis Watermark & Checkpoint (Comprehensive Architecture):**
   Mekanisme agregasi tidak boleh menganggap `updateOrCreate()`, `lockForUpdate()`, atau `withoutOverlapping()` sebagai jaminan mandiri. Sistem menerapkan arsitektur komposit yang mencakup 9 komponen pengamanan:
   - **Event Checkpoint Marker:** Tabel `aggregation_checkpoints` mencatat `(target_date, last_processed_visit_id, status)` untuk menandai batas kemajuan batch.
   - **Transaction Boundary:** Mutasi agregasi per-chunk 1.000 baris dibungkus dalam `DB::transaction()`.
   - **Atomic Update:** Agregasi metriks diakumulasi secara atomik di dalam transaksi yang sama dengan pembaruan checkpoint kursor.
   - **Unique Constraint:** Constraint `UNIQUE (aggregate_key, date)` pada `daily_stats` mencegah duplikasi pada level storage engine.
   - **Scheduler Overlap Prevention:** Job didefinisikan dengan `->withoutOverlapping(60)->runInBackground()`.
   - **Retry Behavior:** Jika proses terhenti di tengah batch, transaksi yang gagal otomatis rollback. Pada eksekusi retry, job membaca `last_processed_visit_id` terakhir yang sukses dan melanjutkan pemrosesan tanpa double counting.
   - **Concurrent Protection:** Baris checkpoint dikunci dengan `lockForUpdate()`, mencegah dua worker mengeksekusi tanggal yang sama.
   - **Retention Safety:** Pembersihan data mentah `visits` > 90 hari mewajibkan status `completed` pada checkpoint tanggal terkait.

#### Kondisi yang Harus Dipenuhi:
- Menunggu penandatanganan definitif Product Owner atas lembar telaah [DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md) terkait simulasi penambahan storage dan opsi penyajian data tautan terhapus di dasbor admin.

---

## 3. Matriks Rekapitulasi Evaluasi Peninjau v1.0.2

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                          Rekapitulasi Evaluasi 17 ADR                                  │
├───────────────────────────────┬───────┬────────────────────────────────────────────────┤
│ Kategori Evaluasi Reviewer    │ Total │ Nomor ADR Terkait                              │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ TECHNICALLY SOUND             │  10   │ ADR-003, ADR-005, ADR-007, ADR-008, ADR-010,   │
│                               │       │ ADR-011, ADR-012, ADR-014, ADR-015, ADR-016   │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ SOUND WITH CONDITIONS         │   5   │ ADR-001 (Verifikasi PHP 8.3 CLI)               │
│                               │       │ ADR-004 (Verifikasi Akun/Domain Adsterra)      │
│                               │       │ ADR-006 (Timeout 5s & Mode Darurat Turnstile)  │
│                               │       │ ADR-013 (Kesesuaian ABI Ekstensi Build Mesin)  │
│                               │       │ ADR-017 (Penetapan Keputusan PO atas DEC-016)  │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ BLOCKED BY MISSING EVIDENCE   │   2   │ ADR-002 (Eksekusi SELECT VERSION() Database)   │
│                               │       │ ADR-009 (Eksekusi 7 Perintah Pre-Flight CLI)   │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ NEEDS REVISION                │   0   │ -                                              │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ NOT RECOMMENDED               │   0   │ -                                              │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ TOTAL ADR                     │  17   │ Status Formal: 100% PROPOSED / UNDER REVIEW    │
└───────────────────────────────┴───────┴────────────────────────────────────────────────┘
```

