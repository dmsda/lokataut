# AI Agent Coding Instructions & Development Guidelines — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-14 |
| **Versi** | 1.2 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Lead AI Architect & Technical Documentation Specialist |
| **Target Pembaca** | Seluruh AI Coding Agent (Claude, Gemini, GPT, dsb.) |
| **Tujuan** | Memberikan instruksi operasional mengikat (*Binding Operational Directives*) yang mengatur tata cara, batasan, standar koding, dan protokol kerja AI Coding Agent selama fase implementasi repositori LokaTaut. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [AGENTS.md](../../AGENTS.md) | File aturan operasional utama di root repository |
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Batasan kebutuhan fitur yang sah |
| [DOC-03 — Scope & Requirements Specification](../01-product/03-scope-requirements.md) | Daftar fitur In-Scope dan Out-of-Scope |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Prinsip modular monolith dan layer aplikasi Laravel 13 |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Standar teknis penulisan kode |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Standar keamanan data dan alur safelink |
| [DOC-12 — Testing Strategy & QA Plan](../06-quality/12-testing-qa.md) | Standar validasi pengujian otomatis |

---

## 1. Peran & Batasan Tanggung Jawab AI Coding Agent

> ### 🛑 ATURAN EMAS: ANDA ADALAH PENGEMBANG (DEVELOPER), BUKAN PRODUCT MANAGER!
> AI Coding Agent **DILARANG** berinisiatif menambahkan fitur baru, mengubah model bisnis, mengubah stack teknologi, atau mengasumsikan kebutuhan pengguna di luar dokumen spesifikasi yang telah disetujui (APPROVED).
> 
> **Pemisahan Peran:**
> - **Product Owner (Dimas Aditya):** Pemilik produk tunggal yang memiliki otoritas mutlak dalam persetujuan scope dan keputusan arsitektur.
> - **AI Documentation Specialist:** Bertanggung jawab memelihara konsistensi dokumen spesifikasi.
> - **AI Coding Agent (Anda):** Bertanggung jawab mengimplementasikan spesifikasi teknis menjadi kode sumber yang bersih, teruji, dan siap produksi.
> - **AI QA Agent:** Bertanggung jawab memverifikasi implementasi kode terhadap test cases.

---

## 2. Batasan Produk & Fitur Terlarang (Absolute Blacklist)

AI Coding Agent **DILARANG KERAS** mengimplementasikan hal-hal berikut tanpa persetujuan tertulis eksplisit dari Product Owner:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        DAFTAR FITUR TERLARANG                          │
├────────────────────────────────────────────────────────────────────────┤
│ ❌ Registrasi Pengguna Publik (Public Registration / Signup)           │
│ ❌ Dashboard Publisher / Member Eksternal                              │
│ ❌ Marketplace Pengiklan (Advertiser Portal)                           │
│ ❌ Dompet Saldo Pengguna (User Wallet / Balance)                       │
│ ❌ Sistem Pembayaran / Payment Gateway / Payout System                 │
│ ❌ Sistem Referral / Komisi Afiliasi                                   │
│ ❌ Bagi Hasil Pendapatan (Revenue Sharing System)                      │
│ ❌ Arsitektur SaaS Multi-Tenant (Pemisahan database per penyewa)       │
│ ❌ Public API Key Management untuk pihak luar                          │
│ ❌ Integrasi Google AdSense pada halaman safelink/countdown            │
│ ❌ Rekayasa HTTP Referrer Organik Palsu (Google Referrer Spoofing)    │
│ ❌ Tombol Unduhan Palsu / Deceptive Clickbait Ads                      │
│ ❌ Iklan popunder pada tombol aksi safelink (Anti-IVT Compliance)      │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Batasan Teknologi & Infrastruktur (Tech Constraints)

1. **Framework & Bahasa:** Wajib menggunakan **Laravel 13.x** dan **PHP 8.3+**. Dilarang mendegradasi versi framework ke Laravel 11/12 yang masa dukungannya telah atau segera berakhir.
2. **Database Engine:** Wajib menggunakan **MySQL 8.0+** murni dengan engine storage InnoDB.
3. **Frontend Stack:**
   - Halaman Publik Safelink: Wajib menggunakan **Blade Views, Semantic HTML5, Native CSS3, dan Vanilla JS**.
   - Dilarang menambahkan runtime React, Vue, SPA, atau build step Tailwind CLI pada shared hosting produksi.
4. **Shared Hosting Compliance (WarnaHost Starter Unlimited):**
   - Alokasi server: 1 CPU Core dan 2 GB RAM.
   - **TIDAK ADA AKSES ROOT**. Dilarang menggunakan perintah sudo, docker, atau service worker latar belakang yang butuh supervisor.
   - **TIDAK ADA REDIS**. Session dan cache aplikasi wajib menggunakan driver `file`.

---

## 4. Standar Koding & Aturan Keamanan Wajib

1. **Strict Typing:** Seluruh file PHP wajib diawali `declare(strict_types=1);`.
2. **PSR-12 Compliance:** Mematuhi penataan indentasi 4 spasi dan tipe data eksplisit.
3. **Thin Controllers:** Controller hanya memanggil Form Request, mengeksekusi Service, dan mengembalikan respon.
4. **URL Tujuan Dirahasiakan:** Kolom `destination_url` dienkripsi simetris (AES-256-CBC) dan dirahasiakan selama proses safelink berlangsung hingga respon HTTP 302 diserahkan.
5. **Validasi Waktu Server-Side:** Evaluasi countdown wajib berdasarkan selisih waktu server (`now() - countdown_started_at >= countdown_seconds`).
6. **Hashed Token Transition:** Eksekusi redirect wajib menggunakan update atomic via hash SHA-256 `UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0` untuk mencegah race condition. Raw token hanya dipegang browser.
7. **Pseudonimisasi IP Pengunjung:** Menggunakan keyed HMAC `hash_hmac('sha256', $ip, config('services.analytics.salt', config('app.key')))`.
8. **Mitigasi Stored XSS:** Pengubahan `ad_code` dibatasi hanya untuk Super Admin dengan pencatatan di `security_logs` dan header CSP ketat.
9. **Admin Password Management:** Penggantian password admin wajib memvalidasi password lama (`current_password`), kompleksitas password baru minimal 12 karakter, konfirmasi, dan rotasi session ID.
10. **Konsistensi Penamaan Command:** Seluruh Artisan command konsol wajib berakhiran sufiks `Command`, seperti `AggregateDailyStatsCommand.php` dan `PruneCacheCommand.php`.

---

## 5. Protokol Pengujian & Definisi Selesai (Definition of Done)

Sebelum melaporkan bahwa sebuah fitur selesai:
1. Menyertakan Feature Test atau Unit Test di direktori `tests/` untuk setiap fitur P0 dan P1 sesuai pemetaan di `DOC-12`.
2. Menjalankan perintah pengujian secara riil: `php artisan test`.
3. Seluruh skenario pengujian harus berstatus **PASS** (100% hijau).
4. Tidak ada fungsi debugging tertinggal (`dd()`, `dump()`, `console.log()`).
5. File `.env` asli tidak ter-commit ke repositori git.

