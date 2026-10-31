<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdSlotController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SecurityLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\SafelinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Informational & Landing Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:10,1');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/help', [PageController::class, 'help'])->name('help');

// Dynamic Technical SEO
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Public Blog & Editorial Content
|--------------------------------------------------------------------------
*/

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Safelink Engine (Rate Limited to 60 req/min)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/s/{slug}', [SafelinkController::class, 'show'])->name('safelink.show');
    Route::post('/s/{slug}/verify', [SafelinkController::class, 'verify'])->name('safelink.verify');
    Route::post('/s/{slug}/proceed', [SafelinkController::class, 'proceed'])->name('safelink.proceed');
    Route::post('/s/{slug}/direct-proceed', [SafelinkController::class, 'directProceed'])->name('safelink.direct_proceed');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Single Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Protected Panel Routes (EnforceSingleAdminSession)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin.auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile & Password Management (FR-AUTH-004)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Link Management
        Route::post('/links/{id}/restore', [LinkController::class, 'restore'])->name('links.restore');
        Route::delete('/links/{id}/force', [LinkController::class, 'forceDelete'])->name('links.force_delete');
        Route::resource('links', LinkController::class);

        // Content Management: Articles & Categories
        Route::post('/articles/{id}/restore', [ArticleController::class, 'restore'])->name('articles.restore');
        Route::delete('/articles/{id}/force', [ArticleController::class, 'forceDelete'])->name('articles.force_delete');
        Route::resource('articles', ArticleController::class);
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

        // Monetization & Ad Slots
        Route::resource('ads', AdSlotController::class)->only(['index', 'edit', 'update']);

        // Analytics & Reporting
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // System Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Security Incident Logs
        Route::get('/security-logs', [SecurityLogController::class, 'index'])->name('security.logs');
    });
