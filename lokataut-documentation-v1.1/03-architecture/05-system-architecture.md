# Software Architecture Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-05 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Principal Software Architect & Senior Laravel Architect |
| **Target Pembaca** | AI Coding Agent, DevOps Engineer, Technical Lead |
| **Tujuan** | Menetapkan arsitektur perangkat lunak Modular Monolith berbasis Laravel 13, dekomposisi layer aplikasi, strategi caching, logging, dan mitigasi batasan shared hosting. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-01 — Product Vision & Strategy](../01-product/01-product-vision.md) | Visi produk dan batasan biaya operasional |
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Spesifikasi fungsional dan aturan bisnis (BR) |
| [DOC-03 — Scope & Requirements](../01-product/03-scope-requirements.md) | Batasan scope dan matriks keterlacakan |
| [DOC-04 — User Flow & System Flow](../02-analysis/04-user-system-flow.md) | Alur interaksi sistem dan transisi state |
| [DOC-06 — Database Design](./06-database-design.md) | Skema data dan strategi indexing MySQL 8 |
| [DOC-07 — Technical Specification](./07-technical-specification.md) | Standar teknis implementasi kode |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Proteksi keamanan arsitektural |
| [DOC-15 — Architecture Decision Records](../09-decisions/15-architecture-decisions.md) | Rekam keputusan ADR-001 s/d ADR-010 |

---

## 1. Architecture Overview & High-Level Topology

LokaTaut dirancang menggunakan gaya arsitektur **Modular Monolith** berbasis framework **Laravel 13.x (PHP 8.3+)**. Sistem ini dioptimalkan secara khusus untuk berjalan di atas infrastruktur **Shared Hosting WarnaHost Starter Unlimited (1 vCPU Core, 2 GB RAM, cPanel/LiteSpeed Web Server)** dengan integrasi **Cloudflare** di layer terdepan sebagai reverse proxy, DNS, CDN, dan edge WAF.

Arsitektur ini sengaja menghindari kompleksitas berlebih (microservices, Docker di produksi, Redis wajib) untuk menjamin prinsip **Zero License Cost, Low Resource Footprint, dan High Maintainability**.

```mermaid
flowchart TD
    subgraph ClientLayer ["Client & Network Perimeter"]
        Visitor["Visitor Browser\n(Desktop/Mobile)"]
        Admin["Admin Browser\n(Authenticated)"]
        CF["Cloudflare Edge Network\n(DNS, WAF, CDN, SSL Full Strict, Turnstile API)"]
    end

    subgraph HostingInfrastructure ["WarnaHost Shared Hosting Infrastructure"]
        LS["LiteSpeed Web Server\n(PHP 8.3 CLI / LSCache / .htaccess)"]
        
        subgraph LaravelApp ["LokaTaut Modular Monolith (Laravel 13.x)"]
            HttpKernel["HTTP Kernel & Middleware Pipeline\n(RateLimiter, SecurityHeaders, CSRF, SingleSession)"]
            
            subgraph CoreModules ["Core Application Modules"]
                AuthMod["Auth Module\n(Login, Session Purge)"]
                LinkMod["Link Management Module\n(CRUD, AES-256, SlugGen)"]
                SafeMod["Safelink Engine Module\n(Turnstile, Server Countdown, 302 Redirect)"]
                AdsMod["Ad Slot Module\n(CSP Protected, Anti-CLS Wrapper)"]
                AnalMod["Analytics Module\n(Keyed HMAC, VisitRecorder, DailyAggregator)"]
                SetMod["Settings & Security Module\n(Cached Config, Audit Logs)"]
            end
            
            ServiceLayer["Service Layer\n(Business Logic, Crypto, Validation)"]
            FileStorage["Local Storage & File Cache\n(storage/framework/cache, sessions)"]
        end
        
        DB[("MySQL 8.0 Database\n(InnoDB Engine, NVMe Storage)")]
    end

    subgraph ExternalThirdParty ["External Services"]
        TurnstileAPI["Cloudflare Turnstile API\n(challenges.cloudflare.com/turnstile/v0/siteverify)"]
        AdNetwork["Adsterra Ad Network\n(Async Display Banners / Social Bar)"]
    end

    Visitor -->|HTTPS Request| CF
    Admin -->|HTTPS Request| CF
    CF -->|Reverse Proxy / Clean Traffic| LS
    LS --> HttpKernel
    HttpKernel --> CoreModules
    CoreModules --> ServiceLayer
    ServiceLayer --> FileStorage
    ServiceLayer --> DB
    ServiceLayer -->|SiteVerify POST| TurnstileAPI
    Visitor -.->|Loads Ad Banners via Browser| AdNetwork
```

---

## 2. Application Layers & Responsibilities

1. **Presentation Layer (Blade Views & Native Assets):**
   - Menghasilkan markup HTML5 semantik murni.
   - Menggunakan Native CSS3 (CSS Custom Properties) dan Vanilla JavaScript ES6+.
   - Tidak menggunakan framework JavaScript reaktif (React/Vue/SPA) pada antarmuka publik demi menjaga skor Core Web Vitals dan kecepatan eksekusi iklan.
2. **HTTP Layer (Controllers, Form Requests, Middleware):**
   - Menerapkan *Thin Controllers* (maksimal 25 baris per method).
   - Validasi data masukan dikapsulkan 100% pada Form Request classes.
   - Middleware menangani rate limiting (60 req/min untuk publik), keamanan CSRF, dan injeksi security headers (`X-Robots-Tag: noindex`, `CSP`).
3. **Service Layer (Domain Business Logic):**
   - Mengkapsulasi seluruh aturan bisnis dan interaksi pihak ketiga (`SafelinkService`, `TokenService`, `TurnstileService`, `SlugGeneratorService`, `VisitRecorderService`).
   - Bebas dari keterikatan HTTP Request langsung (menggunakan DTO atau parameter primitif ter-type-hint).
4. **Data Access Layer (Eloquent Models & Query Builder):**
   - Memanfaatkan fitur attribute casting Laravel 13 untuk enkripsi/dekripsi transparan `destination_url`.
   - Menggunakan kueri berbasis PDO parameter binding murni untuk proteksi SQL Injection 100%.
5. **Storage & Persistence Layer:**
   - Database MySQL 8.0 berbasis InnoDB dengan indexing komposit yang presisi.
   - File-based cache untuk konfigurasi sistem global (`storage/framework/cache`).

---

## 3. Session & Token Architecture

Terdapat pemisahan fundamental antara sesi administrator dan sesi pengunjung safelink:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Session Architecture Matrix                     │
├────────────────────┬─────────────────────┬─────────────────────────────┤
│ Karakteristik      │ Sesi Administrator  │ Sesi Pengunjung Safelink    │
├────────────────────┼─────────────────────┼─────────────────────────────┤
│ Media Penyimpanan  │ File System Laravel │ Database (visitor_sessions) │
│ Durasi Masa Aktif  │ 120 Menit           │ 30 Menit (TTL)              │
│ Mekanisme Binding  │ Laravel Session ID  │ Token Kripto (64 hex)       │
│                    │ + Single Session    │ + User-Agent Hash           │
│ Replay Protection  │ Session ID Regen.   │ Atomic One-Time Flag        │
│                    │                     │ (UPDATE completed = 1)      │
│ Pembersihan Data   │ Bawaan Laravel      │ Cron Pruning Setiap Jam     │
└────────────────────┴─────────────────────┴─────────────────────────────┘
```

---

## 4. Caching & Scaling Strategy pada Shared Hosting

Untuk menjamin performa stabil pada trafik 10.000–50.000 kunjungan per hari tanpa Redis:

1. **System Settings Caching:** Seluruh nilai tabel `settings` disimpan dalam file cache lokal selama 24 jam. Cache otomatis di-flush seketika saat admin memperbarui pengaturan.
2. **Active Ad Slots Caching:** Konfigurasi slot iklan aktif di-cache selama 1 jam untuk menghemat query basis data pada setiap request safelink.
3. **No Dynamic Safelink Page Caching:** Halaman `/s/{slug}` **wajib dinamis (Cache-Control: no-store)** karena membutuhkan token sesi unik dan validasi Turnstile real-time per pengunjung.
4. **Static Assets Caching:** File CSS, JavaScript, dan logo di-cache di Cloudflare edge network selama 30 hari dengan query string versioning (misal: `safelink.css?v=1.1.0`).
5. **Database Query Optimization:** Seluruh kueri lookup link (`slug`) dieksekusi melalui index B-Tree unik dengan kompleksitas O(log N). Kueri dashboard dieksekusi dari tabel ringkasan `daily_stats`, bukan tabel mentah `visits`.

---

## 5. Logging & Audit Architecture

- **Application Logs:** Menggunakan channel `daily` (`storage/logs/laravel-YYYY-MM-DD.log`) dengan rotasi 14 hari. Level produksi diatur ke `warning` dan `error`.
- **Security Audit Trail:** Disimpan ke tabel `security_logs` pada basis data untuk peristiwa kritis (login gagal berulang, manipulasi parameter countdown, dan modifikasi kode iklan `ad_code`).

