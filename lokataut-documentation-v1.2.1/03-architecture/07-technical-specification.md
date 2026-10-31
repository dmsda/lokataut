# Technical Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-07 |
| **Versi** | 1.2.1 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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
│   │       ├── AggregateDailyStatsCommand.php   # Agregasi data harian ke daily_stats (Daily 00:05, withTrashed)
│   │       ├── PruneCacheCommand.php            # Pembersihan cache kedaluwarsa storage/framework/cache (Daily 03:00)
│   │       └── PruneOldVisitDataCommand.php     # Pembersihan log visits > 90 hari (Weekly)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── LinkController.php
│   │   │   │   ├── AdSlotController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── ProfileController.php        # Profil & Pergantian Kata Sandi Admin (FR-AUTH-004)
│   │   │   │   └── SecurityLogController.php
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   └── Public/
│   │   │       └── SafelinkController.php       # Core safelink interaction & anti-cache headers
│   │   ├── Middleware/
│   │   │   ├── EnforceSingleAdminSession.php
│   │   │   ├── ValidateVisitorToken.php
│   │   │   └── SecurityHeadersMiddleware.php    # X-Robots-Tag, CSP, X-Frame-Options, X-LiteSpeed-Cache-Control
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreLinkRequest.php
│   │       │   ├── UpdateLinkRequest.php
│   │       │   ├── StoreAdSlotRequest.php
│   │       │   ├── UpdateAdSlotRequest.php
│   │       │   ├── UpdateSettingsRequest.php
│   │       │   └── UpdatePasswordRequest.php    # Validasi current_password & new_password complex
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
│   │   ├── SafelinkService.php                  # Logika orkestrasi safelink, hashed token & atomic redirect
│   │   ├── TokenService.php                     # Kriptografi 64 hex, token_hash SHA-256 & ANALYTICS_SALT HMAC
│   │   ├── TurnstileService.php                 # Verifikasi API ke Cloudflare SiteVerify & Emergency Mode
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
│   ├── services.php                             # Konfigurasi Turnstile keys & Analytics Salt
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
│   ├── .htaccess                                # LiteSpeed/Apache rewrite & anti-cache rules
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
│       │   ├── profile.blade.php                # Form Profil & Ganti Password Admin (FR-AUTH-004)
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
    AnalyticsController, SettingsController, ProfileController, SecurityLogController
};
use Illuminate\Support\Facades\Route;

// 1. Rute Publik Safelink Engine (Dilindungi Rate Limiter & Header Anti-Cache LiteSpeed)
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

// 3. Rute Terproteksi Admin Dashboard & Profil
Route::prefix('admin')->name('admin.')->middleware(['auth', 'enforce.single.session'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('links', LinkController::class);
    Route::patch('links/{link}/toggle', [LinkController::class, 'toggleStatus'])->name('links.toggle');
    Route::resource('ads', AdSlotController::class)->except(['show']);
    Route::patch('ads/{adSlot}/toggle', [AdSlotController::class, 'toggleStatus'])->name('ads.toggle');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Profil & Pergantian Password Admin (FR-AUTH-004)
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    Route::get('security/logs', [SecurityLogController::class, 'index'])->name('security.logs');
});
```

---

## 4. Spesifikasi Kontrak Service Layer

### 4.1 `SafelinkService` (Hashed Token, Atomic State Transition & Redirect)
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

    /**
     * Inisialisasi sesi safelink: rawToken dikembalikan untuk client,
     * token_hash (SHA-256) disimpan di basis data.
     */
    public function initializeSession(Link $link, Request $request): array
    {
        $rawToken = $this->tokenService->generateSessionToken();
        $tokenHash = hash('sha256', $rawToken);
        $userAgentHash = hash('sha256', (string) $request->userAgent());

        $session = VisitorSession::create([
            'link_id'           => $link->id,
            'token_hash'        => $tokenHash,
            'user_agent_hash'   => $userAgentHash,
            'countdown_seconds' => $link->countdown_seconds,
            'expires_at'        => now()->addMinutes(30),
        ]);

        return [
            'session'  => $session,
            'rawToken' => $rawToken,
        ];
    }

    public function finalizeRedirect(string $rawToken, Request $request): string
    {
        $tokenHash = hash('sha256', $rawToken);

        $session = VisitorSession::where('token_hash', $tokenHash)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session || (!$session->turnstile_verified && !config('services.turnstile.emergency_bypass', false))) {
            throw new InvalidSafelinkSessionException("Sesi tidak valid atau belum diverifikasi.");
        }

        // 1. Validasi Waktu Countdown di Sisi Server
        $elapsedSeconds = now()->diffInSeconds($session->countdown_started_at, true);
        if ($elapsedSeconds < $session->countdown_seconds) {
            throw new InvalidSafelinkSessionException("Hitung mundur belum selesai.");
        }

        // 2. Eksekusi Atomic State Transition via token_hash (Anti Race Condition / Replay Attack)
        $affected = DB::table('visitor_sessions')
            ->where('id', $session->id)
            ->where('completed', false)
            ->update([
                'completed'    => true,
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

### 4.2 `TokenService` (Keyed HMAC IP Pseudonymization & ANALYTICS_SALT)
```php
namespace App\Services;

class TokenService
{
    public function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32)); // 64 Karakter Heksadesimal (256-bit entropy)
    }

    public function hashIpAddress(string $ip): string
    {
        // Menggunakan salt analitik terisolasi tanpa fallback ke APP_KEY (NIST SP 800-57)
        $salt = (string) config('services.analytics.salt');
        
        if (empty($salt)) {
            if (app()->environment('production')) {
                throw new \RuntimeException('Variabel lingkungan ANALYTICS_SALT wajib dikonfigurasi di lingkungan production.');
            }
            $salt = 'testing-analytics-salt-lokataut-do-not-use-in-production';
        }
        
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

---

## 6. Scheduled Commands & Maintenance Logic

### 6.1 `AggregateDailyStatsCommand.php`
Scheduled command harian berjalan pukul 00:05 WIB (`Asia/Jakarta`) dengan proteksi `withoutOverlapping(60)`. Kueri wajib idempoten, menyertakan `Link::withTrashed()`, menangani kunjungan unassigned (`link_id IS NULL`), merekam snapshot `link_slug`, dan mencatat kegagalan ke `security_logs`:
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Link;
use App\Models\Visit;
use App\Models\DailyStat;
use App\Models\SecurityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AggregateDailyStatsCommand extends Command
{
    protected $signature = 'lokataut:aggregate-daily {--date= : Tanggal rekap YYYY-MM-DD (Default: Kemarin)}';
    protected $description = 'Agregasi metrik analitik harian dari tabel visits ke tabel daily_stats';

    public function handle(): int
    {
        $targetDate = $this->option('date') ?? Carbon::yesterday('Asia/Jakarta')->toDateString();
        $this->info("Memulai agregasi analitik harian untuk tanggal: {$targetDate}");

        try {
            // Ambil seluruh link_id unik yang memiliki kunjungan pada tanggal target (termasuk soft-deleted dan null)
            $linkGroups = Visit::whereDate('created_at', $targetDate)
                ->select('link_id')
                ->distinct()
                ->get();

            foreach ($linkGroups as $group) {
                $linkId = $group->link_id;
                $link = $linkId ? Link::withTrashed()->find($linkId) : null;
                $linkSlug = $link ? $link->slug : '[purged-link]';

                $visitsQuery = Visit::whereDate('created_at', $targetDate)
                    ->where('link_id', $linkId);

                $impressions = (clone $visitsQuery)->where('event_type', 'impression')->count();
                $verified = (clone $visitsQuery)->where('event_type', 'verified')->count();
                $completions = (clone $visitsQuery)->where('event_type', 'completed')->count();
                $uniqueVisitors = (clone $visitsQuery)->distinct('ip_hash')->count('ip_hash');
                $desktopCount = (clone $visitsQuery)->where('device_type', 'desktop')->count();
                $mobileCount = (clone $visitsQuery)->where('device_type', 'mobile')->count();
                $tabletCount = (clone $visitsQuery)->where('device_type', 'tablet')->count();
                $botCount = (clone $visitsQuery)->where('device_type', 'bot')->count();

                $topCountries = (clone $visitsQuery)
                    ->whereNotNull('country_code')
                    ->select('country_code', DB::raw('count(*) as total'))
                    ->groupBy('country_code')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->pluck('total', 'country_code')
                    ->toArray();

                // Idempotent Upsert via updateOrCreate
                DailyStat::updateOrCreate(
                    [
                        'link_id' => $linkId,
                        'date'    => $targetDate,
                    ],
                    [
                        'link_slug'       => $linkSlug,
                        'impressions'     => $impressions,
                        'verified'        => $verified,
                        'completions'     => $completions,
                        'unique_visitors' => $uniqueVisitors,
                        'desktop_count'   => $desktopCount,
                        'mobile_count'    => $mobileCount,
                        'tablet_count'    => $tabletCount,
                        'bot_count'       => $botCount,
                        'top_countries'   => $topCountries,
                    ]
                );
            }

            $this->info("Agregasi analitik untuk {$targetDate} sukses.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error("Agregasi analitik gagal untuk tanggal {$targetDate}: " . $e->getMessage(), ['exception' => $e]);
            SecurityLog::create([
                'event_type' => 'daily_aggregation_failed',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Console/Scheduler',
                'details'    => json_encode(['date' => $targetDate, 'error' => $e->getMessage()]),
            ]);
            $this->error("Agregasi gagal: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

### 6.2 `PruneExpiredSessionsCommand.php`
Scheduled command berjalan setiap jam pada menit ke-15 untuk membersihkan sesi kedaluwarsa:
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitorSession;
use Carbon\Carbon;

class PruneExpiredSessionsCommand extends Command
{
    protected $signature = 'lokataut:prune-sessions';
    protected $description = 'Menghapus visitor_sessions yang telah kedaluwarsa lebih dari 24 jam';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subHours(24);
        $deleted = VisitorSession::where('expires_at', '<', $cutoff)->delete();
        $this->info("Pruned {$deleted} expired sessions.");
        return self::SUCCESS;
    }
}
```

### 6.3 `PruneOldVisitDataCommand.php`
Scheduled command berjalan harian pukul 02:00 WIB untuk membersihkan data mentah kunjungan yang melebihi batas retensi 90 hari (DEC-006):
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;
use Carbon\Carbon;

class PruneOldVisitDataCommand extends Command
{
    protected $signature = 'lokataut:prune-visits';
    protected $description = 'Menghapus rekaman tabel visits yang berumur lebih dari 90 hari';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(90);
        $deleted = Visit::where('created_at', '<', $cutoff)->delete();
        $this->info("Pruned {$deleted} old visit records.");
        return self::SUCCESS;
    }
}
```

### 6.4 `PruneCacheCommand.php`
Scheduled command berjalan mingguan (Minggu 03:00 WIB) untuk mencegah kejenuhan kuota Inode hosting cPanel:
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneCacheCommand extends Command
{
    protected $signature = 'lokataut:prune-cache';
    protected $description = 'Menghapus berkas cache usang di storage/framework/cache/data';

    public function handle(): int
    {
        $cachePath = storage_path('framework/cache/data');
        if (is_dir($cachePath)) {
            // Hapus berkas cache yang tidak dimodifikasi lebih dari 7 hari
            exec("find {$cachePath} -type f -mtime +7 -delete");
        }
        $this->info("File cache usang berhasil dibersihkan.");
        return self::SUCCESS;
    }
}
```

