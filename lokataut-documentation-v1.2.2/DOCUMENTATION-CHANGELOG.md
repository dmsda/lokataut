# Documentation Changelog — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | CHANGELOG |
| **Versi** | 1.2.2 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |

---

## Versi 1.2.2 (31 Oktober 2026) — Database Integrity & Final Governance Remediation

### 1. Integritas Agregasi `daily_stats` & Idempotensi Upsert (FIX-001)
- **DOC-06, DOC-07, DOC-15 (ADR-017), DATABASE-INTEGRITY-REVIEW.md:**
  - Menambahkan kolom `aggregate_key VARCHAR(100) NOT NULL` pada tabel `daily_stats` dengan format deterministik `'link:' . $linkIdSnapshot` (misal: `'link:42'`) atau `'system:unassigned'`.
  - Menerapkan constraint unik definitif `UNIQUE KEY daily_stats_key_date_unique (aggregate_key, date)` di mana kedua kolom `NOT NULL`.
  - Menghilangkan celah fatal MySQL di mana `UNIQUE (link_id, date)` membiarkan duplikasi baris ketika `link_id IS NULL`.
  - Menjamin eksekusi ulang (retry) scheduled command `AggregateDailyStatsCommand` bersifat idempoten 100% tanpa risiko penggandaan metrik.

### 2. Snapshot Identitas Tautan Kunjungan (Historical Link Identity) (FIX-002)
- **DOC-06, DOC-07, DOC-02 (BR-016), DOC-04, DOC-05, DOC-15 (ADR-017):**
  - Menambahkan kolom snapshot immutable `link_id_snapshot BIGINT UNSIGNED NOT NULL` dan `link_slug_snapshot VARCHAR(50) NOT NULL` pada tabel `visits`.
  - Memastikan jika sebuah tautan di-hard-delete sebelum nightly cron berjalan (misal: tautan dibuat dan dihapus pada hari yang sama), event kunjungan tidak kehilangan identitas ID dan slug aslinya.
  - Memperbarui `VisitRecorderService` untuk merekam snapshot saat event dicatat, dan memperbarui `AggregateDailyStatsCommand` untuk mengelompokkan data berdasarkan pasangan snapshot tersebut.

### 3. Rekonsiliasi Tata Kelola Persetujuan (Approval Governance) (FIX-003)
- **OPEN-DECISIONS.md, GOVERNANCE-RECONCILIATION-REPORT.md, README.md, PRE-CODING-CHECKLIST.md:**
  - Menghapus seluruh klaim persetujuan fiktif (sebelumnya tercatat DEC-001 s/d DEC-006 disetujui tanpa approval registry resmi).
  - Mengklasifikasikan seluruh keputusan DEC-001 hingga DEC-015 dan seluruh arsitektur ADR-001 hingga ADR-017 sebagai `PROPOSED / UNDER REVIEW` dengan peninjau `Pending PO` dan tanggal `-`.
  - Menegakkan kepatuhan tata kelola bahwa hanya Product Owner (Dimas Aditya) yang berhak memberikan tanda tangan persetujuan formal sebelum fase koding dibuka.

### 4. Standarisasi Taksonomi Kesiapan Keamanan & QA (FIX-004)
- **Seluruh Dokumen (DOC-01 s/d DOC-16), PRE-CODING-CHECKLIST.md, GOVERNANCE-RECONCILIATION-REPORT.md:**
  - Membersihkan klaim prematur seperti "100% Secure", "Production ready", atau "Tests PASS (Hijau)".
  - Menerapkan standar taksonomi 4 status siklus hidup rekayasa perangkat lunak:
    - `SPECIFICATION DOCUMENTED`: Dokumen spesifikasi dan skenario pengujian telah disusun lengkap.
    - `SPECIFICATION REVIEWED`: Desain arsitektur dan model ancaman telah ditinjau (*Secure by Design*).
    - `IMPLEMENTATION NOT STARTED`: Kode sumber aplikasi belum mulai ditulis.
    - `TEST NOT RUN`: Pengujian otomatis belum dieksekusi secara fisik di server.

### 5. Ekspansi Test Suite & Matriks RTM (63 Test Cases) (FIX-005)
- **DOC-12, REQUIREMENT-TRACEABILITY-MATRIX.md:**
  - Memperluas skenario pengujian dari 55 menjadi **63 Test Cases terstruktur** dengan menambahkan 8 test case ketahanan integritas:
    - `TEST-AGG-001`: Daily stats duplicate prevention via `(aggregate_key, date)`
    - `TEST-AGG-002`: Hard delete before daily aggregation
    - `TEST-AGG-003`: Hard delete after daily aggregation
    - `TEST-AGG-004`: Retry aggregation idempotency
    - `TEST-AGG-005`: Null link_id / unassigned visits aggregation
    - `TEST-AGG-006`: Historical slug preservation after link hard delete
    - `TEST-AGG-007`: Concurrent aggregation simulation & unique key collision prevention
    - `TEST-RET-001`: Retention and pruning of visits (> 90 days) after hard delete
  - Menjamin sinkronisasi 100% dua arah antara `DOC-12` dan `RTM` tanpa test case yatim (*zero orphan tests*).

### 6. Sinkronisasi Inventaris Dokumen Terotomasi (FIX-006)
- **README.md, FINAL-CONSISTENCY-REPORT.md, REMEDIATION-REPORT.md, DOCUMENTATION-CHANGELOG.md:**
  - Menghitung jumlah tepat seluruh file Markdown dalam repositori secara terotomasi via script Python untuk memastikan konsistensi inventaris dokumen tanpa selisih.

### 7. Otorisasi Registri Persetujuan Product Owner (Approval Update - 31 Oktober 2026)
- **README.md, OPEN-DECISIONS.md, GOVERNANCE-RECONCILIATION-REPORT.md, PRE-CODING-CHECKLIST.md:**
  - Mencatat persetujuan eksplisit Product Owner (Dimas Aditya) terhadap **14 Keputusan Sah**:
    - `DEC-001` (Laravel 13 Baseline), `DEC-002` (Adsterra Candidate), `DEC-003` (Target WarnaHost), `DEC-004` (Countdown 10s), `DEC-005` (Turnstile Sebelum Timer), `DEC-006` (Retensi Raw 90 Hari), `DEC-007` (Penangguhan Popunder), `DEC-008` (Fitur Ganti Password Admin), `DEC-009` (Hashed Session Token), `DEC-010` (Fallback Vendor Deployment), `DEC-011` (Turnstile Emergency Bypass Switch), `DEC-013` (Kebijakan Soft & Hard Delete), `DEC-014` (Mandatori ANALYTICS_SALT Tanpa Fallback), dan `DEC-015` (Dedicated Aggregate Key NOT NULL).
  - Menegaskan status **`DEC-012`** (Persetujuan Formal ADR) dan **`DEC-016`** (Snapshot Identitas Tautan Kunjungan) tetap **`UNDER REVIEW`** menunggu keputusan terpisah.
  - Menegaskan bahwa seluruh 17 ADR (`ADR-001` s/d `ADR-017`) tetap **`PROPOSED / UNDER REVIEW`** dan tidak disetujui secara otomatis.
  - Memperbarui status suite dokumentasi menjadi **`AWAITING PRODUCT OWNER FINAL DOCUMENTATION SIGN-OFF`**.

---

## Versi 1.2.1 (31 Oktober 2026) — Final Consistency & Traceability Remediation

### 1. Kebijakan Penghapusan Data & Integritas Basis Data (FIX-001)
- **DOC-02, DOC-03, DOC-04, DOC-06, DOC-07, DOC-09, DOC-12, DOC-15 (ADR-015), DOC-16, RTM:**
  - Menetapkan kebijakan penghapusan eksplisit: **Soft Delete** (`deleted_at = NOW()`) sebagai operasi default di panel admin yang mempertahankan seluruh relasi dan metrik analitik.
  - Menetapkan konsekuensi **Hard Delete (Force Delete)** dari Trash:
    - `visitor_sessions`: `ON DELETE CASCADE` (sesi sementara dibersihkan).
    - `visits`: `link_id` menjadi `NULL` (`ON DELETE SET NULL`) agar log trafik mentah global (IP hash, device, browser, country) tidak hilang.
    - `daily_stats`: `link_id` menjadi `NULL` (`ON DELETE SET NULL`), dan ditambahkan kolom `link_slug VARCHAR(50) NULLABLE` sebagai snapshot slug historis saat agregasi dicatat agar grafik laporan masa lalu tetap terbaca.
  - Memastikan scheduled command `AggregateDailyStatsCommand` menyertakan link soft-deleted via `Link::withTrashed()` dan menangani kunjungan unassigned (`link_id IS NULL`).

### 2. Konfigurasi Mandatori ANALYTICS_SALT Tanpa Fallback (FIX-002)
- **DOC-02, DOC-06, DOC-07, DOC-09, DOC-13, DOC-14, DOC-15 (ADR-016), README:**
  - Menghapus secara ketat seluruh fallback `ANALYTICS_SALT` ke `APP_KEY` untuk mematuhi standar Cryptographic Key Separation (NIST SP 800-57 Part 1).
  - Menetapkan bahwa di lingkungan produksi (`APP_ENV=production`), `ANALYTICS_SALT` wajib dikonfigurasi; jika kosong, sistem melempar `\RuntimeException` untuk mencegah pencatatan hash IP tanpa salt.
  - Di lingkungan testing/local, sistem menggunakan fallback ke fixed string `'testing-analytics-salt-lokataut-do-not-use-in-production'`.
  - Mendokumentasikan konsekuensi rotasi kunci terhadap kesinambungan unique visitor historis dan memastikan key rahasia tidak pernah terekspos di log file.

### 3. Klasifikasi Bukti Hosting & Perintah Pre-Flight (FIX-003)
- **SOURCE-VERIFICATION-REGISTER.md, PRE-CODING-CHECKLIST.md, DOC-13, DOC-05, DOC-07, OPEN-DECISIONS.md:**
  - Mengadopsi 3 klasifikasi bukti: `PROVIDER CLAIM VERIFIED`, `ACCOUNT ENVIRONMENT VERIFIED`, dan `NOT VERIFIED`.
  - Mengoreksi status WarnaHost: spesifikasi hardware dan paket hosting diklasifikasikan sebagai `PROVIDER CLAIM VERIFIED`, sedangkan ketersediaan PHP 8.3 CLI, Composer 2, dan akses shell SSH pada akun aktual diklasifikasikan sebagai `NOT VERIFIED (Menunggu Pre-Flight Test)`.
  - Menambahkan 7 baris perintah verifikasi pre-flight di cPanel Terminal/SSH pada `DOC-13` (Bagian 1.1) dan checklist pra-koding.

### 4. Tata Kelola Approval & Pencegahan Approval Fiktif (FIX-004)
- **README.md, OPEN-DECISIONS.md, DOC-15, PRE-CODING-CHECKLIST.md, AGENTS.md:**
  - Memisahkan secara tegas keputusan yang telah disetujui dari usulan baru.
  - Menetapkan status seluruh ADR tetap `PROPOSED / UNDER REVIEW` menunggu peninjauan formal Product Owner.
  - Menegaskan kembali bahwa AI Coding Agent dilarang memberikan approval sepihak atas nama Product Owner.

### 5. Verifikasi Sumber Monetisasi & Anti-IVT (FIX-005)
- **DOC-01, DOC-02, DOC-08, DOC-10, SOURCE-VERIFICATION-REGISTER.md:**
  - Mengoreksi istilah "hukum" pada Google AdSense menjadi `Official Policy` (Google Publisher Policies: Thin Content & Encouraging Clicks) dengan potensi sanksi ad serving limit atau account suspension.
  - Menegaskan status Adsterra sebagai `Candidate Ad Network (Proposed - Pending Account & Domain Verification)`.
  - Menegaskan bahwa jarak vertikal 30px adalah `Internal Engineering Safeguard` (mengacu pada WCAG 2.5.5 Touch Target) untuk memitigasi accidental clicks, bukan klausul kontrak baku dari vendor.
  - Mempertahankan penangguhan format popunder pada fase MVP dan sterilisasi mutlak tombol `#proceed-btn`.

### 6. Ekspansi Test Suite & Keterlacakan Dua Arah 100% (FIX-006)
- **DOC-12, REQUIREMENT-TRACEABILITY-MATRIX.md:**
  - Memperluas skenario pengujian dari 36 menjadi 55 Test Cases terstruktur.
  - Menambahkan status siklus uji eksplisit: Status Dokumen: `DOCUMENTED`, Status Eksekusi: `NOT RUN` (fase pra-koding).
  - Sinkronisasi matriks RTM 100% dua arah tanpa orphan requirements dan tanpa orphan test cases.

### 7. Pengerasan Operasional & Siklus Kunci APP_KEY (FIX-007)
- **DOC-13, DOC-05, DOC-07, DOC-16:**
  - Menegaskan siklus hidup `APP_KEY`: perintah `php artisan key:generate` HANYA dijalankan satu kali saat instalasi awal. Dilarang keras dijalankan pada rilis berkala karena akan membatalkan seluruh data enkripsi `destination_url` dan memutus sesi admin.
  - Menjelaskan batasan keselamatan database rollback: `php artisan migrate:rollback` tidak selalu aman jika skema melakukan drop/alter kolom; mewajibkan snapshot dump basis data via `mysqldump` sebelum migrasi/deployment.
  - Mengonfigurasi rotasi log Laravel channel daily dengan retensi 14 hari.

### 8. Penjadwalan Agregasi Harian & Pruning Terstandarisasi (FIX-008)
- **DOC-04, DOC-07, DOC-11, DOC-13, DOC-16:**
  - Menetapkan timezone `Asia/Jakarta` (WIB) pada Laravel Scheduler.
  - Menstandarisasi penamaan dan frekuensi tugas:
    - `AggregateDailyStatsCommand` (`lokataut:aggregate-daily {--date=}`): Setiap hari 00:05 WIB, idempoten via `updateOrCreate`, snapshot slug, proteksi `withoutOverlapping(60)`.
    - `PruneExpiredSessionsCommand` (`lokataut:prune-sessions`): Setiap jam menit 15 (> 24 jam).
    - `PruneOldVisitDataCommand` (`lokataut:prune-visits`): Setiap hari 02:00 WIB (> 90 hari, DEC-006).
    - `PruneCacheCommand` (`lokataut:prune-cache`): Setiap minggu pada Minggu 03:00 WIB (menjaga kuota Inode cPanel).

---

## Versi 1.2 (31 Oktober 2026) — Post-Independent Review Remediation
*(Lihat rincian riwayat v1.2 pada dokumen arsip `lokataut-documentation-v1.2/DOCUMENTATION-CHANGELOG.md`)*

---

## Versi 1.1 (30 Oktober 2026) — Framework Migration & Security Hardening
*(Lihat rincian riwayat v1.1 pada dokumen arsip `lokataut-documentation-v1.1/DOCUMENTATION-CHANGELOG.md`)*
