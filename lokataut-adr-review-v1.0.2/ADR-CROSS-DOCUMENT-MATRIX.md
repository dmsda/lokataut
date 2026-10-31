# Matriks Keterlacakan Silang Dokumen: 17 ADR — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-MAT-01 |
| **Versi Dokumen** | 1.0.2 (Rekonsiliasi Status Keterpaduan & Pemetaan DEC) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (DOC-01 s/d DOC-16, RTM, & OPEN-DECISIONS) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Technical Documentation Auditor & Lead Systems Analyst |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Tujuan & Metodologi Audit Keterlacakan v1.0.2

Matriks keterlacakan silang (*cross-document traceability matrix*) ini disusun untuk membuktikan secara terukur bahwa seluruh 17 Architecture Decision Records (ADR-001 s/d ADR-017) memiliki relasi dua arah yang utuh terhadap seluruh dokumen baseline `lokataut-documentation-v1.2.2/`.

Pada pemutakhiran Versi 1.0.2:
1. **Penghapusan Pseudo-Relasi DEC:** Menghapus label DEC-012 dari ADR-002, ADR-003, ADR-005, dan ADR-008. Keempatnya diklasifikasikan sebagai `No dedicated DEC relationship (Architectural Baseline)`.
2. **Koreksi Klaim Keterpaduan Objektif:** Mengganti label prematur `100% SELARAS` dengan klasifikasi status terperinci:
   - `DOCUMENTATION ALIGNED`: Spesifikasi dokumen, kebutuhan, dan skema database selaras tanpa konflik teks.
   - `TECHNICAL REVIEW COMPLETED`: Tim peninjau telah menyelesaikan audit teknis dan memberikan rekomendasi.
   - `ENVIRONMENT VERIFICATION PENDING`: Implementasi terblokir bukti pengujian CLI hosting riil (ADR-002, ADR-009).
   - `PRODUCT OWNER APPROVAL PENDING`: Keputusan arsitektur masih menunggu tanda tangan resmi Product Owner.

---

## 2. Tabel Matriks Keterlacakan Silang 17 ADR (v1.0.2)

| ADR ID | Judul Keputusan Arsitektur | Relasi DEC Kanonikal (v1.2.2) | Dokumen Kebutuhan (DOC-01 s/d 03) | Dokumen Arsitektur & UI (DOC-04 s/d 08) | Dokumen Keamanan & Iklan (DOC-09, 10) | Dokumen QA & RTM (DOC-12, RTM) | Dokumen DevOps & Roadmap (DOC-11, 13, 14, 16) | Status Keterpaduan Teknis |
|:---:|---|:---:|---|---|---|---|---|:---:|
| **ADR-001** | Modular Monolith Laravel 13.x | **DEC-001** (Approved) | DOC-02 (PRD §3), DOC-03 (NFR-ARCH-001) | DOC-05 (§2), DOC-07 (§1.1, §2) | DOC-09 (§1) | TEST-DEPLOY-001, TEST-AUTH-001 | DOC-11 (§1), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§2), DOC-16 (Phase 1) | **DOCUMENTATION ALIGNED**<br>*(Prasyarat: Cek PHP 8.3 CLI)* |
| **ADR-002** | MySQL 8.0 sebagai RDBMS Tunggal | *No dedicated DEC relationship* | DOC-02 (PRD §4), DOC-03 (NFR-ARCH-003) | DOC-05 (§3), DOC-06 (Skema DDL), DOC-07 (§4) | DOC-09 (§4) | TEST-DEPLOY-001, TEST-ANA-004 | DOC-11 (§2), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§3), DOC-16 (Phase 1) | **ENVIRONMENT VERIFICATION PENDING**<br>*(Menunggu: mysql -V di cPanel)* |
| **ADR-003** | Blade + Native CSS + Vanilla JS | *No dedicated DEC relationship* | DOC-02 (PRD §5), DOC-03 (NFR-PERF-001) | DOC-04 (§2), DOC-05 (§4), DOC-07 (§6), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§1-§4) | DOC-09 (§6) | TEST-PERF-001, TEST-PERF-002, TEST-SAFE-001 | DOC-14 (Core Web Vitals), DOC-16 (Phase 1, 3) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-004** | Penolakan AdSense & Adsterra Candidate | **DEC-002** (Approved) | DOC-01 (§2), DOC-02 (PRD §6), DOC-03 (FR-ADS-001-004) | DOC-04 (§3), DOC-06 (`ad_slots`), DOC-07 (§5.4) | DOC-09 (§7), DOC-10 (Monetization Spec) | TEST-ADS-001 s/d TEST-ADS-005 | DOC-14 (§3), DOC-16 (Phase 4) | **DOCUMENTATION ALIGNED**<br>*(Prasyarat: Verifikasi Domain Adsterra)* |
| **ADR-005** | Tabel `visitor_sessions` & UA Hash | *No dedicated DEC relationship* | DOC-02 (PRD §3.2), DOC-03 (FR-SAFE-001) | DOC-04 (§2.1), DOC-06 (`visitor_sessions`), DOC-07 (§3.2) | DOC-09 (§2.2), DOC-10 (§2) | TEST-SAFE-001, TEST-SAFE-007, TEST-SAFE-011 | DOC-11 (§3), DOC-14 (§1), DOC-16 (Phase 3) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-006** | Cloudflare Turnstile Mitigasi Bot | **DEC-005 & DEC-011** (Approved) | DOC-02 (PRD §3.3), DOC-03 (FR-SAFE-002) | DOC-04 (§2.2), DOC-06 (`turnstile_verified`), DOC-07 (§3.3) | DOC-09 (§3), DOC-10 (§2) | TEST-SAFE-004, TEST-SAFE-009, TEST-SAFE-010 | DOC-11 (§4), DOC-14 (§2), DOC-16 (Phase 3) | **DOCUMENTATION ALIGNED**<br>*(Prasyarat: Timeout 5s & Mocking)* |
| **ADR-007** | Countdown Server & Atomic Token | **DEC-004** (Approved) | DOC-02 (PRD §3.4), DOC-03 (FR-SAFE-003, 004) | DOC-04 (§2.3), DOC-06 (`completed`), DOC-07 (§3.4) | DOC-09 (§2.3, §2.4) | TEST-SAFE-005, TEST-SAFE-006, TEST-SAFE-007 | DOC-14 (§2), DOC-16 (Phase 3) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-008** | Enkripsi AES-256 Destination URL | *No dedicated DEC relationship* | DOC-02 (PRD §3.1), DOC-03 (FR-LINK-001) | DOC-05 (§3.2), DOC-06 (`links.destination_url`), DOC-07 (§5.2) | DOC-09 (§4.1), DOC-10 (§3) | TEST-LINK-001, TEST-SEC-002 | [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§4), DOC-16 (Phase 2) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-009** | Target WarnaHost Starter Unlimited | **DEC-003** (Approved) | DOC-01 (§3), DOC-02 (PRD §8), DOC-03 (NFR-HOST-001-004) | DOC-05 (§1), DOC-07 (§1.2) | DOC-09 (§8) | TEST-DEPLOY-001 s/d TEST-DEPLOY-004 | DOC-11 (Infrastruktur), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md), DOC-16 (Phase 0, 6) | **ENVIRONMENT VERIFICATION PENDING**<br>*(Menunggu: 7 Perintah Pre-Flight)* |
| **ADR-010** | Retensi 90 Hari & Keyed HMAC IP Hash | **DEC-006** (Approved) | DOC-02 (PRD §7), DOC-03 (FR-ANA-001, NFR-DATA-002) | DOC-05 (§3.3), DOC-06 (`visits.ip_hash`), DOC-07 (§5.5) | DOC-09 (§5.1, §5.2) | TEST-ANA-001, TEST-ANA-007 | DOC-11 (§3), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§5), DOC-16 (Phase 5) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-011** | Hashed Session Token (SHA-256) | **DEC-009** (Approved) | DOC-02 (PRD §3.2), DOC-03 (NFR-SEC-001) | DOC-04 (§2.1), DOC-06 (`token_hash`), DOC-07 (§3.2) | DOC-09 (§2.1) | TEST-SAFE-001, TEST-SEC-001 | DOC-14 (§1), DOC-16 (Phase 3) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-012** | Antarmuka Ganti Password Admin | **DEC-008** (Approved) | DOC-02 (PRD §2.2), DOC-03 (FR-AUTH-004) | DOC-05 (§2.1), DOC-07 (§3.1, §5.1), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§3) | DOC-09 (§1.2) | TEST-AUTH-006, TEST-AUTH-007 | DOC-16 (Phase 1) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-013** | Pre-Built Vendor ZIP Deployment | **DEC-010** (Approved) | DOC-02 (PRD §8.2), DOC-03 (NFR-DEPL-001) | DOC-05 (§1.3), DOC-07 (§1.3) | DOC-09 (§8.2) | TEST-DEPLOY-002, TEST-DEPLOY-003 | DOC-11 (§2), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§2.3), DOC-16 (Phase 7) | **DOCUMENTATION ALIGNED**<br>*(Prasyarat: Keselarasan ABI Build)* |
| **ADR-014** | Suspend Popunder & Tombol Steril | **DEC-007** (Approved) | DOC-02 (PRD §6.2), DOC-03 (FR-ADS-005, NFR-MON-001) | DOC-04 (§3), DOC-07 (§6.2), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§4.2) | DOC-09 (§7.2), DOC-10 (§2, §4) | TEST-ADS-003, TEST-ADS-004 | DOC-14 (§3), DOC-16 (Phase 4) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-015** | Kebijakan Hapus & Retensi Analitik | **DEC-013** (Approved) | DOC-02 (PRD §3.5), DOC-03 (FR-LINK-006, 007) | DOC-05 (§3.4), DOC-06 (§4 Kebijakan Hapus), DOC-07 (§5.2) | DOC-09 (§4.3) | TEST-LINK-006, TEST-LINK-007 | DOC-16 (Phase 2, 5) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-016** | Mandatori ANALYTICS_SALT Mandiri | **DEC-014** (Approved) | DOC-02 (PRD §7.2), DOC-03 (NFR-SEC-006) | DOC-05 (§3.3), DOC-06 (`visits`), DOC-07 (§5.5) | DOC-09 (§5.2) | TEST-ANA-002, TEST-SEC-003 | [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§4.1), DOC-16 (Phase 5) | **TECHNICAL REVIEW COMPLETED**<br>*(PO Approval Pending)* |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot | **DEC-015** (Approved) & **DEC-016** (Review) | DOC-02 (PRD §7.3), DOC-03 (FR-ANA-003, NFR-DATA-003) | DOC-05 (§3.5), DOC-06 (`daily_stats`, `visits`), DOC-07 (§5.5) | DOC-09 (§4.4) | TEST-ANA-004, TEST-LINK-007 | DOC-16 (Phase 5) | **DOCUMENTATION ALIGNED**<br>*(Menunggu Otorisasi DEC-016)* |

---

## 3. Hasil Audit Keterpaduan & Rekonsiliasi Status v1.0.2

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        Rekapitulasi Keterpaduan Status Matriks                         │
├─────────────────────────────────────────┬───────┬──────────────────────────────────────┤
│ Klasifikasi Status Keterpaduan          │ Total │ Daftar ADR Terkait                   │
├─────────────────────────────────────────┼───────┼──────────────────────────────────────┤
│ **TECHNICAL REVIEW COMPLETED**          │   9   │ ADR-003, ADR-005, ADR-007, ADR-008,  │
│ (Audit selesai, siap disetujui PO)      │       │ ADR-010, ADR-011, ADR-012, ADR-014,  │
│                                         │       │ ADR-015, ADR-016                     │
├─────────────────────────────────────────┼───────┼──────────────────────────────────────┤
│ **DOCUMENTATION ALIGNED**               │   6   │ ADR-001 (Kondisi: PHP CLI),          │
│ (Spesifikasi selaras, ada prasyarat)    │       │ ADR-004 (Kondisi: Akun Adsterra),    │
│                                         │       │ ADR-006 (Kondisi: Timeout 5s),       │
│                                         │       │ ADR-013 (Kondisi: ABI Mesin Build),  │
│                                         │       │ ADR-017 (Kondisi: Otorisasi DEC-016) │
├─────────────────────────────────────────┼───────┼──────────────────────────────────────┤
│ **ENVIRONMENT VERIFICATION PENDING**    │   2   │ ADR-002 (Kueri SELECT VERSION()),    │
│ (Terblokir bukti hosting riil pengguna) │       │ ADR-009 (7 Perintah Pre-Flight)      │
├─────────────────────────────────────────┼───────┼──────────────────────────────────────┤
│ **PRODUCT OWNER APPROVAL PENDING**      │  17   │ Seluruh 17 ADR berstatus formal      │
│ (Menunggu pengesahan individual PO)     │       │ PROPOSED / UNDER REVIEW              │
└─────────────────────────────────────────┴───────┴──────────────────────────────────────┘
```

1. **Sinkronisasi Pemetaan DEC:** Tidak ada lagi penyebutan DEC-012 sebagai pseudo-relasi teknis pada ADR-002, ADR-003, ADR-005, dan ADR-008.
2. **Ketiadaan Keputusan Yatim Piatu (Zero Orphan ADRs):** Seluruh 17 ADR terhubung dua arah ke bab spesifikasi teknis dan skenario pengujian QA.
3. **Ketiadaan Kontradiksi Dokumen:** Kontrak routing, nama tabel, kolom, dan atribut cast konsisten 100% lintas repositori.

