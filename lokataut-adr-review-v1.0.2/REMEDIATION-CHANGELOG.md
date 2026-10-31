# Catatan Perubahan Remediasi (Remediation Changelog) v1.0.2 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | REMEDIATION-CHANGELOG |
| **Versi Dokumen** | 1.0.2 |
| **Tanggal Rilis** | 31 Oktober 2026 |
| **Dokumentasi Rujukan** | `lokataut-adr-review-v1.0.1/` $\rightarrow$ `lokataut-adr-review-v1.0.2/` |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (Tidak Berubah / Read-Only) |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) |

---

## 1. Ikhtisar Rilis Remediasi v1.0.2

Rilis `lokataut-adr-review-v1.0.2/` merupakan pemutakhiran terarah dari hasil peninjauan arsitektur v1.0.1. Rilis ini menuntaskan 6 temuan struktural dan teknis yang diidentifikasi oleh Product Owner dalam sesi review.

---

## 2. Rincian Perubahan Terarah (Detailed Changelog)

### Item A: Penghapusan Pseudo-Relasi DEC-012
- **Masalah pada v1.0.1:** ADR-002, ADR-003, ADR-005, dan ADR-008 mencantumkan `DEC-012 Scope` sebagai relasi teknisnya, sehingga mengaburkan fungsi DEC-012 yang sebenarnya merupakan kerangka tata kelola umum.
- **Perbaikan pada v1.0.2:**
  - Menghapus label `DEC-012 Scope` pada ADR-002, ADR-003, ADR-005, dan ADR-008 di seluruh dokumen.
  - Mengubah status relasi keempat ADR tersebut menjadi:  
    `No dedicated DEC relationship (Architectural Baseline)`.
  - DEC-012 ditegaskan murni sebagai kerangka tata kelola dan prosedur pengesahan ADR yang memayungi ke-17 ADR.
- **Berkas Terdampak:** `ADR-INVENTORY.md`, `ADR-INDIVIDUAL-REVIEW.md`, `ADR-CROSS-DOCUMENT-MATRIX.md`, `ADR-RISK-REGISTER.md`, `PRODUCT-OWNER-DECISION-REGISTER.md`, `README.md`.

---

### Item B: Penguatan Arsitektur Idempotensi Agregasi
- **Masalah pada v1.0.1:** Penjelasan idempotensi pada ADR-017 dan DEC-016 terlalu menyederhanakan mekanisme dengan mengandalkan fungsi Laravel (`updateOrCreate()`, `lockForUpdate()`, atau `withoutOverlapping()`) secara individual.
- **Perbaikan pada v1.0.2:**
  - Menyusun **Arsitektur Idempotensi Berbasis Watermark & Checkpoint**:
    1. Tabel metadata `aggregation_checkpoints` dengan field `(target_date, last_processed_visit_id, status, updated_at)`.
    2. Transaksi database per-chunk 1.000 baris (`DB::transaction`).
    3. Row-level locking pada record aggregate (`lockForUpdate`).
    4. Pencegahan tumpang tindih cron (`withoutOverlapping(60)->runInBackground()`).
    5. Jaminan constraint fisik `UNIQUE (aggregate_key, date)` pada `daily_stats` (DEC-015).
  - Melakukan walkthrough komprehensif terhadap **6 skenario kegagalan**:
    1. Batch berhasil seluruhnya.
    2. Batch gagal di tengah proses (rollback bersih ke checkpoint terakhir).
    3. Proses diulang setelah kegagalan (melanjutkan dari `last_processed_visit_id + 1`).
    4. Dua proses berjalan bersamaan (dicegah via cache mutex & DB lock).
    5. Event historis diproses ulang (perlindungan via parameter eksplisit `--recalculate`).
    6. Scheduler melewatkan satu jadwal (prosedur catch-up berurutan).
  - Menegaskan bahwa trafik dari tautan terhapus tetap diagregasikan ke format `link:{snapshot_id}`.
- **Berkas Terdampak:** `DEC-016-TECHNICAL-ANALYSIS.md`, `ADR-INDIVIDUAL-REVIEW.md`, `ADR-TECHNICAL-COMPARISON.md`, `ADR-INVENTORY.md`.

---

### Item C: Revalidasi & Dekomposisi Simulasi Storage DEC-016
- **Masalah pada v1.0.1:** Estimasi pertambahan ukuran baris (+67,5 MB) disajikan tanpa dekomposisi struktur halaman InnoDB dan rentan disalahartikan sebagai pengukuran fisik riil.
- **Perbaikan pada v1.0.2:**
  - Memisahkan secara transparan antara:
    - **Logical Data Size:** ~215 byte per baris (termasuk snapshot 29B).
    - **Estimated Table Size (Clustered Data):** ~298 byte per baris (memperhitungkan record header 5B, MVCC 13B, null bitmap, overhead halaman 16 KB, dan page fill factor 90%).
    - **Estimated Secondary Index Size:** ~186 byte per baris (4 secondary index B-Tree dengan fill factor 75%, termasuk snapshot index ~26B).
    - **Total Storage Projection:** ~484 s/d 500 byte per baris pada disk.
  - Menghitung proyeksi 90 hari:
    - 1.000 visits/day: Data ~26,8 MB, Index ~16,7 MB, Total ~43,6 MB (Porsi Snapshot: +6,4 MB).
    - 10.000 visits/day: Data ~268,2 MB, Index ~167,4 MB, Total ~435,6 MB (Porsi Snapshot: +63,9 MB).
    - 50.000 visits/day: Data ~1,34 GB, Index ~837 MB, Total ~2,18 GB (Porsi Snapshot: +319,5 MB).
  - Menambahkan disclaimer eksplisit bahwa simulasi ini ditujukan untuk *capacity planning* dan wajib divalidasi via pengukuran database riil pasca-implementasi.
  - Menegaskan batasan fisik CloudLinux LVE shared hosting terhadap anggapan "unlimited storage".
- **Berkas Terdampak:** `DEC-016-TECHNICAL-ANALYSIS.md`, `ADR-INDIVIDUAL-REVIEW.md`, `ADR-TECHNICAL-COMPARISON.md`.

---

### Item D: Koreksi Kebijakan Iklan & Aksesibilitas WCAG
- **Masalah pada v1.0.1:** Jarak vertikal 30px sempat diklaim sebagai aturan resmi Adsterra dan bukti otomatis kepatuhan WCAG 2.5.5.
- **Perbaikan pada v1.0.2:**
  - Menegaskan pada ADR-014 bahwa jarak 30px adalah **internal UI safeguard pengembang** untuk mengurangi risiko salah klik (*accidental clicks*).
  - Menegaskan bahwa jarak 30px **BUKAN** persyaratan resmi Adsterra dan **BUKAN** bukti otomatis kepatuhan menyeluruh terhadap standar WCAG 2.5.5.
  - Menegakkan larangan format penempatan iklan yang menyerupai tombol navigasi unduhan (*no deceptive UI / dark patterns*).
  - Menegaskan larangan manipulasi klik paksa (*no forced clicks*).
- **Berkas Terdampak:** `ADR-INVENTORY.md`, `ADR-INDIVIDUAL-REVIEW.md`, `ADR-RISK-REGISTER.md`.

---

### Item E: Rekonsiliasi Status Matriks Keterlacakan
- **Masalah pada v1.0.1:** Kolom status pada `ADR-CROSS-DOCUMENT-MATRIX.md` mencantumkan klaim `100% SELARAS` secara prematur saat masih ada prasyarat hosting yang belum terpenuhi.
- **Perbaikan pada v1.0.2:**
  - Mengganti seluruh label dengan status bernuansa objektif:
    - `DOCUMENTATION ALIGNED` (6 ADR)
    - `TECHNICAL REVIEW COMPLETED` (9 ADR)
    - `ENVIRONMENT VERIFICATION PENDING` (2 ADR: ADR-002 dan ADR-009)
    - `PRODUCT OWNER APPROVAL PENDING` (17 ADR)
- **Berkas Terdampak:** `ADR-CROSS-DOCUMENT-MATRIX.md`.

---

### Item F: Konsistensi Laporan & Integritas Tata Kelola
- **Status 17 ADR:** Tetap formal `PROPOSED / UNDER REVIEW`.
- **Status DEC:** DEC-001 s/d DEC-011, DEC-013, DEC-014, dan DEC-015 tetap `APPROVED`. DEC-012 dan DEC-016 tetap `UNDER REVIEW`.
- **Baseline v1.2.2:** Dipertahankan utuh dan tidak dimodifikasi (*read-only*).
- **Berkas Terdampak:** Seluruh berkas pada `lokataut-adr-review-v1.0.2/`.

