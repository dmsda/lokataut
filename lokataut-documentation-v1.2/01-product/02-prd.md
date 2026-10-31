# Product Requirements Document (PRD) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-02 |
| **Versi** | 1.2 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Lead Product Manager & Senior Systems Analyst |
| **Target Pembaca** | Product Owner, AI Coding Agent, QA Engineer |
| **Tujuan** | Menjadi sumber kebenaran tunggal (*Single Source of Truth*) untuk semua kebutuhan produk, spesifikasi fitur, aturan bisnis, dan batasan fungsional platform LokaTaut. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-01 — Product Vision & Strategy](./01-product-vision.md) | Visi produk, positioning, dan use cases utama |
| [DOC-03 — Scope & Requirements Specification](./03-scope-requirements.md) | Batasan MVP/Post-MVP dan matriks keterlacakan (RTM) |
| [DOC-04 — User Flow & System Flow](../02-analysis/04-user-system-flow.md) | Visualisasi diagram alur sistem dan pengguna (Mermaid) |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Dekomposisi modul dan layer arsitektural Laravel 13 |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Skema data MySQL 8 dan strategi indexing |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Standar perlindungan keamanan kriptografis |
| [DOC-10 — Monetization Specification](../05-security-monetization/10-monetization-specification.md) | Kebijakan dan penempatan slot iklan yang mematuhi aturan |

---

## 1. Product Overview & Core Objective

**LokaTaut** adalah platform pengelolaan tautan dan safelink privat (*Private Link Management & Safelink Platform*) berbasis web modular monolith dengan framework **Laravel 13.x (PHP 8.3+)**. Platform ini dimiliki dan dioperasikan secara eksklusif oleh **satu pemilik tunggal (single administrator)** untuk mengamankan dan memonetisasi tautan unduhan dari website-website miliknya.

Konten unduhan yang dilindungi mencakup aplikasi software, file APK Android, template/aset digital, dan file dokumen. Pengunjung yang mengklik link unduhan dialihkan ke halaman safelink yang menyajikan informasi file, verifikasi bot Cloudflare Turnstile, countdown timer 10 detik, dan slot iklan dari jaringan iklan kandidat (Adsterra/Monetag). URL tujuan akhir diserahkan via respon HTTP 302 Found setelah validasi selesai.

---

## 2. Business Rules (BR)

| Rule ID | Nama Aturan | Deskripsi & Penegakan Teknis |
|---|---|---|
| **BR-001** | Single Administrator | Sistem hanya memiliki 1 akun admin di tabel `admins`. Registrasi publik dinonaktifkan permanen pada tingkat routing. |
| **BR-002** | URL Concealment During Safelink | URL tujuan (`destination_url`) disimpan terenkripsi simetris (AES-256-CBC) dan **dilarang dikirimkan ke peramban selama alur safelink berlangsung**. URL baru dikirim via HTTP 302 Location header saat proses validasi server-side selesai. |
| **BR-003** | Server-Side Countdown Validation | Penyelesaian countdown wajib divalidasi di server (`now() - countdown_started_at >= countdown_seconds`). Manipulasi timer di console JavaScript akan ditolak dengan respon HTTP 422 Unprocessable Entity. |
| **BR-004** | One-Time Atomic Token via Hashed Storage | Token sesi safelink disimpan di database sebagai hash SHA-256 (`token_hash = hash('sha256', $rawToken)`), sedangkan raw token hanya dikirimkan ke peramban. Transisi status redirect dieksekusi secara atomic (`UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`) untuk mencegah race condition / replay attack. |
| **BR-005** | Mandatory Bot Verification | Cloudflare Turnstile wajib diselesaikan dengan sukses sebelum countdown timer dimulai pada sesi pengunjung, kecuali mode darurat aktif via `turnstile_mode`. |
| **BR-006** | Inactive Link Policy | Tautan berstatus `inactive` langsung merespon HTTP 404 (Not Found) tanpa memuat halaman safelink. |
| **BR-007** | Expired Link Policy | Tautan yang telah melewati `expires_at` menampilkan halaman pemberitahuan khusus (*Expired Link Notice*) dengan status HTTP 410 Gone / 200 OK informatif. |
| **BR-008** | Privacy Pseudonymization | Alamat IP pengunjung disamarkan menggunakan keyed HMAC (`hash_hmac('sha256', $ip, config('services.analytics.salt', config('app.key')))`), memisahkan salt analitik persisten dari lifecycle rotasi `APP_KEY`. |
| **BR-009** | Flexible Mobile Session Binding | Sesi pengunjung diikat pada kombinasi **Session Token + User-Agent Hash**. IP subnet (/24) dijadikan verifikasi sekunder untuk mencegah false-positive saat pengunjung mobile berpindah BTS seluler atau berganti jaringan. |
| **BR-010** | Stored XSS Protection | Kode iklan (`ad_code`) disimpan sebagai raw HTML/JS untuk kebutuhan tag iklan, namun pengubahannya dibatasi hanya untuk Super Admin dengan pencatatan audit log di `security_logs` dan proteksi header Content Security Policy (CSP). |
| **BR-011** | AdSense Absolute Prohibition | Google AdSense dilarang keras dipasang pada rute safelink `/s/{slug}` untuk melindungi domain dari penutupan akun permanen. |
| **BR-012** | Automatic Session & Log Pruning | Sesi kedaluwarsa dibersihkan setiap jam (> 24 jam), file cache inode diprune harian, dan data mentah `visits` dibersihkan mingguan (> 90 hari) setelah dirangkum ke tabel `daily_stats`. |
| **BR-013** | Rate Limit Enforcement | Request publik ke `/s/{slug}` dibatasi maksimal 60 request per menit per IP untuk mencegah DoS. |
| **BR-014** | Ad Button Sterilization (Anti-IVT) | Tombol aksi unduhan (`#proceed-btn`) wajib steril 100% dari skrip atau pemicu iklan pihak ketiga. Dilarang keras mengaitkan klik tombol dengan popunder atau direct link untuk mematuhi kebijakan anti-fraud Adsterra/Monetag. |
| **BR-015** | Turnstile Emergency Bypass Policy | Jika API Cloudflare mengalami kegagalan global, admin dapat mengaktifkan mode bypass via pengaturan `turnstile_mode = emergency_bypass` dengan pencatatan audit ketat di `security_logs`. |

---

## 3. Functional Requirements (FR)

### 3.1 Modul Autentikasi Admin (`FR-AUTH-*`)
- **FR-AUTH-001 (Admin Login - P0):** Form login dengan email & password terenkripsi Bcrypt, regenerasi session ID, dan rate limit maksimal 5 percobaan gagal per menit.
- **FR-AUTH-002 (Admin Logout - P0):** Mengakhiri sesi kerja, menghapus session file server-side, dan me-regenerasi token CSRF.
- **FR-AUTH-003 (Single Session Enforcement - P0):** Memastikan admin hanya memiliki satu sesi login aktif. Login dari perangkat baru otomatis mematikan sesi lama.
- **FR-AUTH-004 (Admin Password & Profile Management - P1):** Antarmuka aman untuk memperbarui nama, email, dan kata sandi admin. Validasi password saat ini (`current_password`), validasi kompleksitas password baru (min 12 karakter, campuran huruf besar, kecil, angka, simbol), konfirmasi password, serta regenerasi sesi kerja saat password diperbarui.

### 3.2 Modul Dashboard Admin (`FR-DASH-*`)
- **FR-DASH-001 (Overview Dashboard - P0):** Menampilkan 4 kartu statistik: Total Link, Tayangan (Impressions) Hari Ini, Unduhan Selesai (Completions) Hari Ini, dan Rasio Penyelesaian (Completion Rate %).
- **FR-DASH-002 (Quick Stats & Tren 7 Hari - P1):** Menampilkan grafik tren harian tayangan vs penyelesaian safelink 7 hari terakhir.
- **FR-DASH-003 (Quick Actions - P2):** Tombol pintas "Buat Tautan Baru" dan tabel 5 tautan terbaru.

### 3.3 Modul Manajemen Link (`FR-LINK-*`)
- **FR-LINK-001 (Random Slug Generation - P0):** Sistem otomatis menghasilkan slug acak 6–8 karakter alfanumerik unik jika slug custom tidak diisi.
- **FR-LINK-002 (Custom Slug - P1):** Admin dapat menentukan slug custom (regex: `/^[a-z0-9-]+$/`, panjang 4–50 karakter, unique).
- **FR-LINK-003 (CRUD Destination URL - P0):** Membuat, membaca, mengubah, dan menghapus (soft delete) URL tujuan. Kolom `destination_url` dienkripsi simetris AES-256-CBC.
- **FR-LINK-004 (Status Toggle & Expiration - P0):** Mengaktifkan/menonaktifkan link sewaktu-waktu dan mengatur tanggal kedaluwarsa otomatis.
- **FR-LINK-005 (Source Website Tagging - P1):** Menandai setiap tautan dengan domain web asal (misal: `softwaredownload.id`) untuk membedakan kinerja analitik.

### 3.4 Modul Safelink Engine (`FR-SAFE-*`)
- **FR-SAFE-001 (Landing Page Rendering - P0):** GET `/s/{slug}` memvalidasi status link, membuat sesi baru di `visitor_sessions` dengan menyimpan `token_hash = hash('sha256', $rawToken)` di basis data, mengirimkan `$rawToken` ke view, mencatat event `impression`, dan menyajikan halaman safelink publik dengan slot iklan display dan widget Turnstile. Mengirimkan header `X-LiteSpeed-Cache-Control: no-cache` untuk mencegah server-level caching.
- **FR-SAFE-002 (Cloudflare Turnstile Verification - P0):** Client menyelesaikan tantangan Turnstile dan mengirim token via AJAX POST `/s/{slug}/verify`. Server memvalidasi ke API Cloudflare SiteVerify (atau bypass jika mode darurat aktif) dan mencatat `countdown_started_at = now()`.
- **FR-SAFE-003 (Countdown Timer - P0):** Timer visual (default 10 detik) berdetak di browser. Saat timer mencapai 0, tombol unduhan berubah menjadi aktif (*enabled*).
- **FR-SAFE-004 (Secure Redirect 302 - P0):** POST `/s/{slug}/proceed` memvalidasi `$rawToken` terhadap `token_hash`, memastikan bahwa waktu server telah berlalu minimal 10 detik sejak verifikasi Turnstile dan token belum pernah digunakan. Server menandai token selesai secara atomic (`completed = 1`), mencatat event `completed`, mendekripsi URL, dan merespon HTTP 302 Found.
- **FR-SAFE-005 (Expired & Invalid Notice - P0):** Menampilkan antarmuka ramah pengguna jika link kedaluwarsa (HTTP 410) atau tidak ditemukan (HTTP 404).

### 3.5 Modul Manajemen Iklan (`FR-ADS-*`)
- **FR-ADS-001 (Ad Slot Management - P0):** CRUD konfigurasi slot iklan (Header Banner 728x90, Above Timer 300x250, Below Timer 300x250, Footer) dengan toggle aktif/nonaktif dan target perangkat. Format popunder ditangguhkan sampai ada verifikasi resmi.
- **FR-ADS-002 (Ad Network Code Configuration - P0):** Admin dapat menempelkan script tag iklan banner resmi dari jaringan kandidat. Komponen Blade menyajikan iklan dengan wrapper pencegah CLS dan label wajib `"Advertisement / Iklan Sponsor"`. Tombol navigasi proceed steril dari skrip iklan.

### 3.6 Modul Analitik & Agregasi (`FR-ANAL-*`)
- **FR-ANAL-001 (Event Tracking - P0):** Mencatat tahapan pengunjung (`impression`, `verified`, `completed`) dengan IP tersamarkan (`hash_hmac('sha256', $ip, ANALYTICS_SALT)`), parsing perangkat, dan negara via Cloudflare `CF-IPCountry`.
- **FR-ANAL-002 (Completion Rate Reporting - P1):** Menghitung rasio penyelesaian safelink (Completions / Impressions × 100%) per link dan agregat.
- **FR-ANAL-003 (Daily Aggregation Command - P1):** Scheduled task harian `AggregateDailyStatsCommand` mengagregasi data dari `visits` ke tabel `daily_stats` menggunakan `Link::withTrashed()` untuk memastikan tautan yang dihapus pada hari yang sama tetap terhitung, dengan composite unique index `(link_id, date)`.

### 3.7 Modul Pengaturan & Keamanan (`FR-SET-*` & `FR-SEC-*`)
- **FR-SET-001 (General Settings - P1):** Pengaturan nama platform, default countdown, dan timezone `Asia/Jakarta`.
- **FR-SET-002 (Turnstile Keys Config - P0):** Penyimpanan Turnstile site key dan secret key dengan opsi toggle untuk pengujian lokal.
- **FR-SET-003 (Turnstile Mode & Emergency Bypass - P1):** Opsi konfigurasi mode Turnstile (`enabled`, `disabled`, `emergency_bypass`) untuk menangani gangguan global API pihak ketiga tanpa downtime alur safelink.
- **FR-SEC-001 (Security Audit Logging - P1):** Mencatat percobaan brute force, indikasi bypass countdown, pergantian kata sandi admin, dan modifikasi kode iklan ke tabel `security_logs`.

---

## 4. Non-Functional Requirements (NFR)

- **NFR-PERF-001 (P0):** Waktu muat halaman safelink (LCP) < 1.2 detik pada koneksi 4G.
- **NFR-PERF-002 (P0):** Kueri database pencarian link via slug dieksekusi < 5ms; agregasi analitik < 100ms.
- **NFR-PERF-003 (P0):** Mampu menangani 10.000–50.000 kunjungan per hari pada Shared Hosting 1 CPU Core / 2 GB RAM.
- **NFR-SEC-001 (P0):** Enkripsi simetris simpanan `destination_url` menggunakan AES-256-CBC.
- **NFR-SEC-002 (P0):** Proteksi CSRF wajib pada seluruh request berstatus POST/PUT/PATCH/DELETE.
- **NFR-SEC-003 (P0):** Pencegahan XSS murni melalui auto-escaping Blade `{{ }}` dan proteksi header CSP.
- **NFR-SEC-004 (P0):** Pencegahan SQL Injection 100% menggunakan parameter binding PDO Eloquent.
- **NFR-UX-001 (P0):** Tampilan responsif 100% mobile-first dengan area sentuh minimal 44x44 pixel.
- **NFR-UX-002 (P1):** Rasio kontras teks minimal 4.5:1 (WCAG 2.1 AA compliant).
- **NFR-UX-003 (P1):** Desain bersih bebas dari elemen manipulatif / tombol download palsu bawaan iklan.
- **NFR-MAINT-001 (P0):** Arsitektur modular monolith berbasis Laravel 13 murni, PHP 8.3+, dan standar PSR-12.

