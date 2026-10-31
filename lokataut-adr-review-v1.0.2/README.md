# LokaTaut — Architecture Decision Records (ADR) Review & Governance v1.0.2

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-REV-README-01 |
| **Versi Dokumen** | 1.0.2 (Remediasi Terarah: Pemetaan DEC, Idempotensi, & Simulasi Storage) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |
| **Status Gerbang Akhir** | **READY FOR PRODUCT OWNER ADR REVIEW** *(Dilarang Memulai Koding Aplikasi)* |

---

## 1. Konteks Dokumen & Ringkasan Remediasi v1.0.2

Dokumen ini merupakan pemutakhiran terarah (**Versi 1.0.2**) dari paket peninjauan arsitektur LokaTaut. Versi 1.0.2 diterbitkan untuk menuntaskan seluruh catatan kritis pada audit v1.0.1 dengan ruang lingkup presisi:

1. **Koreksi Pemetaan Relasi DEC (Item A):**  
   Menghapus penyebutan DEC-012 sebagai relasi teknis untuk ADR-002 (MySQL), ADR-003 (Blade), ADR-005 (`visitor_sessions`), dan ADR-008 (AES-256 URL). Keempat keputusan ini ditetapkan berelasi sebagai `No dedicated DEC relationship (Architectural Baseline)`. DEC-012 ditempatkan murni sebagai kerangka tata kelola (*governance reference*) yang memayungi proses persetujuan seluruh ADR.
2. **Penguatan Arsitektur Idempotensi Agregasi (Item B):**  
   Mengevaluasi ulang mekanisme agregasi pada ADR-017 dan DEC-016. Menolak ketergantungan parsial pada fungsi tunggal (`updateOrCreate`, `chunkById`, atau `withoutOverlapping`). Menerapkan **Arsitektur Idempotensi Berbasis Watermark & Checkpoint**:
   - Event checkpoint tracking (`aggregation_checkpoints` dengan `last_processed_visit_id`).
   - Boundary transaksi per-chunk (`DB::transaction`).
   - Atomic update dengan `lockForUpdate()`.
   - Pencegahan tumpang tindih cron (`withoutOverlapping(60)`).
   - Pengujian 6 skenario kegagalan: batch berhasil penuh, kegagalan di tengah batch, retry pasca-kegagalan, eksekusi konkuren, pemrosesan ulang data historis, dan penanganan jadwal cron terlewat (*missed schedule*).
3. **Revalidasi & Dekomposisi Simulasi Storage DEC-016 (Item C):**  
   Membedakan secara transparan antara:
   - *Logical data size* (ukuran data mentah).
   - *Estimated table size* (data halaman InnoDB B-Tree 16 KB clustered index dengan fill factor ~90%).
   - *Estimated index size* (halaman secondary index B-Tree dengan fill factor ~75%).
   - *Total storage projection*.  
   Menegaskan angka sebagai simulasi matematis *capacity planning*, bukan pengukuran fisik aktual. Menolak anggapan hosting "unlimited" sebagai kapasitas tanpa batas fisik.
4. **Koreksi Klaim Kebijakan Iklan & WCAG (Item D):**  
   Menegaskan pada ADR-014 bahwa jarak vertikal 30px adalah *internal UI safeguard*, bukan aturan resmi tertulis dari jaringan Adsterra dan bukan bukti otomatis kepatuhan WCAG 2.5.5 secara menyeluruh. Menegakkan larangan manipulasi tombol navigasi (*no deceptive UI, no forced clicks*).
5. **Rekonsiliasi Status Matriks Keterlacakan (Item E):**  
   Mengganti klaim prematur `100% SELARAS` dengan status bernuansa objektif:
   - `DOCUMENTATION ALIGNED`
   - `TECHNICAL REVIEW COMPLETED`
   - `ENVIRONMENT VERIFICATION PENDING` (untuk ADR-002 dan ADR-009)
   - `PRODUCT OWNER APPROVAL PENDING` (untuk seluruh 17 ADR)
6. **Integritas Baseline Dokumentasi (Item F):**  
   Direktori baseline `lokataut-documentation-v1.2.2/` tetap dipertahankan utuh dan tidak dimodifikasi (*read-only*). Seluruh 17 ADR berstatus formal `PROPOSED / UNDER REVIEW`.

---

## 2. Struktur & Indeks Berkas Peninjauan v1.0.2 (12 Dokumen)

Direktori `lokataut-adr-review-v1.0.2/` terdiri dari **12 dokumen komprehensif**:

| No | Berkas Dokumen | Deskripsi & Ruang Lingkup | Status Formal |
|:---:|---|---|:---:|
| 1 | **[README.md](./README.md)** | Panduan eksekutif, rekapitulasi remediasi v1.0.2, dan protokol gerbang. | `UNDER REVIEW` |
| 2 | **[ADR-INVENTORY.md](./ADR-INVENTORY.md)** | Master inventaris 17 ADR dengan 12 atribut, pemetaan DEC kanonikal tanpa pseudo-DEC. | `PROPOSED` |
| 3 | **[ADR-INDIVIDUAL-REVIEW.md](./ADR-INDIVIDUAL-REVIEW.md)** | Telaah teknis mendalam 17 ADR dari 8 disiplin ilmu teknik dengan analisis batas. | `UNDER REVIEW` |
| 4 | **[ADR-TECHNICAL-COMPARISON.md](./ADR-TECHNICAL-COMPARISON.md)** | Matriks komparasi arsitektur LokaTaut terhadap alternatif industri (Monolith vs Microservices, dll.). | `UNDER REVIEW` |
| 5 | **[DEC-016-TECHNICAL-ANALYSIS.md](./DEC-016-TECHNICAL-ANALYSIS.md)** | Analisis teknis DEC-016: dekomposisi storage InnoDB, Idempotensi Watermark, dan opsi UI. | `UNDER REVIEW` |
| 6 | **[DEC-012-APPROVAL-GOVERNANCE.md](./DEC-012-APPROVAL-GOVERNANCE.md)** | Kerangka tata kelola murni pengesahan ADR: aturan wewenang PO dan evaluasi per ADR. | `UNDER REVIEW` |
| 7 | **[ADR-CROSS-DOCUMENT-MATRIX.md](./ADR-CROSS-DOCUMENT-MATRIX.md)** | Matriks keterlacakan silang dua arah dengan klasifikasi status terperinci. | `UNDER REVIEW` |
| 8 | **[ADR-RISK-REGISTER.md](./ADR-RISK-REGISTER.md)** | Register 14 risiko arsitektural utama dilengkapi tingkat keparahan, mitigasi, deteksi, dan fallback. | `UNDER REVIEW` |
| 9 | **[PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md)** | Lembar keputusan eksekutif resmi bagi Dimas Aditya untuk menetapkan DEC-012, DEC-016, dan ADR. | `ACTION REQUIRED` |
| 10 | **[ADR-REVIEW-REPORT.md](./ADR-REVIEW-REPORT.md)** | Laporan audit sintesis final, daftar periksa pra-koding, dan penetapan status gerbang. | `UNDER REVIEW` |
| 11 | **[REMEDIATION-CHANGELOG.md](./REMEDIATION-CHANGELOG.md)** | Catatan perubahan teknis mendalam (*changelog*) dari rilis v1.0.1 ke v1.0.2. | `COMPLETED` |
| 12 | **[REMEDIATION-VALIDATION-REPORT.md](./REMEDIATION-VALIDATION-REPORT.md)** | Laporan audit kepatuhan dengan status verifikasi eksplisit (PASS / NOT VERIFIED). | `COMPLETED` |

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
> Status di atas adalah **rekomendasi profesional tim peninjau teknis**. Sesuai aturan tata kelola DEC-012, status tersebut **BUKAN persetujuan Product Owner**. Seluruh 17 ADR secara formal saat ini berstatus **`PROPOSED / UNDER REVIEW`** menunggu keputusan individual dari Dimas Aditya.

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

## 6. Arahan Operasional bagi AI Coding Agent

Sesuai aturan operasional [AGENTS.md](../AGENTS.md):
- **DILARANG** memulai implementasi kode (membuat file `.php`, migration, model, view, controller, asset CSS/JS).
- **DILARANG** melakukan koneksi atau manipulasi skema basis data produksi/staging.
- **DILARANG** menganggap keputusan atau ADR berstatus `APPROVED` tanpa pernyataan tertulis eksplisit dari Dimas Aditya.
- Status repositori saat ini adalah **`READY FOR PRODUCT OWNER ADR REVIEW`** dan **TETAP BERADA PADA FASE REVIEW DOKUMENTASI**.

