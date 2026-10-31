# Scope & Requirements Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-03 |
| **Versi** | 1.2.2 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Lead Systems Analyst & Technical Documentation Specialist |
| **Target Pembaca** | Product Owner, AI Coding Agent, QA Engineer |
| **Tujuan** | Menetapkan batasan ruang lingkup proyek secara tegas (In-Scope vs Out-of-Scope), menguraikan batasan MVP dan Post-MVP, serta menyediakan matriks keterlacakan (*traceability*) antar dokumen teknis. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-01 — Product Vision & Strategy](./01-product-vision.md) | Visi produk, positioning, dan prinsip arsitektural |
| [DOC-02 — Product Requirements Document](./02-prd.md) | Sumber daftar kebutuhan fungsional (FR) dan aturan bisnis (BR) |
| [DOC-04 — User Flow & System Flow](../02-analysis/04-user-system-flow.md) | Pemetaan alur sistem dan status pengguna |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Pemetaan komponen arsitektur Laravel 13 |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Pemetaan entitas basis data MySQL 8 |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Standar teknis implementasi kode |
| [DOC-08 — UI/UX Specification](../04-design/08-ui-ux-specification.md) | Pemetaan antarmuka pengguna |
| [DOC-09 — Security & Privacy](../05-security-monetization/09-security-privacy.md) | Pemetaan standar perlindungan data |
| [DOC-12 — Testing Strategy & QA Plan](../06-quality/12-testing-qa.md) | Pemetaan skenario pengujian otomatis |
| [DOC-16 — Implementation Roadmap](../10-roadmap/16-implementation-roadmap.md) | Pemetaan tugas backlog pengembangan |

---

## 1. Project Scope Boundaries

### 1.1 LokaTaut ADALAH (In-Scope Core)
- Platform privat pengelolaan link dan perantara download (*private safelink*) milik satu orang admin (single owner).
- Aplikasi web Modular Monolith berbasis framework modern **Laravel 13.x (PHP 8.3+)** dan **MySQL 8.0+**.
- Pengalihan tautan unduhan melalui halaman safelink yang dilindungi Cloudflare Turnstile dan countdown 10 detik.
- Halaman safelink bersih dengan penempatan slot iklan dinamis dari jaringan mitra kandidat (Adsterra/Monetag).
- Panel dashboard admin terproteksi untuk membuat, mengedit, menonaktifkan, dan memantau analitik trafik link.
- Sistem yang dioptimalkan khusus untuk Shared Hosting WarnaHost Starter Unlimited (1 vCPU Core, 2 GB RAM, cPanel/LiteSpeed).

### 1.2 LokaTaut SECARA MUTLAK BUKAN (Explicitly Out-of-Scope)
AI Coding Agent **DILARANG KERAS** mengimplementasikan fitur-fitur berikut tanpa persetujuan tertulis dari Product Owner:
- ❌ Registrasi pengguna publik / multi-user signup.
- ❌ Dashboard publisher eksternal atau portal pengiklan (*advertiser marketplace*).
- ❌ Dompet saldo pengguna (*user wallet / balance / withdrawal*).
- ❌ Payment gateway atau sistem penarikan dana (*payout automation*).
- ❌ Sistem komisi referral, affiliate, atau bagi hasil (*revenue sharing*).
- ❌ Arsitektur SaaS multi-tenant dengan pemisahan database per penyewa.
- ❌ Public API key management untuk pihak ketiga.
- ❌ Penempatan unit iklan Google AdSense pada halaman safelink/countdown (kepatuhan terhadap Google Publisher Policies untuk mitigasi sanksi akun).
- ❌ Iklan popunder/direct-link yang mengikat event klik tombol aksi safelink (*click hijacking*). Jarak pemisah 30px diterapkan sebagai internal engineering safeguard (WCAG 2.5.5).
- ❌ Rekayasa HTTP Referrer organik palsu (Google Referrer Spoofing).
- ❌ Aplikasi mobile native (iOS / Android).

---

## 2. Pengelompokan Scope: MVP vs Post-MVP

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Feature Scope Categorization                    │
├───────────────────────────────────┬────────────────────────────────────┤
│ MVP Scope (Phase 1 s/d Phase 6)   │ Post-MVP Scope (Future Iterations) │
├───────────────────────────────────┼────────────────────────────────────┤
│ • Admin Authentication & Logout   │ • Bulk Link Creation via CSV       │
│ • Admin Password & Profile Update │ • Export Data Analitik ke CSV/XLS  │
│ • Single Session Enforcement      │ • Generator QR Code per Link       │
│ • Dashboard Ringkasan Metrik      │ • Peta Negara Interaktif Global    │
│ • CRUD Shortlink & Custom Slug    │ • A/B Testing Penempatan Slot Iklan│
│ • Kebijakan Soft & Hard Delete    │ • Link Grouping / Tagging Kategori │
│ • Enkripsi AES-256 Destination URL│ • Deteksi Bot Tingkat Lanjut       │
│ • Source Website Tagging          │ • Notifikasi Telegram saat Server  │
│ • Safelink Page + Turnstile + 10s │   Mengalami Lonjakan Trafik        │
│ • Hashed Session Token (SHA-256)  │ • Evaluasi Iklan Popunder (setelah │
│ • Atomic One-Time 302 Redirect    │   persetujuan akun & domain riil)  │
│ • Ad Slot CRUD (Banner Display)   │                                    │
│ • Analitik Tayangan, Lolos, Selesai│                                    │
│ • Agregasi Harian (Idempoten &    │                                    │
│   Soft-delete-aware)              │                                    │
│ • Pruning Cron (Session, Visits,  │                                    │
│   dan Inode Cache)                │                                    │
│ • Keyed HMAC IP (Mandatori        │                                    │
│   ANALYTICS_SALT tanpa fallback)  │                                    │
│ • Turnstile Emergency Bypass Mode │                                    │
│ • Proteksi CSRF, XSS, SQLi, DoS   │                                    │
│ • Fallback Pre-built Deployment   │                                    │
└───────────────────────────────────┴────────────────────────────────────┘
```

---

## 3. Matriks Traceability Kebutuhan ke Komponen Sistem

Tabel ini merujuk ke [REQUIREMENT-TRACEABILITY-MATRIX.md](../REQUIREMENT-TRACEABILITY-MATRIX.md) untuk memastikan tidak ada fitur tanpa implementasi teknis dan test case:

| Req ID | Kategori | Fitur Utama | Modul Arsitektur | Tabel DB | Test ID | Prioritas |
|---|---|---|---|---|---|:---:|
| `FR-AUTH-001` | Auth | Admin Login & Rate Limiter | `Auth\LoginController` | `admins` | `TEST-AUTH-001` | P0 |
| `FR-AUTH-002` | Auth | Admin Logout & Session Purge | `Auth\LoginController` | `admins` | `TEST-AUTH-002` | P0 |
| `FR-AUTH-003` | Auth | Single Session Enforcement | `Middleware\EnforceSingleSession` | `admins` | `TEST-AUTH-003` | P0 |
| `FR-AUTH-004` | Auth | Admin Password & Profile Management | `Admin\ProfileController` | `admins` | `TEST-AUTH-004` | P1 |
| `FR-DASH-001` | Dashboard | Ringkasan 4 Kartu Metrik | `Admin\DashboardController` | `links`, `daily_stats` | `TEST-DASH-001` | P0 |
| `FR-DASH-002` | Dashboard | Grafik Tren Kunjungan 7 Hari | `Services\AnalyticsService` | `daily_stats` | `TEST-DASH-002` | P1 |
| `FR-LINK-001` | Link | Generator Slug Acak 6-8 Char | `Services\SlugGeneratorService` | `links` | `TEST-LINK-001` | P0 |
| `FR-LINK-002` | Link | Validasi Custom Slug Unik | `Admin\LinkController` | `links` | `TEST-LINK-002` | P1 |
| `FR-LINK-003` | Link | CRUD & Enkripsi Destination URL | `Admin\LinkController` | `links` | `TEST-LINK-003` | P0 |
| `FR-LINK-004` | Link | Status Toggle & Expiration | `Admin\LinkController` | `links` | `TEST-LINK-004` | P0 |
| `FR-LINK-005` | Link | Tagging Domain Asal Sumber | `Admin\LinkController` | `links` | `TEST-LINK-005` | P1 |
| `FR-SAFE-001` | Safelink | Render Halaman Safelink & Hashed Token | `Public\SafelinkController` | `visitor_sessions` | `TEST-SAFE-001` | P0 |
| `FR-SAFE-002` | Safelink | Verifikasi Cloudflare Turnstile | `Services\TurnstileService` | `visitor_sessions` | `TEST-SAFE-002` | P0 |
| `FR-SAFE-003` | Safelink | Server-Side Elapsed Countdown | `Public\SafelinkController` | `visitor_sessions` | `TEST-SAFE-003` | P0 |
| `FR-SAFE-004` | Safelink | Atomic One-Time Redirect 302 | `Services\SafelinkService` | `visitor_sessions` | `TEST-SAFE-004` | P0 |
| `FR-SAFE-005` | Safelink | UI Link Expired (410) & Invalid (404)| `Public\SafelinkController` | `links` | `TEST-SAFE-005` | P0 |
| `FR-ADS-001` | Ads | Manajemen Slot Iklan Banner Display| `Admin\AdSlotController` | `ad_slots` | `TEST-ADS-001` | P0 |
| `FR-ADS-002` | Ads | Injeksi Kode Iklan Aman & Label | `Admin\AdSlotController` | `ad_slots` | `TEST-ADS-002` | P0 |
| `FR-ANAL-001` | Analytics | Event Tracking & Keyed HMAC IP | `Services\VisitRecorderService` | `visits` | `TEST-ANAL-001` | P0 |
| `FR-ANAL-002` | Analytics | Perhitungan Completion Rate (%)| `Services\AnalyticsService` | `daily_stats` | `TEST-ANAL-002` | P1 |
| `FR-ANAL-003` | Analytics | Scheduled Task Agregasi Harian | `Commands\AggregateDailyStatsCommand` | `daily_stats` | `TEST-CLI-001` | P1 |
| `FR-SET-001` | Settings | Pengaturan Umum & Timezone | `Admin\SettingsController` | `settings` | `TEST-SET-001` | P1 |
| `FR-SET-002` | Settings | Pengaturan API Key Turnstile | `Admin\SettingsController` | `settings` | `TEST-SET-002` | P0 |
| `FR-SET-003` | Settings | Turnstile Emergency Bypass Mode| `Admin\SettingsController` | `settings` | `TEST-SET-003` | P1 |
| `FR-SEC-001` | Security | Pencatatan Audit Log Insiden | `Services\SecurityLogService` | `security_logs` | `TEST-SEC-001` | P1 |

---

## 4. Change Control Process

Jika terjadi perubahan kebutuhan atau kendala teknis tak terduga selama fase pengembangan:
1. **Pemberhentian Sementara:** AI Coding Agent wajib menghentikan penulisan kode pada modul terdampak.
2. **Klarifikasi Product Owner:** Masalah dilaporkan secara tertulis di dokumen [`OPEN-DECISIONS.md`](../OPEN-DECISIONS.md).
3. **Dokumentasi ADR:** Keputusan baru dicatat dalam format Architecture Decision Record di [`15-architecture-decisions.md`](../09-decisions/15-architecture-decisions.md).
4. **Pembaruan RTM & Status:** Matriks keterlacakan dan changelog diperbarui sebelum penulisan kode dilanjutkan.

