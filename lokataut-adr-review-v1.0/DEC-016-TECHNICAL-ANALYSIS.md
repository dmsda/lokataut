# Analisis Teknis & Rekomendasi Terbuka: DEC-016 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DEC-016-TECH-01 |
| **Versi Dokumen** | 1.0 |
| **Status Keputusan** | **UNDER REVIEW (Menunggu Persetujuan Formal Product Owner)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md), [DOC-06](../lokataut-documentation-v1.2.2/03-architecture/06-database-design.md), & [ADR-017](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md#adr-017-dedicated-aggregate-key-not-null-dan-snapshot-identitas-tautan-kunjungan)) |
| **Tanggal Analisis** | 31 Oktober 2026 |
| **Penyusun** | Senior Database Architect, Lead Software Architect, & UI/UX Specialist |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Konteks Masalah & Ruang Lingkup Analisis

Pada audit arsitektur v1.2.2, ditemukan kelemahan semantik basis data relasional: pada MySQL/InnoDB, constraint `UNIQUE KEY (link_id, date)` tidak menjamin keunikan ketika `link_id` bernilai `NULL` karena aturan SQL standar `NULL != NULL`. Jika tautan di-hard delete sebelum cron job agregasi tengah malam dijalankan, foreign key `link_id` pada tabel `visits` menjadi `NULL`, yang berisiko memicu duplikasi baris statistik saat retry scheduler dijalankan.

Untuk mengatasi ini, ADR-017 mengusulkan:
1. Kolom snapshot `link_id_snapshot` dan `link_slug_snapshot` pada tabel `visits`.
2. Kolom kunci keras `aggregate_key VARCHAR(100) NOT NULL` pada tabel `daily_stats`.

Product Owner menahan persetujuan **DEC-016** dalam status `UNDER REVIEW` untuk meminta telaah independen terhadap tiga pertanyaan teknis fundamental:
- **Bagian A:** Berapa dampak riil penambahan kolom snapshot terhadap ukuran penyimpanan disk MySQL selama siklus retensi 90 hari?
- **Bagian B:** Bagaimana opsi terbaik menyajikan data performa tautan yang telah di-hard delete pada antarmuka admin dashboard?
- **Bagian C:** Bagaimana mempartisi taksonomi trafik tak bertuan (*unassigned traffic*) agar tidak menumpuk menjadi satu bucket anonim?

Laporan ini menyajikan analisis matematis, perbandingan UX, dan spesifikasi arsitektur untuk memfasilitasi keputusan final Product Owner.

---

## 2. Bagian A: Simulasi Dampak Penyimpanan Disk (Storage Impact Simulation)

### 2.1 Dekomposisi Ukuran Baris (InnoDB Row Breakdown)

Pada tabel `visits` MySQL InnoDB (format baris `DYNAMIC`), kita menghitung pertambahan byte fisik per baris data kunjungan:

```
┌────────────────────────────────────────────────────────────────────────┐
│               Dekomposisi Penambahan Kolom per Baris                   │
├──────────────────────┬────────────────┬───────────────┬────────────────┤
│ Kolom / Komponen     │ Tipe Data      │ Ukuran Data   │ Overhead Index │
├──────────────────────┼────────────────┼───────────────┼────────────────┤
│ Base Row (v1.2.1)    │ -              │ ~250 bytes    │ Termasuk index │
├──────────────────────┼────────────────┼───────────────┼────────────────┤
│ link_id_snapshot     │ BIGINT UNSIGNED│ 8 bytes       │ ~8 bytes       │
│ link_slug_snapshot   │ VARCHAR(50)    │ ~20 bytes avg │ ~8 bytes       │
│                      │ (utf8mb4)      │ (+1B length)  │                │
├──────────────────────┼────────────────┼───────────────┼────────────────┤
│ Total Pertambahan    │ -              │ ~29 bytes     │ ~16 bytes      │
│ Total Net Per Baris  │ -              │       +45 s/d 75 bytes         │
│ Konservatif (Worst)  │ -              │          ~75 bytes             │
└──────────────────────┴────────────────┴───────────────┴────────────────┘
```

Dengan asumsi konservatif (panjang slug rata-rata 20 karakter alfanumerik ditambah overhead halaman indeks B-Tree 16 KB InnoDB), **pertambahan ukuran fisik adalah ~75 bytes per baris**. Ukuran total satu baris `visits` meningkat dari ~250 bytes menjadi **~325 bytes**.

---

### 2.2 Proyeksi Pertumbuhan Disk Selama Siklus Retensi 90 Hari

Sesuai ADR-010, data mentah `visits` disimpan dengan retensi maksimal 90 hari sebelum dibersihkan oleh cron mingguan. Simulasi dihitung pada 3 skenario volume trafik:

| Skenario Trafik | Kunjungan / Hari | Total Baris (90 Hari) | Ukuran Tabel Base (v1.2.1) | Pertambahan Snapshot (v1.2.2) | Total Ukuran Disk Tabel `visits` | % Alokasi NVMe (Asumsi 10 GB) |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| **Rendah (Skenario 1)** | 1.000 / hari | 90.000 baris | ~22,50 MB | **+6,75 MB** | **~29,25 MB** | **0,29%** |
| **Menengah (Skenario 2)** | 10.000 / hari | 900.000 baris | ~225,00 MB | **+67,50 MB** | **~292,50 MB** | **2,93%** |
| **Tinggi (Skenario 3)** | 50.000 / hari | 4.500.000 baris | ~1.125,00 MB (1,10 GB) | **+337,50 MB** (0,33 GB) | **~1.462,50 MB (1,46 GB)** | **14,63%** |

> **Catatan Pertambahan pada Tabel `daily_stats`:**  
> Tabel `daily_stats` hanya menyimpan 1 baris per tautan per hari. Jika sistem mengelola 1.000 tautan selama 90 hari = 90.000 baris. Penambahan kolom `aggregate_key VARCHAR(100)` (+24 bytes per baris) hanya menambah **~2,16 MB**, yang secara komputasi dapat diabaikan.

> **DISCLAIMER ESTIMASI ARSITEKTUR:**  
> Angka-angka di atas merupakan simulasi matematis berbasis spesifikasi format internal InnoDB dan panjang slug tipikal untuk tujuan *capacity planning*. Angka ini bukan pengukuran empiris pada basis data aktif produksi. Variasi aktual dipengaruhi oleh fragmentasi tabel InnoDB, rasio kompresi OS, dan indeks B-Tree page splits.

### 2.3 Kesimpulan Kelayakan Penyimpanan:
Pada paket WarnaHost Starter Unlimited (NVMe SSD), penambahan ukuran maksimum sebesar **+67,5 MB** pada trafik menengah (10.000 kunjungan/hari) atau **+337,5 MB** pada skenario trafik agresif (50.000 kunjungan/hari) **sangat aman dan layak**. Stabilitas dan integritas matematis data agregasi jauh lebih berharga daripada penghematan ~67 MB disk.

---

## 3. Bagian B: Penyajian Analitik Historis pada Dashboard Admin

Ketika tautan mengalami *Hard Delete (Force Delete)*, baris `links` fisik dihapus, namun riwayat kinerja tautan tersebut tersimpan di `daily_stats` dengan `aggregate_key = 'link:42'` dan `link_slug = 'software-v1'`. Kami mengevaluasi 3 opsi antarmuka untuk menyajikan data ini:

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

### 3.1 Rekomendasi Utama Tim: Opsi 2 (Hidden by Default dengan Checkbox Toggle)

Tim merekomendasikan **Opsi 2**: Secara default, antarmuka daftar tautan dan analitik hanya menampilkan tautan aktif dan soft-deleted (Trash). Sebuah checkbox filter disediakan di pojok kanan atas:
$$\text{[ ] } \textbf{Tampilkan Tautan Terhapus Permanen}$$

#### Mockup Tampilan Antarmuka (Ketika Checkbox Dicentang):
```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ Filter: [Semua Status ▼]  [Rentang Waktu: 30 Hari ▼]  [X] Tampilkan Tautan Terhapus    │
├──────────────┬────────────────────────┬──────────┬───────────┬────────────┬────────────┤
│ Slug Tautan  │ Judul / Status         │ Tayangan │ Selesai   │ Konversi % │ Aksi       │
├──────────────┼────────────────────────┼──────────┼───────────┼────────────┼────────────┤
│ photoshop-26 │ Photoshop 2026 Installer│ 14.250   │ 11.400    │ 80,0 %     │ [Edit] [⚙] │
│              │ [Aktif]                │          │           │            │            │
├──────────────┼────────────────────────┼──────────┼───────────┼────────────┼────────────┤
│ win-activator│ Windows Activator      │ 8.120    │ 6.090     │ 75,0 %     │ [Pulihkan] │
│              │ [Di Tempat Sampah]     │          │           │            │            │
├──────────────┼────────────────────────┼──────────┼───────────┼────────────┼────────────┤
│ legacy-tool-v1│ [Tautan Dihapus Permanen│ 3.400    │ 2.550     │ 75,0 %     │ [Lihat     │
│              │ (ID: #14)] [Terhapus]  │          │           │            │  Grafik]   │
└──────────────┴────────────────────────┴──────────┴───────────┴────────────┴────────────┘
```

#### Aturan Tampilan & Perilaku:
1. Kolom tautan terhapus permanen ditampilkan dengan teks abu-abu miring (*muted text*), badge warna netral `[Terhapus Permanen]`, dan slug snapshot aslinya.
2. Tombol `Edit` atau `Buka Link` dinonaktifkan (disabled), hanya tombol `Lihat Grafik Historis` yang aktif.
3. Total agregat metrik kartu ringkasan dashboard (Total Tayangan & Unduhan) **selalu menyertakan** trafik dari tautan terhapus agar laporan pendapatan bulanan klop 100% dengan dasbor jaringan iklan.

---

## 4. Bagian C: Taksonomi & Partisi Trafik Tak Bertuan (Unassigned Traffic)

Untuk menghindari penumpukan anomali trafik ke dalam satu bucket global yang ambigu (`system:unassigned`), tim menyusun **arsitektur partisi taksonomi trafik** berbasis `aggregate_key`:

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                      Taksonomi Partisi aggregate_key (daily_stats)                      │
├──────────────────────────┬─────────────────────────────┬────────────────────────────────┤
│ Pola Nilai aggregate_key │ Definisi / Sumber Trafik    │ Skenario Pemicu                │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `link:{id}`              │ Trafik Tautan Sah           │ Kunjungan normal pada tautan   │
│ (Contoh: `link:42`)      │ (Snapshot ID Preserved)     │ (aktif, expired, atau deleted) │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `traffic:purged_legacy`  │ Trafik Tautan Pra-Snapshot  │ Data migrasi lama yang dihapus │
│                          │ (Legacy Purged Records)     │ sebelum v1.2.2 diterapkan      │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `traffic:invalid_slug`   │ Akses Slug Fiktif / 404     │ Bot/perayap menebak slug acak  │
│                          │                             │ yang tidak pernah terdaftar    │
├──────────────────────────┼─────────────────────────────┼────────────────────────────────┤
│ `system:anomaly`         │ Bypass / Request Anomali    │ Direct POST proceed tanpa sesi │
│                          │                             │ atau pelanggaran token         │
└──────────────────────────┴─────────────────────────────┴────────────────────────────────┘
```

### Implementasi Logika Resolusi Kunci (Scheduled Command):
```php
// app/Console/Commands/AggregateDailyStatsCommand.php
$aggregateKey = match (true) {
    $visit->link_id_snapshot > 0 => 'link:' . $visit->link_id_snapshot,
    $visit->is_legacy_purged     => 'traffic:purged_legacy',
    $visit->is_invalid_slug      => 'traffic:invalid_slug',
    default                      => 'system:anomaly',
};
```

Keuntungan Partisi:
- Administrator dapat melihat secara terpisah berapa banyak trafik bot yang mencoba menebak URL (`traffic:invalid_slug`) tanpa mencampurkannya dengan trafik tautan unduhan yang sah.

---

## 5. Bagian D: Rekomendasi Final & Kriteria Keberhasilan (Acceptance Criteria)

### Rekomendasi Tim Peninjau:
Tim peninjau menyarankan Product Owner untuk **MENYETUJUI (APPROVE)** implementasi teknis DEC-016 dengan ketentuan:
1. Mengadopsi penambahan kolom snapshot pada tabel `visits` dan `aggregate_key` pada `daily_stats` (ADR-017).
2. Menerapkan penyajian antarmuka **Opsi 2 (Hidden by Default dengan Checkbox Toggle)**.
3. Menerapkan taksonomi partisi trafik tak bertuan sesuai Bagian C.

### Kriteria Keberhasilan (Acceptance Criteria) untuk Approval:
- [ ] Retensi data 90 hari terbukti membatasi pembengkakan disk tabel `visits` di bawah 1,5 GB pada skenario trafik puncak.
- [ ] Kueri upsert agregasi harian bersifat idempoten: eksekusi ulang `php artisan lokataut:aggregate-daily` 3x berturut-turut menghasilkan jumlah baris `daily_stats` yang persis sama tanpa error duplikasi MySQL.
- [ ] Tautan yang di-hard delete dari Trash mempertahankan baris statistiknya dengan `link_slug` snapshot yang dapat ditampilkan saat filter diaktifkan di dasbor admin.
- [ ] Seluruh skenario pengujian terkait (`TEST-LINK-006`, `TEST-LINK-007`, `TEST-ANA-004`) terverifikasi siap uji.

---

> **STATUS KEPUTUSAN:**  
> Dokumen ini berstatus **`UNDER REVIEW`**. Pengesahan formal menunggu input dan penandatanganan resmi oleh Product Owner pada [PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md).

