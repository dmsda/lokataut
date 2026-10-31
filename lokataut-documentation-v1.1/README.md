# LokaTaut — Complete Product Documentation & Technical Blueprint (v1.1)

> **Status Suite Dokumentasi:** `READY FOR PRODUCT OWNER FINAL REVIEW`  
> **Versi Dokumentasi:** 1.1 (Audit & Consistency Revision)  
> **Terakhir Diperbarui:** 30 Oktober 2026  
> **Target Domain:** `lokataut.com`  
> **Model Bisnis:** Private / Single Owner Monetization  
> **Infrastruktur Target:** Shared Hosting WarnaHost Starter Unlimited (PHP 8.3+, MySQL 8, cPanel, LiteSpeed) + Cloudflare

---

## 1. Pengenalan Proyek (Project Overview)

**LokaTaut** adalah platform pengelolaan tautan dan perantara unduhan privat (*Private Link Management & Safelink Platform*) berbasis web modular monolith dengan **Laravel 13.x (PHP 8.3+)**. Platform ini dikembangkan secara custom dan dioperasikan secara eksklusif oleh **satu pemilik tunggal (single administrator)** untuk mengamankan dan memonetisasi tautan unduhan dari jaringan website pribadinya (seperti aplikasi software, APK Android, template/aset kreatif digital, dokumen, dsb.).

LokaTaut **BUKAN** platform shortlink publik, bukan marketplace periklanan, dan tidak menyediakan registrasi pengguna publik, dompet publisher, atau sistem bagi hasil komisi.

---

## 2. Tabel Registri Persetujuan (Approval Registry)

Sesuai aturan tata kelola proyek, keputusan teknis dan dokumen hanya dinyatakan sah jika telah disetujui secara eksplisit oleh **Product Owner (Dimas Aditya)**:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Official Approval Registry                                   │
├─────────┬──────────────────────────────────────────┬──────────────┬──────────────┬─────────────┤
│ Ref ID  │ Dokumen / Topik Keputusan                │ Status       │ Tanggal      │ Reviewer    │
├─────────┼──────────────────────────────────────────┼──────────────┼──────────────┼─────────────┤
│ DEC-001 │ Baseline Framework: Laravel 13.x (PHP 8.3)│ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-002 │ Status Monetisasi: Candidate Adsterra    │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-003 │ Target Hosting: WarnaHost Starter Unltd. │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-004 │ Durasi Hitung Mundur Default (10 Detik)  │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-005 │ Titik Verifikasi Turnstile (Sebelum Timer)│ APPROVED    │ 30 Okt 2026  │ Dimas A.    │
│ DEC-006 │ Kebijakan Retensi Log Mentah (90 Hari)   │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ SUITE   │ Keseluruhan 16 Dokumen Spesifikasi v1.1  │ UNDER REVIEW │ 30 Okt 2026  │ Pending Sign│
└─────────┴──────────────────────────────────────────┴──────────────┴──────────────┴─────────────┘
```

---

## 3. Indeks Master Dokumentasi v1.1

Dokumentasi LokaTaut v1.1 disusun dalam hierarki direktori logis:

```
lokataut-documentation-v1.1/
├── README.md                                          # Indeks master & Approval Registry
├── DOCUMENTATION-AUDIT-REPORT.md                      # Laporan lengkap audit temuan independen
├── DOCUMENTATION-CHANGELOG.md                         # Log riwayat perubahan spesifikasi v1.0 -> v1.1
├── REQUIREMENT-TRACEABILITY-MATRIX.md                 # Matriks keterlacakan FR/NFR ke Test & Code
├── OPEN-DECISIONS.md                                  # Log keputusan terbuka untuk Product Owner
├── SOURCE-VERIFICATION-REGISTER.md                    # Bukti verifikasi sumber resmi (ToS / Tech)
│
├── 01-product/                                        # Area Produk & Kebutuhan Bisnis
│   ├── 01-product-vision.md                           # DOC-01: Visi, misi, problem statement, use cases
│   ├── 02-prd.md                                      # DOC-02: Single Source of Truth kebutuhan fitur
│   └── 03-scope-requirements.md                       # DOC-03: Batasan MVP, RTM, & daftar Out-of-Scope
│
├── 02-analysis/                                       # Area Analisis Alur
│   └── 04-user-system-flow.md                         # DOC-04: Diagram alur sistem & pengguna (Mermaid)
│
├── 03-architecture/                                   # Area Arsitektur & Spesifikasi Teknis
│   ├── 05-system-architecture.md                      # DOC-05: Arsitektur modular monolith & topology
│   ├── 06-database-design.md                          # DOC-06: ERD, kamus data MySQL 8, & indexing
│   └── 07-technical-specification.md                  # DOC-07: Struktur folder, routing, & service layer
│
├── 04-design/                                         # Area Desain Antarmuka
│   └── 08-ui-ux-specification.md                      # DOC-08: Design tokens, wireframes, & label sponsor
│
├── 05-security-monetization/                          # Area Keamanan & Monetisasi
│   ├── 09-security-privacy.md                         # DOC-09: STRIDE model, anti-bypass, keyed HMAC
│   └── 10-monetization-specification.md               # DOC-10: Larangan AdSense & integrasi Adsterra
│
├── 06-quality/                                        # Area Kualitas & Pengujian
│   ├── 11-seo-performance.md                          # DOC-11: 3-lapis noindex & target Core Web Vitals
│   └── 12-testing-qa.md                               # DOC-12: Test registry P0/P1, mocking, & Pest 3
│
├── 07-operations/                                     # Area Operasional & Infrastruktur
│   └── 13-deployment-operations.md                    # DOC-13: Panduan deployment WarnaHost aman
│
├── 08-ai-guidelines/                                  # Area Panduan Koding AI
│   └── 14-ai-agent-instructions.md                    # DOC-14: Pedoman mengikat untuk AI Coding Agent
│
├── 09-decisions/                                      # Area Rekam Keputusan Arsitektur
│   └── 15-architecture-decisions.md                   # DOC-15: Architecture Decision Records (ADR 001-010)
│
└── 10-roadmap/                                        # Area Rencana Implementasi
    └── 16-implementation-roadmap.md                   # DOC-16: Roadmap 8 fase pengembangan & backlog task
```

---

## 4. Rangkuman Pilar Arsitektur & Keamanan Kunci

1. **Framework Modern (Laravel 13.x):** Menggantikan Laravel 11 yang telah EOL sejak Maret 2026. Didukung penuh hingga Maret 2028 dengan runtime PHP 8.3+.
2. **Kerahasiaan URL Tujuan:** Disimpan dengan enkripsi simetris AES-256-CBC dan **dirahasiakan selama proses safelink berlangsung**. URL baru diserahkan via respon HTTP 302 Location header saat countdown dan Turnstile tervalidasi sukses di server.
3. **Validasi Waktu Server-Side & Atomic Token:** Hitung mundur dihitung berdasarkan selisih waktu server (`now() - countdown_started_at >= countdown_seconds`). Manipulasi timer di console peramban ditolak HTTP 422. Transisi status token dieksekusi secara atomic (`UPDATE visitor_sessions SET completed = 1 WHERE token = ? AND completed = 0`) untuk mencegah race condition.
4. **Pseudonimisasi Privasi via Keyed HMAC:** Alamat IP pengunjung disamarkan menggunakan `hash_hmac('sha256', $ip, config('app.analytics_salt'))` untuk menghormati privasi pengunjung tanpa menyimpan IP mentah di log analitik.
5. **Mitigasi Stored XSS:** Pengubahan kode iklan `ad_code` dibatasi hanya untuk Super Admin dengan pencatatan di `security_logs` dan proteksi header Content Security Policy (CSP).
6. **Kepatuhan Iklan & Anti Dark-Patterns:** Google AdSense dilarang keras. Penempatan unit iklan kandidat (Adsterra) wajib menyertakan label visual `"Advertisement / Iklan Sponsor"` dan berjarak aman minimal 30 pixel dari tombol unduhan untuk mencegah klik tidak sengaja.
7. **Isolasi Deployment Shared Hosting:** Seluruh source code aplikasi diletakkan secara aman di `/home/username/lokataut/` (di luar `public_html`), sehingga file `.env` dan kode privat tidak mungkin terekspos ke publik.

---

## 5. Instruksi Bagi AI Coding Agent

- **Wajib Membaca:** [`AGENTS.md`](../AGENTS.md) di root repository sebelum mengeksekusi tugas apapun.
- **Patuhi Fase Roadmap:** Eksekusi tugas secara berurutan mulai dari **Fase 1 (TASK-101)** pada [`DOC-16`](./10-roadmap/16-implementation-roadmap.md). Dilarang melompati fase jika prasyarat fase sebelumnya belum lolos pengujian otomatis.
- **Dilarang Menambah Fitur di Luar Scope:** Jangan menambahkan fitur publik (registrasi, dompet, payout, referral, SaaS multi-tenant).

