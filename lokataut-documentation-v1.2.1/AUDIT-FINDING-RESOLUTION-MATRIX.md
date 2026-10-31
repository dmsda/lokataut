# Matriks Resolusi Temuan Audit & Konsistensi Dokumentasi LokaTaut v1.2.1

> **Status Dokumen:** `100% RESOLVED & SYNCHRONIZED`  
> **Versi:** 1.2.1  
> **Terakhir Diperbarui:** 31 Oktober 2026  
> **Basis Rujukan:** `lokataut-independent-review-v1.1/INDEPENDENT-AUDIT-REPORT.md` & Master Remediation Prompt v1.2.1

---

## 1. Matriks Penyelarasan Temuan Audit Independen (REV-*)

Tabel berikut melacak status penanganan 11 temuan audit dari Laporan Audit Independen v1.1 dan bagaimana solusi arsitektural diterapkan secara konsisten:

| Finding ID | Prioritas | Subjek Temuan | Dokumen yang Diperbarui | Ringkasan Solusi yang Diterapkan | Status Resolusi |
|---|:---:|---|---|---|:---:|
| **REV-SEC-001** | `P1` | Plaintext Session Token Storage | `DOC-02`, `DOC-03`, `DOC-04`, `DOC-05`, `DOC-06`, `DOC-07`, `DOC-09`, `DOC-12`, `DOC-14`, `DOC-15`, `DOC-16`, `RTM` | Mengubah skema DB menjadi `token_hash VARCHAR(64) UNIQUE` (SHA-256). Raw token hanya dipegang browser. Atomic transition: `UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`. | **RESOLVED** |
| **REV-AUTH-001** | `P1` | Ketiadaan Fitur Ubah Password Admin | `DOC-01`, `DOC-02`, `DOC-03`, `DOC-04`, `DOC-05`, `DOC-07`, `DOC-08`, `DOC-09`, `DOC-12`, `DOC-14`, `DOC-15`, `DOC-16`, `RTM` | Menambahkan `FR-AUTH-004: Admin Password & Profile Management`, rute `GET /admin/profile` dan `PUT /admin/profile/password`, `ProfileController`, `UpdatePasswordRequest` (validasi password lama, 12+ char kompleks), dan rotasi session ID. | **RESOLVED** |
| **REV-QA-001** | `P1` | 17 Test Case Hilang & 9 Orphan Tests | `DOC-03`, `DOC-12`, `RTM` | Menuliskan tabel spesifikasi 17 test case yang hilang secara lengkap dengan Preconditions, Steps, Expected Results, dan Priority di `DOC-12`. Mendaftarkan 9 test case yatim ke RTM. | **RESOLVED** |
| **REV-OPS-001** | `P1` | Risiko Memori Composer di Shared Hosting | `DOC-03`, `DOC-05`, `DOC-07`, `DOC-13`, `DOC-15`, `DOC-16` | Mendokumentasikan prosedur *Fallback Pre-Built Vendor Deployment* (kompilasi dependensi di mesin lokal/CI ber-PHP 8.3 matching, package ZIP release, upload & extract di server cPanel) serta prosedur rollback cepat. | **RESOLVED** |
| **REV-MON-001** | `P1` | Risiko Fraud IVT Popunder Adsterra | `DOC-01`, `DOC-02`, `DOC-03`, `DOC-04`, `DOC-08`, `DOC-10`, `DOC-14`, `DOC-15` | Mengisolasi tombol navigasi unduhan (`#proceed-btn`) secara mutlak dari skrip atau pemicu iklan pihak ketiga. Menangguhkan (*suspend*) format popunder pada MVP. Menetapkan label disclaimer `"Advertisement / Iklan Sponsor"` pada seluruh banner. | **RESOLVED** |
| **REV-DB-001** | `P2` | Foreign Key On Delete Cascade Visits | `DOC-06`, `DOC-07`, `DOC-15` | Mengubah kolom `visits.link_id` menjadi `BIGINT UNSIGNED NULL` dengan konstrain `ON DELETE SET NULL`. Riwayat analitik kunjungan tetap tersimpan permanen meskipun record induk tautan dihapus. | **RESOLVED** |
| **REV-DB-002** | `P2` | Agregasi Harian Soft-Deleted Links | `DOC-02`, `DOC-04`, `DOC-07`, `DOC-12`, `DOC-16`, `RTM` | Menetapkan kueri command harian `AggregateDailyStatsCommand` mengeksekusi `Link::withTrashed()->chunk(...)`, menjamin tautan yang di-soft-delete pada hari yang sama tetap terhitung di `daily_stats`. | **RESOLVED** |
| **REV-SEC-002** | `P2` | Ketiadaan Emergency Bypass Turnstile | `DOC-02`, `DOC-03`, `DOC-04`, `DOC-06`, `DOC-07`, `DOC-09`, `DOC-12`, `DOC-16`, `RTM` | Menambahkan setting `turnstile_mode` (`enabled`, `disabled`, `emergency_bypass`) pada tabel `settings` dan panel admin. Bypass darurat mencatat audit log khusus di `security_logs`. | **RESOLVED** |
| **REV-PERF-001** | `P2` | Akumulasi Inode File Cache di Hosting | `DOC-02`, `DOC-04`, `DOC-05`, `DOC-07`, `DOC-11`, `DOC-12`, `DOC-13`, `DOC-16` | Menambahkan scheduled command mingguan `lokataut:prune-cache` (Minggu pukul 03:00 WIB) untuk membersihkan file cache usang di `storage/framework/cache/data`, menjaga kuota inode cPanel WarnaHost. | **RESOLVED** |
| **REV-OPS-002** | `P2` | Kebocoran Sesi via LiteSpeed Server Cache| `DOC-05`, `DOC-07`, `DOC-11`, `DOC-12`, `DOC-13`, `DOC-16` | Menyuntikkan header respon `X-LiteSpeed-Cache-Control: no-cache` dan `Cache-Control: private, no-store, no-cache` pada rute safelink publik, serta menyematkan directive bypass di `.htaccess`. | **RESOLVED** |
| **REV-DOC-001** | `P3` | Inkonsistensi Penamaan Command Kelas | `DOC-03`, `DOC-07`, `DOC-12`, `DOC-13`, `DOC-14`, `DOC-16`, `RTM` | Menyeragamkan seluruh dokumen menggunakan sufiks `Command` (`AggregateDailyStatsCommand.php`, `PruneCacheCommand.php`, `PruneExpiredSessionsCommand.php`, `PruneOldVisitDataCommand.php`). | **RESOLVED** |

---

## 2. Matriks Resolusi Perbaikan Konsistensi Akhir v1.2.1 (FIX-*)

| Fix ID | Subjek Perbaikan | Dokumen Terdampak | Tindakan Remediasi v1.2.1 | Status |
|---|---|---|---|:---:|
| **FIX-001** | Kebijakan Deletion & Retensi Basis Data | DOC-02, 03, 04, 06, 07, 09, 12, 15 (ADR-015), 16, RTM | Menetapkan Soft Delete sebagai default operasional. Pada Hard Delete: `visitor_sessions` di-cascade, `visits.link_id` dan `daily_stats.link_id` disetel `NULL` (`ON DELETE SET NULL`) dengan snapshot `link_slug` historis disimpan pada `daily_stats`. | **RESOLVED** |
| **FIX-002** | Mandatori ANALYTICS_SALT Tanpa Fallback | DOC-02, 06, 07, 09, 13, 14, 15 (ADR-016), README | Menghapus seluruh fallback implisit `ANALYTICS_SALT` ke `APP_KEY`. Menegakkan exception `RuntimeException` jika kosong di production. Mendokumentasikan dampak rotasi key terhadap unique visitor. | **RESOLVED** |
| **FIX-003** | Klasifikasi Bukti Hosting & Pre-Flight Commands | SOURCE-VERIFICATION-REGISTER, PRE-CODING-CHECKLIST, DOC-13, DOC-05, DOC-07, OPEN-DECISIONS | Mengadopsi 3 klasifikasi bukti. Status WarnaHost: hardware `PROVIDER CLAIM VERIFIED`, akun aktual `NOT VERIFIED`. Menambahkan 7 baris perintah Pre-Flight Verification di cPanel Terminal/SSH. | **RESOLVED** |
| **FIX-004** | Tata Kelola Approval & Pencegahan Fiktif | README, OPEN-DECISIONS, ADR, PRE-CODING-CHECKLIST, AGENTS.md | Memisahkan keputusan disetujui (DEC-001 s/d 006) dari usulan baru (DEC-007 s/d 014). Status seluruh ADR tetap `PROPOSED`. Suite berstatus `READY FOR PRODUCT OWNER FINAL REVIEW`. | **RESOLVED** |
| **FIX-005** | Verifikasi Sumber Monetisasi & Anti-IVT | DOC-01, 02, 08, 10, SOURCE-VERIFICATION-REGISTER | Mengoreksi klaim Google AdSense sebagai Official Policy (bukan hukum). Menegaskan Adsterra sebagai kandidat (pending verifikasi akun riil). Menegaskan jarak 30px sebagai Internal Engineering Safeguard (WCAG 2.5.5). | **RESOLVED** |
| **FIX-006** | QA Traceability & Edge Cases (55 Tests) | DOC-12, RTM | Memperluas skenario uji menjadi 55 test cases mencakup edge cases concurrency, replay, expired, turnstile timeout/invalid, delete soft/hard, retry aggregation, scheduler overlap, dan rollback. Status: `DOCUMENTED` / `NOT RUN`. | **RESOLVED** |
| **FIX-007** | Pengerasan Operasional & APP_KEY Lifecycle | DOC-13, 05, 07, 16 | Menegaskan `php artisan key:generate` HANYA dijalankan 1 kali saat instalasi awal. Menjelaskan batasan keselamatan rollback dan mewajibkan snapshot `mysqldump` sebelum rilis. | **RESOLVED** |
| **FIX-008** | Agregasi Terjadwal & Batas Retensi | DOC-04, 07, 11, 13, 16 | Menetapkan timezone `Asia/Jakarta`, `--date` backfill, idempotency `updateOrCreate`, overlap prevention, dan query yang mencakup link soft-deleted serta unassigned visits. | **RESOLVED** |
