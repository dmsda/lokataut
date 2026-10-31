# Requirement Traceability Matrix (RTM) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | RTM-v1.2 |
| **Versi** | 1.2 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Status** | COMPLETE (100% Bi-Directional Synchronized, Zero Orphan Tests) |

---

## 1. Matriks Keterlacakan Kebutuhan Fungsional (Functional Traceability)

Tabel ini memastikan setiap kebutuhan fungsional (FR) dari PRD terpetakan secara utuh ke Scope, Komponen Arsitektur, Tabel Database, Halaman UI, Standar Keamanan, Test Case Otomatis (termasuk positive dan negative test cases), dan Task Roadmap:

| Req ID | Nama Kebutuhan | PRD | Scope | Komponen Arsitektur | Tabel Database | View UI | Security Rule | Test Case ID | Roadmap Task | Prioritas |
|---|---|---|---|---|---|---|---|---|---|:---:|
| **FR-AUTH-001** | Admin Login & Brute-force Throttling | DOC-02 §3.1 | DOC-03 §3 | `Auth\LoginController` | `admins` | `auth/login.blade.php` | Bcrypt, Throttling | `TEST-AUTH-001`, `TEST-AUTH-002`, `TEST-AUTH-003` | `TASK-103`, `TASK-104` | P0 |
| **FR-AUTH-002** | Admin Logout & Session Purge | DOC-02 §3.1 | DOC-03 §3 | `Auth\LoginController` | `admins` | `admin/sidebar.blade.php` | Session Invalidation | `TEST-AUTH-005` | `TASK-103` | P0 |
| **FR-AUTH-003** | Single Session Enforce | DOC-02 §3.1 | DOC-03 §3 | `Middleware\EnforceSingleAdminSession` | `admins` | - | Session Fixation Mitig. | `TEST-AUTH-004` | `TASK-104` | P0 |
| **FR-AUTH-004** | Admin Password & Profile Management | DOC-02 §3.1 | DOC-03 §3 | `Admin\ProfileController` | `admins` | `admin/profile.blade.php` | Current Pwd & Session Regen | `TEST-AUTH-006`, `TEST-AUTH-007` | `TASK-105` | P1 |
| **FR-DASH-001** | Dashboard Overview (4 Metrik) | DOC-02 §3.2 | DOC-03 §3 | `Admin\DashboardController` | `links`, `daily_stats` | `admin/dashboard.blade.php` | Auth Guard | `TEST-DASH-001` | `TASK-504` | P0 |
| **FR-DASH-002** | Quick Stats & Tren 7 Hari | DOC-02 §3.2 | DOC-03 §3 | `Services\AnalyticsService` | `daily_stats` | `admin/dashboard.blade.php` | Parameter Binding | `TEST-DASH-002` | `TASK-504` | P1 |
| **FR-LINK-001** | Random Slug Generation (6-8 char) | DOC-02 §3.3 | DOC-03 §3 | `Services\SlugGeneratorService`| `links` | `admin/links/create.blade.php` | Regex `[a-z0-9-]` | `TEST-LINK-001` | `TASK-202` | P0 |
| **FR-LINK-002** | Custom Slug Unik | DOC-02 §3.3 | DOC-03 §3 | `Admin\LinkController` | `links` | `admin/links/create.blade.php` | Unique Validation | `TEST-LINK-002`, `TEST-LINK-003` | `TASK-203` | P1 |
| **FR-LINK-003** | CRUD Destination URL | DOC-02 §3.3 | DOC-03 §3 | `Admin\LinkController` | `links` | `admin/links/index.blade.php` | AES-256 Symmetric Enc. | `TEST-LINK-003`, `TEST-LINK-004` | `TASK-203` | P0 |
| **FR-LINK-004** | Status Toggle & Expiration | DOC-02 §3.3 | DOC-03 §3 | `Admin\LinkController` | `links` | `admin/links/index.blade.php` | Form Request Validate | `TEST-LINK-005`, `TEST-LINK-006` | `TASK-205` | P0 |
| **FR-LINK-005** | Source Website Tagging | DOC-02 §3.3 | DOC-03 §3 | `Admin\LinkController` | `links` | `admin/links/create.blade.php` | Nullable Sanitized | `TEST-LINK-001` | `TASK-205` | P1 |
| **FR-SAFE-001** | Safelink Landing Page (Hashed Token) | DOC-02 §3.4 | DOC-03 §3 | `Public\SafelinkController` | `visitor_sessions` | `public/safelink.blade.php` | Hashed Token & URL Concealment | `TEST-SAFE-001` | `TASK-304` | P0 |
| **FR-SAFE-002** | Turnstile Verification | DOC-02 §3.4 | DOC-03 §3 | `Services\TurnstileService` | `visitor_sessions` | `public/turnstile-widget` | SiteVerify API | `TEST-SAFE-004` | `TASK-303` | P0 |
| **FR-SAFE-003** | Server-Side Countdown Elapsed Time | DOC-02 §3.4 | DOC-03 §3 | `Public\SafelinkController` | `visitor_sessions` | `public/countdown-timer` | Server Timestamp Delta | `TEST-SAFE-006` | `TASK-304` | P0 |
| **FR-SAFE-004** | Secure Redirect 302 & Atomic Check | DOC-02 §3.4 | DOC-03 §3 | `Services\SafelinkService` | `visitor_sessions` | - | Atomic Trans. via token_hash | `TEST-SAFE-005`, `TEST-SAFE-007`, `TEST-SAFE-008`, `TEST-SEC-003` | `TASK-306` | P0 |
| **FR-SAFE-005** | Expired & Invalid Notice Page | DOC-02 §3.4 | DOC-03 §3 | `Public\SafelinkController` | `links` | `public/expired.blade.php` | HTTP 410 / 404 Status | `TEST-SAFE-002`, `TEST-SAFE-003` | `TASK-307` | P0 |
| **FR-ADS-001** | Ad Slot Management & Status Toggle | DOC-02 §3.5 | DOC-03 §3 | `Admin\AdSlotController` | `ad_slots` | `admin/ads/index.blade.php` | CSP Header Protection | `TEST-ADS-001`, `TEST-ADS-002` | `TASK-402` | P0 |
| **FR-ADS-002** | Ad Code Config, Label & Anti-IVT | DOC-02 §3.5 | DOC-03 §3 | `Admin\AdSlotController` | `ad_slots` | `admin/ads/edit.blade.php` | Super Admin Only & Anti-IVT | `TEST-ADS-003` | `TASK-402` | P0 |
| **FR-ANAL-001** | Event Tracking & IP Pseudonymization | DOC-02 §3.6 | DOC-03 §3 | `Services\VisitRecorderService`| `visits` | - | Keyed HMAC (`ANALYTICS_SALT`) | `TEST-ANAL-001` | `TASK-502` | P0 |
| **FR-ANAL-002** | Completion Rate & Date Range Filter | DOC-02 §3.6 | DOC-03 §3 | `Services\AnalyticsService` | `daily_stats` | `admin/analytics/index` | Query Aggregation | `TEST-ANAL-002` | `TASK-504` | P1 |
| **FR-ANAL-003** | Daily Aggregation (`Link::withTrashed`)| DOC-02 §3.6 | DOC-03 §3 | `Commands\AggregateDailyStatsCommand` | `daily_stats` | - | Soft-delete Aware Aggr. | `TEST-CLI-001` | `TASK-503` | P1 |
| **FR-SET-001** | General Settings & Timezone | DOC-02 §3.7 | DOC-03 §3 | `Admin\SettingsController` | `settings` | `admin/settings/index` | File-based Cache TTL | `TEST-SET-001` | `TASK-605` | P1 |
| **FR-SET-002** | Turnstile Keys Config | DOC-02 §3.7 | DOC-03 §3 | `Admin\SettingsController` | `settings` | `admin/settings/index` | Encrypted Secret | `TEST-SET-002` | `TASK-605` | P0 |
| **FR-SET-003** | Turnstile Emergency Bypass Switch | DOC-02 §3.7 | DOC-03 §3 | `Admin\SettingsController` | `settings` | `admin/settings/index` | Audit Logged Fallback | `TEST-SET-003` | `TASK-605` | P1 |
| **FR-SEC-001** | Security Audit Logging | DOC-02 §3.7 | DOC-03 §3 | `Services\SecurityLogService` | `security_logs` | `admin/security/logs` | Immutable Inserts | `TEST-SEC-004` | `TASK-601` | P1 |

---

## 2. Matriks Keterlacakan Kebutuhan Non-Fungsional (Non-Functional Traceability)

| NFR ID | Deskripsi Kebutuhan | Kategori | Target Kinerja / Ambang Batas | Komponen Validasi | Test Case ID | Status |
|---|---|---|---|---|---|:---:|
| **NFR-PERF-001** | Waktu muat halaman safelink | Performa | LCP < 1.2 detik pada jaringan 4G | Google Lighthouse Mobile | `TEST-PERF-001` | PASS TARGET |
| **NFR-PERF-002** | Waktu eksekusi kueri DB & LiteSpeed | Performa | Lookup link < 5ms, Agregasi < 100ms | MySQL EXPLAIN, Header Anti-Cache | `TEST-PERF-002`, `TEST-PERF-003` | PASS TARGET |
| **NFR-PERF-003** | Kapasitas shared hosting | Skalabilitas| 10.000–50.000 kunjungan/hari (1 vCPU) | Memory Footprint Profiling | `TEST-PERF-003` | PASS TARGET |
| **NFR-SEC-001** | Enkripsi simetris URL & Token Hash | Keamanan | AES-256-CBC & SHA-256 token_hash | Eloquent Crypt & Token Hash Check | `TEST-LINK-003`, `TEST-SEC-002` | PASS TARGET |
| **NFR-SEC-002** | Proteksi CSRF | Keamanan | Wajib pada seluruh request non-GET | Middleware `ValidateCsrfToken` | `TEST-AUTH-005` | PASS TARGET |
| **NFR-SEC-003** | Sanitasi XSS & CSP | Keamanan | Output Blade `{{ }}`, CSP script-src | View Audit & Security Test | `TEST-SEC-001` | PASS TARGET |
| **NFR-SEC-004** | Rate Limiting IP Publik | Keamanan | 60 request per menit per IP | Laravel Throttle Middleware | `TEST-SEC-005` | PASS TARGET |
| **NFR-SEC-005** | Skema URL Whitelist | Keamanan | Skema terbatas `http://`, `https://` | Form Request Validation | `TEST-LINK-004` | PASS TARGET |
| **NFR-UX-001** | Responsivitas Mobile | UX | 100% Responsif (320px s/d 1920px) | CSS Media Queries & Emulation | `TEST-UX-001` | PASS TARGET |
| **NFR-UX-002** | Aksesibilitas Web | UX | Kontras teks ≥ 4.5:1 (WCAG 2.1 AA) | Axe DevTools / Lighthouse | `TEST-UX-002` | PASS TARGET |
| **NFR-MAINT-001**| Standar Kode & Database Rollback | Pemeliharaan| PSR-12, `strict_types=1`, Migration Rollback| PHP CS Fixer & Migration Integrity | `TEST-REG-001`, `TEST-REG-002` | PASS TARGET |

---

## 3. Matriks Keterlacakan Tugas Pemeliharaan Terjadwal (Scheduled Maintenance Traceability)

| Aturan Bisnis | Deskripsi Tugas Terjadwal | Frekuensi | Command Console | Test Case ID | Status |
|---|---|---|---|---|:---:|
| **BR-012 (a)** | Pembersihan Sesi Safelink Kedaluwarsa (> 24 jam) | Setiap Jam | `PruneExpiredSessionsCommand` | `TEST-CLI-002` | PASS TARGET |
| **BR-012 (b)** | Pembersihan File Cache Kedaluwarsa (Inode Pruning)| Setiap Hari (03:00) | `PruneCacheCommand` | `TEST-CLI-003` | PASS TARGET |
| **BR-012 (c)** | Pembersihan Log Kunjungan Mentah (> 90 hari) | Setiap Minggu | `PruneOldVisitDataCommand` | `TEST-CLI-004` | PASS TARGET |
