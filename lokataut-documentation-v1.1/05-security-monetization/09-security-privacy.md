# Security & Privacy Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-09 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
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

## 3. Server-Side Elapsed Time & Atomic One-Time Token

### 3.1 Validasi Waktu Hitung Mundur Server-Side
Sistem menolak ketergantungan pada JavaScript client. Saat pengunjung lolos verifikasi Turnstile:
1. Server mencatat `countdown_started_at = now()` pada tabel `visitor_sessions`.
2. Saat request POST `/s/{slug}/proceed` masuk, server menghitung selisih waktu:
   $$\text{elapsed\_seconds} = \text{now}() - \text{countdown\_started\_at}$$
3. Jika $\text{elapsed\_seconds} < \text{countdown\_seconds}$, request langsung ditolak dengan kode **HTTP 422 Unprocessable Entity** dan insiden dicatat ke `security_logs`.

### 3.2 Transisi Status Atomic (Pencegahan Race Condition & Replay Attack)
Untuk mencegah dua request konkuren menggunakan token yang sama secara simultan:
```sql
UPDATE visitor_sessions 
SET completed = 1, completed_at = NOW() 
WHERE token = ? AND completed = 0;
```
Jika baris yang terpengaruh (*affected rows*) adalah 0, request ditolak sebagai replay attack.

---

## 4. Perlindungan Privasi & Pseudonimisasi IP Pengunjung

### 4.1 Keyed HMAC Pseudonymization
Alamat IP pengunjung safelink tidak pernah disimpan dalam bentuk teks mentah pada tabel analitik `visits`. Sistem menggunakan keyed HMAC:
```php
$ipHash = hash_hmac('sha256', $request->ip(), config('app.analytics_salt'));
```
- Variabel `ANALYTICS_SALT` disimpan terpisah di `.env` dan dirotasi secara berkala (misal: setiap 12 bulan) tanpa merusak integritas `APP_KEY`.
- Sistem mengakui bahwa penghitungan *Unique Visitors* berdasarkan hash IP adalah pendekatan estimasi (karena dynamic IP dan CGNAT pada jaringan seluler).

### 4.2 Justifikasi Penyimpanan IP Mentah pada Modul Tertentu
Sesuai prinsip *Legitimate Security Interest* (GDPR Recital 49):
- Tabel `admins.last_login_ip` dan `security_logs.ip_address` diizinkan menyimpan IP mentah (IPv4/IPv6) semata-mata untuk keperluan audit keamanan, deteksi penyusupan akun, dan pemblokiran serangan DoS di level Cloudflare WAF.

### 4.3 Pengikatan Sesi yang Fleksibel untuk Pengguna Mobile
Sesi pengunjung diikat pada kombinasi: **Session Token (64 hex) + User-Agent Hash**. IP hash dijadikan verifikasi sekunder. Jika pengunjung mobile berpindah BTS seluler atau berganti jaringan (WiFi ke 4G) selama 10 detik hitung mundur, sesi tidak langsung dibatalkan selama User-Agent tetap identik.

---

## 5. Mitigasi Stored XSS pada Penempatan Kode Iklan (`ad_code`)

Karena kode iklan disimpan dalam bentuk raw HTML/JavaScript dari ad network, terdapat risiko Stored XSS jika akun admin disusupi. LokaTaut menerapkan mitigasi berlapis:

1. **Otorisasi Super Admin:** Pengubahan tabel `ad_slots` dibatasi secara ketat hanya untuk sesi admin terautentikasi penuh dengan CSRF token valid.
2. **Audit Logging Wajib:** Setiap penambahan atau pengubahan kode iklan otomatis dicatat ke tabel `security_logs` beserta alamat IP dan waktu modifikasi.
3. **Content Security Policy (CSP):** Middleware `SecurityHeadersMiddleware` menyuntikkan header CSP yang membatasi eksekusi script hanya ke domain CDN ad network resmi yang disetujui:
```http
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://challenges.cloudflare.com https://*.adsterra.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;
```

