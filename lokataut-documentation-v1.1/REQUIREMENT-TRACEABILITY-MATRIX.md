# Requirement Traceability Matrix (RTM) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | RTM-v1.1 |
| **Versi** | 1.1 |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Status** | COMPLETE |

---

## 1. Matriks Keterlacakan Kebutuhan Fungsional (Functional Traceability)

Tabel ini memastikan setiap kebutuhan fungsional (FR) dari PRD terpetakan secara utuh ke Scope, Komponen Arsitektur, Tabel Database, Halaman UI, Standar Keamanan, Test Case Otomatis, dan Task Roadmap:

| Req ID | Nama Kebutuhan | PRD | Scope | Komponen Arsitektur | Tabel Database | View UI | Security Rule | Test Case | Roadmap Task | Prioritas |
|---|---|---|---|---|---|---|---|---|---|:---:|
| **FR-AUTH-001** | Admin Login | DOC-02 §7.1 | DOC-03 §2.1 | `Auth\LoginController` | `admins` | `auth/login.blade.php` | Bcrypt, Throttling | `TEST-AUTH-001` | `TASK-103` | P0 |
| **FR-AUTH-002** | Admin Logout | DOC-02 §7.1 | DOC-03 §2.1 | `Auth\LoginController` | `admins` | `admin/sidebar.blade.php` | Session Invalidation | `TEST-AUTH-005` | `TASK-103` | P0 |
| **FR-AUTH-003** | Single Session Enforce | DOC-02 §7.1 | DOC-03 §2.1 | `Middleware\EnforceSingleSession` | `admins` | - | Session Fixation Mitig. | `TEST-AUTH-004` | `TASK-104` | P0 |
| **FR-DASH-001** | Dashboard Overview | DOC-02 §7.2 | DOC-03 §2.1 | `Admin\DashboardController` | `links`, `daily_stats` | `admin/dashboard.blade.php` | Auth Guard | `TEST-DASH-001` | `TASK-504` | P0 |
| **FR-DASH-002** | Quick Stats & Tren | DOC-02 §7.2 | DOC-03 §2.1 | `Services\AnalyticsService` | `daily_stats` | `admin/dashboard.blade.php` | Parameter Binding | `TEST-DASH-002` | `TASK-504` | P1 |
| **FR-LINK-001** | Random Slug Gen | DOC-02 §7.3 | DOC-03 §2.1 | `Services\SlugGeneratorService`| `links` | `admin/links/create.blade.php` | Regex `[a-z0-9-]` | `TEST-LINK-001` | `TASK-202` | P0 |
| **FR-LINK-002** | Custom Slug | DOC-02 §7.3 | DOC-03 §2.1 | `Admin\LinkController` | `links` | `admin/links/create.blade.php` | Unique Validation | `TEST-LINK-002` | `TASK-203` | P1 |
| **FR-LINK-003** | CRUD Destination URL | DOC-02 §7.3 | DOC-03 §2.1 | `Admin\LinkController` | `links` | `admin/links/index.blade.php` | AES-256 Symmetric Enc. | `TEST-LINK-001` | `TASK-203` | P0 |
| **FR-LINK-004** | Status Toggle | DOC-02 §7.3 | DOC-03 §2.1 | `Admin\LinkController` | `links` | `admin/links/index.blade.php` | Form Request Validate | `TEST-LINK-005` | `TASK-205` | P0 |
| **FR-LINK-005** | Source Web Tagging | DOC-02 §7.3 | DOC-03 §2.1 | `Admin\LinkController` | `links` | `admin/links/create.blade.php` | Nullable Sanitized | `TEST-LINK-001` | `TASK-205` | P1 |
| **FR-SAFE-001** | Safelink Landing Page| DOC-02 §7.4 | DOC-03 §2.1 | `Public\SafelinkController` | `visitor_sessions` | `public/safelink.blade.php` | URL Concealment | `TEST-SAFE-001` | `TASK-304` | P0 |
| **FR-SAFE-002** | Turnstile Verify | DOC-02 §7.4 | DOC-03 §2.1 | `Services\TurnstileService` | `visitor_sessions` | `public/turnstile-widget` | SiteVerify API | `TEST-SAFE-004` | `TASK-303` | P0 |
| **FR-SAFE-003** | Countdown Timer | DOC-02 §7.4 | DOC-03 §2.1 | `Public\SafelinkController` | `links` | `public/countdown-timer` | Server Timestamp Delta | `TEST-SAFE-004` | `TASK-304` | P0 |
| **FR-SAFE-004** | Secure Redirect 302 | DOC-02 §7.4 | DOC-03 §2.1 | `Services\SafelinkService` | `visitor_sessions` | - | Atomic State Trans. | `TEST-SAFE-005` | `TASK-306` | P0 |
| **FR-SAFE-005** | Expired/Invalid Page | DOC-02 §7.4 | DOC-03 §2.1 | `Public\SafelinkController` | `links` | `public/expired.blade.php` | HTTP 410 / 404 Status | `TEST-SAFE-002` | `TASK-307` | P0 |
| **FR-ADS-001** | Ad Slot Management | DOC-02 §7.5 | DOC-03 §2.1 | `Admin\AdSlotController` | `ad_slots` | `admin/ads/index.blade.php` | CSP Header Protection | `TEST-ADS-001` | `TASK-402` | P0 |
| **FR-ADS-002** | Ad Code Config | DOC-02 §7.5 | DOC-03 §2.1 | `Admin\AdSlotController` | `ad_slots` | `admin/ads/edit.blade.php` | Super Admin Auth Only | `TEST-ADS-001` | `TASK-402` | P0 |
| **FR-ANAL-001** | Event Tracking | DOC-02 §7.6 | DOC-03 §2.1 | `Services\VisitRecorderService`| `visits` | - | Keyed HMAC IP Hash | `TEST-ANAL-001` | `TASK-502` | P0 |
| **FR-ANAL-002** | Completion Rate | DOC-02 §7.6 | DOC-03 §2.1 | `Services\AnalyticsService` | `daily_stats` | `admin/analytics/index` | Query Aggregation | `TEST-ANAL-002` | `TASK-504` | P1 |
| **FR-ANAL-003** | Daily Aggregation | DOC-02 §7.6 | DOC-03 §2.1 | `Commands\AggregateDailyStats` | `daily_stats` | - | Composite Unique Index | `TEST-CRON-002` | `TASK-503` | P1 |
| **FR-SET-001** | General Settings | DOC-02 §7.7 | DOC-03 §2.1 | `Admin\SettingsController` | `settings` | `admin/settings/index` | File-based Cache TTL | `TEST-SET-001` | `TASK-605` | P1 |
| **FR-SET-002** | Turnstile Keys Config| DOC-02 §7.7 | DOC-03 §2.1 | `Admin\SettingsController` | `settings` | `admin/settings/index` | Encrypted Secret | `TEST-SET-002` | `TASK-605` | P0 |
| **FR-SEC-001** | Security Audit Logs | DOC-02 §7.7 | DOC-03 §2.1 | `Services\SecurityLogService` | `security_logs` | `admin/security/logs` | Immutable Inserts | `TEST-SEC-001` | `TASK-601` | P1 |

---

## 2. Matriks Keterlacakan Kebutuhan Non-Fungsional (Non-Functional Traceability)

| NFR ID | Deskripsi Kebutuhan | Kategori | Target Kinerja / Ambang Batas | Komponen Validasi | Test Case | Status |
|---|---|---|---|---|---|:---:|
| **NFR-PERF-001** | Waktu muat halaman safelink | Performa | LCP < 1.2 detik pada jaringan 4G | Google Lighthouse Mobile | `TEST-PERF-001` | PASS TARGET |
| **NFR-PERF-002** | Waktu eksekusi kueri DB | Performa | Lookup link < 5ms, Agregasi < 100ms | MySQL EXPLAIN & Profiler | `TEST-PERF-002` | PASS TARGET |
| **NFR-PERF-003** | Kapasitas shared hosting | Skalabilitas| 10.000–50.000 kunjungan/hari (1 vCPU) | Apache Benchmark / k6 test | `TEST-PERF-003` | PASS TARGET |
| **NFR-SEC-001** | Enkripsi simetris URL | Keamanan | AES-256-CBC pada `destination_url` | Eloquent Crypt Casting | `TEST-SEC-002` | PASS TARGET |
| **NFR-SEC-002** | Proteksi CSRF | Keamanan | Wajib pada seluruh request non-GET | Middleware `ValidateCsrfToken` | `TEST-SEC-003` | PASS TARGET |
| **NFR-SEC-003** | Sanitasi XSS | Keamanan | Output Blade `{{ }}`, CSP script-src | View Audit & Security Test | `TEST-SEC-004` | PASS TARGET |
| **NFR-SEC-004** | Proteksi SQL Injection | Keamanan | 100% Prepared Statements PDO | Query Builder Inspection | `TEST-SEC-005` | PASS TARGET |
| **NFR-SEC-005** | Skema URL Whitelist | Keamanan | Skema terbatas `http://`, `https://` | Form Request Validation | `TEST-LINK-004` | PASS TARGET |
| **NFR-UX-001** | Responsivitas Mobile | UX | 100% Responsif (320px s/d 1920px) | CSS Media Queries & Emulation | `TEST-UX-001` | PASS TARGET |
| **NFR-UX-002** | Aksesibilitas Web | UX | Kontras teks ≥ 4.5:1 (WCAG 2.1 AA) | Axe DevTools / Lighthouse | `TEST-UX-002` | PASS TARGET |
| **NFR-MAINT-001**| Standar Kode Bersih | Pemeliharaan| PSR-12, `strict_types=1`, Thin Controller| PHP CS Fixer & Linter | `TEST-MAINT-001`| PASS TARGET |

