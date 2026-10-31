# Testing Strategy & Quality Assurance Plan — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-12 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Senior QA Engineer & Test Automation Specialist |
| **Target Pembaca** | AI Coding Agent, QA Tester, Software Engineer |
| **Tujuan** | Menetapkan rencana pengujian menyeluruh, skenario automated test, penanganan kasus negatif/edge cases, dan acceptance criteria untuk menjamin keandalan sistem sebelum rilis produksi. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Sumber kebutuhan fungsional (FR) dan aturan bisnis (BR) |
| [DOC-03 — Scope & Requirements Specification](../01-product/03-scope-requirements.md) | Matriks ketertelusuran kebutuhan ke test case |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Arsitektur kode dan kontrak routing untuk pengujian |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Kriteria pengujian keamanan dan pencegahan bypass |
| [AGENTS.md](../../AGENTS.md) | Kewajiban eksekusi test sebelum menandai pekerjaan selesai |

---

## 1. Piramida & Strategi Pengujian (Testing Strategy)

LokaTaut menerapkan pendekatan pengujian otomatis berbasis framework **PHPUnit / Pest** bawaan Laravel 11 yang terbagi dalam 3 tingkatan:

```
                  / \
                 /   \
                / E2E \       Manual UAT & Browser Testing (5%)
               /-------\
              / Feature \     Feature & Integration Tests (65%)
             /-----------\    (HTTP requests, DB, Session, Turnstile)
            /  Unit Tests \   Unit Tests (30%)
           /---------------\  (TokenService, Crypto, Regex, SlugGen)
```

1. **Unit Testing:** Menguji komponen terisolasi tanpa dependensi database (misal: algoritma generator slug, kalkulator timestamp countdown, hashing IP).
2. **Feature Testing:** Menguji alur HTTP menyeluruh (routing, middleware, form request validation, interaksi database Eloquent, dan respon Blade).
3. **Mocking External APIs:** Cloudflare Turnstile API di-mock pada lingkungan pengujian otomatis (`Http::fake()`) untuk memastikan test suite dapat dijalankan offline tanpa koneksi internet.

---

## 2. Test Case Registry (Fitur Kritis P0 & P1)

### 2.1 Modul Autentikasi Admin (`TEST-AUTH-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-AUTH-001** | Login admin dengan kredensial valid | POST `/login` dengan email & password terdaftar | Status HTTP 302 ke `/admin`, session autentikasi aktif, `last_login_at` terupdate | P0 |
| **TEST-AUTH-002** | Login admin dengan password salah | POST `/login` dengan password keliru | Status HTTP 302 kembali ke `/login`, pesan error 'Kredensial tidak cocok', sesi tamu | P0 |
| **TEST-AUTH-003** | Throttling brute-force login | Lakukan POST `/login` gagal 5x berturut-turut dalam 1 menit | Percobaan ke-6 menghasilkan HTTP 429 Too Many Requests | P0 |
| **TEST-AUTH-004** | Proteksi route admin dari tamu | GET `/admin` tanpa sesi login | Status HTTP 302 dialihkan ke `/login` | P0 |
| **TEST-AUTH-005** | Logout admin mengakhiri sesi | POST `/logout` dengan CSRF token valid | Sesi dihapus, session ID diregenerasi, redirect ke `/login` | P0 |

---

### 2.2 Modul Manajemen Link (`TEST-LINK-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-LINK-001** | Pembuatan link baru dengan random slug | Admin submit POST `/admin/links` dengan `destination_url` valid tanpa slug | Record tersimpan di database, slug acak 6-8 char terisi otomatis, URL terenkripsi di DB | P0 |
| **TEST-LINK-002** | Pembuatan link dengan custom slug unik | Admin submit custom slug `photoshop-2026` | Record tersimpan dengan slug `photoshop-2026` | P1 |
| **TEST-LINK-003** | Validasi penolakan custom slug duplikat | Admin submit slug yang sudah pernah terdaftar | Respon error validasi 422: 'Slug sudah digunakan' | P0 |
| **TEST-LINK-004** | Validasi skema URL tujuan berbahaya | Admin submit `destination_url` = `javascript:alert(1)` atau `file:///etc/passwd` | Validasi gagal, ditolak sistem (harus `http://` atau `https://`) | P0 |
| **TEST-LINK-005** | Toggle status link aktif / nonaktif | Admin kirim PATCH `/admin/links/{id}/toggle` | Status kolom berubah antara `active` dan `inactive` | P0 |
| **TEST-LINK-006** | Soft delete link | Admin kirim DELETE `/admin/links/{id}` | Kolom `deleted_at` terisi, data analitik historis tetap utuh | P1 |

---

### 2.3 Modul Safelink Engine & Alur Pengunjung (`TEST-SAFE-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-SAFE-001** | Akses shortlink valid oleh pengunjung | GET `/s/{slug}` dengan status link aktif | HTTP 200, sesi baru tersimpan di `visitor_sessions`, event `impression` tercatat di `visits`, tombol disabled | P0 |
| **TEST-SAFE-002** | Akses shortlink tidak terdaftar | GET `/s/tidak-ada-123` | HTTP 404 (Blade invalid.blade.php) | P0 |
| **TEST-SAFE-003** | Akses shortlink yang sudah kadaluwarsa | GET `/s/{slug}` di mana `expires_at` < NOW() | HTTP 410 / 200 (Blade expired.blade.php) | P0 |
| **TEST-SAFE-004** | Verifikasi Turnstile berhasil | POST `/s/{slug}/verify` dengan turnstile token valid (Mock HTTP 200) | JSON `{ success: true, countdown_seconds: 10 }`, DB `turnstile_verified = true` | P0 |
| **TEST-SAFE-005** | Eksekusi redirect sah setelah countdown selesai | POST `/s/{slug}/proceed` setelah waktu countdown berlalu >= 10 detik | HTTP 302 Found dengan header `Location: destination_url` terdekripsi, `completed = true` | P0 |
| **TEST-SAFE-006** | **(PENTING) Percobaan Bypass Countdown via JS** | POST `/s/{slug}/proceed` baru berselang 2 detik setelah verifikasi | **HTTP 422 Unprocessable Entity: Bypass Attempt Rejected**, insiden dicatat di `security_logs` | P0 |
| **TEST-SAFE-007** | **(PENTING) Percobaan Replay Token yang Telah Selesai** | POST `/s/{slug}/proceed` kedua kalinya menggunakan token yang sama | **HTTP 422: Token Already Consumed**, redirect ditolak | P0 |
| **TEST-SAFE-008** | Percobaan proceed tanpa verifikasi Turnstile | POST `/s/{slug}/proceed` saat `turnstile_verified = false` | **HTTP 422: Bot Verification Required** | P0 |

---

### 2.4 Modul Manajemen Iklan (`TEST-ADS-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-ADS-001** | Simpan script kode iklan baru | Admin submit script HTML/JS Adsterra ke slot `above_timer` | Data tersimpan utuh di DB tanpa terdistorsi sanitasi tag script | P0 |
| **TEST-ADS-002** | Toggle nonaktifkan slot iklan | Admin matikan slot `header` | Slot iklan tersebut tidak dirender sama sekali pada view halaman `/s/{slug}` | P1 |

---

### 2.5 Modul Scheduled Tasks & Pruning (`TEST-CRON-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-CRON-001** | Pembersihan sesi kadaluwarsa | Jalankan artisan `lokataut:prune-sessions` | Sesi di `visitor_sessions` yang berusia > 24 jam terhapus bersih dari database | P1 |
| **TEST-CRON-002** | Agregasi statistik harian | Jalankan artisan `lokataut:aggregate-stats` | Data event `visits` kemarin direkap ke tabel `daily_stats` dengan hitungan unik dan total yang akurat | P1 |

---

## 3. Contoh Implementasi Automated Feature Test (PHP / Pest)

Berikut adalah template test suite untuk validasi alur safelink anti-bypass yang wajib disertakan pada repository:

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Link;
use App\Models\VisitorSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class SafelinkFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_cannot_bypass_countdown_timer(): void
    {
        // 1. Arrange: Buat link aktif dengan countdown 10 detik
        $link = Link::create([
            'slug' => 'test-software',
            'destination_url' => 'https://example.com/download.zip',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        // Mock Cloudflare Turnstile API response
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        // 2. Act: Kunjungi halaman safelink
        $response = $this->get('/s/test-software');
        $response->assertStatus(200);

        $session = VisitorSession::where('link_id', $link->id)->first();
        $this->assertNotNull($session);

        // Verifikasi Turnstile
        $this->postJson('/s/test-software/verify', [
            'session_token' => $session->token,
            'turnstile_token' => 'dummy-valid-token',
        ])->assertJson(['success' => true]);

        // 3. Act Curang: Langsung tembak proceed 1 detik kemudian!
        $bypassResponse = $this->post('/s/test-software/proceed', [
            'session_token' => $session->token,
        ]);

        // 4. Assert: Server menolak dengan kode 422!
        $bypassResponse->assertStatus(422);
        $this->assertFalse((bool) $session->fresh()->completed);
    }
}
```

---

## 4. Kriteria Kelulusan Pengujian (Quality Gate)

Aplikasi LokaTaut dinyatakan siap rilis ke lingkungan produksi jika:
1. **Test Suite Coverage:** 100% test case berlabel `P0` dan minimal 90% test case berlabel `P1` berstatus **PASS** pada perintah `php artisan test`.
2. **Zero Critical Vulnerabilities:** Tidak ada kebocoran URL tujuan sebelum verifikasi dan tidak ada celah manipulasi parameter timer.
3. **Responsive Visual Integrity:** Tidak ada horizontal scrollbar atau UI overlap pada pengujian peramban Chrome, Safari, dan Firefox (tampilan Mobile 360px & Desktop 1366px).

