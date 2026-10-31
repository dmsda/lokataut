# Database Integrity & Aggregation Architecture Review — LokaTaut v1.2.2

| Atribut | Nilai |
|---|---|
| **Document ID** | DATABASE-INTEGRITY-REVIEW |
| **Versi** | 1.2.2 |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Senior Database Architect & Software Architect |
| **Target Pembaca** | Product Owner (Dimas Aditya), Backend Engineer, Database Administrator |
| **Status Dokumen** | `SPECIFICATION REVIEWED (Ready for Product Owner Final Review)` |

---

## 1. Latar Belakang & Identifikasi Masalah

Pada evaluasi arsitektur v1.2.1, relasi antara entitas `links`, `visitor_sessions`, `visits`, dan `daily_stats` diaudit ulang. Ditemukan dua kelemahan fundamental pada implementasi MySQL:

### 1.1 Masalah 1: Karakteristik `NULL` pada Unique Index MySQL (`daily_stats`)
- Pada skema v1.2.1, kolom `daily_stats.link_id` didefinisikan sebagai `NULLABLE` dengan constraint `ON DELETE SET NULL` agar data statistik harian tidak terhapus saat tautan induk di-hard delete.
- Indeks unik komposit didefinisikan sebagai `UNIQUE KEY (link_id, date)`.
- **Kelemahan Kritis MySQL/InnoDB:** Berdasarkan standar SQL dan implementasi InnoDB Engine, perbandingan `NULL = NULL` menghasilkan `UNKNOWN`. Akibatnya, MySQL **mengizinkan banyak baris bernilai `NULL` pada indeks UNIQUE**.
- Konsekuensinya: Jika beberapa tautan di-hard delete pada tanggal yang sama, atau jika terjadi eksekusi ulang (*retry*) agregasi, database akan mengizinkan duplikasi baris dengan `(NULL, '2026-10-31')`. Perintah `INSERT ... ON DUPLICATE KEY UPDATE` maupun Eloquent `updateOrCreate` tidak dapat mendeteksi konflik keunikan pada kolom `NULL`, sehingga metrik tayangan dan konversi akan terduplikasi.

### 1.2 Masalah 2: Hilangnya Identitas Tautan Sebelum Agregasi Harian (`visits`)
- Tautan yang di-hard delete di tengah hari (misalnya pukul 14:00 WIB) akan memicu foreign key `ON DELETE SET NULL` pada tabel `visits`.
- Akibatnya, seluruh event kunjungan (`impression`, `verified`, `completed`) yang terjadi antara pukul 00:00 hingga 14:00 pada hari tersebut seketika kehilangan `link_id` (`link_id` menjadi `NULL`).
- Saat scheduled command `AggregateDailyStatsCommand` berjalan pada pukul 00:05 WIB dini hari berikutnya, sistem tidak lagi dapat mengidentifikasi tautan mana yang menghasilkan kunjungan-kunjungan tersebut. Data mentah tersebut terdorong ke bucket *unassigned* anonim, merusak akurasi pelaporan historis per tautan.

---

## 2. Evaluasi Alternatif Solusi (FIX-001 & FIX-002)

Empat alternatif arsitektur dievaluasi untuk menyelesaikan kedua kendala di atas dengan mempertimbangkan batasan shared hosting (1 vCPU, 2 GB RAM, MySQL 8.0):

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 Matriks Evaluasi Alternatif Solusi                              │
├─────────┬──────────────────────────┬──────────────┬──────────────┬──────────────────────────────┤
│ Opsi    │ Pendekatan               │ Kelebihan    │ Kekurangan   │ Kelayakan Shared Hosting     │
├─────────┼──────────────────────────┼──────────────┼──────────────┼──────────────────────────────┤
│ Opsi 1  │ Dedicated Aggregate Key  │ Sederhana,   │ Butuh 1 col  │ SANGAT TINGGI (Dipilih)      │
│         │ NOT NULL + Snapshots     │ 100% idemp., │ tambahan di  │ Efisien, performa B-Tree     │
│         │ di visits & daily_stats  │ zero orphan  │ tabel        │ optimal, O(log N)            │
├─────────┼──────────────────────────┼──────────────┼──────────────┼──────────────────────────────┤
│ Opsi 2  │ Historical Link Identity │ Normalisasi  │ Butuh tabel  │ SEDANG                       │
│         │ Table (Tabel Terpisah)   │ murni        │ baru & relasi│ Menambah overhead JOIN & IOPS│
│         │                          │              │ kompleks     │ disk pada shared hosting     │
├─────────┼──────────────────────────┼──────────────┼──────────────┼──────────────────────────────┤
│ Opsi 3  │ Dedicated Unassigned     │ Mengisolasi  │ Tidak mem-   │ RENDAH                       │
│         │ Record per Hari          │ link terhapus│ perbaiki data│ Membuang data atribusi       │
│         │                          │              │ per link     │ kunjungan sebelum dihapus    │
├─────────┼──────────────────────────┼──────────────┼──────────────┼──────────────────────────────┤
│ Opsi 4  │ Struktur Agregasi        │ Pemisahan    │ Dual write,  │ RENDAH                       │
│         │ Terpisah Global Stats    │ tegas        │ kompleksitas │ Overhead pemeliharaan ganda  │
│         │                          │              │ scheduler    │ pada cronjob shared hosting  │
└─────────┴──────────────────────────┴──────────────┴──────────────┴──────────────────────────────┘
```

---

## 3. Keputusan Desain Terpilih (The Chosen Architecture)

Berdasarkan evaluasi di atas, LokaTaut v1.2.2 mengadopsi **Opsi 1: Dedicated Aggregate Key NOT NULL dengan Snapshot Identitas Permanen**. Pendekatan ini merupakan solusi yang paling sederhana, deterministik, dan bebas dari celah `NULL` pada MySQL.

### 3.1 Struktur Tabel `daily_stats` yang Disempurnakan
Tabel `daily_stats` menggunakan kolom identitas agregasi mandatori:
1. `aggregate_key VARCHAR(100) NOT NULL`:
   - Untuk tautan spesifik: `'link:' . $link_id_snapshot` (misal: `'link:42'`).
   - Untuk trafik global/sistem tanpa tautan: `'system:unassigned'`.
2. `date DATE NOT NULL`: Tanggal agregasi (YYYY-MM-DD).
3. `link_id BIGINT UNSIGNED NULLABLE`: Foreign key relasional ke `links.id` (`ON DELETE SET NULL`) untuk kemudahan relasi Eloquent aktif.
4. `link_slug VARCHAR(50) NOT NULL`: Snapshot slug tautan saat agregasi (misal: `'photoshop-2026'` atau `'[unassigned]'`).
5. **Indeks Keunikan Komposit (Deterministic Unique Constraint):**
   ```sql
   UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)
   ```
   > **Keunggulan Teknis:** Karena kolom `aggregate_key` dan `date` **keduanya `NOT NULL`**, MySQL/InnoDB menjamin integritas keunikan 100%. Tidak ada toleransi baris ganda. Operasi `INSERT ... ON DUPLICATE KEY UPDATE` dan `updateOrCreate` menjadi idempoten secara absolut.

### 3.2 Struktur Tabel `visits` yang Disempurnakan
Untuk mencegah hilangnya informasi sebelum agregasi harian dieksekusi:
1. `link_id BIGINT UNSIGNED NULLABLE`: Foreign key ke `links.id` (`ON DELETE SET NULL`).
2. `link_id_snapshot BIGINT UNSIGNED NOT NULL`: Nilai ID tautan yang dicatat saat event kunjungan dibuat. Nilai ini bersifat **immutable** (tidak terpengaruh oleh foreign key cascading).
3. `link_slug_snapshot VARCHAR(50) NOT NULL`: Nilai slug tautan saat kunjungan dibuat (misal: `'photoshop-2026'`).

---

## 4. Analisis Skenario Ekstrem (Edge Cases Validation)

### 4.1 Skenario 1: Tautan Di-Hard Delete SEBELUM Agregasi Harian Berjalan
- **Kronologi:**
  - 10:00 WIB: Pengunjung mengakses `/s/software-x`. 100 kunjungan tercatat di `visits` dengan `link_id = 15`, `link_id_snapshot = 15`, `link_slug_snapshot = 'software-x'`.
  - 14:00 WIB: Admin mengeksekusi Hard Delete pada link ID 15 dari Trash.
  - Dampak database: Baris di `links` terhapus. Kolom `visits.link_id` menjadi `NULL` via `ON DELETE SET NULL`.
  - 00:05 WIB (Dini hari): `AggregateDailyStatsCommand` berjalan untuk tanggal kemarin.
- **Logika Eksekusi Command:**
  - Command mengelompokkan data berdasarkan `link_id_snapshot` dan `link_slug_snapshot` dari tabel `visits`:
    ```sql
    SELECT link_id_snapshot, link_slug_snapshot, event_type, COUNT(*) 
    FROM visits 
    WHERE DATE(created_at) = '2026-10-31'
    GROUP BY link_id_snapshot, link_slug_snapshot, event_type;
    ```
  - Command menyusun `aggregate_key = 'link:15'`, `link_slug = 'software-x'`, `link_id = NULL`.
- **Hasil:** Seluruh 100 kunjungan berhasil diagregasi secara utuh ke `daily_stats` dengan identitas slug `'software-x'`. **Zero data loss.**

### 4.2 Skenario 2: Eksekusi Retry Agregasi (Idempotency Test)
- **Kronologi:**
  - Command `lokataut:aggregate-daily --date=2026-10-31` dijalankan pertama kali: 1 baris terbentuk untuk `aggregate_key = 'link:15'`, `date = '2026-10-31'`, `impressions = 100`.
  - Operator menjalankan ulang command secara manual (retry) untuk tanggal yang sama.
- **Hasil:**
  - Karena `(aggregate_key, date)` berstatus UNIQUE NOT NULL, MySQL melakukan UPDATE pada baris yang sama.
  - Nilai `impressions` tetap 100. Tidak ada duplikasi baris, tidak ada inflasi angka metrik.

### 4.3 Skenario 3: Penanganan Kunjungan Tanpa Link Valid (Orphan / Direct Safe Route)
- Jika request safelink masuk dengan slug tidak valid atau bypass buatan:
- `link_id_snapshot` diisi `0`, `link_slug_snapshot` diisi `'[unassigned]'`.
- `aggregate_key` menjadi `'system:unassigned'`.
- Metrik tersimpan rapi pada baris sistem tanpa mencemari laporan performa tautan unduhan milik admin.

---

## 5. Kesimpulan & Implikasi Migrasi

Desain ini menyelesaikan 100% temuan FIX-001 dan FIX-002:
1. **Garansi Idempotensi:** Indeks `(aggregate_key, date)` yang `NOT NULL` mematikan risiko duplikasi data pada shared hosting.
2. **Integritas Historis:** Data riwayat analitik tetap dapat dilaporkan per tautan meskipun entitas tautan fisiknya telah dibersihkan permanen dari database.
3. **Kompatibilitas:** Sepenuhnya kompatibel dengan MySQL 8.0 dan MariaDB 10.5+ tanpa memerlukan ekstensi khusus atau engine storage sekunder.

