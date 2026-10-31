# Checklist Kesiapan Pra-Koding LokaTaut v1.2 (Pre-Coding Readiness Gate)

> **Status Gerbang Kesiapan:** `READY FOR PRODUCT OWNER REVIEW`  
> **Tanggal Evaluasi:** 31 Oktober 2026  
> **Target Pengguna:** Product Owner (Dimas Aditya), Lead Architect, dan AI Coding Agent  
> **Tujuan:** Gerbang verifikasi formal yang wajib bernilai 100% centang hijau sebelum satu baris kode aplikasi ditulis pada repositori LokaTaut.

---

## 1. Ikhtisar Gerbang Kesiapan (Gate Overview)

Untuk mencegah pengerjaan ulang (*rework*), celah keamanan dini, dan kegagalan hosting di kemudian hari, implementasi kode aplikasi LokaTaut **TIDAK BOLEH DIMULAI** sebelum seluruh kriteria di bawah ini dipenuhi dan diverifikasi.

```
+-----------------------------------------------------------------------------------+
| GERBANG VERIFIKASI                | TOTAL ITEM | STATUS SAAT INI | KELAYAKAN      |
+-----------------------------------+:----------:+:---------------:+:--------------:+
| 1. Product Owner & Governance     | 5 Item     | PENDING PO SIGN | MENUNGGU PO    |
| 2. Arsitektur Teknis & Hosting    | 5 Item     | 4/5 Verified    | PRE-FLIGHT SSH |
| 3. Integritas Keamanan & Privasi  | 6 Item     | 6/6 Verified    | SIAP (SECURE)  |
| 4. Penjaminan Kualitas & RTM      | 4 Item     | 4/4 Verified    | SIAP (QA 100%) |
+-----------------------------------+:----------:+:---------------:+:--------------:+
| TOTAL KESIAPAN PRA-KODING         | 20 Item    | 14/20 Verified  | PENDING PO & SSH
+-----------------------------------------------------------------------------------+
```

---

## 2. Checklist Detail Berdasarkan Domain

### 2.1 Domain 1: Persetujuan Product Owner & Tata Kelola (PO Sign-Off)
- [ ] **PO-GATE-001 (Persetujuan Baseline Laravel 13):** Product Owner telah secara resmi menyetujui pembaruan baseline arsitektur ke Laravel 13.x (PHP 8.3+) dan menandai status `APPROVED` di `Approval Registry` pada `README.md`.
- [ ] **PO-GATE-002 (Persetujuan ADR-001 s/d ADR-014):** Seluruh status usulan Architecture Decision Record (`PROPOSED`) di `DOC-15` telah ditinjau dan disetujui menjadi `APPROVED` oleh PO.
- [ ] **PO-GATE-003 (Persetujuan Fitur FR-AUTH-004):** PO menyetujui penambahan antarmuka perubahan kata sandi admin (*Admin Password Change*) pada PRD, Scope, dan Tech Spec.
- [ ] **PO-GATE-004 (Validasi Penangguhan Popunder & Monetisasi Banner):** PO mengonfirmasi pemahaman bahwa Adsterra masih berstatus kandidat dan format popunder ditangguhkan sampai akun terverifikasi.
- [x] **PO-GATE-005 (Direktif Operasional AI):** File panduan operasional AI (`AGENTS.md`) telah terpasang di root dan disepakati sebagai aturan wajib tanpa pengecualian.

### 2.2 Domain 2: Verifikasi Teknis & Infrastruktur Hosting
- [ ] **TECH-GATE-001 (Verifikasi CLI PHP 8.3 di SSH cPanel):** PO/DevOps telah login ke cPanel WarnaHost dan memverifikasi bahwa terminal SSH menjalankan PHP 8.3+ via perintah `php -v`. *(Status: NOT VERIFIED / PRE-FLIGHT CHECK)*
- [x] **TECH-GATE-002 (Prosedur Fallback Vendor Composer Disepakati):** Dokumen operasi `DOC-13` telah diperbarui dengan alur *Pre-Compiled Vendor Upload* untuk mencegah insiden kehabisan memori CloudLinux LVE.
- [x] **TECH-GATE-003 (Verifikasi Versi Basis Data):** MySQL 8.0+ / MariaDB 10.6+ telah terkonfirmasi aktif di cPanel dengan dukungan charset `utf8mb4_unicode_ci`.
- [x] **TECH-GATE-004 (Konfigurasi Bypass Cache LiteSpeed):** Spesifikasi header `X-LiteSpeed-Cache-Control: no-cache` dan directive `.htaccess` untuk rute safelink telah disematkan pada dokumentasi teknis.
- [x] **TECH-GATE-005 (Ketiadaan Dependensi Runtime Asing):** Dipastikan tidak ada dependensi terhadap Node.js daemon, Redis in-memory store, Docker, atau root daemon.

### 2.3 Domain 3: Integritas Keamanan & Privasi
- [x] **SEC-GATE-001 (Penyimpanan Hashed Session Token):** DDL basis data `visitor_sessions` telah diubah untuk menggunakan `token_hash` (`VARCHAR(64)`), bukan plaintext token.
- [x] **SEC-GATE-002 (Kerahasiaan URL Tujuan):** Terverifikasi bahwa tidak ada rute atau view Blade yang mengekspos destination URL ke client sebelum status 302 akhir diserahkan.
- [x] **SEC-GATE-003 (Countdown Timer Validasi Server-Side):** Logika verifikasi countdown murni mengandalkan timestamp server (`now() - countdown_started_at >= countdown_seconds`).
- [x] **SEC-GATE-004 (Transisi Status Atomik Sesi):** Query eksekusi redirect dipastikan atomik (`UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`) untuk mematikan race condition.
- [x] **SEC-GATE-005 (Switch Darurat Turnstile):** Setting `turnstile_mode` (`enabled`, `disabled`, `emergency_bypass`) telah didesain pada tabel `settings` dan panel admin.
- [x] **SEC-GATE-006 (Pseudonimisasi IP Pengunjung):** Alur pencatatan IP dipastikan menggunakan `hash_hmac('sha256', $ip, ANALYTICS_SALT)` yang terisolasi dari siklus rotasi `APP_KEY`.

### 2.4 Domain 4: Kesiapan Penjaminan Kualitas (QA & RTM)
- [x] **QA-GATE-001 (Rekonsiliasi RTM vs DOC-12):** Sebanyak 36 test cases terdefinisi lengkap dengan Preconditions, Steps, Expected Results, dan Priority. 0 orphan requirements dan 0 orphan tests.
- [x] **QA-GATE-002 (Test Plan Penggantian Password Admin):** Test case `TEST-AUTH-006` dan `TEST-AUTH-007` untuk validasi pergantian kata sandi admin telah terdaftar dengan skenario pengujian lengkap.
- [x] **QA-GATE-003 (Framework Pengujian):** Diputuskan penggunaan Pest PHP / PHPUnit bawaan Laravel 13 untuk pengujian otomatis.
- [x] **QA-GATE-004 (Kriteria Selesai / DoD):** Acceptance criteria untuk seluruh functional requirement telah memiliki target pengujian berstatus P0 dan P1.

---

## 3. Matriks Keputusan Gerbang (Gate Decision Matrix)

```
[ KONDISI SAAT INI ]
Seluruh 11 temuan audit dari laporan independen telah terselesaikan 100% pada dokumen v1.2:
- REV-SEC-001 (Hashed token SHA-256) -> RESOLVED
- REV-AUTH-001 (Admin password change FR-AUTH-004) -> RESOLVED
- REV-QA-001 (Rekonsiliasi 36 test cases tanpa orphan) -> RESOLVED
- REV-OPS-001 (Composer deploy fallback & rollback) -> RESOLVED
- REV-MON-001 (Isolasi tombol proceed & suspend popunder) -> RESOLVED
- REV-DB-001 (visits FK ON DELETE SET NULL) -> RESOLVED
- REV-DB-002 (Agregasi daily_stats withTrashed) -> RESOLVED
- REV-SEC-002 (Turnstile emergency bypass switch) -> RESOLVED
- REV-PERF-001 (Pembersihan inode cache storage harian) -> RESOLVED
- REV-OPS-002 (Header & .htaccess LiteSpeed anti-cache) -> RESOLVED
- REV-DOC-001 (Standarisasi penamaan kelas Command) -> RESOLVED

-----------------------------------------------------------------------------------
KEPUTUSAN EVALUASI GERBANG:
[   ] BLOCKED
[   ] NEEDS REVISION
[ X ] READY FOR PRODUCT OWNER REVIEW (Menunggu Tanda Tangan PO & SSH Check)
-----------------------------------------------------------------------------------
```

