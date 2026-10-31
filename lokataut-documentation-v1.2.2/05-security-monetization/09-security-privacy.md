# Security & Privacy Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-09 |
| **Versi** | 1.2.2 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Application Security Engineer & Data Privacy Specialist |
| **Target Pembaca** | AI Coding Agent, Security Auditor, DevOps Engineer |
| **Tujuan** | Menetapkan model ancaman (STRIDE), standar pengamanan kriptografis, pencegahan eksploitasi safelink, mitigasi Stored XSS, serta kepatuhan privasi data pengunjung. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan keamanan fungsional dan aturan bisnis (BR) |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Proteksi layer arsitektur dan lifecycle request |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Enkripsi kolom database dan hashing data privasi |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Konfigurasi middleware keamanan dan service layer |
| [AGENTS.md](../../AGENTS.md) | Larangan keras bagi Coding Agent terkait celah keamanan |

---

## 1. Threat Modeling (STRIDE Analysis)

```
┌────────────────────────────────────────────────────────────────────────┐
│                        STRIDE Threat Matrix                            │
├───────────────┬───────────────────────────────┬────────────────────────┤
│ Kategori      │ Potensi Ancaman pada Safelink │ Mitigasi LokaTaut      │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Spoofing      │ Bot meniru identitas visitor  │ Cloudflare Turnstile   │
│               │ atau memalsukan session token │ + Token Kripto 64-hex  │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Tampering     │ Mengubah countdown timer via  │ Validasi timestamp     │
│               │ JavaScript console di browser │ wajib di server-side   │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Repudiation   │ Manipulasi log analitik       │ Keyed HMAC IP Hash     │
│               │ atau perubahan script iklan   │ + Audit Log di DB      │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Information   │ URL tujuan bocor di HTML/JS   │ Destination URL di-    │
│ Disclosure    │ sebelum countdown selesai     │ simpan aman di server  │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Denial of     │ Flooding request ke /s/{slug} │ Rate Limiter (60/min)  │
│ Service (DoS) │ untuk menghabiskan CPU server │ + Cloudflare Edge WAF  │
├───────────────┼───────────────────────────────┼────────────────────────┤
│ Elevation of  │ Mengakses panel admin / XSS   │ Single Session Auth    │
│ Privilege     │ melalui input kode iklan      │ + CSP Header + Audit   │
└───────────────┴───────────────────────────────┴────────────────────────┘
```

---

## 2. Prinsip Kerahasiaan URL Tujuan (Destination URL Confidentiality)

> ### 🛑 KLARIFIKASI TEKNIS: BATASAN KERAHASIAAN URL TUJUAN
> URL tujuan unduhan yang sebenarnya **DIRAHASIAKAN DAN TIDAK PERNAH DIKIRIMKAN** ke peramban pengunjung selama proses safelink berlangsung (sebelum verifikasi Turnstile disetujui dan countdown 10 detik selesai tervalidasi di server).
> 
> Namun, setelah seluruh kriteria keamanan terpenuhi, server akan merespon dengan header `Location: https://destination-url.com/file.zip` (HTTP 302 Found). Pada titik ini, peramban pengunjung secara teknis menerima URL tujuan untuk mengeksekusi unduhan. Sistem **tidak mengklaim** dapat menyembunyikan URL tujuan setelah redirect selesai dieksekusi.

1. **Enkripsi saat Disimpan:** Kolom `destination_url` pada tabel `links` disimpan dalam bentuk ciphertext terenkripsi simetris menggunakan algoritma **AES-256-CBC** via class `Illuminate\Support\Facades\Crypt` dengan kunci `APP_KEY`.
2. **Dekripsi Just-in-Time:** URL tujuan hanya didekripsi di memori PHP pada milidetik terakhir saat method `SafelinkService@finalizeRedirect` berhasil memvalidasi token sesi dan waktu countdown.

---

## 3. Server-Side Elapsed Time & Hashed Session Token

### 3.1 Validasi Waktu Hitung Mundur Server-Side
Sistem menolak ketergantungan pada JavaScript client. Saat pengunjung lolos verifikasi Turnstile:
1. Server mencatat `countdown_started_at = now()` pada tabel `visitor_sessions`.
2. Saat request POST `/s/{slug}/proceed` masuk, server menghitung selisih waktu:
   $$\text{elapsed\_seconds} = \text{now}() - \text{countdown\_started\_at}$$
3. Jika $\text{elapsed\_seconds} < \text{countdown\_seconds}$, request langsung ditolak dengan kode **HTTP 422 Unprocessable Entity** dan insiden dicatat ke `security_logs`.

### 3.2 Hashed Session Token & Transisi Status Atomic (OWASP ASVS v4.0 Section 3.2.4)
Untuk mematuhi prinsip *Defense-in-Depth*, token sesi safelink disimpan di database dalam bentuk cryptographic digest (hash SHA-256), sedangkan nilai raw token 32-byte acak hanya dipegang oleh peramban pengunjung:
1. **Pembuatan Sesi:**
   - Server menghasilkan `$rawToken = bin2hex(random_bytes(32))` (entropi 256-bit).
   - Server menghitung `$tokenHash = hash('sha256', $rawToken)`.
   - Simpan `$tokenHash` ke kolom `visitor_sessions.token_hash`.
   - Kirimkan `$rawToken` ke peramban pengunjung.
2. **Validasi & Transisi Status Atomic:**
   Saat pengunjung menekan tombol proceed, browser mengirimkan `$rawToken`. Server menghitung `hash('sha256', $rawToken)` dan menjalankan kueri pembaruan atomik:
```sql
UPDATE visitor_sessions 
SET completed = 1, completed_at = NOW() 
WHERE token_hash = ? AND completed = 0;
```
Jika baris yang terpengaruh (*affected rows*) adalah 0, request ditolak sebagai replay attack atau token kedaluwarsa. Sekalipun basis data mengalami kebocoran backup, penyerang tidak dapat merekayasa `$rawToken` karena sifat one-way hash SHA-256.

---

## 4. Perlindungan Privasi & Pseudonimisasi IP Pengunjung

### 4.1 Keyed HMAC Pseudonymization via Mandatori ANALYTICS_SALT
Alamat IP pengunjung safelink tidak pernah disimpan dalam bentuk teks mentah pada tabel analitik `visits`. Sistem menggunakan keyed HMAC dengan pemisahan kunci mandatori tanpa fallback:
```php
$salt = (string) config('services.analytics.salt');

if (empty($salt)) {
    if (app()->environment('production')) {
        throw new \RuntimeException('Variabel lingkungan ANALYTICS_SALT wajib dikonfigurasi di lingkungan production.');
    }
    $salt = 'testing-analytics-salt-lokataut-do-not-use-in-production';
}

$ipHash = hash_hmac('sha256', $request->ip(), $salt);
```
- **Penghapusan Fallback ke APP_KEY:** Fallback otomatis ke `APP_KEY` dihapus secara ketat demi mematuhi prinsip separasi kunci kriptografi NIST SP 800-57 Part 1. `APP_KEY` dan `ANALYTICS_SALT` memiliki masa aktif dan tujuan yang independen.
- **Dampak Rotasi Kunci:** Nilai `ANALYTICS_SALT` bersifat persisten. Jika salt dirotasi, hasil hash dari IP yang sama di masa depan tidak akan cocok dengan hash masa lalu (memutus kontinuitas *returning unique visitor* historis), namun integritas data record `visits` dan `daily_stats` masa lalu tetap utuh. Kunci rahasia ini dilarang keras dicatat di log file atau exception stack trace.
- **Karakteristik Metrik:** Sistem mengakui bahwa penghitungan *Unique Visitors* berdasarkan hash IP adalah pendekatan estimasi (karena dynamic IP dan CGNAT pada jaringan seluler).

### 4.2 Justifikasi Penyimpanan IP Mentah pada Modul Tertentu
Sesuai prinsip *Legitimate Security Interest* (GDPR Recital 49):
- Tabel `admins.last_login_ip` dan `security_logs.ip_address` diizinkan menyimpan IP mentah (IPv4/IPv6) semata-mata untuk keperluan audit keamanan, deteksi penyusupan akun, dan pemblokiran serangan DoS di level Cloudflare WAF.

### 4.3 Pengikatan Sesi yang Fleksibel untuk Pengguna Mobile
Sesi pengunjung diikat pada kombinasi: **token_hash + User-Agent Hash**. IP subnet (/24 untuk IPv4 atau /64 untuk IPv6) dijadikan verifikasi sekunder. Jika pengunjung mobile berpindah BTS seluler atau berganti jaringan (WiFi ke 4G) selama 10 detik hitung mundur, sesi tidak langsung dibatalkan selama User-Agent tetap identik.

---

## 5. Mitigasi Stored XSS pada Penempatan Kode Iklan (`ad_code`)

Karena kode iklan disimpan dalam bentuk raw HTML/JavaScript dari ad network, terdapat risiko Stored XSS jika akun admin disusupi. LokaTaut menerapkan mitigasi berlapis:

1. **Otorisasi Super Admin:** Pengubahan tabel `ad_slots` dibatasi secara ketat hanya untuk sesi admin terautentikasi penuh dengan CSRF token valid.
2. **Audit Logging Wajib:** Setiap penambahan atau pengubahan kode iklan otomatis dicatat ke tabel `security_logs` beserta alamat IP dan waktu modifikasi.
3. **Content Security Policy (CSP):** Middleware `SecurityHeadersMiddleware` menyuntikkan header CSP yang membatasi eksekusi script hanya ke domain CDN ad network resmi yang disetujui:
```http
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com https://*.adsterra.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;
```

---

## 6. Kebijakan Kegagalan Turnstile & Mode Darurat (Turnstile Emergency Policy)

Jika layanan pihak ketiga Cloudflare Turnstile mengalami insiden global atau API endpoint `challenges.cloudflare.com` mengalami timeout berkepanjangan:
1. **Dampak Tanpa Mitigasi:** Seluruh alur safelink akan macet pada langkah verifikasi bot, menyebabkan pengunjung gagal mengunduh file dan mematikan pendapatan monetisasi.
2. **Emergency Bypass Setting:** Administrator dapat mengaktifkan mode bypass melalui pengaturan `turnstile_mode` pada menu `/admin/settings` (pilihan: `enabled`, `disabled`, `emergency_bypass`).
3. **Penegakan Keamanan Saat Bypass:**
   - Ketika `emergency_bypass` aktif, server melewati panggilan API ke Cloudflare dan langsung mencatat `turnstile_verified = 1` serta `countdown_started_at = now()`.
   - Setiap sesi yang memanfaatkan bypass darurat dicatat secara otomatis ke tabel `security_logs` dengan event `turnstile_emergency_bypass_used` untuk audit forensik.
   - Mode bypass wajib dimatikan kembali segera setelah layanan Cloudflare pulih normal.

---

## 7. Pemetaan Kepatuhan Standar OWASP ASVS v4.0

| Bagian Standar OWASP ASVS v4.0 | Deskripsi Persyaratan | Implementasi LokaTaut v1.2 | Status Kepatuhan |
|---|---|---|:---:|
| **ASVS 2.1.1 (Password Complexity)** | Minimal 12 karakter dengan kombinasi huruf, angka, simbol | `UpdatePasswordRequest` & `AdminLoginRequest` menerapkan aturan kompleksitas ketat (`FR-AUTH-004`). | **COMPLIANT** |
| **ASVS 2.1.8 (Credential Rotation)** | Kemampuan rotasi password yang aman via antarmuka | Rute `PUT /admin/profile/password` dengan verifikasi password lama (`current_password`) dan rotasi session ID. | **COMPLIANT** |
| **ASVS 3.2.1 (Session Entropy)** | Token sesi memiliki entropi minimal 128-bit acak | Menggunakan CSPRNG `random_bytes(32)` (256-bit entropy). | **COMPLIANT** |
| **ASVS 3.2.4 (Session Token Storage)**| Token sesi tidak disimpan dalam bentuk plaintext di database | Kolom `visitor_sessions.token_hash` menyimpan nilai `hash('sha256', $rawToken)`. Raw token hanya dipegang browser. | **COMPLIANT** |
| **ASVS 4.1.1 (Access Control)** | Menegakkan prinsip hak akses terendah | Rute administratif dilindungi middleware `auth` dan `enforce.single.session`. | **COMPLIANT** |
| **ASVS 5.1.1 (Input Validation)** | Seluruh data masukan divalidasi ketat di server-side | Menggunakan Laravel Form Request classes untuk semua input formulir dan AJAX. | **COMPLIANT** |
| **ASVS 5.3.4 (Stored XSS Protection)** | Sanitasi input dan proteksi injeksi script | Input `ad_code` dikunci untuk Super Admin, dicatat di audit log, dan dilindungi header CSP ketat. | **COMPLIANT** |
| **ASVS 8.3.1 (Sensitive Data Storage)**| Enkripsi data sensitif saat diam (*at-rest*) | Kolom `links.destination_url` dienkripsi simetris AES-256-CBC; IP disamarkan via keyed HMAC. | **COMPLIANT** |

