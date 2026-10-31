# Analisis Teknis & Rekomendasi Terbuka: DEC-016 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DEC-016-TECH-01 |
| **Versi Dokumen** | 1.0.1 (Pemisahan Tegas dari DEC-015 & Koreksi Taksonomi) |
| **Status Keputusan** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) & [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md)) |
| **Tanggal Analisis** | 31 Oktober 2026 |
| **Penyusun** | Senior Database Architect, Lead Software Architect, & UI/UX Specialist |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Pemisahan Tegas DEC-015 (Approved) dan DEC-016 (Under Review)

Untuk menjaga integritas tata kelola arsitektur, batasan kedua keputusan ini dipertegas secara definitif:

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
> 2. Simulasi pertambahan kapasitas penyimpanan disk selama retensi 90 hari.
> 3. Rekomendasi penyajian analitik historis tautan terhapus pada dasbor admin.
> 4. Taksonomi atribusi trafik dan arsitektur idempotensi agregasi 3-pilar.

---

## 2. Bagian A: Simulasi Dampak Penyimpanan Disk (Storage Impact Simulation)

### 2.1 Model Simulasi & Asumsi Eksplisit
Simulasi dihitung berdasarkan arsitektur InnoDB MySQL (format baris `DYNAMIC` dengan ukuran page 16 KB):
- **Ukuran Baris Dasar `visits` (v1.2.1):** ~250 byte per baris (termasuk index komposit).
- **Kolom Snapshot Tambahan (DEC-016):**
  - `link_id_snapshot` (`BIGINT UNSIGNED NOT NULL`): 8 byte data fisik + ~8 byte overhead index.
  - `link_slug_snapshot` (`VARCHAR(50) NOT NULL`): Panjang rata-rata slug realistis 20 karakter alfanumerik (`utf8mb4` = ~20 byte + 1 byte panjang) + ~8 byte overhead index.
- **Total Pertambahan Bersih:** Sekitar **+45 s/d 75 byte per baris** (asumsi konservatif: **+75 byte per baris**). Total ukuran baris `visits` menjadi **~325 byte**.

---

### 2.2 Proyeksi Kapasitas Selama Siklus Retensi 90 Hari

Sesuai DEC-006 (Approved), data mentah `visits` dibersihkan oleh scheduler setelah 90 hari. Proyeksi dihitung pada 3 skenario volume trafik:

| Skenario Trafik | Kunjungan / Hari | Total Baris (90 Hari) | Ukuran Base (v1.2.1) | Pertambahan Snapshot (DEC-016) | Total Ukuran Disk Tabel `visits` | % Alokasi NVMe (Asumsi 10 GB) |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| **Rendah (Skenario 1)** | 1.000 / hari | 90.000 baris | ~22,50 MB | **+6,75 MB** | **~29,25 MB** | **0,29%** |
| **Menengah (Skenario 2)** | 10.000 / hari | 900.000 baris | ~225,00 MB | **+67,50 MB** | **~292,50 MB** | **2,93%** |
| **Tinggi / Puncak (Skenario 3)** | 50.000 / hari | 4.500.000 baris | ~1.125,00 MB (1,10 GB) | **+337,50 MB** (0,33 GB) | **~1.462,50 MB (1,46 GB)** | **14,63%** |

> **DISCLAIMER SIMULASI ARSITEKTUR:**  
> Angka-angka di atas merupakan **simulasi matematis berbasis spesifikasi baris InnoDB dan asumsi panjang slug rata-rata** untuk keperluan *capacity planning*. Angka ini bukan pengukuran empiris pada basis data aktif produksi. Variasi aktual di server WarnaHost akan dipengaruhi oleh fragmentasi tabel InnoDB, index page fills (biasanya 70-80% pada B-Tree), dan rasio kompresi storage host.

### 2.3 Kesimpulan Kelayakan Penyimpanan:
Pada paket WarnaHost Starter Unlimited (NVMe SSD), pertambahan penyimpanan sebesar **+67,5 MB** pada trafik normal (10.000 kunjungan/hari) atau **+337,5 MB** pada skenario puncak agresif **sangat aman dan layak**. Manfaat pelestarian audit log jauh melampaui biaya komputasi storage tersebut.

---

## 3. Bagian B: Penyajian Analitik Historis pada Dashboard Admin

Ketika tautan mengalami *Hard Delete (Force Delete)*, rekaman baris pada tabel `links` dihapus secara fisik. Namun data kinerja historisnya tersimpan permanen di `daily_stats`. Tim peninjau mengevaluasi 3 opsi antarmuka bagi Product Owner:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                               Perbandingan 3 Opsi Penyajian UI                                  │
├───────────────────────┬───────────────────────────────┬─────────────────────────────────────────┤
│ Opsi Antarmuka        │ Kelebihan                     │ Kekurangan                              │
├───────────────────────┼───────────────────────────────┼─────────────────────────────────────────┤
│ **Opsi 1: Inline**    │ Seluruh data masa lalu terlihat│ Mengotori tabel operasional tautan      │
│ (Menyatu dalam tabel  │ dalam satu tampilan tabel      │ aktif; membingungkan admin jika ada     │
│ utama dengan badge)   │ tanpa klik tambahan.          │ ratusan tautan sampah yang sudah dihapus│
├───────────────────────┼───────────────────────────────┼─────────────────────────────────────────┤
│ **Opsi 2: Hidden by** │ **Tampilan operasional harian │ Membutuhkan 1 klik interaksi pengguna   │
│ **Default w/ Toggle** │ bersih; data historis tetap   │ untuk mencentang filter (checkbox).     │
│ *(REKOMENDASI TIM)*   │ dapat diakses saat audit;     │                                         │
│                       │ memisahkan link aktif & mati.**│                                         │
├───────────────────────┼───────────────────────────────┼─────────────────────────────────────────┤
│ **Opsi 3: Dedicated** │ Isolasi sempurna antara tautan│ Memecah ringkasan metrik; sulit melihat │
│ **Tab Riwayat**       │ aktif dan tautan terhapus.    │ korelasi pendapatan total lintas link.  │
└───────────────────────┴───────────────────────────────┴─────────────────────────────────────────┘
```

### 3.1 Rekomendasi Peninjau: Opsi 2 (Hidden by Default dengan Checkbox Toggle)
Tim merekomendasikan **Opsi 2**: Secara default, antarmuka daftar tautan dan analitik hanya menampilkan tautan aktif dan soft-deleted (Trash). Sebuah checkbox filter disediakan:
$$\text{[ ] } \textbf{Tampilkan Tautan Terhapus Permanen}$$

#### Mockup Antarmuka (Ketika Checkbox Dicentang):
```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ Filter: [Semua Status ▼]  [Rentang Waktu: 30 Hari ▼]  [X] Tampilkan Tautan Terhapus    │
├──────────────┬────────────────────────┬──────────┬───────────┬────────────┬────────────┤
│ Slug Tautan  │ Judul / Status         │ Tayangan │ Selesai   │ Konversi % │ Aksi       │
├──────────────┼────────────────────────┼──────────┼───────────┼────────────┼────────────┤
│ photoshop-26 │ Photoshop 2026 Installer│ 14.250   │ 11.400    │ 80,0 %     │ [Edit] [⚙] │
│              │ [Aktif]                │          │           │            │            │
├──────────────┼────────────────────────┼──────────┼───────────┼────────────┼────────────┤
│ legacy-tool  │ [Tautan Dihapus        │ 3.400    │ 2.550     │ 75,0 %     │ [Lihat     │
│ (ID: #14)    │ Permanen] [Terhapus]   │          │           │            │  Grafik]   │
└──────────────┴────────────────────────┴──────────┴───────────┴────────────┴────────────┘
```

> **CATATAN STATUS KEPUTUSAN:**  
> Opsi 2 di atas berstatus **REKOMENDASI TIM PENINJAU**. Product Owner memiliki hak prerogatif penuh untuk memilih Opsi 1, Opsi 2, atau Opsi 3 pada lembar [PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md).

---

## 4. Bagian C: Taksonomi Trafik & Arsitektur Idempotensi 3-Pilar

### 4.1 Koreksi Taksonomi Trafik Tautan Terhapus
Untuk memastikan tidak ada data tautan yang hilang atau salah atrisi, aturan penentuan `aggregate_key` diperjelas:

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                      Taksonomi Partisi aggregate_key (daily_stats)                      │
├──────────────────────────┬─────────────────────────────┬────────────────────────────────┤
│ Format aggregate_key     │ Definisi Sumber Trafik      │ Penjelasan Status Data         │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ **`link:{id}`**          │ **Trafik Tautan Sah**       │ **Kunjungan normal, termasuk   │
│ (Contoh: `link:42`)      │ **(Aktif, Soft-Deleted,     │ tautan yang telah di-hard      │
│                          │ maupun Hard-Deleted)**      │ delete dari Trash (Preserved)**│
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `traffic:purged_legacy`  │ Trafik Tautan Pra-Snapshot  │ Data migrasi lama yang dihapus │
│                          │                             │ sebelum v1.2.2 diterapkan      │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `traffic:invalid_slug`   │ Akses Slug Fiktif / 404     │ Percobaan tebakan bot pada slug│
│                          │                             │ yang tidak pernah terdaftar    │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `system:anomaly`         │ Bypass / Request Korup      │ Direct POST proceed tanpa sesi │
└──────────────────────────┴─────────────────────────────┴────────────────────────────────┘
```

> **PRINSIP EMAS ATRIBUSI HISTORI:**  
> Jika tautan dengan ID 42 di-hard delete, baris kunjungannya di tabel `visits` tetap memiliki `link_id_snapshot = 42` dan `link_slug_snapshot = 'photoshop-2026'`. Oleh karena itu, agregasi hariannya **tetap menggunakan `aggregate_key = 'link:42'`**. Data tersebut **TIDAK PERNAH** dibuang ke `traffic:purged_legacy`.

---

### 4.2 Arsitektur Idempotensi 3-Pilar (Melampaui updateOrCreate Sederhana)

`DailyStat::updateOrCreate()` pada Laravel murni mengeksekusi `SELECT` lalu `INSERT / UPDATE`. Pada lingkungan konkuren atau jika scheduled command di-retry saat eksekusi pertama masih berjalan, `updateOrCreate()` standar rentan memicu *race condition* dan deadlock.

Untuk menjamin idempotensi 100% pada tingkat enterprise, sistem menerapkan **Arsitektur Idempotensi 3-Pilar**:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        Arsitektur Idempotensi Agregasi 3-Pilar                         │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│   [ Pilar 1: Scheduler Overlap Control ]                                               │
│   Schedule::command('lokataut:aggregate-daily')                                        │
│       ->withoutOverlapping(60)                                                         │
│       ->runInBackground();                                                             │
│                                                                                        │
│   [ Pilar 2: Database Transaction & Row-Level Lock ]                                   │
│   DB::transaction(function () use ($batchDate, $key) {                                 │
│       $stat = DailyStat::where('aggregate_key', $key)                                  │
│           ->where('date', $batchDate)                                                  │
│           ->lockForUpdate()                                                            │
│           ->first();                                                                   │
│       // Kalkulasi akumulasi idempoten                                                 │
│   });                                                                                  │
│                                                                                        │
│   [ Pilar 3: Chunked Batch Event Processing ]                                          │
│   Visit::whereDate('created_at', $batchDate)                                           │
│       ->chunkById(1000, function ($visits) { ... });                                   │
│                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

Dengan ketiga pilar ini:
1. Dua proses cron tidak akan pernah berjalan bersamaan (`withoutOverlapping`).
2. Mutasi baris `daily_stats` dikunci secara eksklusif dalam transaksi basis data (`lockForUpdate`).
3. Konsumsi RAM terkontrol di bawah 64 MB berkat pembagian batch 1.000 baris (`chunkById`).

---

## 5. Bagian D: Rekomendasi Final & Kriteria Keberhasilan (Acceptance Criteria)

### Rekomendasi Tim Peninjau:
Tim peninjau merekomendasikan Product Owner untuk **MENYETUJUI (APPROVE)** implementasi teknis DEC-016 dengan parameter:
1. Penambahan kolom `visits.link_id_snapshot` dan `visits.link_slug_snapshot`.
2. Penerapan penyajian UI **Opsi 2 (Checkbox Toggle "Tampilkan Tautan Terhapus")**.
3. Penerapan taksonomi partisi `link:{snapshot_id}` untuk tautan terhapus dan Idempotensi 3-Pilar.

### Kriteria Keberhasilan (Acceptance Criteria) untuk Otorisasi:
- [ ] Retensi data 90 hari menjaga ukuran tabel `visits` di bawah 1,5 GB pada skenario trafik puncak.
- [ ] Kueri agregasi harian bersifat idempoten: eksekusi ulang 3x berturut-turut menghasilkan data yang identik tanpa error constraint.
- [ ] Tautan yang di-hard delete tetap terlacak dengan format `link:{id}` dan slug snapshot terbaca pada grafik analitik.
- [ ] Test case `TEST-LINK-007` dan `TEST-ANA-004` siap dieksekusi pada Phase 5.

---

> **STATUS KEPUTUSAN:**  
> Dokumen ini berstatus **`UNDER REVIEW`**. Pengesahan formal menunggu input resmi dari Product Owner pada [PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md).

