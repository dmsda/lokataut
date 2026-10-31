# User Flow & System Flow — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-04 |
| **Versi** | 1.2 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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

## 1.2 Admin Profile & Password Change Flow (`FR-AUTH-004`)

Alur pembaruan profil dan penggantian kata sandi administrator tunggal dengan verifikasi kata sandi lama, validasi kompleksitas, dan rotasi sesi:

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Administrator
    participant Browser as Admin Browser
    participant Router as Laravel 13 Router
    participant Controller as Admin\ProfileController
    participant DB as MySQL (admins & security_logs)

    Admin->>Browser: Akses menu /admin/profile
    Browser->>Router: GET /admin/profile
    Router->>Controller: show()
    Controller-->>Browser: Render profile.blade.php (Form Profil & Password)

    Admin->>Browser: Input Current Password, New Password, Konfirmasi, & Submit
    Browser->>Router: PUT /admin/profile/password (UpdatePasswordRequest)
    Router->>Controller: updatePassword(request)

    Controller->>Controller: Validasi Hash::check(current_password, admin.password)
    alt Password Lama Salah
        Controller->>DB: INSERT security_logs (event: password_change_failed, reason: invalid_current_password)
        Controller-->>Browser: HTTP 302 Redirect Back (Error: "Password saat ini tidak valid")
    else Password Lama Cocok
        Note over Controller: Validasi Kompleksitas Password Baru:\nMinimal 12 karakter, campuran huruf besar, kecil, angka, dan simbol.
        alt Password Baru Lemah atau Konfirmasi Tidak Cocok
            Controller-->>Browser: HTTP 422 Unprocessable Entity (Validation Error)
        else Validasi Sukses
            Controller->>DB: UPDATE admins SET password = Hash::make(new_password), updated_at = NOW()
            Controller->>Controller: session()->regenerate() (Rotasi ID Sesi)
            Controller->>DB: INSERT security_logs (event: password_change_success)
            Controller-->>Browser: HTTP 302 Redirect ke /admin/profile (Success: "Password berhasil diperbarui")
        end
    end
```

---

## 2. Alur Pengunjung Safelink (Visitor Journey — Core Flow)

Alur terpenting platform yang menjamin bahwa URL tujuan tidak terekspos sebelum pengunjung menyelesaikan verifikasi Turnstile dan hitung mundur 10 detik di sisi server, dengan token sesi tersimpan dalam format hash SHA-256:

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
        Note over Server: Buat token acak & simpan hash SHA-256 ke DB
        Server->>Server: $rawToken = bin2hex(random_bytes(32))
        Server->>Server: $tokenHash = hash('sha256', $rawToken)
        Server->>DB: INSERT visitor_sessions (token_hash, link_id, user_agent_hash, expires_at)
        Server->>DB: INSERT visits (event_type = 'impression', ip_hmac)
        Note over Server: Set header X-LiteSpeed-Cache-Control: no-cache
        Server-->>Browser: Render safelink.blade.php ($rawToken + Ads Banner + Turnstile + Button Disabled)
    end

    Note over Browser,Turnstile: Pengunjung Menyelesaikan Tantangan Bot (atau Emergency Bypass)
    Browser->>Turnstile: Verifikasi Interaksi Pengguna
    Turnstile-->>Browser: Return cf-turnstile-response token

    Browser->>Server: AJAX POST /s/{slug}/verify (rawToken, cf-turnstile-response)
    Server->>Server: $tokenHash = hash('sha256', rawToken)
    
    alt Mode Darurat Aktif (turnstile_mode == 'emergency_bypass')
        Server->>DB: INSERT security_logs (event: turnstile_emergency_bypass_used)
        Note over Server: Lewatkan verifikasi eksternal saat API Cloudflare down
    else Mode Standar
        Server->>Turnstile: POST siteverify (secret_key, response_token)
        Turnstile-->>Server: JSON { success: true }
    end
    
    Server->>DB: UPDATE visitor_sessions SET turnstile_verified = 1, countdown_started_at = NOW() WHERE token_hash = $tokenHash
    Server->>DB: INSERT visits (event_type = 'verified')
    Server-->>Browser: JSON { success: true, countdown_seconds: 10 }

    Note over Browser: JavaScript Menjalankan Timer Visual 10..9..8..0
    Browser->>Browser: Timer Capai 0s -> Aktifkan Tombol "Lanjutkan Unduhan"

    Visitor->>Browser: Klik "Lanjutkan Unduhan" (Steril dari skrip iklan / anti-IVT)
    Browser->>Server: POST /s/{slug}/proceed (rawToken, _token)
    Server->>Server: $tokenHash = hash('sha256', rawToken)
    Server->>DB: SELECT * FROM visitor_sessions WHERE token_hash = $tokenHash
    
    Note over Server: Validasi Server-Side Wajib:
    Note over Server: 1. turnstile_verified == 1
    Note over Server: 2. NOW() - countdown_started_at >= countdown_seconds
    
    alt Percobaan Curang (JS Manipulation / Bypass Waktu)
        Server->>DB: INSERT security_logs (event: countdown_bypass_attempt)
        Server-->>Browser: HTTP 422 Unprocessable Entity ("Countdown belum selesai")
    else Validasi Waktu & Token Lolos
        Note over Server: Atomic Update via token_hash Mencegah Race Condition!
        Server->>DB: UPDATE visitor_sessions SET completed = 1, completed_at = NOW() WHERE token_hash = $tokenHash AND completed = 0
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
    Scheduler --> Task3["Setiap Hari (03:00 WIB):\nlokataut:prune-cache"]
    Scheduler --> Task4["Setiap Minggu:\nlokataut:prune-visit-logs"]

    Task1 --> Action1["DELETE FROM visitor_sessions\nWHERE expires_at < NOW() OR (completed = 1 AND created_at < NOW() - 24 Jam)"]
    
    Task2 --> Action2["Kueri Agregasi dari Tabel visits untuk Hari Kemarin:\nLink::withTrashed() agar link terhapus siang hari tetap terhitung"]
    Action2 --> Action2b["INSERT INTO daily_stats (link_id, date, ...) ON DUPLICATE KEY UPDATE"]
    
    Task3 --> Action3["Pembersihan File Cache Kedaluwarsa di storage/framework/cache/data\n(Mencegah Kehabisan Kuota Inode Shared Hosting)"]

    Task4 --> Action4["DELETE FROM visits\nWHERE created_at < NOW() - 90 Hari (Menjaga Kapasitas Disk NVMe)"]
```

