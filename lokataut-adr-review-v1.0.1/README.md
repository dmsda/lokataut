# LokaTaut — Architecture Decision Records (ADR) Review & Governance v1.0.1

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-REV-README-01 |
| **Versi Dokumen** | 1.0.1 (Remediasi Konsistensi & Pemetaan DEC) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Persetujuan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |
| **Status Gerbang Akhir** | **READY FOR PRODUCT OWNER ADR REVIEW** *(Dilarang Memulai Koding Aplikasi)* |

---

## 1. Konteks Dokumen & Catatan Remediasi v1.0.1

Dokumen ini merupakan pemutakhiran terarah (**Versi 1.0.1**) dari hasil review arsitektur independen terhadap 17 Architecture Decision Records (ADR-001 s/d ADR-017) LokaTaut. Versi 1.0.1 disusun secara khusus untuk menindaklanjuti audit Product Owner atas rilis v1.0, dengan cakupan perbaikan presisi:

1. **Sinkronisasi Pemetaan DEC ke Baseline v1.2.2:**  
   Mengembalikan seluruh pemetaan 16 keputusan produk (DEC-001 s/d DEC-016) agar 100% identik dengan baseline kanonikal [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md). Menghilangkan kekeliruan v1.0 yang sempat memetakan nomor DEC mengikuti nomor ADR.
2. **Pemisahan Tegas Tata Kelola DEC-012 dari Persetujuan ADR:**  
   Menegaskan bahwa DEC-012 **hanya** mengatur instrumen tata kelola, aturan batas kewenangan AI, dan prosedur persetujuan. DEC-012 tidak lagi dicampuradukkan sebagai persetujuan borongan terhadap ke-17 ADR. Setiap ADR wajib ditinjau dan disetujui secara individual oleh Product Owner. Rekomendasi tim peninjau (`TECHNICALLY SOUND`, dll.) murni merupakan analisis ahli, bukan status persetujuan.
3. **Pemisahan Tegas DEC-015 dan DEC-016:**  
   Menegaskan bahwa `aggregate_key VARCHAR(100) NOT NULL` pada tabel `daily_stats` telah **RESMI DISETUJUI (APPROVED)** sebagai **DEC-015** pada 31 Oktober 2026. Analisis terbuka pada **DEC-016** difokuskan secara murni pada snapshot identitas tabel sumber (`visits.link_id_snapshot` dan `visits.link_slug_snapshot`), pelestarian riwayat analitik saat hard delete, simulasi penambahan storage, dan rekomendasi penyajian UI.
4. **Penyempurnaan Arsitektur Idempotensi Agregasi:**  
   Memperbaiki asumsi bahwa `updateOrCreate()` saja sudah cukup. Menetapkan arsitektur idempotensi 3-pilar: transaksi basis data (`DB::transaction`), penguncian baris (*row lock*), dan pencegahan tumpang tindih penjadwalan (*scheduler overlap prevention* via `withoutOverlapping()`).
5. **Koreksi Taksonomi Trafik Tautan Terhapus:**  
   Memastikan bahwa kunjungan dari tautan yang telah di-hard delete namun memiliki snapshot identitas **tetap menggunakan partisi `link:{id}`** (berbasis ID snapshot), sehingga data analitik historisnya tetap teratribusi ke tautan tersebut. Taksonomi `traffic:purged_legacy` dibatasi khusus untuk data lawas yang tidak memiliki snapshot.
6. **Kalibrasi Klaim Kebijakan & Teknis:**  
   Menyatakan secara akurat bahwa jarak visual 30px adalah *internal engineering safeguard* berbasis WCAG 2.5.5, bukan regulasi resmi Adsterra. Cloudflare Turnstile dijelaskan berbasis ketentuan paket gratis saat ini tanpa klaim "unlimited mutlak".
7. **Integritas Baseline Dokumentasi:**  
   Direktori baseline `lokataut-documentation-v1.2.2/` dipertahankan utuh dan tidak dimodifikasi sama sekali (*read-only*).

---

## 2. Struktur & Indeks Dokumen Review v1.0.1

Direktori `lokataut-adr-review-v1.0.1/` memuat 10 dokumen analisis dan tata kelola:

| No | Berkas Dokumen | Deskripsi & Ruang Lingkup | Status Formal |
|:---:|---|---|:---:|
| 1 | **[README.md](./README.md)** | Panduan eksekutif, rekam catatan remediasi v1.0.1, dan instruksi gerbang PO. | `UNDER REVIEW` |
| 2 | **[ADR-INVENTORY.md](./ADR-INVENTORY.md)** | Master inventaris 17 ADR dengan 12 atribut lengkap, sinkron dengan pemetaan DEC kanonikal. | `PROPOSED` |
| 3 | **[ADR-INDIVIDUAL-REVIEW.md](./ADR-INDIVIDUAL-REVIEW.md)** | Telaah teknis mendalam 17 ADR lintas 8 disiplin ilmu teknik dengan analisis batas yang diperketat. | `UNDER REVIEW` |
| 4 | **[ADR-TECHNICAL-COMPARISON.md](./ADR-TECHNICAL-COMPARISON.md)** | Matriks komparasi arsitektur LokaTaut terhadap alternatif industri (Monolith vs Microservices, dll.). | `UNDER REVIEW` |
| 5 | **[DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md)** | Analisis mendalam khusus DEC-016: snapshot identitas `visits`, simulasi storage, dan opsi UI. | `UNDER REVIEW` |
| 6 | **[DEC-012-APPROVAL-GOVERNANCE.md](./DEC-012-APPROVAL-GOVERNANCE.md)** | Tata kelola murni persetujuan ADR: aturan, prosedur pengesahan, dan lembar kerja evaluasi ADR. | `UNDER REVIEW` |
| 7 | **[ADR-CROSS-DOCUMENT-MATRIX.md](./ADR-CROSS-DOCUMENT-MATRIX.md)** | Matriks keterlacakan silang dua arah memetakan 17 ADR ke DOC-01 s/d DOC-16, RTM, dan DEC. | `UNDER REVIEW` |
| 8 | **[ADR-RISK-REGISTER.md](./ADR-RISK-REGISTER.md)** | Register 14 risiko arsitektural utama dilengkapi tingkat keparahan, mitigasi, deteksi, dan fallback. | `UNDER REVIEW` |
| 9 | **[PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md)** | Lembar keputusan eksekutif resmi bagi Dimas Aditya untuk menetapkan DEC-012, DEC-016, dan ADR. | `ACTION REQUIRED` |
| 10 | **[ADR-REVIEW-REPORT.md](./ADR-REVIEW-REPORT.md)** | Laporan audit sintesis komprehensif, daftar periksa pra-koding, dan penetapan status gerbang. | `UNDER REVIEW` |

---

## 3. Matriks Kanonikal Status 16 Keputusan Produk (DEC Registry)

Berikut adalah status resmi 16 Keputusan Produk (*Decision Register*) yang diselaraskan secara sempurna dengan [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md):

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Open Decisions Summary Matrix                                        │
├─────────┬──────────────────────────────────┬──────────────┬──────────────┬─────────────┬───────────────┤
│ DEC ID  │ Topik Keputusan Kanonikal        │ Status       │ Tanggal      │ Otoritas PO │ Bukti Otoritas│
├─────────┼──────────────────────────────────┼──────────────┼──────────────┼─────────────┼───────────────┤
│ DEC-001 │ Migrasi Framework ke Laravel 13  │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-002 │ Status Kandidat Jaringan Iklan   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-003 │ Verifikasi Runtime WarnaHost     │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-004 │ Durasi Default Countdown (10s)   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-005 │ Turnstile Placement (Sebelum)    │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-006 │ Retensi Data Mentah (90 Hari)    │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-007 │ Penangguhan Format Popunder      │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-008 │ Fitur Ganti Password (FR-AUTH-004│ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-009 │ Hashed Token Sesi (token_hash)   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-010 │ Fallback Vendor Deploy (DOC-13)  │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-011 │ Turnstile Emergency Bypass Switch│ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-012 │ Approval Formal ADR-001 s/d 017  │ UNDER REVIEW │ -            │ Pending PO  │ Butuh TTD PO  │
│ DEC-013 │ Kebijakan Soft & Hard Delete     │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-014 │ Mandatori ANALYTICS_SALT Mandiri │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-015 │ Dedicated Aggregate Key NOT NULL │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-016 │ Snapshot Identitas Tautan Kunj.  │ UNDER REVIEW │ -            │ Pending PO  │ Butuh TTD PO  │
└─────────┴──────────────────────────────────┴──────────────┴──────────────┴─────────────┴───────────────┘
```

---

## 4. Rekapitulasi Evaluasi Peninjau terhadap 17 ADR

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        Tally Evaluasi 17 Architecture Decisions                        │
├───────────────────────────────┬───────┬────────────────────────────────────────────────┤
│ Kategori Evaluasi Reviewer    │ Total │ Nomor ADR                                      │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ 1. TECHNICALLY SOUND          │  10   │ ADR-003, ADR-005, ADR-007, ADR-008, ADR-010,   │
│                               │       │ ADR-011, ADR-012, ADR-014, ADR-015, ADR-016   │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ 2. SOUND WITH CONDITIONS      │   5   │ ADR-001, ADR-004, ADR-006, ADR-013, ADR-017   │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ 3. BLOCKED BY MISSING EVIDENCE│   2   │ ADR-002, ADR-009                               │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ 4. NEEDS REVISION             │   0   │ -                                              │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ 5. NOT RECOMMENDED            │   0   │ -                                              │
├───────────────────────────────┼───────┼────────────────────────────────────────────────┤
│ TOTAL ADR                     │  17   │ Status Formal: 100% PROPOSED / UNDER REVIEW    │
└───────────────────────────────┴───────┴────────────────────────────────────────────────┘
```

> **CATATAN TATA KELOLA:**  
> Status di atas adalah **rekomendasi profesional tim peninjau teknis**. Sesuai aturan DEC-012, status tersebut **BUKAN persetujuan Product Owner**. Seluruh 17 ADR secara hukum arsitektur saat ini berstatus **`PROPOSED / UNDER REVIEW`** menunggu keputusan individual dari Dimas Aditya.

---

## 5. Protokol Pemeriksaan Pra-Penerbangan Hosting (Pre-Flight Checks)

Untuk membuka blokir pada **ADR-002 (MySQL/MariaDB)** dan **ADR-009 (WarnaHost Starter)**, Product Owner atau DevOps diwajibkan mengeksekusi 7 perintah berikut pada terminal SSH / cPanel Terminal WarnaHost:

```bash
# 1. Periksa versi PHP CLI default
php -v

# 2. Periksa ketersediaan ekstensi PHP kritis
php -m | grep -E 'pdo_mysql|bcmath|openssl|curl|mbstring|xml|fileinfo'

# 3. Periksa memory limit PHP CLI
php -i | grep memory_limit

# 4. Verifikasi binary PHP 8.3 spesifik cPanel
/usr/local/bin/ea-php83 -v

# 5. Verifikasi varian & versi RDBMS (MySQL 8.0 vs MariaDB 10.5+)
mysql -V

# 6. Periksa kapasitas storage disk
df -h

# 7. Periksa batasan proses sistem CloudLinux LVE
ulimit -a
```

Hasil eksekusi riil dari terminal tersebut dicatat pada laporan pre-flight untuk mengubah rekomendasi `BLOCKED BY MISSING EVIDENCE` menjadi `TECHNICALLY SOUND`.

---

## 6. Arahan & Larangan Keras bagi AI Coding Agent

Sesuai aturan operasional [AGENTS.md](../AGENTS.md):
- **DILARANG** memulai implementasi kode (membuat file `.php`, migration, model, view, controller, asset CSS/JS).
- **DILARANG** melakukan koneksi atau manipulasi skema basis data produksi/staging.
- **DILARANG** menganggap keputusan atau ADR berstatus `APPROVED` tanpa pernyataan tertulis eksplisit dari Dimas Aditya.
- Status repositori saat ini adalah **`READY FOR PRODUCT OWNER ADR REVIEW`** dan **TETAP BERADA PADA FASE REVIEW DOKUMENTASI**.

