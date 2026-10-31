# Matriks Keterlacakan Silang Dokumen: 17 ADR — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-MAT-01 |
| **Versi Dokumen** | 1.0.1 (Remediasi Pemetaan DEC & Keterlacakan Kanonikal) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (DOC-01 s/d DOC-16, RTM, & OPEN-DECISIONS) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Technical Documentation Auditor & Lead Systems Analyst |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Tujuan & Metodologi Audit Keterlacakan v1.0.1

Matriks keterlacakan silang (*cross-document traceability matrix*) ini disusun untuk membuktikan secara terukur bahwa seluruh 17 Architecture Decision Records (ADR-001 s/d ADR-017) memiliki relasi dua arah yang utuh terhadap seluruh dokumen baseline `lokataut-documentation-v1.2.2/`.

Pada pemutakhiran Versi 1.0.1:
1. **Penyelarasan Pemetaan DEC:** Setiap ADR dipetakan secara akurat ke nomor keputusan produk kanonikal pada [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md), memperbaiki ketidaksesuaian nomor DEC yang ditemukan pada versi 1.0.
2. **Ketercakupan Dokumen Tanpa Celah:** Memverifikasi korelasi dari dokumen visi (DOC-01), PRD (DOC-02), ruang lingkup (DOC-03), arsitektur (DOC-04 s/d DOC-08), keamanan & monetisasi (DOC-09, DOC-10), operasi & deployment (DOC-11, DOC-13, DOC-14), rencana pengujian (DOC-12, RTM), hingga roadmap (DOC-16).
3. **Catatan Validasi Keterpaduan:** Matriks mencatat status keselarasan teknis secara objektif, termasuk kondisi prasyarat yang masih terbuka (seperti eksekusi terminal pre-flight hosting untuk ADR-002 dan ADR-009).

---

## 2. Tabel Matriks Keterlacakan Silang 17 ADR (v1.0.1)

| ADR ID | Judul Keputusan Arsitektur | Relasi DEC Kanonikal (v1.2.2) | Dokumen Kebutuhan (DOC-01 s/d 03) | Dokumen Arsitektur & UI (DOC-04 s/d 08) | Dokumen Keamanan & Iklan (DOC-09, 10) | Dokumen QA & RTM (DOC-12, RTM) | Dokumen DevOps & Roadmap (DOC-11, 13, 14, 16) | Status Keterpaduan Teknis |
|:---:|---|:---:|---|---|---|---|---|:---:|
| **ADR-001** | Modular Monolith Laravel 13.x | **DEC-001** (Approved) | DOC-02 (PRD §3), DOC-03 (NFR-ARCH-001) | DOC-05 (§2), DOC-07 (§1.1, §2) | DOC-09 (§1) | TEST-DEPLOY-001, TEST-AUTH-001 | DOC-11 (§1), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§2), DOC-16 (Phase 1) | **SELARAS (Kondisi: Cek PHP CLI)** |
| **ADR-002** | MySQL 8.0 sebagai RDBMS Tunggal | *Arsitektur Umum* | DOC-02 (PRD §4), DOC-03 (NFR-ARCH-003) | DOC-05 (§3), DOC-06 (Skema DDL), DOC-07 (§4) | DOC-09 (§4) | TEST-DEPLOY-001, TEST-ANA-004 | DOC-11 (§2), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§3), DOC-16 (Phase 1) | **TERBLOKIR BUKTI HOSTING** |
| **ADR-003** | Blade + Native CSS + Vanilla JS | *Arsitektur Umum* | DOC-02 (PRD §5), DOC-03 (NFR-PERF-001) | DOC-04 (§2), DOC-05 (§4), DOC-07 (§6), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§1-§4) | DOC-09 (§6) | TEST-PERF-001, TEST-PERF-002, TEST-SAFE-001 | DOC-14 (Core Web Vitals), DOC-16 (Phase 1, 3) | **SELARAS 100%** |
| **ADR-004** | Penolakan AdSense & Adsterra Candidate | **DEC-002** (Approved) | DOC-01 (§2), DOC-02 (PRD §6), DOC-03 (FR-ADS-001-004) | DOC-04 (§3), DOC-06 (`ad_slots`), DOC-07 (§5.4) | DOC-09 (§7), DOC-10 (Monetization Spec) | TEST-ADS-001 s/d TEST-ADS-005 | DOC-14 (§3), DOC-16 (Phase 4) | **SELARAS (Kondisi: Akun/Domain)** |
| **ADR-005** | Tabel `visitor_sessions` & UA Hash | *Arsitektur Umum* | DOC-02 (PRD §3.2), DOC-03 (FR-SAFE-001) | DOC-04 (§2.1), DOC-06 (`visitor_sessions`), DOC-07 (§3.2) | DOC-09 (§2.2), DOC-10 (§2) | TEST-SAFE-001, TEST-SAFE-007, TEST-SAFE-011 | DOC-11 (§3), DOC-14 (§1), DOC-16 (Phase 3) | **SELARAS 100%** |
| **ADR-006** | Cloudflare Turnstile Mitigasi Bot | **DEC-005 & DEC-011** (Approved) | DOC-02 (PRD §3.3), DOC-03 (FR-SAFE-002) | DOC-04 (§2.2), DOC-06 (`turnstile_verified`), DOC-07 (§3.3) | DOC-09 (§3), DOC-10 (§2) | TEST-SAFE-004, TEST-SAFE-009, TEST-SAFE-010 | DOC-11 (§4), DOC-14 (§2), DOC-16 (Phase 3) | **SELARAS (Kondisi: Timeout 5s)** |
| **ADR-007** | Countdown Server & Atomic Token | **DEC-004** (Approved) | DOC-02 (PRD §3.4), DOC-03 (FR-SAFE-003, 004) | DOC-04 (§2.3), DOC-06 (`completed`), DOC-07 (§3.4) | DOC-09 (§2.3, §2.4) | TEST-SAFE-005, TEST-SAFE-006, TEST-SAFE-007 | DOC-14 (§2), DOC-16 (Phase 3) | **SELARAS 100%** |
| **ADR-008** | Enkripsi AES-256 Destination URL | *Arsitektur Umum* | DOC-02 (PRD §3.1), DOC-03 (FR-LINK-001) | DOC-05 (§3.2), DOC-06 (`links.destination_url`), DOC-07 (§5.2) | DOC-09 (§4.1), DOC-10 (§3) | TEST-LINK-001, TEST-SEC-002 | [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§4), DOC-16 (Phase 2) | **SELARAS 100%** |
| **ADR-009** | Target WarnaHost Starter Unlimited | **DEC-003** (Approved) | DOC-01 (§3), DOC-02 (PRD §8), DOC-03 (NFR-HOST-001-004) | DOC-05 (§1), DOC-07 (§1.2) | DOC-09 (§8) | TEST-DEPLOY-001 s/d TEST-DEPLOY-004 | DOC-11 (Infrastruktur), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md), DOC-16 (Phase 0, 6) | **TERBLOKIR BUKTI HOSTING** |
| **ADR-010** | Retensi 90 Hari & Keyed HMAC IP Hash | **DEC-006** (Approved) | DOC-02 (PRD §7), DOC-03 (FR-ANA-001, NFR-DATA-002) | DOC-05 (§3.3), DOC-06 (`visits.ip_hash`), DOC-07 (§5.5) | DOC-09 (§5.1, §5.2) | TEST-ANA-001, TEST-ANA-007 | DOC-11 (§3), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§5), DOC-16 (Phase 5) | **SELARAS 100%** |
| **ADR-011** | Hashed Session Token (SHA-256) | **DEC-009** (Approved) | DOC-02 (PRD §3.2), DOC-03 (NFR-SEC-001) | DOC-04 (§2.1), DOC-06 (`token_hash`), DOC-07 (§3.2) | DOC-09 (§2.1) | TEST-SAFE-001, TEST-SEC-001 | DOC-14 (§1), DOC-16 (Phase 3) | **SELARAS 100%** |
| **ADR-012** | Antarmuka Ganti Password Admin | **DEC-008** (Approved) | DOC-02 (PRD §2.2), DOC-03 (FR-AUTH-004) | DOC-05 (§2.1), DOC-07 (§3.1, §5.1), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§3) | DOC-09 (§1.2) | TEST-AUTH-006, TEST-AUTH-007 | DOC-16 (Phase 1) | **SELARAS 100%** |
| **ADR-013** | Pre-Built Vendor ZIP Deployment | **DEC-010** (Approved) | DOC-02 (PRD §8.2), DOC-03 (NFR-DEPL-001) | DOC-05 (§1.3), DOC-07 (§1.3) | DOC-09 (§8.2) | TEST-DEPLOY-002, TEST-DEPLOY-003 | DOC-11 (§2), [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§2.3), DOC-16 (Phase 7) | **SELARAS (Kondisi: Build ABI)** |
| **ADR-014** | Suspend Popunder & Tombol Steril | **DEC-007** (Approved) | DOC-02 (PRD §6.2), DOC-03 (FR-ADS-005, NFR-MON-001) | DOC-04 (§3), DOC-07 (§6.2), [DOC-08](../lokataut-documentation-v1.2.2/04-design/08-ui-ux-specification.md) (§4.2) | DOC-09 (§7.2), DOC-10 (§2, §4) | TEST-ADS-003, TEST-ADS-004 | DOC-14 (§3), DOC-16 (Phase 4) | **SELARAS 100%** |
| **ADR-015** | Kebijakan Hapus & Retensi Analitik | **DEC-013** (Approved) | DOC-02 (PRD §3.5), DOC-03 (FR-LINK-006, 007) | DOC-05 (§3.4), DOC-06 (§4 Kebijakan Hapus), DOC-07 (§5.2) | DOC-09 (§4.3) | TEST-LINK-006, TEST-LINK-007 | DOC-16 (Phase 2, 5) | **SELARAS 100%** |
| **ADR-016** | Mandatori ANALYTICS_SALT Mandiri | **DEC-014** (Approved) | DOC-02 (PRD §7.2), DOC-03 (NFR-SEC-006) | DOC-05 (§3.3), DOC-06 (`visits`), DOC-07 (§5.5) | DOC-09 (§5.2) | TEST-ANA-002, TEST-SEC-003 | [DOC-13](../lokataut-documentation-v1.2.2/07-operations/13-deployment-operations.md) (§4.1), DOC-16 (Phase 5) | **SELARAS 100%** |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot | **DEC-015** (Approved) & **DEC-016** (Review) | DOC-02 (PRD §7.3), DOC-03 (FR-ANA-003, NFR-DATA-003) | DOC-05 (§3.5), DOC-06 (`daily_stats`, `visits`), DOC-07 (§5.5) | DOC-09 (§4.4) | TEST-ANA-004, TEST-LINK-007 | DOC-16 (Phase 5) | **SELARAS (Menunggu TTD DEC-016)** |

---

## 3. Hasil Audit Keterpaduan & Keselarasan v1.0.1

1. **Sinkronisasi Pemetaan DEC:** Seluruh 16 keputusan produk (DEC-001 s/d DEC-016) telah terpetakan secara presisi mengikuti dokumen kanonikal [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md).
2. **Ketiadaan Keputusan Yatim Piatu (Zero Orphan ADRs):** Tidak ada satupun ADR yang berdiri sendiri tanpa payung kebutuhan (FR/NFR), arsitektur, dan rencana pengujian QA.
3. **Ketiadaan Kontradiksi Spesifikasi:** Seluruh nama kolom (`token_hash`, `destination_url`, `aggregate_key`, `link_id_snapshot`, `link_slug_snapshot`) dan endpoint routing (`/admin/profile`, `/s/{slug}/verify`, `/s/{slug}/proceed`) telah konsisten lintas dokumen.
4. **Item Terbuka yang Memerlukan Aksi:**
   - Eksekusi 7 perintah pre-flight hosting untuk memvalidasi `ADR-002` dan `ADR-009`.
   - Keputusan formal Product Owner atas tata kelola `DEC-012` dan spesifikasi teknis `DEC-016`.

