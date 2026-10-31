# Technical Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-07 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Senior Laravel Architect & Technical Documentation Specialist |
| **Target Pembaca** | AI Coding Agent, Software Engineer |
| **Tujuan** | Menjadi pedoman teknis operasional (*Implementation Blueprint*) yang menetapkan standar implementasi kode, struktur folder, kontrak service, routing, validasi, dan arsitektur controller. |

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
│ Framework Web        │ Laravel 11.x (Modular Monolith)      │
│ Database Engine      │ MySQL 8.0+ (InnoDB Engine)           │
│ Template Engine      │ Laravel Blade (Native Components)    │
│ Frontend CSS         │ Native CSS3 (CSS Variables, Flex/Grid)│
│ Frontend JavaScript  │ Vanilla JS ES6+ (No jQuery/React/Vue)│
│ Package Manager      │ Composer 2.x (Local home directory)  │
│ Web Server Target    │ LiteSpeed / Nginx via cPanel         │
│ CDN & Edge WAF       │ Cloudflare (Free Tier)               │
│ Bot Mitigation       │ Cloudflare Turnstile (Managed API)   │
│ Ad Network Primer    │ Adsterra (HTML/JS Asynchronous Tags) │
└──────────────────────┴──────────────────────────────────────┘
```

---

## 2. Struktur Direktori Lengkap (Application Folder Structure)

```
lokataut/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── PruneExpiredSessionsCommand.php  # Pembersihan sesi > 24 jam
│   │       ├── AggregateDailyStatsCommand.php   # Agregasi data harian ke daily_stats
│   │       └── PruneOldVisitDataCommand.php     # Pembersihan log visits > 90 hari
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
│   │   │   └── SecurityHeadersMiddleware.php
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
│   │   ├── SafelinkService.php                  # Logika orkestrasi safelink
│   │   ├── TokenService.php                     # Kriptografi generate & verify token
│   │   ├── TurnstileService.php                 # Verifikasi API ke Cloudflare
│   │   ├── SlugGeneratorService.php             # Pembuatan slug acak unik
│   │   ├── AnalyticsService.php                 # Query agregasi analitik
│   │   ├── VisitRecorderService.php             # Insert async-friendly event visit
│   │   ├── SettingsService.php                  # Read/write key-value dengan file cache
│   │   └── SecurityLogService.php               # Pencatatan event insiden keamanan
│   └── Helpers/
│       └── helpers.php                          # Helper format angka, sanitasi
├── bootstrap/
│   └── app.php                                  # Konfigurasi routing & middleware Laravel 11
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
│   │   ├── admin.css                            # CSS murni untuk admin dashboard
│   │   └── safelink.css                         # CSS ultra-lightweight untuk public safelink
│   └── js/
│       ├── admin.js                             # Vanilla JS admin interactions
│       └── safelink.js                          # Countdown & Turnstile callback logic
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── admin.blade.php                  # Master layout dashboard
│       │   └── public.blade.php                 # Master layout halaman publik safelink
│       ├── components/
│       │   ├── admin/
│       │   │   ├── sidebar.blade.php
│       │   │   ├── header.blade.php
│       │   │   ├── stats-card.blade.php
│       │   │   └── alert.blade.php
│       │   └── public/
│       │       ├── ad-slot.blade.php            # Render slot iklan dinamis
│       │       ├── countdown-timer.blade.php
│       │       └── turnstile-widget.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── links/
│       │   │   ├── index.blade.php
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   └── show.blade.php
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
│       │   ├── safelink.blade.php               # Halaman utama transisi safelink
│       │   ├── expired.blade.php                # Informasi link kedaluwarsa
│       │   └── invalid.blade.php                # Informasi 404 link tidak ditemukan
│       └── errors/
│           ├── 403.blade.php
│           ├── 404.blade.php
│           ├── 422.blade.php
│           ├── 429.blade.php
│           └── 500.blade.php
├── routes/
│   ├── web.php                                  # Seluruh rute publik dan admin
│   └── console.php                              # Scheduler definition
├── tests/
│   ├── Feature/
│   │   ├── AdminAuthTest.php
│   │   ├── LinkManagementTest.php
│   │   ├── SafelinkFlowTest.php
│   │   └── AdSlotManagementTest.php
│   └── Unit/
│       ├── TokenServiceTest.php
│       └── SlugGeneratorTest.php
├── .env.example
├── composer.json
└── AGENTS.md
```

---

## 3. Strategi Routing & Konvensi Endpoint

Routing dikonfigurasi pada `routes/web.php` dengan pembagian tiga grup yang jelas:

### 3.1 Public Guest Routes (Safelink Engine)
Endpoint yang diakses pengunjung tanpa autentikasi, dilindungi rate limiting ketat:

```php
use App\Http\Controllers\Public\SafelinkController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])->group(function () {
    // 1. Tampilkan Halaman Safelink
    Route::get('/s/{slug}', [SafelinkController::class, 'show'])
        ->name('safelink.show')
        ->where('slug', '[a-z0-9-]+');

    // 2. Verifikasi Token Cloudflare Turnstile via AJAX
    Route::post('/s/{slug}/verify', [SafelinkController::class, 'verify'])
        ->name('safelink.verify')
        ->where('slug', '[a-z0-9-]+');

    // 3. Eksekusi Redirect Final setelah Countdown Selesai
    Route::post('/s/{slug}/proceed', [SafelinkController::class, 'proceed'])
        ->name('safelink.proceed')
        ->where('slug', '[a-z0-9-]+');
});
```

### 3.2 Authentication Routes
```php
use App\Http\Controllers\Auth\LoginController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
```

### 3.3 Protected Admin Dashboard Routes
```php
use App\Http\Controllers\Admin\{
    DashboardController, LinkController, AdSlotController,
    AnalyticsController, SettingsController, SecurityLogController
};

Route::prefix('admin')->name('admin.')->middleware(['auth', 'enforce.single.session'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Link Management Resource
    Route::resource('links', LinkController::class);
    Route::patch('links/{link}/toggle', [LinkController::class, 'toggleStatus'])->name('links.toggle');

    // Ad Slot Management
    Route::resource('ads', AdSlotController::class)->except(['show']);
    Route::patch('ads/{adSlot}/toggle', [AdSlotController::class, 'toggleStatus'])->name('ads.toggle');

    // Analytics & Reports
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/links/{link}', [AnalyticsController::class, 'linkDetail'])->name('analytics.link');

    // System Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Security Audit Logs
    Route::get('security/logs', [SecurityLogController::class, 'index'])->name('security.logs');
});
```

---

## 4. Spesifikasi Kontrak Service Layer

Sesuai aturan arsitektur, Controller tidak boleh menjalankan kueri SQL atau manipulasi kriptografis secara langsung. Seluruh logika didelegasikan ke Service Layer:

### 4.1 `SafelinkService` Interface
```php
namespace App\Services;

use App\Models\Link;
use App\Models\VisitorSession;
use Illuminate\Http\Request;

class SafelinkService
{
    /**
     * Memvalidasi keabsahan link dan menginisialisasi sesi pengunjung baru.
     */
    public function initializeSession(Link $link, Request $request): VisitorSession;

    /**
     * Memverifikasi token Cloudflare Turnstile dan menandai dimulainya timer.
     */
    public function processTurnstileVerification(string $sessionToken, string $turnstileResponse, Request $request): bool;

    /**
     * Memvalidasi kriteria redirect server-side (waktu countdown & turnstile)
     * dan mengembalikan destination URL terdekripsi.
     *
     * @throws \App\Exceptions\InvalidSafelinkSessionException
     */
    public function finalizeRedirect(string $sessionToken, Request $request): string;
}
```

### 4.2 `TokenService`
```php
namespace App\Services;

class TokenService
{
    /**
     * Menghasilkan string acak kriptografis 64 karakter heksadesimal.
     */
    public function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Membuat hash SHA-256 dari alamat IP untuk privasi.
     */
    public function hashIpAddress(string $ip): string
    {
        return hash('sha256', $ip . config('app.key'));
    }
}
```

### 4.3 `TurnstileService`
```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    public function verify(string $token, ?string $remoteIp = null): bool
    {
        if (!config('services.turnstile.enabled', true)) {
            return true; // Bypass untuk testing lokal
        }

        $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret_key'),
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        return (bool) $response->json('success', false);
    }
}
```

---

## 5. Form Requests & Aturan Validasi Input

### 5.1 `StoreLinkRequest`
```php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'destination_url'   => ['required', 'url', 'max:2048', 'starts_with:http://,https://'],
            'title'             => ['nullable', 'string', 'max:255'],
            'custom_slug'       => ['nullable', 'string', 'min:4', 'max:50', 'regex:/^[a-z0-9-]+$/', 'unique:links,slug'],
            'source_website'    => ['nullable', 'string', 'max:255'],
            'countdown_seconds' => ['required', 'integer', 'min:5', 'max:120'],
            'expires_at'        => ['nullable', 'date', 'after:now'],
            'status'            => ['required', 'in:active,inactive'],
        ];
    }
}
```

### 5.2 `SafelinkProceedRequest`
```php
namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class SafelinkProceedRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'session_token' => ['required', 'string', 'size:64'],
        ];
    }
}
```

---

## 6. Model Relationships, Casts & Scopes

### 6.1 `App\Models\Link`
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            get: fn (string $val) => Crypt::decryptString($val),
            set: fn (string $val) => Crypt::encryptString($val),
        );
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function visitorSessions(): HasMany
    {
        return $this->hasMany(VisitorSession::class);
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(DailyStat::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
```

---

## 7. Frontend Standards & Asset Delivery

### 7.1 Native CSS Architecture
- File CSS diletakkan langsung di `public/css/safelink.css` dan di-include via tag `<link>` standar tanpa runtime loader.
- Menggunakan CSS Custom Properties untuk kemudahan styling:
```css
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --bg-page: #f8fafc;
    --card-bg: #ffffff;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --font-stack: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
```

### 7.2 Vanilla JavaScript Lifecycle di Halaman Safelink
1. Halaman memuat file `public/js/safelink.js`.
2. Widget Turnstile memicu callback JavaScript saat challenge lolos:
```javascript
window.onTurnstileSuccess = function(turnstileToken) {
    const sessionToken = document.querySelector('input[name="session_token"]').value;
    const slug = window.LokaTautConfig.slug;

    fetch(`/s/${slug}/verify`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ session_token: sessionToken, turnstile_token: turnstileToken })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('turnstile-container').style.display = 'none';
            document.getElementById('timer-container').style.display = 'block';
            startVisualCountdown(data.countdown_seconds);
        }
    });
};
```
3. Fungsi `startVisualCountdown(seconds)` menjalankan interval 1 detik. Saat 0 detik tercapai, tombol "Lanjutkan Unduhan" diaktifkan dan form melakukan submit ke `/s/{slug}/proceed`.

---

## 8. Standar Koding & Pedoman Khusus AI Coding Agent

1. **Strict Types:** Seluruh file PHP wajib diawali `declare(strict_types=1);`.
2. **No Raw Secrets:** Kunci API Turnstile wajib dibaca via `config('services.turnstile.site_key')`, bukan `env(...)` di dalam class aplikasi.
3. **Prepared Statements:** Dilarang menggunakan raw SQL `DB::raw()` dengan penggabungan string variabel yang tidak disanitasi.
4. **Thin Controllers:** Controller hanya bertugas menerima request, memanggil service, dan merender view. Batasi panjang method controller maksimal 25 baris.
5. **No Production Build Dependencies:** Jangan menambahkan langkah `npm run build` yang wajib dijalankan di server shared hosting produksi. Seluruh asset CSS dan JS harus dapat langsung disajikan dari folder `public/`.

