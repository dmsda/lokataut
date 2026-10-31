# AI Agent Coding Instructions & Development Guidelines — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-14 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Lead AI Architect & Technical Documentation Specialist |
| **Target Pembaca** | Seluruh AI Coding Agent (Claude, Gemini, GPT, dsb.) |
| **Tujuan** | Memberikan instruksi operasional mengikat (*Binding Operational Directives*) yang mengatur tata cara, batasan, standar koding, dan protokol kerja AI Coding Agent selama fase implementasi repository LokaTaut. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [AGENTS.md](../../AGENTS.md) | File aturan operasional utama di root repository |
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Batasan kebutuhan fitur yang sah |
| [DOC-03 — Scope & Requirements Specification](../01-product/03-scope-requirements.md) | Daftar fitur In-Scope dan Out-of-Scope |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Prinsip modular monolith dan layer aplikasi |
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
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Batasan Teknologi & Infrastruktur (Tech Constraints)

1. **Framework & Bahasa:** Wajib menggunakan **Laravel 11.x** dan **PHP 8.3+**. Dilarang mendegradasi versi PHP atau mengganti framework (misal: beralih ke Express.js, Go, Python Flask).
2. **Database Engine:** Wajib menggunakan **MySQL 8.0+** murni dengan InnoDB engine. Dilarang menambahkan MongoDB, PostgreSQL, atau database NoSQL lainnya.
3. **Frontend Stack:**
   - Halaman Publik Safelink: Wajib menggunakan **Blade Views, Semantic HTML5, Native CSS3, dan Vanilla JS**.
   - **DILARANG** menambahkan framework JavaScript frontend (React, Vue, Angular, Svelte) atau CSS build tools berat (Tailwind CLI di runtime shared hosting produksi).
4. **Shared Hosting Compliance (WarnaHost Starter Unlimited):**
   - Server memiliki alokasi 1 CPU Core dan 2 GB RAM.
   - **TIDAK ADA AKSES ROOT**. Dilarang menulis script yang membutuhkan `sudo`, instalasi paket sistem operasi (`apt-get`, `yum`), atau daemon Docker.
   - **TIDAK ADA REDIS / MEMCACHED**. Gunakan driver session dan cache berbasis `file`.

---

## 4. Standar Koding & Konvensi Penamaan (Coding Standards)

### 4.1 Standar PHP & Laravel
- **Strict Typing:** Seluruh file PHP wajib diawali `declare(strict_types=1);`.
- **PSR-12 Compliance:** Mematuhi penataan indentasi 4 spasi, kurung kurawal pembuka method di baris baru, dan tipe data parameter/return yang eksplisit.
- **Thin Controllers:** Controller hanya bertugas memanggil Form Request, mengeksekusi Service, dan mengembalikan respon view/redirect. Logika bisnis dilarang berada di controller.
- **Kueri Basis Data:** Wajib menggunakan Eloquent ORM atau Query Builder dengan parameter binding. Dilarang melakukan string concatenation pada query SQL.

### 4.2 Konvensi Penamaan (Naming Conventions)
- **Controller:** PascalCase berakhiran `Controller` (contoh: `LinkController.php`).
- **Service:** PascalCase berakhiran `Service` (contoh: `SafelinkService.php`).
- **Model:** PascalCase tunggal (contoh: `Link.php`, `VisitorSession.php`).
- **Form Request:** PascalCase berawalan aksi (contoh: `StoreLinkRequest.php`).
- **Blade View:** kebab-case (contoh: `safelink.blade.php`, `stats-card.blade.php`).
- **Tabel Database:** snake_case jamak (contoh: `visitor_sessions`, `daily_stats`).
- **Kolom Database:** snake_case (contoh: `destination_url`, `countdown_seconds`).

---

## 5. Aturan Keamanan Wajib (Mandatory Security Rules)

1. **URL Tujuan Dirahasiakan:** Kolom `destination_url` wajib dienkripsi simetris menggunakan Laravel Crypt (`Crypt::encryptString`) saat disimpan di basis data dan **TIDAK BOLEH** muncul di kode HTML sebelum redirect dieksekusi.
2. **Validasi Waktu Server-Side:** Perhitungan countdown timer wajib dievaluasi berdasarkan selisih waktu server (`now() - countdown_started_at >= countdown_seconds`). Manipulasi JavaScript client-side tidak boleh dapat meloloskan proses redirect.
3. **Kriptografi Token Sesi:** Token sesi safelink wajib dihasilkan menggunakan generator pseudo-random aman kriptografis:
```php
$token = bin2hex(random_bytes(32));
```
4. **One-Time Redirect Token:** Setiap token sesi yang telah sukses mengarahkan pengunjung wajib ditandai `completed = true` dan tidak boleh digunakan kembali (*anti-replay attack*).
5. **Sanitasi Privasi Pengunjung:** Alamat IP pengunjung wajib disamarkan menggunakan hash satu arah SHA-256 sebelum disimpan ke tabel analitik `visits`.

---

## 6. Protokol Pengujian & Definisi Selesai (Definition of Done)

Sebelum melaporkan bahwa sebuah fitur atau tugas telah selesai, AI Coding Agent **WAJIB**:

1. **Menulis Automated Test:** Menyertakan Feature Test atau Unit Test di direktori `tests/` untuk setiap fitur P0 dan P1 yang dikerjakan.
2. **Eksekusi Test Suite:** Menjalankan perintah pengujian secara riil:
```bash
php artisan test
```
3. **Zero Test Failure:** Seluruh skenario pengujian harus berstatus **PASS** (warna hijau).
4. **Pembersihan Kode Debug:** Memastikan tidak ada fungsi debugging tertinggal (`dd()`, `dump()`, `ray()`, `var_dump()`, atau `console.log()`).
5. **Audit File Rahasia:** Memastikan file `.env` asli tidak ter-commit ke dalam repositori git.

---

## 7. Protokol Berhenti Darurat (Emergency Stop Protocol)

Jika AI Coding Agent menemukan kondisi berikut selama implementasi:
- Terdapat pertentangan (*inconsistency*) antara PRD (`DOC-02`), Arsitektur (`DOC-05`), atau Spesifikasi Teknis (`DOC-07`).
- Kebutuhan teknis tidak didukung oleh lingkungan Shared Hosting WarnaHost (misal: memori limit habis saat build asset).
- Muncul permintaan fitur yang berpotensi melanggar kebijakan privasi atau periklanan.

**TINDAKAN WAJIB:**  
**Hentikan penulisan kode pada modul terdampak seketika**, laporkan temuan kendala kepada Product Owner (Dimas Aditya), dan tunggu instruksi atau perubahan resmi pada Architecture Decision Record (ADR).

