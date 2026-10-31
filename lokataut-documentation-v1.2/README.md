# LokaTaut — Complete Product Documentation & Technical Blueprint (v1.2)

> **Status Suite Dokumentasi:** `READY FOR PRODUCT OWNER REVIEW`  
> **Versi Dokumentasi:** 1.2 (Post-Independent Review Remediation)  
> **Terakhir Diperbarui:** 31 Oktober 2026  
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
│ DEC-007 │ Penangguhan Format Popunder (Anti-IVT)   │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ DEC-008 │ Fitur Ganti Password Admin (FR-AUTH-004) │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ DEC-009 │ Hashed Session Token (SHA-256 token_hash)│ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ DEC-010 │ Fallback Vendor Deployment (DOC-13)      │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ DEC-011 │ Turnstile Emergency Bypass Switch        │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ SUITE   │ Keseluruhan 16 Dokumen Spesifikasi v1.2  │ UNDER REVIEW │ 31 Okt 2026  │ Pending Sign│
└─────────┴──────────────────────────────────────────┴──────────────┴──────────────┴─────────────┘
```

---

## 3. Indeks Master Dokumentasi v1.2

Dokumentasi LokaTaut v1.2 disusun dalam hierarki direktori logis:

```
lokataut-documentation-v1.2/
├── README.md                                          # Indeks master & Approval Registry (Berkas ini)
├── REMEDIATION-REPORT.md                              # Laporan detail remediasi temuan audit independen
├── AUDIT-FINDING-RESOLUTION-MATRIX.md                 # Matriks status 11 resolusi temuan (P1–P3)
├── PRE-CODING-CHECKLIST.md                            # Checklist gerbang kesiapan pra-koding 20 item
├── REQUIREMENT-TRACEABILITY-MATRIX.md                 # Matriks keterlacakan 36 test cases 100% dua arah
├── DOCUMENTATION-CHANGELOG.md                         # Log riwayat pembaruan spesifikasi v1.1 -> v1.2
├── OPEN-DECISIONS.md                                  # Log keputusan terbuka untuk Product Owner
├── SOURCE-VERIFICATION-REGISTER.md                    # Bukti verifikasi sumber resmi (NIST, OWASP, ToS)
│
├── 01-product/                                        # Area Produk & Kebutuhan Bisnis
│   ├── 01-product-vision.md                           # DOC-01: Visi, misi, problem statement, use cases
│   ├── 02-prd.md                                      # DOC-02: Single Source of Truth kebutuhan fitur (FR-AUTH-004)
│   └── 03-scope-requirements.md                       # DOC-03: Batasan MVP, RTM, & daftar Out-of-Scope
│
├── 02-analysis/                                       # Area Analisis Alur
│   └── 04-user-system-flow.md                         # DOC-04: Diagram alur sistem & pengguna (Mermaid)
│
├── 03-architecture/                                   # Area Arsitektur & Spesifikasi Teknis
│   ├── 05-system-architecture.md                      # DOC-05: Arsitektur modular monolith & topology
│   ├── 06-database-design.md                          # DOC-06: ERD, kamus data MySQL 8, & token_hash
│   └── 07-technical-specification.md                  # DOC-07: Struktur folder, routing, & service layer
│
├── 04-design/                                         # Area Desain Antarmuka
│   └── 08-ui-ux-specification.md                      # DOC-08: Design tokens, wireframes, profil & label sponsor
│
├── 05-security-monetization/                          # Area Keamanan & Monetisasi
│   ├── 09-security-privacy.md                         # DOC-09: STRIDE model, anti-bypass, hashed token, keyed HMAC
│   └── 10-monetization-specification.md               # DOC-10: Larangan AdSense, isolasi tombol, kandidat Adsterra
│
├── 06-quality/                                        # Area Kualitas & Pengujian
│   ├── 11-seo-performance.md                          # DOC-11: 3-lapis noindex, inode cache & LiteSpeed protection
│   └── 12-testing-qa.md                               # DOC-12: Test registry 36 test cases P0/P1, mocking, & Pest 3
│
├── 07-operations/                                     # Area Operasional & Infrastruktur
│   └── 13-deployment-operations.md                    # DOC-13: Panduan deployment WarnaHost & Fallback Vendor Bundle
│
├── 08-ai-guidelines/                                  # Area Panduan Koding AI
│   └── 14-ai-agent-instructions.md                    # DOC-14: Pedoman mengikat untuk AI Coding Agent
│
├── 09-decisions/                                      # Area Rekam Keputusan Arsitektur
│   └── 15-architecture-decisions.md                   # DOC-15: Architecture Decision Records (ADR 001-014)
│
└── 10-roadmap/                                        # Area Rencana Implementasi
    └── 16-implementation-roadmap.md                   # DOC-16: Roadmap 8 fase pengembangan & backlog task
```

---

## 4. Rangkuman Pilar Arsitektur & Keamanan Kunci v1.2

1. **Framework Modern (Laravel 13.x):** Baseline teknologi modern dengan runtime PHP 8.3+, didukung patch bug dan keamanan resmi hingga Maret 2028.
2. **Kerahasiaan URL Tujuan:** Disimpan dengan enkripsi simetris AES-256-CBC dan **dirahasiakan selama proses safelink berlangsung**. URL baru diserahkan via respon HTTP 302 Location header saat countdown dan Turnstile tervalidasi sukses di server.
3. **Penyimpanan Hashed Session Token (Defense-in-Depth):** Token sesi safelink disimpan di basis data sebagai hash SHA-256 (`token_hash VARCHAR(64)`), sedangkan raw token 32-byte acak hanya dipegang oleh browser pengunjung (OWASP ASVS v4.0 Section 3.2.4).
4. **Validasi Waktu Server-Side & Atomic Token:** Hitung mundur dihitung berdasarkan selisih waktu server (`now() - countdown_started_at >= countdown_seconds`). Manipulasi timer di console peramban ditolak HTTP 422. Transisi status token dieksekusi secara atomic (`UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`) untuk mencegah race condition.
5. **Admin Password & Profile Management (`FR-AUTH-004`):** Administrator tunggal memiliki antarmuka mandiri untuk mengganti kata sandi dengan validasi kata sandi saat ini (`current_password`), validasi kompleksitas kata sandi baru (12+ char), dan rotasi session ID.
6. **Pseudonimisasi Privasi via ANALYTICS_SALT:** Alamat IP pengunjung disamarkan menggunakan `hash_hmac('sha256', $ip, config('services.analytics.salt'))` yang terisolasi dari siklus rotasi `APP_KEY` untuk menjamin konsistensi analitik historis.
7. **Kepatuhan Iklan & Anti Dark-Patterns:** Google AdSense dilarang keras. Penempatan unit iklan kandidat (Adsterra) difokuskan pada Display Banner terlabel jelas `"Advertisement / Iklan Sponsor"` dengan jarak minimal 30px dari tombol aksi. Format popunder ditangguhkan (*suspended*) dan tombol navigasi diisolasi 100% dari skrip iklan pihak ketiga (Anti-IVT).
8. **Strategi Deployment Berdaya Tahan Tinggi (Fallback Deployment):** Menyediakan prosedur rilis cadangan berbasis *Pre-Built Production Bundle* (arsip ZIP yang memuat folder `vendor/` hasil build lingkungan ber-PHP 8.3 identik) untuk memitigasi risiko *Out Of Memory* saat menjalankan Composer di shared hosting CloudLinux WarnaHost.
9. **Keterlacakan Penuh (100% Traceability):** Sebanyak 36 test cases terdaftar lengkap di `DOC-12` dan terpetakan dua arah tanpa celah pada `REQUIREMENT-TRACEABILITY-MATRIX.md`.

---

## 5. Instruksi Bagi AI Coding Agent

- **Wajib Membaca:** [`AGENTS.md`](../AGENTS.md) di root repository sebelum mengeksekusi tugas apapun.
- **Patuhi Fase Roadmap:** Eksekusi tugas secara berurutan mulai dari **Fase 1 (TASK-101)** pada [`DOC-16`](./10-roadmap/16-implementation-roadmap.md). Dilarang melompati fase jika prasyarat fase sebelumnya belum lolos pengujian otomatis.
- **Dilarang Menambah Fitur di Luar Scope:** Jangan menambahkan fitur publik (registrasi, dompet, payout, referral, SaaS multi-tenant).
