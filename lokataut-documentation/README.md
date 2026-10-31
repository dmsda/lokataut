# LokaTaut — Complete Product Documentation & Technical Blueprint

> **Status Dokumentasi:** `UNDER REVIEW`  
> **Terakhir Diperbarui:** 30 Oktober 2026  
> **Target Domain:** `lokataut.com`  
> **Model Bisnis:** Private / Single Owner Monetization  
> **Infrastruktur Target:** WarnaHost Starter Unlimited (PHP 8.3+, MySQL 8, cPanel, LiteSpeed) + Cloudflare

---

## 1. Pengenalan Proyek (Project Introduction)

**LokaTaut** adalah platform pengelolaan tautan dan safelink privat (*Private Link Management & Safelink Platform*) yang dikembangkan secara custom untuk kebutuhan pribadi satu pemilik (single owner/administrator). Platform ini digunakan untuk mengamankan dan memonetisasi tautan unduhan dari website milik sendiri (software/aplikasi, file APK Android, template/aset kreatif, dokumen digital, dsb.).

Setiap pengunjung yang mengklik tautan unduhan akan melewati halaman safelink bersih yang dilengkapi dengan:
- **Cloudflare Turnstile** untuk verifikasi bot tanpa puzzle gambar yang menyulitkan.
- **Hitung Mundur (Countdown 10 Detik)** dengan validasi waktu ketat di sisi server (*Server-Side Elapsed Time Enforcement*).
- **Slot Iklan Teroptimasi** dari mitra periklanan resmi yang mengizinkan format safelink (**Adsterra** sebagai primer, **Monetag** sebagai sekunder).
- **Pengalihan Aman (HTTP 302 Found)** langsung ke file unduhan asli tanpa pernah membocorkan URL tujuan di sisi client peramban sebelum proses selesai.

---

## 2. Checklist Kesiapan Sebelum Coding (Documentation Readiness)

Sesuai arahan Product Owner, **tahap penulisan kode sumber (coding) DILARANG dimulai** sebelum checklist kesiapan dokumentasi berikut ditinjau dan disetujui secara resmi.

```
LokaTaut Documentation Readiness
[====================> ] 100% Generated (Menunggu Persetujuan Final)

[x] Product Vision dan Business Model terdokumentasi (DOC-01)
[x] PRD dan Scope terdefinisi lengkap (DOC-02 & DOC-03)
[x] User Flow dan System Flow termodelkan dalam Mermaid (DOC-04)
[x] Architecture dan Database Design terspesifikasi detail (DOC-05 & DOC-06)
[x] Technical Specification & Struktur Folder siap (DOC-07)
[x] UI/UX Specification & Wireframe tersedia (DOC-08)
[x] Security dan Monetization Specification tervalidasi (DOC-09 & DOC-10)
[x] SEO dan Performance Specification terkunci noindex (DOC-11)
[x] Testing dan QA Plan mencakup skenario bypass (DOC-12)
[x] Deployment dan Operations Guide untuk WarnaHost tersedia (DOC-13)
[x] AI Agent Instructions (DOC-14) & ADR (DOC-15) tersedia
[x] Implementation Roadmap & Backlog bertahap siap dieksekusi (DOC-16)
[x] Operational Rules AGENTS.md aktif di root repository
```

---

## 3. Indeks & Hierarki Dokumen (Documentation Index)

Dokumentasi LokaTaut disusun dalam 10 folder fungsional berurutan:

```
lokataut-documentation/
├── README.md                                          # Indeks utama & panduan dokumentasi
│
├── 01-product/
│   ├── 01-product-vision.md                           # DOC-01: Visi, misi, problem statement, use case
│   ├── 02-prd.md                                      # DOC-02: Single Source of Truth kebutuhan fitur
│   └── 03-scope-requirements.md                       # DOC-03: Batasan MVP, traceability matrix, out-of-scope
│
├── 02-analysis/
│   └── 04-user-system-flow.md                         # DOC-04: Diagram Mermaid seluruh alur sistem & user
│
├── 03-architecture/
│   ├── 05-system-architecture.md                      # DOC-05: Desain arsitektur modular monolith & layer
│   ├── 06-database-design.md                          # DOC-06: ERD, kamus data, tipe data, dan strategi indexing
│   └── 07-technical-specification.md                  # DOC-07: Struktur folder, routing, service, dan form request
│
├── 04-design/
│   └── 08-ui-ux-specification.md                      # DOC-08: Design tokens, wireframe safelink, dan dashboard admin
│
├── 05-security-monetization/
│   ├── 09-security-privacy.md                         # DOC-09: STRIDE threat model, anti-bypass timer, enkripsi AES-256
│   └── 10-monetization-specification.md               # DOC-10: Peringatan larangan AdSense & integrasi Adsterra
│
├── 06-quality/
│   ├── 11-seo-performance.md                          # DOC-11: Kebijakan noindex safelink & target Core Web Vitals
│   └── 12-testing-qa.md                               # DOC-12: Test cases P0/P1, mocking Turnstile, automated tests
│
├── 07-operations/
│   └── 13-deployment-operations.md                    # DOC-13: Panduan deployment WarnaHost (folder di luar public_html)
│
├── 08-ai-guidelines/
│   └── 14-ai-agent-instructions.md                    # DOC-14: Pedoman mengikat untuk AI Coding Agent
│
├── 09-decisions/
│   └── 15-architecture-decisions.md                   # DOC-15: Architecture Decision Records (ADR-001 s/d ADR-010)
│
└── 10-roadmap/
    └── 16-implementation-roadmap.md                   # DOC-16: Roadmap 8 fase pengembangan & task backlog
```

---

## 4. Log Keputusan Penting (Decision Log)

### 4.1 Keputusan yang Telah Disepakati (Approved Decisions)
1. **Framework & Bahasa:** Laravel 11.x berjalan pada PHP 8.3+ murni (Strict Types enabled).
2. **Database Engine:** MySQL 8.0+ dengan engine storage InnoDB.
3. **Frontend Architecture:** Laravel Blade Views, Semantic HTML5, Native CSS3 (CSS Variables), dan Vanilla JavaScript ES6+. Menolak SPA (React/Vue) dan build step berat di runtime shared hosting.
4. **Kebijakan Jaringan Iklan:** Menolak keras Google AdSense pada halaman safelink/countdown untuk melindungi akun dari pemblokiran; memilih **Adsterra** sebagai mitra primer ($5 min payout, approval instan).
5. **Keamanan Safelink:** Validasi countdown wajib dievaluasi di sisi server (*Server-Side Timestamp Delta Check*) untuk mencegah bypass via console JavaScript di peramban. URL tujuan dienkripsi simetris AES-256 di basis data.
6. **Mitigasi Bot:** Menggunakan Cloudflare Turnstile sebelum timer berjalan.
7. **Infrastruktur Produksi:** Shared Hosting WarnaHost Starter Unlimited (~Rp 79.000/bln) dengan proteksi edge Cloudflare. Folder aplikasi diletakkan secara aman **di luar `public_html`**.
8. **Retensi Data:** Data mentah `visits` disimpan 90 hari sebelum dipruning, statistik diringkas permanen ke tabel `daily_stats`.

---

## 5. Panduan Bagi AI Coding Agent (Instructions for AI Agent)

Jika Anda adalah AI Coding Agent yang bekerja pada repositori ini:

1. **Wajib Membaca `AGENTS.md` di Root:** File ini berisi aturan operasional yang tidak boleh dilanggar.
2. **Jangan Mengubah Scope:** Dilarang mengimplementasikan fitur publik seperti registrasi member, dompet saldo, payment gateway, multi-tenant SaaS, atau revenue sharing. LokaTaut adalah platform milik satu admin tunggal.
3. **Ikuti Roadmap Bertahap (`DOC-16`):** Mulai pekerjaan dari **Fase 1 (TASK-101)**. Jangan melompati fase jika prasyarat fase sebelumnya belum selesai.
4. **Disiplin Pengujian:** Tulis Feature Test untuk setiap fitur P0/P1 dan pastikan `php artisan test` berstatus **PASS** sebelum menyatakan tugas selesai (*Definition of Done*).
5. **Protokol Berhenti:** Jika ada keraguan atau konflik spesifikasi, hentikan pekerjaan dan minta klarifikasi dari Product Owner (Dimas Aditya).

