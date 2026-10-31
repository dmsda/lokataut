# Testing Strategy & Quality Assurance Plan — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-12 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Senior QA Engineer & Test Automation Specialist |
| **Target Pembaca** | AI Coding Agent, QA Tester, Software Engineer |
| **Tujuan** | Menetapkan rencana pengujian menyeluruh, skenario automated test berbasis Laravel 13 (PHPUnit 11 / Pest 3), penanganan kasus negatif/edge cases, dan acceptance criteria sebelum rilis produksi. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Sumber kebutuhan fungsional (FR) dan aturan bisnis (BR) |
| [DOC-03 — Scope & Requirements Specification](../01-product/03-scope-requirements.md) | Matriks keterlacakan kebutuhan ke test case |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Arsitektur kode dan kontrak routing untuk pengujian |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Kriteria pengujian keamanan dan pencegahan bypass |
| [AGENTS.md](../../AGENTS.md) | Kewajiban eksekusi test sebelum menandai pekerjaan selesai |

---

## 1. Test Registry (Skenario Uji Kritis P0 & P1)

### 1.1 Modul Autentikasi Admin (`TEST-AUTH-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-AUTH-001** | Login admin dengan kredensial valid | POST `/login` dengan email & password terdaftar | Status HTTP 302 ke `/admin`, session autentikasi aktif, `last_login_at` terupdate | P0 |
| **TEST-AUTH-002** | Login admin dengan password salah | POST `/login` dengan password keliru | Status HTTP 302 kembali ke `/login`, pesan error 'Kredensial tidak cocok', sesi tamu | P0 |
| **TEST-AUTH-003** | Throttling brute-force login | Lakukan POST `/login` gagal 5x berturut-turut dalam 1 menit | Percobaan ke-6 menghasilkan HTTP 429 Too Many Requests | P0 |
| **TEST-AUTH-004** | Proteksi route admin dari tamu | GET `/admin` tanpa sesi login | Status HTTP 302 dialihkan ke `/login` | P0 |
| **TEST-AUTH-005** | Logout admin mengakhiri sesi | POST `/logout` dengan CSRF token valid | Sesi dihapus, session ID diregenerasi, redirect ke `/login` | P0 |

---

### 1.2 Modul Manajemen Link (`TEST-LINK-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-LINK-001** | Pembuatan link baru dengan random slug | Admin submit POST `/admin/links` dengan `destination_url` valid tanpa slug | Record tersimpan di database, slug acak 6-8 char terisi otomatis, URL terenkripsi di DB | P0 |
| **TEST-LINK-002** | Pembuatan link dengan custom slug unik | Admin submit custom slug `photoshop-2026` | Record tersimpan dengan slug `photoshop-2026` | P1 |
| **TEST-LINK-003** | Validasi penolakan custom slug duplikat | Admin submit slug yang sudah pernah terdaftar | Respon error validasi 422: 'Slug sudah digunakan' | P0 |
| **TEST-LINK-004** | Validasi skema URL tujuan berbahaya | Admin submit `destination_url` = `javascript:alert(1)` atau `file:///etc/passwd` | Validasi gagal, ditolak sistem (harus `http://` atau `https://`) | P0 |
| **TEST-LINK-005** | Toggle status link aktif / nonaktif | Admin kirim PATCH `/admin/links/{id}/toggle` | Status kolom berubah antara `active` dan `inactive` | P0 |
| **TEST-LINK-006** | Soft delete link | Admin kirim DELETE `/admin/links/{id}` | Kolom `deleted_at` terisi, data analitik historis tetap utuh | P1 |

---

### 1.3 Modul Safelink Engine & Alur Pengunjung (`TEST-SAFE-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-SAFE-001** | Akses shortlink valid oleh pengunjung | GET `/s/{slug}` dengan status link aktif | HTTP 200, sesi baru tersimpan di `visitor_sessions`, event `impression` tercatat di `visits`, tombol disabled | P0 |
| **TEST-SAFE-002** | Akses shortlink tidak terdaftar | GET `/s/tidak-ada-123` | HTTP 404 (Blade invalid.blade.php) | P0 |
| **TEST-SAFE-003** | Akses shortlink yang sudah kadaluwarsa | GET `/s/{slug}` di mana `expires_at` < NOW() | HTTP 410 / 200 (Blade expired.blade.php) | P0 |
| **TEST-SAFE-004** | Verifikasi Turnstile berhasil | POST `/s/{slug}/verify` dengan turnstile token valid (Mock HTTP 200) | JSON `{ success: true, countdown_seconds: 10 }`, DB `turnstile_verified = true` | P0 |
| **TEST-SAFE-005** | Eksekusi redirect sah setelah countdown selesai | POST `/s/{slug}/proceed` setelah waktu countdown berlalu >= 10 detik | HTTP 302 Found dengan header `Location: destination_url` terdekripsi, `completed = true` | P0 |
| **TEST-SAFE-006** | **(KRITIS) Percobaan Bypass Countdown via JS** | POST `/s/{slug}/proceed` baru berselang 2 detik setelah verifikasi | **HTTP 422 Unprocessable Entity: Bypass Attempt Rejected**, insiden dicatat di `security_logs` | P0 |
| **TEST-SAFE-007** | **(KRITIS) Percobaan Replay Token yang Telah Selesai** | POST `/s/{slug}/proceed` kedua kalinya menggunakan token yang sama | **HTTP 422: Token Already Consumed**, redirect ditolak | P0 |
| **TEST-SAFE-008** | Percobaan proceed tanpa verifikasi Turnstile | POST `/s/{slug}/proceed` saat `turnstile_verified = false` | **HTTP 422: Bot Verification Required** | P0 |

---

### 1.4 Modul Manajemen Iklan & Keamanan XSS (`TEST-ADS-*` & `TEST-SEC-*`)

| Test ID | Skenario Pengujian | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|:---:|
| **TEST-ADS-001** | Simpan script kode iklan baru | Admin submit script HTML/JS Adsterra ke slot `above_timer` | Data tersimpan utuh di DB, audit log modifikasi tercatat di `security_logs` | P0 |
| **TEST-ADS-002** | Toggle nonaktifkan slot iklan | Admin matikan slot `header` | Slot iklan tersebut tidak dirender sama sekali pada view halaman `/s/{slug}` | P1 |
| **TEST-SEC-001** | Pencegahan Stored XSS via Unauthorized User | User unauthenticated mencoba kirim PUT `/admin/ads/{id}` dengan script berbahaya | Ditolak seketika oleh middleware auth (HTTP 302 ke login) | P0 |

---

## 2. Contoh Implementasi Automated Feature Test (Laravel 13 / Pest 3)

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
            'slug' => 'photoshop-2026',
            'destination_url' => 'https://example.com/photoshop.zip',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        // Mock Cloudflare Turnstile API response
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        // 2. Act: Kunjungi halaman safelink
        $response = $this->get('/s/photoshop-2026');
        $response->assertStatus(200);

        $session = VisitorSession::where('link_id', $link->id)->first();
        $this->assertNotNull($session);

        // Verifikasi Turnstile via AJAX
        $this->postJson('/s/photoshop-2026/verify', [
            'session_token' => $session->token,
            'turnstile_token' => 'dummy-valid-turnstile-token',
        ])->assertJson(['success' => true]);

        // 3. Act Curang: Langsung tembak proceed 1 detik kemudian!
        $bypassResponse = $this->post('/s/photoshop-2026/proceed', [
            'session_token' => $session->token,
        ]);

        // 4. Assert: Server menolak dengan kode 422!
        $bypassResponse->assertStatus(422);
        $this->assertFalse((bool) $session->fresh()->completed);
    }
}
```

---

## 3. Kriteria Kelulusan Pengujian (Quality Gate)

1. **Test Coverage:** 100% test case berlabel `P0` dan minimal 90% test case berlabel `P1` wajib berstatus **PASS** pada perintah `php artisan test`.
2. **Zero Security Vulnerabilities:** Tidak ada celah manipulasi parameter timer dan tidak ada kebocoran URL tujuan sebelum verifikasi selesai.

