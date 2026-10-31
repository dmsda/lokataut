# Technical Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-07 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Lead Laravel Architect & Technical Documentation Specialist |
| **Target Pembaca** | AI Coding Agent, Software Engineer |
| **Tujuan** | Menjadi pedoman teknis operasional (*Implementation Blueprint*) yang menetapkan standar implementasi kode, struktur folder, kontrak service, routing, validasi, dan arsitektur controller berbasis Laravel 13. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kontrak spesifikasi fitur (FR) dan aturan bisnis (BR) |
| [DOC-05 — Software Architecture](./05-system-architecture.md) | Desain layer dan arsitektur modular monolith |
| [DOC-06 — Database Design](./06-database-design.md) | Definisi kolom, tipe data, dan indexing tabel MySQL |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Standar validasi token kriptografis dan Turnstile |
| [AGENTS.md](../../AGENTS.md) | Aturan operasional wajib bagi AI Coding Agent |

---

## 1. Technology Stack & Runtime Baseline

```
┌─────────────────────────────────────────────────────────────┐
│                    LokaTaut Tech Stack                      │
├──────────────────────┬──────────────────────────────────────┤
│ Bahasa Pemrograman   │ PHP 8.3+ (Strict Types Enabled)      │
│ Framework Web        │ Laravel 13.x (Modular Monolith)      │
│ Database Engine      │ MySQL 8.0+ (InnoDB Engine)           │
│ Template Engine      │ Laravel Blade (Native Components)    │
│ Frontend CSS         │ Native CSS3 (CSS Variables, Flex/Grid)│
│ Frontend JavaScript  │ Vanilla JS ES6+ (No jQuery/React/Vue)│
│ Package Manager      │ Composer 2.x (Local home directory)  │
│ Web Server Target    │ LiteSpeed / Nginx via cPanel         │
│ CDN & Edge WAF       │ Cloudflare (Free Tier, Full Strict)  │
│ Bot Mitigation       │ Cloudflare Turnstile (Managed API)   │
│ Kandidat Ad Network  │ Adsterra (Async Tags) - Proposed     │
└──────────────────────┴──────────────────────────────────────┘
```

---

## 2. Struktur Direktori Aplikasi (Folder Structure)

```
lokataut/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── PruneExpiredSessionsCommand.php  # Pembersihan sesi > 24 jam (Hourly)
│   │       ├── AggregateDailyStatsCommand.php   # Agregasi data harian ke daily_stats (Daily 00:05)
│   │       └── PruneOldVisitDataCommand.php     # Pembersihan log visits > 90 hari (Weekly)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LinkController.php
│   │   │   │   ├── AdSlotController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   └── SecurityLogController.php
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   └── Public/
│   │   │       └── SafelinkController.php       # Core safelink interaction
│   │   ├── Middleware/
│   │   │   ├── EnforceSingleAdminSession.php
│   │   │   ├── ValidateVisitorToken.php
│   │   │   └── SecurityHeadersMiddleware.php    # X-Robots-Tag, CSP, X-Frame-Options
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreLinkRequest.php
│   │       │   ├── UpdateLinkRequest.php
│   │       │   ├── StoreAdSlotRequest.php
│   │       │   ├── UpdateAdSlotRequest.php
│   │       │   └── UpdateSettingsRequest.php
│   │       ├── Auth/
│   │       │   └── AdminLoginRequest.php
│   │       └── Public/
│   │           ├── SafelinkVerifyRequest.php
│   │           └── SafelinkProceedRequest.php
│   ├── Models/
│   │   ├── Admin.php
│   │   ├── Link.php
│   │   ├── VisitorSession.php
│   │   ├── Visit.php
│   │   ├── DailyStat.php
│   │   ├── AdSlot.php
│   │   ├── Setting.php
│   │   └── SecurityLog.php
│   ├── Services/
│   │   ├── SafelinkService.php                  # Logika orkestrasi safelink & atomic redirect
│   │   ├── TokenService.php                     # Kriptografi 64 hex & Keyed HMAC-SHA256
│   │   ├── TurnstileService.php                 # Verifikasi API ke Cloudflare SiteVerify
│   │   ├── SlugGeneratorService.php             # Pembuatan slug acak unik 6-8 char
│   │   ├── AnalyticsService.php                 # Query agregasi analitik dashboard
│   │   ├── VisitRecorderService.php             # Insert async-friendly event visit
│   │   ├── SettingsService.php                  # Read/write key-value dengan file cache
│   │   └── SecurityLogService.php               # Pencatatan event insiden keamanan
│   └── Helpers/
│       └── helpers.php
├── bootstrap/
│   └── app.php                                  # Konfigurasi routing & middleware Laravel 13
├── config/
│   ├── app.php
│   ├── database.php
│   ├── services.php                             # Konfigurasi Turnstile keys
│   └── session.php
├── database/
│   ├── migrations/
│   │   ├── 2026_10_30_000001_create_admins_table.php
│   │   ├── 2026_10_30_000002_create_links_table.php
│   │   ├── 2026_10_30_000003_create_visitor_sessions_table.php
│   │   ├── 2026_10_30_000004_create_visits_table.php
│   │   ├── 2026_10_30_000005_create_daily_stats_table.php
│   │   ├── 2026_10_30_000006_create_ad_slots_table.php
│   │   ├── 2026_10_30_000007_create_settings_table.php
│   │   └── 2026_10_30_000008_create_security_logs_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminSeeder.php
│       └── SettingsSeeder.php
├── public/
│   ├── .htaccess                                # LiteSpeed/Apache rewrite & security rules
│   ├── index.php
│   ├── css/
│   │   ├── admin.css                            # CSS murni admin dashboard
│   │   └── safelink.css                         # CSS ultra-lightweight public safelink (< 10 KB)
│   └── js/
│       ├── admin.js                             # Vanilla JS admin interactions
│       └── safelink.js                          # Countdown & Turnstile callback logic
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php
│       │   └── public.blade.php
│       ├── components/
│       │   ├── admin/
│       │   │   ├── sidebar.blade.php
│       │   │   ├── header.blade.php
│       │   │   ├── stats-card.blade.php
│       │   │   └── alert.blade.php
│       │   └── public/
│       │       ├── ad-slot.blade.php            # Render slot iklan dinamis dengan label sponsor
│       │       ├── countdown-timer.blade.php
│       │       └── turnstile-widget.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── links/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   └── edit.blade.php
│       │   ├── ads/
│       │   │   ├── index.blade.php
│       │   │   └── edit.blade.php
│       │   ├── analytics/
│       │   │   └── index.blade.php
│       │   ├── settings/
│       │   │   └── index.blade.php
│       │   └── security/
│       │       └── logs.blade.php
│       ├── auth/
│       │   └── login.blade.php
│       ├── public/
│       │   ├── safelink.blade.php
│       │   ├── expired.blade.php
│       │   └── invalid.blade.php
│       └── errors/
│           ├── 403.blade.php
│           ├── 404.blade.php
│           ├── 410.blade.php
│           ├── 422.blade.php
│           ├── 429.blade.php
│           └── 500.blade.php
├── routes/
│   ├── web.php
│   └── console.php
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 3. Strategi Routing & Middleware

Dikonfigurasi pada `routes/web.php`:

```php
use App\Http\Controllers\Public\SafelinkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{
    DashboardController, LinkController, AdSlotController,
    AnalyticsController, SettingsController, SecurityLogController
};
use Illuminate\Support\Facades\Route;

// 1. Rute Publik Safelink Engine (Dilindungi Rate Limiter & Header Keamanan)
Route::middleware(['throttle:60,1', 'security.headers'])->group(function () {
    Route::get('/s/{slug}', [SafelinkController::class, 'show'])
        ->name('safelink.show')
        ->where('slug', '[a-z0-9-]+');

    Route::post('/s/{slug}/verify', [SafelinkController::class, 'verify'])
        ->name('safelink.verify')
        ->where('slug', '[a-z0-9-]+');

    Route::post('/s/{slug}/proceed', [SafelinkController::class, 'proceed'])
        ->name('safelink.proceed')
        ->where('slug', '[a-z0-9-]+');
});

// 2. Rute Autentikasi Admin
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 3. Rute Terproteksi Admin Dashboard
Route::prefix('admin')->name('admin.')->middleware(['auth', 'enforce.single.session'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('links', LinkController::class);
    Route::patch('links/{link}/toggle', [LinkController::class, 'toggleStatus'])->name('links.toggle');
    Route::resource('ads', AdSlotController::class)->except(['show']);
    Route::patch('ads/{adSlot}/toggle', [AdSlotController::class, 'toggleStatus'])->name('ads.toggle');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('security/logs', [SecurityLogController::class, 'index'])->name('security.logs');
});
```

---

## 4. Spesifikasi Kontrak Service Layer

### 4.1 `SafelinkService` (Atomic State Transition & Redirect)
```php
namespace App\Services;

use App\Models\Link;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InvalidSafelinkSessionException;

class SafelinkService
{
    public function __construct(
        protected TokenService $tokenService,
        protected TurnstileService $turnstileService
    ) {}

    public function initializeSession(Link $link, Request $request): VisitorSession
    {
        $token = $this->tokenService->generateSessionToken();
        $userAgentHash = hash('sha256', (string) $request->userAgent());

        return VisitorSession::create([
            'link_id' => $link->id,
            'token' => $token,
            'user_agent_hash' => $userAgentHash,
            'countdown_seconds' => $link->countdown_seconds,
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function finalizeRedirect(string $sessionToken, Request $request): string
    {
        $session = VisitorSession::where('token', $sessionToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session || !$session->turnstile_verified) {
            throw new InvalidSafelinkSessionException("Sesi tidak valid atau belum diverifikasi.");
        }

        // 1. Validasi Waktu Countdown di Sisi Server
        $elapsedSeconds = now()->diffInSeconds($session->countdown_started_at, true);
        if ($elapsedSeconds < $session->countdown_seconds) {
            throw new InvalidSafelinkSessionException("Hitung mundur belum selesai.");
        }

        // 2. Eksekusi Atomic State Transition (Anti Race Condition / Replay Attack)
        $affected = DB::table('visitor_sessions')
            ->where('id', $session->id)
            ->where('completed', false)
            ->update([
                'completed' => true,
                'completed_at' => now(),
            ]);

        if ($affected === 0) {
            throw new InvalidSafelinkSessionException("Token sudah pernah digunakan.");
        }

        // 3. Dekripsi dan Kembalikan Destination URL
        return $session->link->destination_url;
    }
}
```

### 4.2 `TokenService` (Keyed HMAC IP Pseudonymization)
```php
namespace App\Services;

class TokenService
{
    public function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 Karakter Heksadesimal
    }

    public function hashIpAddress(string $ip): string
    {
        $salt = (string) config('app.analytics_salt', config('app.key'));
        return hash_hmac('sha256', $ip, $salt);
    }
}
```

---

## 5. Model Casting & Enkripsi AES-256 (`App\Models\Link`)

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class Link extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug', 'destination_url', 'title', 'source_website',
        'status', 'countdown_seconds', 'expires_at',
        'total_visits', 'total_completions', 'meta_description'
    ];

    protected $casts = [
        'countdown_seconds' => 'integer',
        'total_visits'      => 'integer',
        'total_completions' => 'integer',
        'expires_at'        => 'datetime',
    ];

    /**
     * Enkripsi simetris otomatis untuk destination_url.
     */
    protected function destinationUrl(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }
}
```

