# Testing Strategy & Quality Assurance Plan — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-12 |
| **Versi** | 1.2 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Kriteria pengujian keamanan, hashed token, dan Turnstile |
| [REQUIREMENT-TRACEABILITY-MATRIX.md](../REQUIREMENT-TRACEABILITY-MATRIX.md) | Matriks keterlacakan 100% dua arah tanpa orphan test |
| [AGENTS.md](../../AGENTS.md) | Kewajiban eksekusi test riil sebelum menandai pekerjaan selesai |

---

## 1. Test Registry Menyeluruh (Skenario Uji Kritis P0 & P1)

### 1.1 Modul Autentikasi & Profil Admin (`TEST-AUTH-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-AUTH-001** | Login admin dengan kredensial valid | Database memiliki akun admin seeder | POST `/login` dengan email & password terdaftar | HTTP 302 ke `/admin`, session autentikasi aktif, `last_login_at` terupdate | P0 |
| **TEST-AUTH-002** | Login admin dengan password salah | Database memiliki akun admin | POST `/login` dengan password keliru | HTTP 302 kembali ke `/login`, pesan error 'Kredensial tidak cocok', sesi tamu | P0 |
| **TEST-AUTH-003** | Throttling brute-force login | Route login aktif | Lakukan POST `/login` gagal 5x berturut-turut dalam 1 menit | Percobaan ke-6 menghasilkan HTTP 429 Too Many Requests | P0 |
| **TEST-AUTH-004** | Single session enforcement | Admin login di Perangkat A | Admin login di Perangkat B dengan akun sama | Sesi di Perangkat A otomatis invalidated saat melakukan request berikutnya | P0 |
| **TEST-AUTH-005** | Logout admin mengakhiri sesi | Admin dalam kondisi login | POST `/logout` dengan CSRF token valid | Sesi dihapus, session ID diregenerasi, redirect ke `/login` | P0 |
| **TEST-AUTH-006** | Admin Password Change sukses | Admin login, akses `/admin/profile` | PUT `/admin/profile/password` dengan `current_password` valid dan password baru kompleks (12+ char) | HTTP 302 kembali dengan pesan sukses, password ter-hash baru di DB, sesi login diregenerasi | P1 |
| **TEST-AUTH-007** | Admin Password Change gagal (password lama salah) | Admin login | PUT `/admin/profile/password` dengan `current_password` salah | HTTP 302 kembali dengan error validasi, password di DB tidak berubah, insiden dicatat di `security_logs` | P1 |

---

### 1.2 Modul Dashboard Admin (`TEST-DASH-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-DASH-001** | Agregasi kartu metrik ringkasan dashboard | Database memuat tautan dan data `daily_stats` | GET `/admin` sebagai admin terautentikasi | HTTP 200, view menampilkan Total Link, Total Tayangan Hari Ini, Unduhan Selesai, dan Completion Rate % | P0 |
| **TEST-DASH-002** | Tampilan tren kunjungan 7 hari & tautan terbaru | Database memuat data rekap 7 hari | GET `/admin` | HTTP 200, grafik 7 hari terisi data numerik valid, tabel 5 link terbaru ter-render rapi | P1 |

---

### 1.3 Modul Manajemen Link (`TEST-LINK-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-LINK-001** | Pembuatan link baru dengan random slug | Admin terautentikasi | Submit POST `/admin/links` dengan `destination_url` valid tanpa custom slug | Record tersimpan di DB, slug acak 6-8 char alfanumerik unik, `destination_url` terenkripsi AES-256 | P0 |
| **TEST-LINK-002** | Pembuatan link dengan custom slug unik | Admin terautentikasi | Submit custom slug `photoshop-2026` | Record tersimpan dengan slug `photoshop-2026` | P1 |
| **TEST-LINK-003** | Validasi penolakan custom slug duplikat | Slug `tools-2026` sudah terdaftar | Submit slug `tools-2026` kembali | Respon error validasi 422: 'Slug sudah digunakan' | P0 |
| **TEST-LINK-004** | Validasi skema URL tujuan berbahaya | Admin terautentikasi | Submit `destination_url` = `javascript:alert(1)` atau `file:///etc/passwd` | Validasi gagal 422, ditolak sistem (wajib protokol `http://` atau `https://`) | P0 |
| **TEST-LINK-005** | Toggle status link aktif / nonaktif | Link berstatus active | Kirim PATCH `/admin/links/{id}/toggle` | Status kolom pada DB berubah menjadi `inactive`; akses publik merespon HTTP 404 | P0 |
| **TEST-LINK-006** | Soft delete link mempertahankan relasi | Link memiliki 50 visits | Kirim DELETE `/admin/links/{id}` | Kolom `deleted_at` terisi, data pada tabel `visits` dan `daily_stats` tetap utuh | P1 |

---

### 1.4 Modul Safelink Engine & Alur Pengunjung (`TEST-SAFE-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-SAFE-001** | Akses shortlink valid oleh pengunjung | Link aktif tersedia | GET `/s/{slug}` | HTTP 200, session dibuat dengan `token_hash = sha256($rawToken)` di DB, raw token di view, event `impression` dicatat, header `X-LiteSpeed-Cache-Control: no-cache` terpasang | P0 |
| **TEST-SAFE-002** | Akses shortlink tidak terdaftar | Slug tidak ada di DB | GET `/s/tidak-ada-123` | HTTP 404 (Render invalid.blade.php) | P0 |
| **TEST-SAFE-003** | Akses shortlink kadaluwarsa | Link memiliki `expires_at` < NOW() | GET `/s/{slug}` | HTTP 410 (Render expired.blade.php) | P0 |
| **TEST-SAFE-004** | Verifikasi Turnstile berhasil | Sesi safelink aktif | POST `/s/{slug}/verify` dengan turnstile token valid (Mock HTTP 200) | JSON `{ success: true, countdown_seconds: 10 }`, DB `turnstile_verified = true`, `countdown_started_at = NOW()` | P0 |
| **TEST-SAFE-005** | Eksekusi redirect sah setelah countdown selesai | Sesi verified, waktu berlalu >= 10 detik | POST `/s/{slug}/proceed` dengan `$rawToken` | HTTP 302 Found dengan header `Location: destination_url` terdekripsi, `completed = true` | P0 |
| **TEST-SAFE-006** | **(KRITIS) Tolak Bypass Countdown via JS** | Sesi verified baru 2 detik | POST `/s/{slug}/proceed` | **HTTP 422 Unprocessable Entity: Bypass Rejected**, insiden dicatat di `security_logs` | P0 |
| **TEST-SAFE-007** | **(KRITIS) Tolak Replay Token yang Telah Digunakan** | Sesi sudah berstatus `completed = true` | POST `/s/{slug}/proceed` kedua kalinya | **HTTP 422: Token Already Consumed**, redirect ditolak via atomic update | P0 |
| **TEST-SAFE-008** | Tolak proceed tanpa verifikasi Turnstile | Sesi berstatus `turnstile_verified = false` | POST `/s/{slug}/proceed` | **HTTP 422: Bot Verification Required** | P0 |

---

### 1.5 Modul Manajemen Iklan & Kepatuhan Monetisasi (`TEST-ADS-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-ADS-001** | Simpan script kode iklan banner baru | Admin terautentikasi | Submit script tag Adsterra Banner 300x250 ke slot `above_timer` | Record tersimpan di DB, audit log modifikasi dicatat di `security_logs` | P0 |
| **TEST-ADS-002** | Toggle nonaktifkan slot iklan | Slot iklan berstatus aktif | Admin matikan slot `header` | Slot iklan tersebut tidak dirender sama sekali pada view halaman `/s/{slug}` | P1 |
| **TEST-ADS-003** | Sterilisasi tombol navigasi dari skrip iklan | Halaman safelink dimuat dengan iklan aktif | Inspeksi DOM elemen `#proceed-btn` | Tombol navigasi tidak terikat event listener pihak ketiga atau link popunder (Anti-IVT compliant) | P1 |

---

### 1.6 Modul Analitik & Agregasi (`TEST-ANAL-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-ANAL-001** | Pencatatan event kunjungan & pseudonimisasi IP | Request masuk ke safelink | Pemicuan event `impression`, `verified`, `completed` | Record masuk ke tabel `visits` dengan kolom `ip_hash` terenkripsi HMAC (`ANALYTICS_SALT`), IP mentah tidak tersimpan | P0 |
| **TEST-ANAL-002** | Filter analitik & rasio penyelesaian | Data `daily_stats` tersedia | Akses `/admin/analytics?date_from=...&date_to=...` | HTTP 200, perhitungan rasio completions/impressions akurat sesuai rentang tanggal | P1 |

---

### 1.7 Modul Pengaturan & Keamanan (`TEST-SET-*` & `TEST-SEC-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-SET-001** | Update pengaturan umum platform | Admin terautentikasi | PUT `/admin/settings` mengubah nama platform dan countdown default | Pengaturan terupdate di DB dan cache settings ter-flush | P1 |
| **TEST-SET-002** | Update credentials Turnstile | Admin terautentikasi | PUT `/admin/settings` mengubah site key dan secret key | Data terupdate, validasi format string aman | P0 |
| **TEST-SET-003** | Turnstile Emergency Bypass Mode | Turnstile API Cloudflare mengalami downtime | Admin ubah `turnstile_mode = emergency_bypass` di settings | Safelink meloloskan verifikasi bot tanpa memanggil API Cloudflare; audit log tercatat | P1 |
| **TEST-SEC-001** | Pencegahan Stored XSS via Unauthorized User | User unauthenticated | Kirim PUT `/admin/ads/{id}` dengan script berbahaya | Ditolak seketika oleh middleware auth (HTTP 302 ke login) | P0 |
| **TEST-SEC-002** | Verifikasi Penyimpanan Hashed Session Token | Sesi safelink baru dibuat | Query database `visitor_sessions` | Kolom `token_hash` berisi string SHA-256 64-karakter, tidak ada plaintext raw token di database | P0 |
| **TEST-SEC-003** | Simulasi serbuan konkuren (Race Condition) | 1 sesi sah ready to proceed | Kirim 5 request POST `/s/{slug}/proceed` serentak | Tepat 1 request berhasil HTTP 302, 4 request lainnya gagal HTTP 422 (Atomic check) | P0 |
| **TEST-SEC-004** | Pencatatan audit log peristiwa kritis | Terjadi login gagal, ubah password, atau ubah iklan | Query tabel `security_logs` | Event tercatat dengan IP asli, user-agent, dan detail insiden JSON | P1 |
| **TEST-SEC-005** | Rate Limiting IP publik pada safelink | IP mengirim request masif | Kirim 65 request dalam 1 menit ke `/s/{slug}` dari 1 IP | Request ke-61 menerima HTTP 429 Too Many Requests | P0 |

---

### 1.8 Scheduled CLI Commands (`TEST-CLI-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-CLI-001** | Agregasi analitik harian (termasuk soft-deleted) | Tabel `visits` terisi; ada 1 link yang di-soft delete pukul 14:00 | Jalankan Artisan `php artisan lokataut:aggregate-stats` | Data teragregasi ke `daily_stats`, kunjungan link yang telah di-soft-delete tetap terhitung via `Link::withTrashed()` | P1 |
| **TEST-CLI-002** | Pembersihan sesi kadaluwarsa | Terdapat sesi `visitor_sessions` > 24 jam | Jalankan Artisan `php artisan lokataut:prune-sessions` | Record sesi kadaluwarsa terhapus dari database | P1 |
| **TEST-CLI-003** | Pembersihan file cache kedaluwarsa (Inode Pruning) | Folder `storage/framework/cache/data` terisi file cache usang | Jalankan Artisan `php artisan lokataut:prune-cache` | File cache dengan usia > 2 hari terhapus, kuota inode cPanel terjaga | P1 |
| **TEST-CLI-004** | Pembersihan log kunjungan historis (> 90 hari) | Terdapat rekaman `visits` > 90 hari | Jalankan Artisan `php artisan lokataut:prune-visit-logs` | Record lama terhapus, data `daily_stats` permanen tidak terpengaruh | P1 |

---

### 1.9 Kinerja, UX, & Integritas Regresi (`TEST-PERF-*`, `TEST-UX-*`, `TEST-REG-*`)

| Test ID | Skenario Pengujian | Precondition | Langkah Pengujian | Hasil yang Diharapkan | Prioritas |
|---|---|---|---|---|:---:|
| **TEST-PERF-001** | Waktu respon halaman safelink < 200ms | Server lokal aktif | Benchmark HTTP GET `/s/{slug}` dengan 50 concurrent requests | Rata-rata waktu respon server-side < 200 ms | P1 |
| **TEST-PERF-002** | Verifikasi header anti-cache LiteSpeed | Route publik safelink | Inspeksi header respon dari `GET /s/{slug}` | Memuat header `X-LiteSpeed-Cache-Control: no-cache` dan `Cache-Control: no-store, private` | P1 |
| **TEST-PERF-003** | Footprint memori command agregasi < 64 MB | 10.000 record visits | Eksekusi command agregasi dengan memory profiling | Penggunaan memori puncak tidak melebihi 64 MB (Aman untuk shared hosting 2 GB) | P1 |
| **TEST-UX-001** | Responsivitas tampilan mobile viewport 360px | Browser mobile emulation | Render `/s/{slug}` pada lebar layar 360px | Tidak ada elemen horizontal overflow, target sentuh tombol minimal 44x44px | P1 |
| **TEST-UX-002** | Kontras warna aksesibilitas WCAG 2.1 AA | Halaman safelink & admin | Audit kontras teks tombol dan label menggunakan Lighthouse | Rasio kontras teks terhadap latar belakang minimal 4.5:1 | P1 |
| **TEST-REG-001** | Eksekusi seluruh test suite otomatis | Seluruh modul Phase 1–6 selesai | Jalankan perintah `php artisan test` | **100% Tests PASS (Hijau), 0 Failure, 0 Error** | P0 |
| **TEST-REG-002** | Integritas migrasi dan rollback basis data | Database bersih | Eksekusi `php artisan migrate`, lalu `php artisan migrate:rollback`, lalu `migrate` kembali | Seluruh migrasi dan rollback berjalan bersih tanpa error foreign key | P0 |

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
        $response->assertHeader('X-LiteSpeed-Cache-Control', 'no-cache');

        // Pastikan token disimpan dalam bentuk hash SHA-256 di basis data
        $session = VisitorSession::where('link_id', $link->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals(64, strlen($session->token_hash));

        // Ambil rawToken dari payload view
        $rawToken = $response->viewData('rawToken');
        $this->assertEquals($session->token_hash, hash('sha256', $rawToken));

        // Verifikasi Turnstile via AJAX
        $this->postJson('/s/photoshop-2026/verify', [
            'raw_token' => $rawToken,
            'turnstile_token' => 'dummy-valid-turnstile-token',
        ])->assertJson(['success' => true]);

        // 3. Act Curang: Langsung tembak proceed 1 detik kemudian!
        $bypassResponse = $this->post('/s/photoshop-2026/proceed', [
            'raw_token' => $rawToken,
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
3. **Database Migration Integrity:** Perintah `php artisan migrate:fresh --seed` berjalan mulus tanpa kegagalan constraint foreign key.
