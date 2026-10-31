# Open Decisions Registry — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | OPEN-DECISIONS |
| **Versi** | 1.2.2 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Status** | ACTIVE (Reconciled with Product Owner Approvals) |

---

## 1. Daftar Keputusan Terbuka (Open Decisions Log)

Dokumen ini mencatat seluruh keputusan arsitektural, produk, dan teknis platform LokaTaut. Pada **31 Oktober 2026**, **Product Owner (Dimas Aditya)** telah memberikan persetujuan eksplisit terhadap **14 Keputusan Terpilih (DEC-001 s/d DEC-011, DEC-013, DEC-014, dan DEC-015)** melalui konfirmasi resmi dalam sesi komunikasi proyek.

Sesuai aturan tata kelola `AGENTS.md`, AI Coding Agent **DILARANG** menyetujui keputusan yang belum disetujui (DEC-012 dan DEC-016 tetap `UNDER REVIEW / PROPOSED`) dan dilarang menyetujui ADR secara otomatis.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Open Decisions Summary Matrix                                        │
├─────────┬──────────────────────────────────┬──────────────┬──────────────┬─────────────┬───────────────┤
│ DEC ID  │ Topik Keputusan                  │ Status       │ Tanggal      │ Approval PO │ Bukti Otoritas│
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

## 2. Rincian & Catatan Keputusan

### DEC-001: Migrasi Framework ke Laravel 13.x
- **Konteks:** Laravel 11 telah melewati masa dukungan keamanan (EOL) pada 12 Maret 2026.
- **Keputusan:** Mengadopsi **Laravel 13.x** dengan runtime **PHP 8.3+** sebagai baseline baku untuk menjamin dukungan keamanan hingga Maret 2028.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-002: Status Kandidat Jaringan Iklan (Adsterra)
- **Konteks:** Menghindari asumsi sepihak mengenai persetujuan format iklan safelink tanpa verifikasi akun riil.
- **Keputusan:** Menetapkan Adsterra sebagai `CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT & DOMAIN VERIFICATION)`. Desain slot iklan dibuat agnostik sehingga dapat memuat script jaringan lain jika diperlukan.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-003: Verifikasi Runtime Hosting WarnaHost Starter
- **Konteks:** Keterbatasan resource 1 vCPU dan 2 GB RAM pada shared hosting.
- **Keputusan:** Menetapkan WarnaHost Starter Unlimited sebagai `TARGET HOSTING (NEEDS PRE-DEPLOYMENT RUNTIME VERIFICATION)`. Wajib memverifikasi ketersediaan PHP 8.3 CLI dan Composer 2 di cPanel sebelum instalasi final.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-004: Durasi Default Countdown Timer
- **Konteks:** Keseimbangan antara pengalaman pengunjung dan waktu tayang impresi iklan.
- **Keputusan:** 10 detik ditetapkan sebagai durasi default global, dengan fleksibilitas dikonfigurasi per tautan melalui panel admin.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-005: Titik Penempatan Cloudflare Turnstile
- **Konteks:** Menentukan kapan tantangan verifikasi bot dimunculkan kepada pengunjung.
- **Keputusan:** Turnstile ditempatkan **sebelum countdown dimulai**. Setelah verifikasi lolos via AJAX, timer 10 detik mulai berdetak.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-006: Kebijakan Retensi Data Kunjungan
- **Konteks:** Mengontrol ukuran tabel `visits` agar tidak melampaui kapasitas disk shared hosting.
- **Keputusan:** Data mentah disimpan maksimal **90 hari** sebelum dibersihkan via scheduler harian, setelah diringkas permanen ke `daily_stats`.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-007: Penangguhan Format Popunder & Isolasi Tombol Navigasi
- **Konteks:** Menghindari penalti Invalid Traffic (IVT) Adsterra atas pelanggaran *forced/incentivized clicks*.
- **Keputusan:** Menangguhkan format popunder pada MVP dan memfokuskan monetisasi pada banner display terlabel jelas dengan jarak aman 30px dari tombol aksi.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-008: Penambahan Fitur Ganti Kata Sandi Admin (`FR-AUTH-004`)
- **Konteks:** Menyediakan antarmuka mandiri bagi admin tunggal untuk merotasi kata sandi akun secara aman.
- **Keputusan:** Menyetujui implementasi `FR-AUTH-004`, rute `/admin/profile`, dan validasi kata sandi lama/baru.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-009: Penyimpanan Hashed Session Token (`token_hash`)
- **Konteks:** Mematuhi prinsip *Defense-in-Depth* OWASP ASVS v4.0 Section 3.2.4.
- **Keputusan:** Menyetujui skema `visitor_sessions.token_hash` (SHA-256) untuk melindungi token dari kebocoran basis data.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-010: Prosedur Fallback Pre-Built Vendor Deployment
- **Konteks:** Mitigasi risiko *Process Killed / Out Of Memory* saat live `composer install` di CloudLinux WarnaHost.
- **Keputusan:** Menyetujui opsi rilis paket vendor pre-built via ZIP archive sebagai cadangan utama pada `DOC-13`.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-011: Mode Darurat Turnstile (`turnstile_mode`)
- **Konteks:** Menjamin kelangsungan akses unduhan pengunjung jika API Cloudflare Turnstile mengalami outage global.
- **Keputusan:** Menyetujui penambahan setting switch darurat `turnstile_mode = emergency_bypass` dengan audit log di `security_logs`.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-012: Persetujuan Formal Dokumen ADR-001 s/d ADR-017
- **Konteks:** Memberikan otorisasi formal pada berkas Architecture Decision Record sebelum fase implementasi kode dibuka.
- **Aturan Formal Persetujuan ADR:**
  1. **Otoritas Tunggal:** Architecture Decision Records (ADR) merupakan instrumen tata kelola permanen. AI Coding Agent dan AI Documentation Specialist **TIDAK MEMILIKI WEWENANG** menyetujui ADR atas nama Product Owner.
  2. **Independensi Status:** Setiap ADR (`ADR-001` s/d `ADR-017`) harus memiliki status mandiri dan bukti persetujuan tersendiri.
  3. **Pemisahan Keputusan Produk vs Rekam Arsitektur:** Meskipun Product Owner telah menyetujui 14 keputusan produk/kebijakan (DEC-001 s/d DEC-011, DEC-013 s/d DEC-015), berkas arsitektur teknis di `DOC-15` tetap berstatus `PROPOSED / UNDER REVIEW` hingga Product Owner secara eksplisit mengesahkan DEC-012.
- **Status:** `UNDER REVIEW by Product Owner`.
- **Approval Evidence:** Pending Product Owner Formal Decision.

### DEC-013: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Snapshot Slug
- **Konteks:** Menjaga keutuhan analitik historis saat tautan dihapus permanen.
- **Keputusan:** Menyetujui kebijakan: Soft Delete sebagai default operasional (`deleted_at = NOW()`); Hard Delete menyetel `visits.link_id` dan `daily_stats.link_id` menjadi `NULL` (`ON DELETE SET NULL`) dengan snapshot `link_slug` historis tersimpan pada `daily_stats`.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-014: Mandatori Pemisahan Kunci ANALYTICS_SALT Tanpa Fallback
- **Konteks:** Menjamin isolasi siklus kunci analitik dari `APP_KEY` sesuai standar NIST SP 800-57 Part 1.
- **Keputusan:** Menyetujui penghapusan mutlak fallback `ANALYTICS_SALT` ke `APP_KEY`, penegakan exception `RuntimeException` jika tidak dikonfigurasi di production, dan dokumentasi batas kontinuitas unique visitor saat rotasi kunci.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-015: Dedicated Aggregate Key NOT NULL pada Tabel `daily_stats`
- **Konteks:** Mengatasi kelemahan MySQL/InnoDB di mana `UNIQUE KEY (link_id, date)` membiarkan duplikasi baris ketika `link_id IS NULL`, serta menjamin idempotensi upsert dan keamanan proses retry nightly command.
- **Keputusan:** Menyetujui penambahan kolom `aggregate_key VARCHAR(100) NOT NULL` (format `'link:' . $id` atau `'system:unassigned'`) dan constraint `UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)` di mana kedua kolom `NOT NULL`.
- **Status:** `APPROVED`.
- **Otoritas Persetujuan:** Product Owner (Dimas Aditya).
- **Tanggal Persetujuan:** 2026-10-31.
- **Bukti Persetujuan (Approval Evidence):** Explicit Product Owner confirmation in project conversation (31 Oktober 2026).

### DEC-016: Snapshot Identitas Tautan Kunjungan (`visits.link_id_snapshot` & `link_slug_snapshot`)
- **Konteks:** Menjaga kontinuitas identitas tautan pada rekaman event `visits` jika tautan di-hard delete sebelum scheduled command nightly dijalankan.
- **Pemeriksaan Kompatibilitas terhadap DEC-015:**
  - DEC-015 berfokus pada **tabel target (`daily_stats`)** dengan memberlakukan kunci partisi unik NOT NULL.
  - DEC-016 berfokus pada **tabel sumber (`visits`)** dengan menambahkan snapshot data saat event dicatat.
  - **Kesimpulan:** Keduanya saling melengkapi (*harmonious & complementary*), tidak saling bertentangan.
- **Detail Teknis yang Membutuhkan Keputusan Product Owner:**
  1. **Trade-off Penyimpanan (Storage Overhead):** Menambahkan 2 kolom snapshot (`BIGINT UNSIGNED` 8 byte + `VARCHAR(50)` ~51 byte + index) pada tabel `visits` menambah footprint ~1.5–3 MB per hari (135–270 MB per 90 hari). Apakah penambahan ini disetujui dalam batas kuota disk NVMe hosting?
  2. **Representasi Visual pada Dashboard:** Ketika link di-hard delete, bagaimana agregasi historis berbasis `link_id_snapshot` disajikan pada UI? Apakah diberi label `[Terhapus: slug-asli]` atau disembunyikan secara default kecuali admin mencentang filter khusus?
  3. **Penanganan Event Kunjungan Anomali:** Jika terjadi kunjungan safelink di mana link di-hard delete tepat sebelum redirect selesai, event dikelompokkan ke `aggregate_key = 'system:unassigned'`. Apakah bucket anomali ini perlu ditampilkan pada kartu metrik dashboard admin?
- **Status:** `UNDER REVIEW / PROPOSED (Menunggu Keputusan Product Owner)`.
- **Approval Evidence:** Pending Product Owner Formal Decision.
