# User Flow & System Flow — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-04 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Senior Systems Analyst & Lead Software Architect |
| **Target Pembaca** | AI Coding Agent, Frontend/Backend Engineer, QA Tester |
| **Tujuan** | Mendokumentasikan alur interaksi pengguna (user flow) dan logika internal sistem (system flow) menggunakan diagram Mermaid yang presisi dan implementable. |

---

## Dependensi Dokumen

| Dokumen | ID | Keterangan |
|---|---|---|
| [Product Requirements Document](../01-product/02-prd.md) | DOC-02 | Referensi ID kebutuhan fungsional (FR) dan aturan bisnis (BR) |
| [Scope & Requirements Specification](../01-product/03-scope-requirements.md) | DOC-03 | Batasan fitur MVP dan dependensi komponen |
| [Software Architecture](../03-architecture/05-system-architecture.md) | DOC-05 | Arsitektur modular monolith dan request lifecycle Laravel 13 |
| [Database Design](../03-architecture/06-database-design.md) | DOC-06 | Skema persistensi data sesi dan kunjungan |
| [Security & Privacy](../05-security-monetization/09-security-privacy.md) | DOC-09 | Protokol keamanan kriptografis dan pencegahan bypass |

---

## 1. Admin Login & Authentication Flow

Alur autentikasi administrator tunggal dengan perlindungan brute-force (maksimal 5 percobaan gagal per menit) dan proteksi single session:

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Administrator
    participant Browser as Admin Browser
    participant Router as Laravel 13 Router
    participant Throttle as RateLimiter Middleware
    participant Controller as Auth\LoginController
    participant DB as MySQL (admins & security_logs)

    Admin->>Browser: Akses URL /login
    Browser->>Router: GET /login
    Router-->>Browser: Tampilkan Form Login (login.blade.php + CSRF)

    Admin->>Browser: Input Email, Password, & Submit
    Browser->>Router: POST /login (Credentials + _token)
    Router->>Throttle: Cek Percobaan Login (Maks 5 / menit)
    
    alt Percobaan Gagal Melebihi Batas (>= 5 kali)
        Throttle-->>Browser: HTTP 429 Too Many Requests (Delay progresif)
    else Masih dalam Batas Normal
        Throttle->>Controller: Loloskan Request
        Controller->>DB: Query Admin WHERE email = ?
        alt Kredensial Salah
            Controller->>DB: INSERT security_logs (event: login_failed, IP, User-Agent)
            Controller-->>Browser: HTTP 302 Redirect Back (Error: "Kredensial tidak cocok")
        else Kredensial Benar
            Controller->>Controller: Regenerasi Session ID (Cegah Fixation)
            Controller->>DB: UPDATE admins SET last_login_at = NOW(), last_login_ip = ?
            Controller->>DB: INSERT security_logs (event: login_success)
            Controller-->>Browser: HTTP 302 Redirect ke /admin (Dashboard)
        end
    end
```

---

## 2. Alur Pengunjung Safelink (Visitor Journey — Core Flow)

Alur terpenting platform yang menjamin bahwa URL tujuan tidak terekspos sebelum pengunjung menyelesaikan verifikasi Turnstile dan hitung mundur 10 detik di sisi server:

```mermaid
sequenceDiagram
    autonumber
    actor Visitor as Pengunjung
    participant Browser as Visitor Browser
    participant Server as LokaTaut Engine (Laravel 13)
    participant Turnstile as Cloudflare Turnstile API
    participant DB as MySQL Database

    Visitor->>Browser: Klik link download di website sumber
    Browser->>Server: GET /s/{slug}
    Server->>DB: SELECT * FROM links WHERE slug = ? AND deleted_at IS NULL
    
    alt Link Tidak Ditemukan atau Inactive
        Server-->>Browser: HTTP 404 Not Found (invalid.blade.php)
    else Link Expired (expires_at < NOW())
        Server-->>Browser: HTTP 410 Gone (expired.blade.php)
    else Link Aktif & Valid
        Server->>Server: Generate token = bin2hex(random_bytes(32))
        Server->>DB: INSERT visitor_sessions (token, link_id, user_agent_hash, expires_at)
        Server->>DB: INSERT visits (event_type = 'impression', ip_hmac)
        Server-->>Browser: Render safelink.blade.php (Ads + Turnstile + Button Disabled)
    end

    Note over Browser,Turnstile: Pengunjung Menyelesaikan Tantangan Bot
    Browser->>Turnstile: Verifikasi Interaksi Pengguna
    Turnstile-->>Browser: Return cf-turnstile-response token

    Browser->>Server: AJAX POST /s/{slug}/verify (session_token, cf-turnstile-response)
    Server->>Turnstile: POST siteverify (secret_key, response_token)
    Turnstile-->>Server: JSON { success: true }
    
    Server->>DB: UPDATE visitor_sessions SET turnstile_verified = 1, countdown_started_at = NOW()
    Server->>DB: INSERT visits (event_type = 'verified')
    Server-->>Browser: JSON { success: true, countdown_seconds: 10 }

    Note over Browser: JavaScript Menjalankan Timer Visual 10..9..8..0
    Browser->>Browser: Timer Capai 0s -> Aktifkan Tombol "Lanjutkan Unduhan"

    Visitor->>Browser: Klik "Lanjutkan Unduhan"
    Browser->>Server: POST /s/{slug}/proceed (session_token, _token)
    Server->>DB: SELECT * FROM visitor_sessions WHERE token = ?
    
    Note over Server: Validasi Server-Side Wajib:
    Note over Server: 1. turnstile_verified == 1
    Note over Server: 2. NOW() - countdown_started_at >= countdown_seconds
    
    alt Percobaan Curang (JS Manipulation / Bypass Waktu)
        Server->>DB: INSERT security_logs (event: countdown_bypass_attempt)
        Server-->>Browser: HTTP 422 Unprocessable Entity ("Countdown belum selesai")
    else Validasi Waktu & Token Lolos
        Note over Server: Atomic Update Mencegah Race Condition!
        Server->>DB: UPDATE visitor_sessions SET completed = 1, completed_at = NOW() WHERE token = ? AND completed = 0
        alt Affected Rows == 0 (Token Sudah Pernah Digunakan / Replay Attack)
            Server-->>Browser: HTTP 422 Unprocessable Entity ("Token kadaluwarsa")
        else Affected Rows == 1 (Valid & Unik)
            Server->>DB: INSERT visits (event_type = 'completed')
            Server->>DB: UPDATE links SET total_completions = total_completions + 1
            Server->>Server: Dekripsi destination_url (AES-256)
            Server-->>Browser: HTTP 302 Found (Location: https://download.com/file.zip)
            Browser->>Visitor: Unduhan File Dimulai Otomatis
        end
    end
```

---

## 3. Safelink State Machine Diagram

Status siklus hidup sesi pengunjung pada tabel `visitor_sessions`:

```mermaid
stateDiagram-v2
    [*] --> Initialized: GET /s/{slug}
    Initialized --> TurnstilePending: Halaman Safelink Dimuat
    TurnstilePending --> TurnstileVerified: AJAX /verify Sukses
    TurnstilePending --> VerificationFailed: Turnstile Gagal / Timeout
    
    TurnstileVerified --> CountingDown: countdown_started_at Dicatat Server
    CountingDown --> ReadyToProceed: Waktu Server Berlalu >= 10s
    CountingDown --> BypassDetected: POST /proceed Prematur (< 10s)
    BypassDetected --> CountingDown: Ditolak HTTP 422 (Wajib Tunggu)
    
    ReadyToProceed --> Completed: Atomic UPDATE completed = 1 & Redirect 302
    Completed --> [*]: Unduhan Selesai
    
    Initialized --> Expired: Melewati Batas TTL (30 Menit)
    TurnstilePending --> Expired: Melewati Batas TTL
    CountingDown --> Expired: Melewati Batas TTL
    Expired --> [*]: Sesi Dihapus via Cron Pruning
```

---

## 4. Alur Manajemen Tautan Admin (Create Shortlink Flow)

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Administrator
    participant Browser as Admin Browser
    participant Controller as Admin\LinkController
    participant Service as Services\SlugGeneratorService
    participant DB as MySQL Database

    Admin->>Browser: Buka menu "Buat Tautan Baru"
    Browser->>Controller: GET /admin/links/create
    Controller-->>Browser: Render view links/create.blade.php

    Admin->>Browser: Isi URL Tujuan, Judul, Website Sumber, & Submit
    Browser->>Controller: POST /admin/links (StoreLinkRequest)
    
    Note over Controller: Validasi Form:
    Note over Controller: 1. URL valid (http/https)
    Note over Controller: 2. Custom slug unik & regex [a-z0-9-]
    
    alt Custom Slug Dikosongkan
        Controller->>Service: generateUniqueSlug()
        Service->>DB: Cek Keunikan String Acak (6-8 char)
        Service-->>Controller: Return unique_slug
    end

    Controller->>Controller: Enkripsi destination_url via AES-256-CBC
    Controller->>DB: INSERT INTO links (slug, destination_url, title, status, ...)
    Controller-->>Browser: HTTP 302 Redirect ke /admin/links (Notifikasi Sukses)
```

---

## 5. Alur Agregasi Analitik & Pruning Berkala (Cron Flow)

```mermaid
flowchart TD
    StartCron["Cron Job cPanel Mengeksekusi\nartisan schedule:run Setiap Menit"] --> Scheduler["Laravel 13 Scheduler (routes/console.php)"]
    
    Scheduler --> Task1["Setiap Jam:\nlokataut:prune-sessions"]
    Scheduler --> Task2["Setiap Hari (00:05 WIB):\nlokataut:aggregate-stats"]
    Scheduler --> Task3["Setiap Minggu:\nlokataut:prune-visit-logs"]

    Task1 --> Action1["DELETE FROM visitor_sessions\nWHERE expires_at < NOW() OR (completed = 1 AND created_at < NOW() - 24 Jam)"]
    
    Task2 --> Action2["Kueri Agregasi dari Tabel visits untuk Hari Kemarin:\nHitung Impressions, Verified, Completions, Device, & Country"]
    Action2 --> Action2b["INSERT INTO daily_stats (link_id, date, ...) ON DUPLICATE KEY UPDATE"]
    
    Task3 --> Action3["DELETE FROM visits\nWHERE created_at < NOW() - 90 Hari (Menjaga Kapasitas Disk NVMe)"]
```

