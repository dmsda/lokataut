# LokaTaut — Complete Product Documentation & Technical Blueprint (v1.2.1)

> **Status Suite Dokumentasi:** `READY FOR PRODUCT OWNER FINAL REVIEW`  
> **Versi Dokumentasi:** 1.2.1 (Final Consistency & Traceability Remediation)  
> **Terakhir Diperbarui:** 31 Oktober 2026  
> **Target Domain:** `lokataut.com`  
> **Model Bisnis:** Private / Single Owner Monetization  
> **Infrastruktur Target:** Shared Hosting WarnaHost Starter Unlimited (PHP 8.3+, MySQL 8, cPanel, LiteSpeed) + Cloudflare

---

## 1. Pengenalan Proyek (Project Overview)

**LokaTaut** adalah platform pengelolaan tautan dan perantara unduhan privat (*Private Link Management & Safelink Platform*) berbasis web modular monolith dengan framework modern **Laravel 13.x (PHP 8.3+)**. Platform ini dikembangkan secara custom dan dioperasikan secara eksklusif oleh **satu pemilik tunggal (single administrator)** untuk mengamankan dan memonetisasi tautan unduhan dari jaringan website pribadinya (seperti aplikasi software, APK Android, template/aset kreatif digital, dokumen, dsb.).

LokaTaut **BUKAN** platform shortlink publik, bukan marketplace periklanan, dan tidak menyediakan registrasi pengguna publik, dompet publisher, sistem referral, atau bagi hasil komisi.

---

## 2. Tabel Registri Persetujuan (Approval Registry)

Sesuai aturan tata kelola proyek, keputusan teknis dan dokumen hanya dinyatakan sah jika telah disetujui secara eksplisit oleh **Product Owner (Dimas Aditya)**. AI Coding Agent **DILARANG** memberikan persetujuan fiktif atas nama Product Owner:

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
│ DEC-012 │ Approval Formal ADR-001 s/d ADR-016      │ UNDER REVIEW │ 31 Okt 2026  │ Pending Sign│
│ DEC-013 │ Kebijakan Hapus Data & Snapshot Slug     │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ DEC-014 │ Mandatori ANALYTICS_SALT Tanpa Fallback  │ PROPOSED     │ 31 Okt 2026  │ Pending Sign│
│ SUITE   │ Keseluruhan 16 Dokumen Spesifikasi v1.2.1│ UNDER REVIEW │ 31 Okt 2026  │ Pending Sign│
└─────────┴──────────────────────────────────────────┴──────────────┴──────────────┴─────────────┘
```

---

## 3. Indeks Master Dokumentasi v1.2.1

Dokumentasi LokaTaut v1.2.1 disusun dalam hierarki direktori logis:

```
lokataut-documentation-v1.2.1/
├── README.md                                          # Indeks master & Approval Registry (Berkas ini)
├── FINAL-CONSISTENCY-REPORT.md                        # Laporan konsistensi & audit final v1.2.1
├── REMEDIATION-REPORT.md                              # Laporan remediasi temuan audit
├── AUDIT-FINDING-RESOLUTION-MATRIX.md                 # Matriks status 11 resolusi temuan audit
├── PRE-CODING-CHECKLIST.md                            # Checklist gerbang kesiapan pra-koding 20 item
├── REQUIREMENT-TRACEABILITY-MATRIX.md                 # Matriks keterlacakan 55 test cases 100% dua arah
├── DOCUMENTATION-CHANGELOG.md                         # Log riwayat pembaruan spesifikasi
├── OPEN-DECISIONS.md                                  # Log keputusan terbuka untuk Product Owner
├── SOURCE-VERIFICATION-REGISTER.md                    # Bukti verifikasi sumber resmi (NIST, OWASP, ToS)
│
├── 01-product/                                        # Area Produk & Kebutuhan Bisnis
│   ├── 01-product-vision.md                           # DOC-01: Visi, misi, problem statement, use cases
│   ├── 02-prd.md                                      # DOC-02: Single Source of Truth kebutuhan fitur & aturan bisnis
│   └── 03-scope-requirements.md                       # DOC-03: Batasan MVP, RTM, & daftar Out-of-Scope
│
├── 02-analysis/                                       # Area Analisis Alur
│   └── 04-user-system-flow.md                         # DOC-04: Diagram alur sistem, pengguna, & penghapusan (Mermaid)
│
├── 03-architecture/                                   # Area Arsitektur & Spesifikasi Teknis
│   ├── 05-system-architecture.md                      # DOC-05: Arsitektur modular monolith & topology
│   ├── 06-database-design.md                          # DOC-06: ERD, kamus data MySQL 8, & kebijakan retensi
│   └── 07-technical-specification.md                  # DOC-07: Struktur folder, routing, command, & service layer
│
├── 04-design/                                         # Area Desain Antarmuka
│   └── 08-ui-ux-specification.md                      # DOC-08: Design tokens, wireframes, profil & jarak aman 30px
│
├── 05-security-monetization/                          # Area Keamanan & Monetisasi
│   ├── 09-security-privacy.md                         # DOC-09: STRIDE model, anti-bypass, hashed token, keyed HMAC
│   └── 10-monetization-specification.md               # DOC-10: Larangan AdSense, isolasi tombol, kandidat Adsterra
│
├── 06-quality/                                        # Area Kualitas & Pengujian
│   ├── 11-seo-performance.md                          # DOC-11: 3-lapis noindex, inode cache & LiteSpeed protection
│   └── 12-testing-qa.md                               # DOC-12: Test registry 55 test cases P0/P1, Pest 3, & QA gate
│
├── 07-operations/                                     # Area Operasional & Infrastruktur
│   └── 13-deployment-operations.md                    # DOC-13: Panduan deployment WarnaHost, Pre-flight SSH, & Rollback
│
├── 08-ai-guidelines/                                  # Area Panduan Koding AI
│   └── 14-ai-agent-instructions.md                    # DOC-14: Pedoman mengikat untuk AI Coding Agent
│
├── 09-decisions/                                      # Area Rekam Keputusan Arsitektur
│   └── 15-architecture-decisions.md                   # DOC-15: ADR-001 s/d ADR-016 (Lengkap dengan konteks & konsekuensi)
│
└── 10-roadmap/                                        # Area Rencana Implementasi
    └── 16-implementation-roadmap.md                   # DOC-16: 8 Fase roadmap, 28 task backlog, & Acceptance Criteria
```

---

## 4. Empat Pilar Utama Keandalan Teknis LokaTaut v1.2.1

1. **Keamanan Tanpa Kompromi (Security by Design):** Token sesi safelink disimpan dalam bentuk digest kriptografis SHA-256 (`token_hash`), transisi status redirect atomic, URL tujuan tidak pernah dikirim sebelum countdown selesai, dan salt pseudonimisasi IP terisolasi penuh via `ANALYTICS_SALT` tanpa fallback ke `APP_KEY` (NIST SP 800-57).
2. **Kepatuhan Kebijakan Periklanan (Monetization & Anti-IVT):** Google AdSense tidak digunakan pada safelink sesuai kebijakan Google Publisher Policies. Monetisasi menggunakan banner display terlabel jelas berjarak aman minimal 30px dari elemen aksi (internal safeguard mengacu WCAG 2.5.5). Tombol navigasi aksi (`#proceed-btn`) disterilkan secara mutlak dari script iklan, dan format popunder ditangguhkan pada MVP.
3. **Integritas Basis Data & Retensi Terukur:** Operasi penghapusan default adalah Soft Delete yang menjaga keutuhan 100% data analitik. Operasi Hard Delete menerapkan `ON DELETE SET NULL` pada `visits` dan `daily_stats` dengan snapshot `link_slug` historis tetap tersimpan. Retensi data mentah `visits` dibatasi 90 hari, sesi dibersihkan 24 jam, dan `daily_stats` permanen.
4. **Kompatibilitas Shared Hosting Realistis:** Arsitektur modular monolith dioptimalkan untuk batasan 1 vCPU dan 2 GB RAM WarnaHost. Dilengkapi direktif bypass cache LiteSpeed, scheduler mingguan pembersihan file cache usang untuk melindungi kuota Inode cPanel, prosedur mitigasi memori via *Fallback Pre-Built Vendor Deployment*, serta backup mandatori `mysqldump` sebelum migrasi.
