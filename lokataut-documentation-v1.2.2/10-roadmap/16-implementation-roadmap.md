# Implementation Roadmap & Task Backlog — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-16 |
| **Versi** | 1.2.2 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Lead Project Manager & Technical Architect |
| **Target Pembaca** | Product Owner, AI Coding Agent, QA Engineer |
| **Tujuan** | Menyusun tahapan pengembangan terstruktur (*Phase-by-Phase Roadmap*) dan backlog tugas teknis berurutan berbasis Laravel 13 agar AI Coding Agent mengimplementasikan sistem secara konsisten dan teruji. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Sumber kebutuhan fitur dan prioritas |
| [DOC-03 — Scope & Requirements Specification](../01-product/03-scope-requirements.md) | Batasan fitur MVP vs Post-MVP |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Struktur folder dan standar koding implementasi Laravel 13 |
| [DOC-12 — Testing Strategy & QA Plan](../06-quality/12-testing-qa.md) | Kriteria kelulusan pengujian pada setiap task |
| [AGENTS.md](../../AGENTS.md) | Kewajiban mematuhi urutan roadmap pengembangan |

---

## 1. Ikhtisar Fase Implementasi (Implementation Phases)

Pengembangan LokaTaut dibagi menjadi **8 Fase Berurutan**. AI Coding Agent **DILARANG** mengerjakan tugas pada fase berikutnya sebelum seluruh kriteria penerimaan fase sebelumnya berstatus **SELESAI (DONE)** dan lolos pengujian otomatis (`php artisan test`).

```mermaid
flowchart LR
    P0["Fase 0\nPersetujuan Dokumen"] --> P1["Fase 1\nFondasi Laravel 13"]
    P1 --> P2["Fase 2\nManajemen Link"]
    P2 --> P3["Fase 3\nSafelink Engine"]
    P3 --> P4["Fase 4\nSlot Iklan Agnostik"]
    P4 --> P5["Fase 5\nAnalitik & Agregasi"]
    P5 --> P6["Fase 6\nKeamanan & Cron Pruning"]
    P6 --> P7["Fase 7\nQA & Deployment"]
```

---

## 2. Rincian Task Backlog Per Fase

---

### FASE 0: Persetujuan Dokumentasi & Pre-Flight Checks
- **Tujuan:** Memastikan seluruh spesifikasi v1.2.1 disetujui Product Owner dan pengujian pre-flight lingkungan hosting riil dijalankan sebelum penulisan kode sumber.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Dokumen Rujukan |
|---|---|---|---|:---:|---|
| **TASK-001** | Documentation | Verifikasi kelengkapan 16 dokumen, audit report, dan file `AGENTS.md` v1.2.1 | - | P0 | DOC-01 s/d DOC-16 |
| **TASK-002** | Governance | Peninjauan dan persetujuan resmi (APPROVAL) dari Product Owner (DEC-007 s/d DEC-014) | TASK-001 | P0 | Approval Registry |
| **TASK-003** | Operations | Eksekusi Pre-Flight Verification Commands pada SSH/Terminal cPanel WarnaHost | TASK-002 | P0 | DOC-13 (Sec 1.1) |

---

### FASE 1: Fondasi Proyek & Autentikasi Admin Tunggal (Laravel 13)
- **Tujuan:** Menyiapkan kerangka kerja Laravel 13.x, skema database awal, konfigurasi `.env`, login admin yang aman, dan antarmuka ganti password admin (`FR-AUTH-004`).

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-101** | Foundation | Inisialisasi proyek Laravel 13.x, setup `.env.example`, konfigurasi MySQL 8 | TASK-002 | P0 | Laravel 13 fresh install berjalan di PHP 8.3 |
| **TASK-102** | Database | Migrasi tabel `admins` dan pembuatan `AdminSeeder` | TASK-101 | P0 | Tabel `admins` terbentuk dengan index unik email |
| **TASK-103** | Auth | Implementasi `LoginController`, `AdminLoginRequest`, view `login.blade.php`, dan logout | TASK-102 | P0 | Admin sukses login/logout; password ter-hash Bcrypt |
| **TASK-104** | Security | Rate limiting login (maks 5 percobaan/menit) dan proteksi single session | TASK-103 | P0 | Percobaan ke-6 gagal menghasilkan HTTP 429 |
| **TASK-105** | Profile | Implementasi `ProfileController`, `UpdatePasswordRequest`, view `profile.blade.php`, dan rotasi sesi (FR-AUTH-004) | TASK-104 | P1 | Admin dapat mengganti password dengan validasi password lama & kompleksitas baru |
| **TASK-106** | Testing | Tulis dan eksekusi `AdminAuthTest` (Pest 3 / PHPUnit 11) | TASK-105 | P0 | Seluruh skenario `TEST-AUTH-*` berstatus PASS |

---

### FASE 2: Modul Manajemen Link (Link Management)
- **Tujuan:** Admin dapat membuat, mengubah, menonaktifkan, dan menghapus tautan unduhan dengan slug unik.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-201** | Database | Migrasi tabel `links` dengan enkripsi simetris pada kolom `destination_url` | TASK-106 | P0 | Model `Link` memiliki attribute casting encrypt/decrypt |
| **TASK-202** | Service | Implementasi `SlugGeneratorService` (6–8 karakter acak alfanumerik unik) | TASK-201 | P0 | Generator menghasilkan slug acak dan memverifikasi keunikan |
| **TASK-203** | Controller | Implementasi `LinkController` (CRUD: index, create, store, edit, update, Soft Delete, Hard Delete dari Trash) | TASK-202 | P0 | Form request memvalidasi format URL dan custom slug |
| **TASK-204** | UI/UX | Implementasi Blade views manajemen link (`index`, `create`, `edit`, `trash`) dengan Native CSS | TASK-203 | P0 | Tampilan responsif mobile/desktop, tombol copy link aktif |
| **TASK-205** | Feature | Fitur toggle status link (Active/Inactive) via AJAX/PATCH dan tagging website sumber | TASK-204 | P1 | Status link dapat diubah instan tanpa reload halaman |
| **TASK-206** | Testing | Tulis dan persiapkan `LinkManagementTest` (mencakup Soft Delete & Hard Delete) | TASK-205 | P0 | Skenario `TEST-LINK-*` terdaftar dan tervalidasi |

---

### FASE 3: Safelink Engine & Alur Verifikasi Pengunjung (Core Safelink)
- **Tujuan:** Menampilkan halaman safelink yang memuat Turnstile, countdown 10 detik, dan redirect aman anti-bypass via hashed session token.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-301** | Database | Migrasi tabel `visitor_sessions` dengan kolom `token_hash VARCHAR(64)` | TASK-206 | P0 | Tabel terbuat dengan index token_hash unik dan TTL expires_at |
| **TASK-302** | Service | Implementasi `TokenService` (token acak 32 bytes, token_hash SHA-256, & Keyed HMAC IP dengan `ANALYTICS_SALT`) | TASK-301 | P0 | Token hash SHA-256 tersimpan di DB, IP disamarkan |
| **TASK-303** | Service | Implementasi `TurnstileService` (integrasi Cloudflare SiteVerify & Switch Darurat) | TASK-302 | P0 | API verifikasi valid; mode emergency bypass berfungsi |
| **TASK-304** | Controller | Implementasi `SafelinkController@show` dan view `safelink.blade.php` | TASK-303 | P0 | Halaman publik safelink dimuat dengan header `X-LiteSpeed-Cache-Control: no-cache` |
| **TASK-305** | Controller | Implementasi `SafelinkController@verify` (AJAX Turnstile callback handler) | TASK-304 | P0 | Verifikasi Turnstile mencatat `countdown_started_at = now()` |
| **TASK-306** | Controller | Implementasi `SafelinkController@proceed` dengan **Server-Side Elapsed Time & Atomic State Transition via token_hash** | TASK-305 | P0 | Redirect 302 ke URL asli; menolak bypass waktu dengan HTTP 422 |
| **TASK-307** | UI/UX | View khusus link kedaluwarsa (`expired.blade.php`) dan tidak valid (`invalid.blade.php`) | TASK-306 | P0 | Respon HTTP 410 dan 404 menampilkan UI ramah pengguna |
| **TASK-308** | Testing | Tulis dan eksekusi `SafelinkFlowTest` (mencakup simulasi curang bypass JS) | TASK-307 | P0 | Seluruh skenario `TEST-SAFE-*` berstatus PASS |

---

### FASE 4: Manajemen Slot Iklan (Monetization Engine)
- **Tujuan:** Admin dapat mengelola penempatan kode iklan kandidat (Adsterra/Monetag) secara dinamis di halaman safelink dengan banner display terlabel jelas.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-401** | Database | Migrasi tabel `ad_slots` (posisi: header, above_timer, below_timer, footer) | TASK-308 | P0 | Skema slot iklan siap menampung kode tag script iklan |
| **TASK-402** | Controller | Implementasi `AdSlotController` di panel admin untuk CRUD slot iklan | TASK-401 | P0 | Admin dapat memasukkan script tag dan toggle aktif/mati |
| **TASK-403** | UI/UX | Pembuatan Blade Component `<x-public.ad-slot>` dengan container pencegah CLS, label sponsor, dan tombol unduhan steril dari iklan | TASK-402 | P0 | Iklan termuat asinkron dengan jarak aman minimal 30px |
| **TASK-404** | Testing | Tulis dan eksekusi `AdSlotManagementTest` | TASK-403 | P1 | Seluruh skenario `TEST-ADS-*` berstatus PASS |

---

### FASE 5: Analitik Trafik & Agregasi Data
- **Tujuan:** Mencatat impresi dan completion rate serta menyajikan grafik performa di dashboard.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-501** | Database | Migrasi tabel `visits` (link_id nullable ON DELETE SET NULL, immutable snapshot: `link_id_snapshot`, `link_slug_snapshot`) dan tabel `daily_stats` (kolom `aggregate_key NOT NULL`, `UNIQUE KEY (aggregate_key, date)`, `link_id nullable`, `link_slug NOT NULL`) | TASK-404 | P0 | Skema event logging kunjungan dan agregat harian definitif siap |
| **TASK-502** | Service | Implementasi `VisitRecorderService` untuk mencatat event `impression` & `completed` via HMAC (`ANALYTICS_SALT` tanpa fallback) serta mengabadikan snapshot ID & slug | TASK-501 | P0 | Event tercatat dengan hash IP, snapshot link, dan parsing perangkat/browser |
| **TASK-503** | Console | Pembuatan artisan command `AggregateDailyStatsCommand` (berbasis `link_id_snapshot` & `link_slug_snapshot`, upsert idempoten via `aggregate_key`) | TASK-502 | P1 | Data kemarin terekap ke `daily_stats` tanpa risiko duplikasi NULL, termasuk tautan terhapus |
| **TASK-504** | Controller | Implementasi `DashboardController` dan view metrik (Total Klik, Completion Rate, Tren) | TASK-503 | P0 | Dashboard admin menampilkan 4 kartu statistik dan grafik mingguan |
| **TASK-505** | Testing | Pengujian fungsional analitik dan agregasi | TASK-504 | P1 | Kueri dashboard dieksekusi < 100ms |

---

### FASE 6: Pengerasan Keamanan, Optimasi & Cron Job
- **Tujuan:** Memastikan aplikasi tahan serangan, ramah kuota disk shared hosting, dan cron job berjalan lancar.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-601** | Security | Migrasi tabel `security_logs` dan pembuatan `SecurityLogService` | TASK-505 | P1 | Percobaan bypass token dan edit kode iklan dicatat di database |
| **TASK-602** | Middleware | Terapkan `SecurityHeadersMiddleware` (CSP, X-Robots-Tag noindex, X-LiteSpeed-Cache-Control) | TASK-601 | P0 | Header respon HTTP menyertakan noindex dan anti-cache |
| **TASK-603** | Console | Pembuatan artisan command `PruneExpiredSessionsCommand`, `PruneCacheCommand` (mingguan), dan `PruneOldVisitDataCommand` (90 hari) | TASK-602 | P0 | Sesi > 24 jam, cache file usang > 7 hari, dan log mentah > 90 hari otomatis terhapus |
| **TASK-604** | Scheduler | Pendaftaran jadwal tugas di `routes/console.php` dengan `Asia/Jakarta` dan `withoutOverlapping` | TASK-603 | P0 | `schedule:run` mengeksekusi tugas pruning dan agregasi |
| **TASK-605** | Optimization| Audit performa kueri database (EXPLAIN query) dan optimasi cache file | TASK-604 | P0 | Seluruh kueri publik lolos evaluasi performa |

---

### FASE 7: Full QA, UAT & Panduan Deployment Produksi
- **Tujuan:** Verifikasi akhir seluruh skenario dan instalasi ke cPanel WarnaHost.

| Task ID | Modul | Deskripsi Tugas | Dependensi | Prioritas | Acceptance Criteria |
|---|---|---|---|:---:|---|
| **TASK-701** | Quality | Eksekusi seluruh automated test suite (`php artisan test`) | TASK-605 | P0 | **100% test case PASS (Coverage P0/P1 hijau)** |
| **TASK-702** | UAT | Manual User Acceptance Testing alur pengunjung pada perangkat mobile fisik | TASK-701 | P0 | Safelink berjalan mulus di browser Android/iOS |
| **TASK-703** | Deployment| Deployment ke Shared Hosting WarnaHost sesuai panduan DOC-13 (struktur folder aman di luar `public_html` dengan Fallback Pre-Built Release) | TASK-702 | P0 | Aplikasi live di `lokataut.com` dengan SSL Full Strict |
| **TASK-704** | Operations| Setup Cron Job cPanel `schedule:run` dan pengujian di server live | TASK-703 | P0 | Cron berjalan otomatis setiap menit |

---

## 3. Aturan Pelaksanaan Bagi AI Coding Agent

1. **Satu Task dalam Satu Waktu:** Selesaikan task secara berurutan berdasarkan nomor ID (`TASK-101`, lalu `TASK-102`, dst.).
2. **Jangan Melompati Fase:** Dilarang mengerjakan Fase 3 (Safelink Engine) jika Fase 2 (Link Management) belum memiliki test suite yang lolos.
3. **Konfirmasi Setiap Penyelesaian:** Setelah menyelesaikan satu task, jalankan automated test terkait dan laporkan hasilnya sebelum meminta izin melanjutkan ke task berikutnya.

