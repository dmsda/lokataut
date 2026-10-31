# Analisis Teknis & Rekomendasi Terbuka: DEC-016 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DEC-016-TECH-01 |
| **Versi Dokumen** | 1.0.2 (Penguatan Arsitektur Idempotensi & Dekomposisi Storage InnoDB) |
| **Status Keputusan** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md)) |
| **Tanggal Analisis** | 31 Oktober 2026 |
| **Penyusun** | Senior Database Architect, Lead Software Architect, & UI/UX Specialist |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Pemisahan Tegas DEC-015 (Approved) dan DEC-016 (Under Review)

Untuk menjaga integritas tata kelola arsitektur, batasan kedua keputusan ini dipisahkan secara definitif:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        Diferensiasi Ruang Lingkup DEC-015 vs DEC-016                   │
├──────────────────────────┬─────────────────────────────┬───────────────────────────────┤
│ Atribut Evaluasi         │ DEC-015 (STATUS: APPROVED)  │ DEC-016 (STATUS: UNDER REVIEW)│
├──────────────────────────┼─────────────────────────────┼───────────────────────────────┤
│ **Tabel Sasaran**        │ `daily_stats` (Tabel Target)│ `visits` (Tabel Sumber Log)   │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────┤
│ **Struktur Kolom**       │ `aggregate_key VARCHAR(100)`│ `link_id_snapshot BIGINT`     │
│                          │ NOT NULL                    │ `link_slug_snapshot VARCHAR`  │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────┤
│ **Constraint Database**  │ `UNIQUE (aggregate_key,date)`│ Index B-Tree komposit         │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────┤
│ **Fokus Penyelesaian**   │ Menghilangkan anomali dupli-│ Mempertahankan jejak identitas│
│                          │ kasi NULL pada MySQL        │ saat hard delete & histori UI │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────┤
│ **Status Persetujuan**   │ **APPROVED (31 Okt 2026)**  │ **UNDER REVIEW (Menunggu PO)**│
└──────────────────────────┴─────────────────────────────┴───────────────────────────────┘
```

> **CATATAN PENGAWASAN TATA KELOLA:**  
> Keputusan **DEC-015 telah sah disetujui Product Owner**. Dokumen analisis ini **TIDAK** membuka kembali atau meminta persetujuan ulang atas `aggregate_key NOT NULL` pada `daily_stats`. Ruang lingkup analisis DEC-016 murni terbatas pada:
> 1. Penambahan 2 kolom snapshot identitas pada tabel sumber `visits`.
> 2. Dekomposisi teknis dan simulasi pertambahan kapasitas penyimpanan disk selama retensi 90 hari.
> 3. Arsitektur Idempotensi Agregasi Berbasis Watermark & Checkpoint (menguji 6 skenario kegagalan).
> 4. Rekomendasi penyajian analitik historis tautan terhapus pada dasbor admin.

---

## 2. Bagian A: Dekomposisi Teknis & Simulasi Penyimpanan Disk (Storage Simulation)

### 2.1 Audit Asumsi & Dekomposisi Struktur Baris InnoDB

Dalam mengestimasi kapasitas basis data pada engine MySQL InnoDB (format baris `DYNAMIC` dengan page size default 16 KB), analisis membedakan secara ketat antara **Logical Data Size**, **Estimated Table Size (Clustered Index)**, dan **Estimated Secondary Index Size**.

#### 1. Logical Data Size (Ukuran Data Mentah per Baris):
- **Baris Dasar `visits` (v1.2.1):**
  - `id`: 8 byte (`BIGINT UNSIGNED`)
  - `link_id`: 8 byte (`BIGINT UNSIGNED NULL`)
  - `ip_hash`: 64 byte (`VARCHAR(64)` heksadesimal SHA-256)
  - `user_agent_hash`: 64 byte (`VARCHAR(64)` heksadesimal)
  - `referrer_host`: rata-rata ~25 byte (`VARCHAR(100) NULL`)
  - `country_code`: 2 byte (`CHAR(2) NULL`)
  - `device_type`: ~7 byte (`VARCHAR(20)`)
  - `created_at` & `updated_at`: 8 byte (2x `TIMESTAMP` @ 4 byte)
  - *Subtotal Logical Dasar:* **~186 byte per baris**.
- **Kolom Snapshot Tambahan (DEC-016):**
  - `link_id_snapshot`: 8 byte (`BIGINT UNSIGNED NOT NULL`)
  - `link_slug_snapshot`: panjang rata-rata slug 20 karakter alfanumerik (`utf8mb4` = ~20 byte + 1 byte length prefix)
  - *Subtotal Logical Snapshot:* **~29 byte per baris**.
- **Total Logical Data:** **~215 byte per baris**.

#### 2. Estimated Table Size (Clustered Index / Data Pages):
- **Overhead Fisik Baris InnoDB:**
  - Record Header: 5 byte per baris (delete flag, min-record, record type, heap no, next-record pointer).
  - MVCC System Fields: `DB_TRX_ID` (6 byte) + `DB_ROLL_PTR` (7 byte) = 13 byte.
  - Null Bitmap & Variable-length Field Offset Array: ~8 byte.
  - *Ukuran Fisik Record di Halaman:* ~245 s/d 260 byte per baris.
- **Overhead Halaman InnoDB (16 KB Page = 16.384 byte):**
  - Page Header & File Header/Trailer: 120 byte.
  - Page Directory (Slot Array): ~2 byte per 4-8 record (~100 byte per halaman penuh).
  - Effective Usable Space: ~16.150 byte per halaman.
  - **Page Fill Factor:** Karena `id` bertipe `AUTO_INCREMENT` berurutan, InnoDB mencapai fill factor optimal sekitar **~90%** (~14.500 byte usable per halaman sebelum split).
  - *Jumlah Baris per Halaman 16 KB:* $\approx 14.500 / 260 \approx 55 \text{ baris per halaman}$.
  - *Alokasi Ruang Fisik Tabel per Baris:* $\approx 16.384 / 55 \approx \mathbf{298 \text{ byte per baris}}$. (Tambahan snapshot DEC-016 menyumbang **~45 byte per baris** pada data clustered).

#### 3. Estimated Secondary Index Size (Index Overhead):
Tabel `visits` memiliki 4 secondary index B-Tree:
1. `idx_created_at` (`created_at` 4B + `id` PK 8B = 12B logical $\rightarrow$ ~22B fisik).
2. `idx_link_id` (`link_id` 8B + `id` PK 8B = 16B logical $\rightarrow$ ~26B fisik).
3. `idx_ip_hash_created_at` (`ip_hash` 64B + `created_at` 4B + `id` PK 8B = 76B logical $\rightarrow$ ~95B fisik).
4. `idx_link_id_snapshot` (DEC-016: `link_id_snapshot` 8B + `id` PK 8B = 16B logical $\rightarrow$ ~26B fisik).
- Secondary index memiliki fill factor rata-rata **~75%** pada trafik acak (B-Tree splits).
- Total alokasi index sekunder dasar: ~160 byte per baris.
- Tambahan alokasi index sekunder snapshot (`idx_link_id_snapshot`): **~26 byte per baris**.
- *Total Secondary Index Space per Baris:* $\approx \mathbf{186 \text{ byte per baris}}$.

#### 4. Total Storage Projection (Data + Index):
- **Dasar (v1.2.1):** Table Data ~253 byte + Index ~160 byte = **~413 byte per baris**.
- **Tambahan DEC-016:** Table Data ~45 byte + Index ~26 byte = **~71 byte per baris** (dibulatkan konservatif **~75 byte per baris**).
- **Total Fisik Lengkap:** **~484 s/d 500 byte per baris pada disk**.

---

### 2.2 Proyeksi Kapasitas Selama Siklus Retensi 90 Hari

Sesuai DEC-006 (Approved), data mentah `visits` dibersihkan berkala oleh scheduler setelah 90 hari. Proyeksi dihitung pada 3 skenario volume:

| Skenario Trafik | Kunjungan / Hari | Total Baris (90 Hari) | Logical Data Size | Estimated Table Size (Data) | Estimated Index Size | Total Storage Projection | Porsi Snapshot DEC-016 |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Rendah (Skenario 1)** | 1.000 / hari | 90.000 baris | ~19,35 MB | ~26,82 MB | ~16,74 MB | **~43,56 MB** | **+6,39 MB** |
| **Menengah (Skenario 2)** | 10.000 / hari | 900.000 baris | ~193,50 MB | ~268,20 MB | ~167,40 MB | **~435,60 MB** | **+63,90 MB** |
| **Puncak (Skenario 3)** | 50.000 / hari | 4.500.000 baris | ~967,50 MB | ~1.341,00 MB (1,31 GB) | ~837,00 MB (0,82 GB) | **~2.178,00 MB (2,13 GB)** | **+319,50 MB** |

> **DISCLAIMER ESTIMASI ARSITEKTUR:**  
> Angka-angka di atas merupakan **simulasi matematis berbasis layout internal InnoDB dan asumsi panjang slug tipikal** untuk keperluan *capacity planning*. Angka ini **BUKAN pengukuran empiris pada basis data aktif produksi**.  
> Hasil akhir wajib divalidasi melalui pengukuran aktual (`SHOW TABLE STATUS LIKE 'visits'` dan query pada `information_schema.TABLES`) setelah fase implementasi berjalan.

### 2.3 Batasan Kapasitas Hosting Shared WarnaHost:
Meskipun paket WarnaHost dipasarkan dengan label "Starter Unlimited", sistem operasi CloudLinux LVE menerapkan batas fisik nyata terhadap:
1. Alokasi kuota disk fisik per cPanel user (biasanya 10 GB hingga 20 GB batas wajar I/O).
2. Batas *inodes* (jumlah file total di cPanel, umumnya 250.000 s/d 500.000 inodes).
3. Batas IOPS (Input/Output Operations per Second) dan disk throughput (1–2 MB/s burst).

**Kesimpulan Evaluasi:**  
Beban total ~435,6 MB pada trafik menengah (10.000 visits/hari) dengan porsi snapshot ~63,9 MB sepenuhnya berada dalam ambang batas aman hosting. Namun, klaim "unlimited storage" dilarang dijadikan pembenaran untuk meniadakan cron pembersihan retensi 90 hari.

---

## 3. Bagian B: Arsitektur Idempotensi Agregasi Berbasis Watermark & Checkpoint

### 3.1 Kelemahan Ketergantungan Parsial
Audit membuktikan bahwa menggunakan fitur Laravel secara terpisah tidak menjamin idempotensi:
- `updateOrCreate()` saja rentan *race condition* jika dieksekusi bersamaan.
- `lockForUpdate()` saja tidak mencegah duplikasi jika kueri dieksekusi di luar transaksi database.
- `withoutOverlapping()` saja mengandalkan cache lock yang dapat kedaluwarsa jika proses memakan waktu melebihi lock timeout.
- `chunkById()` saja tidak menjamin konsistensi jika proses crash di tengah batch dan diulang dari awal.

### 3.2 Desain Sistem: 9 Pilar Idempotensi Terpadu
Untuk memastikan tidak ada kunjungan yang dihitung dua kali (*double counted*) maupun terlewat (*lost*), sistem menerapkan **Arsitektur Idempotensi Berbasis Watermark**:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│               Arsitektur Idempotensi Berbasis Watermark & Checkpoint                   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│   [ Tabel aggregation_checkpoints ]                                                    │
│   - target_date (DATE, PK)                                                             │
│   - status (ENUM: pending, in_progress, completed, failed)                             │
│   - last_processed_visit_id (BIGINT UNSIGNED NOT NULL DEFAULT 0)                       │
│   - total_processed (INT UNSIGNED DEFAULT 0)                                          │
│   - updated_at (TIMESTAMP)                                                             │
│                                                                                        │
│   [ Alur Eksekusi Scheduled Command: lokataut:aggregate-daily ]                       │
│                                                                                        │
│   1. Isolasi Scheduler: withoutOverlapping(60)->runInBackground()                     │
│   2. Inisialisasi Checkpoint: Baca status tanggal kemarin                              │
│   3. Per-Chunk Processing:                                                             │
│      DB::transaction(function() {                                                      │
│          a. Ambil 1.000 baris visits WHERE date = target AND id > last_processed_id    │
│          b. Kunci baris daily_stats via lockForUpdate()                                │
│          c. Akumulasi metrik secara atomik (impressions, completions)                  │
│          d. Perbarui last_processed_visit_id = MAX(chunk_visit_id)                     │
│          e. Update checkpoint kursor di dalam transaksi yang sama                      │
│      });                                                                               │
│   4. Finalisasi: Tandai status = 'completed' ketika seluruh chunk selesai              │
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 3.3 Walkthrough 6 Skenario Pengujian Kegagalan (Failure Scenarios)

#### Skenario 1: Batch Berhasil Seluruhnya
- Target tanggal: `2026-10-30` (Total 10.000 visits, ID: 1 s/d 10.000).
- Eksekusi berjalan dalam 10 chunk (@ 1.000 baris). Setiap chunk menyelesaikan akumulasi dan memajukan `last_processed_visit_id` bertahap (1.000, 2.000, ... 10.000).
- Checkpoint ditandai `completed`.
- **Hasil:** Tepat 10.000 kunjungan terakumulasi. Idempotensi terjaga.

#### Skenario 2: Batch Gagal di Tengah Proses (Mid-Batch Halt)
- Pada chunk ke-6 (ID 5.001 s/d 6.000), koneksi database terputus atau batas memori terlampaui pada record ID 5.432.
- **Mekanisme Penanganan:** Transaksi `DB::transaction()` untuk chunk ke-6 **otomatis dibatalkan (ROLLBACK)** secara penuh oleh engine InnoDB.
- Nilai `last_processed_visit_id` pada checkpoint tetap berada pada angka **5.000** (posisi chunk ke-5 yang sukses dikomit).
- Status checkpoint dicatat sebagai `failed`.
- **Hasil:** Tidak ada data setengah jadi dari chunk ke-6 yang tersimpan. Data aman pada state konsisten ID 5.000.

#### Skenario 3: Proses Diulang Pasca-Kegagalan (Retry After Failure)
- Operator atau scheduler retry menjalankan kembali `lokataut:aggregate-daily` untuk tanggal `2026-10-30`.
- Command membaca checkpoint: status = `failed`, `last_processed_visit_id = 5000`.
- Kueri mengambil data mulai dari: `WHERE created_at BETWEEN ... AND id > 5000 LIMIT 1000`.
- Record ID 1 s/d 5.000 **dilewati sepenuhnya** (tidak dihitung ulang). Pemrosesan melanjutkan chunk ID 5.001 s/d 6.000 dari titik awal yang bersih.
- **Hasil:** Nol kunjungan dihitung ganda. Proses selesai hingga ID 10.000 dengan akurasi 100%.

#### Skenario 4: Dua Proses Berjalan Bersamaan (Concurrent Execution)
- Dua proses cron atau admin manual menjalankan command agregasi pada detik yang sama.
- **Mekanisme Penanganan:**
  - Lapis 1: `withoutOverlapping(60)` di level cache mutex menolak eksekusi proses kedua.
  - Lapis 2: Jika dijalankan manual via CLI ganda, proses pertama melakukan `SELECT ... lockForUpdate()` pada baris `aggregation_checkpoints` untuk tanggal terkait. Proses kedua mendeteksi status `in_progress` atau tertahan menunggu lock rilis, lalu keluar dengan aman (*exit gracefully*) tanpa melakukan mutasi.
- **Hasil:** Zero race condition, zero duplicate metrics.

#### Skenario 5: Event Lama Diproses Ulang (Historical Reprocessing)
- Admin menjalankan perintah koreksi paksa: `php artisan lokataut:aggregate-daily --date=2026-10-30 --recalculate`.
- **Mekanisme Penanganan:** Parameter `--recalculate` secara eksplisit menghapus baris `daily_stats` untuk tanggal tersebut di dalam transaksi, mereset `last_processed_visit_id = 0`, dan mengulang kursor dari awal.
- Jika dijalankan tanpa parameter `--recalculate`, command memeriksa status checkpoint: jika `completed`, command langsung berhenti dan menolak pemrosesan ulang (*no-op*).
- **Hasil:** Perlindungan mutlak dari eksekusi ganda yang tidak disengaja.

#### Skenario 6: Scheduler Melewatkan Satu Jadwal (Missed Schedule Catch-Up)
- Server mengalami downtime pada pukul 00:05 (jadwal harian terlewat).
- Command terjadwal berikutnya menyertakan prosedur *catch-up audit*: memeriksa apakah ada tanggal dalam rentang 7 hari terakhir yang belum berstatus `completed`.
- Command memproses tanggal-tanggal yang terlewat secara berurutan (*sequential date processing*) sebelum memproses tanggal hari ini.
- **Hasil:** Tidak ada data kunjungan yang hilang akibat server restart atau pemadaman sementara.

---

### 3.4 Rekomendasi Utama: Pendekatan Watermark Checkpoint vs Alternatif

```
┌────────────────────────────────────────────────────────────────────────────────────────────────┐
│                              Perbandingan Pendekatan Idempotensi                               │
├──────────────────────────┬─────────────────────────────┬───────────────────────────────────────┤
│ Pendekatan               │ Kelebihan                   │ Kekurangan / Alasan Penolakan         │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────────────┤
│ **Opsi A: updateOrCreate**│ Sederhana diimplementasikan │ Rentan double-counting saat retry     │
│ Sederhana Tanpa Kursor   │                             │ partial failure; rentan deadlock      │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────────────┤
│ **Opsi B: Full Table**   │ Selalu menghasilkan angka   │ Full table scan jutaan baris boros    │
│ **Recalculation Harian** │ akurat dari nol             │ RAM & I/O; memicu OOM CloudLinux      │
├──────────────────────────┼─────────────────────────────┼───────────────────────────────────────┤
│ **Opsi C: Watermark &**  │ **Toleran partial failure,  │ Menambah 1 tabel metadata migrasi     │
│ **Checkpoint Batching**  │ **hemat RAM, aman retry,    │ (`aggregation_checkpoints`).           │
│ *(REKOMENDASI UTAMA)*    │ **kebal tumpang tindih.**   │                                       │
└──────────────────────────┴─────────────────────────────┴───────────────────────────────────────┘
```

**Rekomendasi Mutlak Tim:** Menerapkan **Opsi C (Watermark & Checkpoint)** sebagai standar implementasi resmi pada Phase 5.

---

## 4. Bagian C: Taksonomi Trafik & Penyajian Antarmuka Dashboard

### 4.1 Koreksi Atribusi Trafik Tautan Terhapus
Kunjungan dari tautan yang telah di-hard delete **tetap diagregasikan ke format `link:{link_id_snapshot}`** (contoh: `link:42`), sehingga riwayat statistiknya tetap teratribusi ke tautan tersebut.
- `traffic:purged_legacy`: Khusus data historis lama sebelum kolom snapshot diterapkan.
- `traffic:invalid_slug`: Percobaan akses pada slug acak yang tidak pernah ada di database.
- `system:anomaly`: Direct POST proceed tanpa sesi valid.

### 4.2 Opsi Penyajian pada Dashboard Admin
Tim mengevaluasi 3 opsi antarmuka:
- **Opsi 1 (Inline with Badge):** Menampilkan semua link termasuk yang dihapus dalam tabel utama dengan badge `[Terhapus]`.
- **Opsi 2 (Hidden by Default dengan Toggle Checkbox — Rekomendasi Tim):** Tabel hanya menampilkan link aktif & trash secara default. Checkbox *"Tampilkan Tautan Terhapus"* disediakan untuk menampilkan data historis saat diaudit.
- **Opsi 3 (Dedicated Tab):** Memisahkan data tautan terhapus ke dalam tab khusus.

> **CATATAN KEPUTUSAN:**  
> Opsi 2 berstatus **REKOMENDASI PENINJAU**. Product Owner bebas memilih Opsi 1, 2, atau 3 pada [PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md).

---

## 5. Bagian D: Rekomendasi Final & Kriteria Keberhasilan DEC-016

### Rekomendasi Tim Peninjau:
Tim peninjau menyarankan Product Owner untuk **MENYETUJUI (APPROVE)** DEC-016 dengan parameter:
1. Penambahan snapshot `link_id_snapshot` dan `link_slug_snapshot` pada `visits`.
2. Penerapan Arsitektur Idempotensi Berbasis Watermark & Checkpoint (Opsi C).
3. Penerapan penyajian UI Opsi 2 (Checkbox Toggle).

### Kriteria Keberhasilan (Acceptance Criteria):
- [ ] Simulasi storage menunjukkan pertambahan kapasitas ~63,9 MB per 90 hari pada trafik normal aman di bawah batas hosting.
- [ ] Seluruh 6 skenario kegagalan idempotensi tertangani tanpa double counting atau data loss.
- [ ] Pengukuran aktual storage wajib diverifikasi pasca-implementasi migrasi database.

