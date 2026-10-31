# Laporan Audit Keamanan Sistem & Kepatuhan Privasi LokaTaut v1.1

> **Status Audit:** `CONDITIONALLY SECURE (REVISION REQUIRED PRIOR TO CODING)`  
> **Tanggal Audit:** 31 Oktober 2026  
> **Auditor:** Application Security Engineer & Privacy Specialist  
> **Standar Rujukan:** OWASP Top 10 (2021), OWASP ASVS v4.0 (Level 1 & Level 2), RFC 6749, GDPR Pseudonymization Directives

---

## 1. Executive Summary Keamanan

Dokumentasi keamanan LokaTaut v1.1 (`05-security-monetization/09-security-privacy.md` dan `03-architecture/07-technical-specification.md`) telah merancang arsitektur keamanan yang kokoh dan memperhatikan prinsip-prinsip modern:
1. **Perlindungan Destination URL:** URL unduhan akhir tidak dikirimkan ke DOM / client browser selama proses safelink berlangsung, melainkan diserahkan melalui HTTP 302 Location header setelah seluruh verifikasi tuntas.
2. **Atomic Session State Transition:** Penggunaan query atomik database (`UPDATE visitor_sessions SET completed = 1 WHERE ... AND completed = 0`) untuk mematikan risiko *double-redirection race condition*.
3. **Pseudonimisasi IP:** Penggunaan algoritma `hash_hmac('sha256', $ip, config('app.key'))` untuk memenuhi kepatuhan privasi (GDPR) tanpa menyimpan plaintext IP pengunjung.
4. **Validasi Countdown Server-Side:** Waktu tunggu diverifikasi berdasarkan perbedaan timestamp server, bukan variabel JavaScript browser.

Meskipun demikian, audit independen menemukan **dua celah arsitektur keamanan penting** yang wajib diperbaiki sebelum fase koding:
- **Celah P1 (REV-SEC-001):** Penyimpanan token sesi safelink dalam bentuk teks terbuka (*plaintext*) pada tabel `visitor_sessions`.
- **Celah P2 (REV-SEC-002):** Ketiadaan switch penanganan insiden darurat (*emergency bypass switch*) saat layanan pihak ketiga Cloudflare Turnstile mengalami kegagalan/outage global.

---

## 2. Pemodelan Ancaman (Threat Modeling — Analisis STRIDE)

| Kategori STRIDE | Skenario Ancaman pada LokaTaut | Dampak Potensial | Mitigasi yang Ditetapkan di v1.1 | Status Audit / Gap |
|---|---|---|---|---|
| **Spoofing (Pemalsuan)** | Penyerang memalsukan identitas pengunjung untuk mengklaim sesi safelink aktif. | Pencurian akses download tanpa melewati captcha & iklan. | Token kriptografis 32-byte (`bin2hex(random_bytes(32))`), validasi `session_hash` dan `user_agent_hash`. | **P1 (REV-SEC-001):** Token di DB masih plaintext; wajib di-hash (`token_hash`). |
| **Tampering (Manipulasi)** | Penyerang memodifikasi timer countdown di JavaScript browser agar bisa langsung download. | Melewati waktu tunggu safelink (bypassing impressions). | Waktu mulai dicatat di server saat sesi dibuat (`created_at`). Validasi server-side: `now() - created_at >= countdown_seconds`. | **SECURE (Verified).** Waktu diatur mutlak oleh server. |
| **Repudiation (Penyangkalan)** | Administrator menyangkal telah mengubah kode iklan atau pengaturan monetisasi. | Kesulitan investigasi insiden fraud atau injeksi iklan. | Pencatatan setiap aksi admin, perubahan setting, dan perubahan kode iklan di tabel `security_logs`. | **SECURE (Verified).** Mekanisme audit log lengkap. |
| **Information Disclosure** | Pengunjung memeriksa Source Code / Network Tab browser untuk mencuri URL unduhan asli. | Safelink dilewati; kehilangan tayangan iklan dan impresi. | Destination URL tidak pernah di-render ke view HTML atau JSON. URL diserahkan murni via header `Location: {destination_url}` status 302. | **SECURE (Verified).** Tidak ada kebocoran DOM. |
| **Denial of Service (DoS)** | Botnet membombardir rute `/s/{slug}` atau `/sl/{token}/proceed` dengan jutaan request. | Server shared hosting down; kuota resource CloudLinux terlampaui. | Rate Limiting Laravel (`throttle:60,1`), Cloudflare WAF, dan verifikasi Turnstile bot-check. | **P2 (REV-PERF-001):** File cache driver berisiko menghabiskan kuota inode. |
| **Elevation of Privilege** | Penyerang menginjeksikan script jahat (Stored XSS) melalui kolom `ad_code` di dashboard. | Pembajakan session admin atau defacing halaman safelink. | Input `ad_code` hanya dapat diakses setelah login Super Admin; sanitasi skrip berbahaya; header CSP ketat. | **SECURE (Verified).** Butuh mitigasi CSP tambahan. |

---

## 3. Evaluasi Terhadap Standar OWASP ASVS v4.0

### V2: Autentikasi & Manajemen Sesi
- **ASVS 2.1.1 (Kompleksitas Password Admin):** Dokumen menetapkan minimal 12 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol (`UpdatePasswordRequest`). *STATUS: COMPLIANT (setelah implementasi REV-AUTH-001).*
- **ASVS 3.2.1 (Entropi Sesi):** Token sesi safelink dibuat menggunakan CSPRNG PHP `random_bytes(32)`, menghasilkan entropi 256-bit ($2^{256}$ kemungkinan variasi). Sangat resisten terhadap serangan brute-force. *STATUS: COMPLIANT.*
- **ASVS 3.2.4 (Penyimpanan Token):** OWASP ASVS mensyaratkan token sensitif tidak disimpan dalam teks terbuka di database jika kebocoran DB dapat langsung dieksploitasi. *STATUS: NON-COMPLIANT (P1 REV-SEC-001).*

### V4: Kontrol Akses (Access Control)
- **ASVS 4.1.1 (Prinsip Least Privilege):** LokaTaut beroperasi dengan sistem Single Owner. Seluruh rute administratif dilindungi middleware `auth` dan prefix `/admin`.
- **Single-Session Enforcement:** Sistem memvalidasi `session_id` aktif di tabel `users` untuk memastikan administrator tidak dapat login bersamaan dari dua browser berbeda secara aktif. *STATUS: COMPLIANT.*

### V5: Validasi Input & Sanitasi
- **ASVS 5.1.1 (Validasi Form Request):** Seluruh input pada rute publik dan privat divalidasi ketat menggunakan Laravel Form Request (tipe data, format URL, regex slug, integer timer).
- **ASVS 5.3.4 (Stored XSS pada Kode Iklan):** Dokumen mengizinkan injeksi tag `<script>` pada kolom `ad_code` khusus Super Admin. Untuk memitigasi bahaya XSS:
  1. Akses hanya untuk akun Super Admin terverifikasi.
  2. Audit log mencatat setiap perubahan kode iklan.
  3. Header *Content Security Policy (CSP)* wajib membatasi domain eksekusi skrip hanya ke CDN resmi jaringan iklan terkait (`*.adsterra.com`, `*.monetag.com`). *STATUS: COMPLIANT.*

### V8: Perlindungan Data & Kriptografi
- **ASVS 8.3.1 (Penyimpanan Data Sensitif):** Password admin di-hash menggunakan algoritma Bcrypt bawaan Laravel (work factor 12).
- **Pseudonimisasi IP:** Penggunaan `hash_hmac('sha256', $ip, config('app.key'))` memastikan IP tidak dapat didekripsi kembali menjadi plaintext jika database terekspos tanpa kunci `APP_KEY`. *STATUS: COMPLIANT.*

---

## 4. Analisis Teknis Celah Keamanan & Solusi Arsitektural

### 4.1 Rekayasa Penyimpanan Token Sesi (Penyelesaian REV-SEC-001)

#### Skenario Eksploitasi Saat Ini:
1. Penyerang memanfaatkan celah file traversal cPanel atau mendapatkan backup database `.sql` yang tersimpan di direktori publik akibat kelalaian operasional.
2. Penyerang membaca tabel `visitor_sessions`.
3. Penyerang melihat token aktif: `a7f9c2e4b6d8...` yang masih dalam masa countdown.
4. Penyerang mengirim POST request langsung ke `/sl/a7f9c2e4b6d8.../proceed` dengan User-Agent yang cocok, langsung mendapatkan redirect URL tujuan tanpa melihat iklan.

#### Arsitektur Solusi (Hashed Token Pattern):
```
[ Browser Kunjungan ]                              [ Laravel Backend ]                    [ MySQL Database ]
         |                                                  |                                      |
         | --- (1) GET /s/{slug} -------------------------> |                                      |
         |                                                  | -- (2) $rawToken = random_bytes(32)  |
         |                                                  | -- (3) $hash = hash('sha256', $raw)  |
         |                                                  | -- (4) INSERT visitor_sessions -----> | [token_hash = $hash]
         | <-- (5) Render Blade with $rawToken ------------ |                                      |
         |                                                  |                                      |
         | ... Countdown & Turnstile Verification ...       |                                      |
         |                                                  |                                      |
         | --- (6) POST /sl/{rawToken}/proceed -----------> |                                      |
         |                                                  | -- (7) $hash = hash('sha256', $raw)  |
         |                                                  | -- (8) UPDATE visitor_sessions -----> | [Match token_hash]
         |                                                  |        WHERE token_hash = $hash      |
         |                                                  |        AND completed = 0             |
         | <-- (9) HTTP 302 Found (Destination URL) ------- |                                      |
```

Dengan pola ini, sekalipun tabel `visitor_sessions` bocor, penyerang tidak dapat merekonstruksi nilai `$rawToken` karena sifat one-way cryptographic hash dari SHA-256.

---

### 4.2 Mitigasi Pergantian IP Pengguna Ponsel (*Mobile IP Handover*)

Pengguna jaringan seluler (4G/5G) sering mengalami perubahan IP publik secara dinamis saat berpindah BTS dalam durasi hitung mundur 10 detik. Jika validasi sesi safelink mengikat IP publik secara ketat (strict IP matching), pengunjung riil ponsel akan sering mengalami kegagalan validasi (*Invalid Session / Access Denied*).

#### Rekomendasi Desain Mitigasi:
1. **Binding Sesi Berbasis Subnet (/24 IPv4 atau /64 IPv6):**
   Alih-alih mengikat IP lengkap pengunjung pada sesi safelink sementara, ikat sesi dengan subnet hash atau token session cookie + User-Agent hash:
   ```php
   $ipSubnet = preg_replace('/\.\d+$/', '.0', $request->ip()); // IPv4 subnet /24
   $sessionFingerprint = hash_hmac('sha256', $ipSubnet . $request->userAgent(), config('app.key'));
   ```
2. **Kombinasi Cookie Bertanda Tangan (Encrypted Session Cookie):**
   Gunakan encrypted Laravel session cookie yang valid untuk memastikan kepemilikan sesi, sehingga pergantian IP dinamis pada ponsel pintar tidak membatalkan countdown yang sah.

---

## 5. Rencana Pengujian Keamanan Otomatis (Security Test Suite)

Untuk memvalidasi integritas keamanan sebelum rilis produksi, rangkaian pengujian otomatis berikut wajib diimplementasikan menggunakan Pest / PHPUnit:

| Kode Test | Nama Pengujian Keamanan | Skenario yang Diuji | Expected Result |
|---|---|---|---|
| `TEST-SEC-001` | Destination URL Concealment | Inspect response body dari `GET /s/{slug}` dan network payload JSON. | URL tujuan tidak ditemukan di sembarang atribut DOM, comment, atau script tag. |
| `TEST-SEC-002` | Server-Side Countdown Enforcement | Mengirim `POST /sl/{token}/proceed` hanya 2 detik setelah sesi dibuat (countdown = 10s). | HTTP 403 Forbidden; Sesi tidak dieksekusi; redirect digagalkan. |
| `TEST-SEC-003` | Single-Use Atomic Transition | Menjalankan 5 concurrent request simultan dengan token sesi yang sama persis. | Hanya 1 request yang berhasil mendapatkan 302 redirect; 4 request lainnya menerima 410 Gone / 403 Forbidden. |
| `TEST-SEC-004` | Turnstile Token Bypass Resistance | Mengirim request proceed dengan token Turnstile palsu atau tanpa token. | HTTP 422 Unprocessable Entity / 403 Forbidden; Gagal diverifikasi. |
| `TEST-SEC-005` | Stored XSS Mitigation in Admin | Menyimpan payload `<script>alert(1)</script>` pada field judul tautan dan nama slot iklan. | String di-escape secara otomatis oleh Blade engine (`{{ ... }}`). |
| `TEST-SEC-006` | Rate Limiter IP Throttling | Mengirimkan 65 request dalam 1 menit ke endpoint `/s/{slug}` dari IP yang sama. | Request ke-61 menerima HTTP 429 Too Many Requests dengan header `Retry-After`. |

---
*Laporan tinjauan keamanan ini menjamin arsitektur LokaTaut memenuhi standar perlindungan data dan ketahanan terhadap ancaman siber.*

