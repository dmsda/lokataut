# Checklist Kesiapan Pra-Koding LokaTaut v1.1 (Pre-Coding Gate)

> **Status Gate:** `HOLD (PENDING RESOLUTION OF P1 FINDINGS)`  
> **Tanggal Evaluasi:** 31 Oktober 2026  
> **Target Pengguna:** Product Owner (Dimas Aditya), Lead Architect, dan AI Coding Agent  
> **Tujuan:** Gerbang verifikasi formal yang wajib bernilai 100% centang hijau sebelum satu baris kode aplikasi ditulis.

---

## 1. Ikhtisar Gerbang Kesiapan (Gate Overview)

Untuk mencegah pengerjaan ulang (*rework*), celah keamanan dini, dan kegagalan hosting di kemudian hari, implementasi kode aplikasi LokaTaut **TIDAK BOLEH DIMULAI** sebelum seluruh kriteria di bawah ini dipenuhi dan diverifikasi.

```
+-----------------------------------------------------------------------------------+
| GERBANG VERIFIKASI                | TOTAL ITEM | STATUS SAAT INI | KELAYAKAN      |
+-----------------------------------+:----------:+:---------------:+:--------------:+
| 1. Product Owner & Governance     | 5 Item     | 2/5 Verified    | PENDING PO     |
| 2. Arsitektur Teknis & Hosting    | 5 Item     | 3/5 Verified    | PENDING VERIF  |
| 3. Integritas Keamanan & Privasi  | 6 Item     | 4/6 Verified    | REVISI SEC-001 |
| 4. Penjaminan Kualitas & RTM      | 4 Item     | 2/4 Verified    | REVISI QA-001  |
+-----------------------------------+:----------:+:---------------:+:--------------:+
| TOTAL KESIAPAN PRA-KODING         | 20 Item    | 11/20 (55%)     | STATUS: HOLD   |
+-----------------------------------------------------------------------------------+
```

---

## 2. Checklist Detail Berdasarkan Domain

### 2.1 Domain 1: Persetujuan Product Owner & Tata Kelola (PO Sign-Off)
- [ ] **PO-GATE-001 (Persetujuan Baseline Laravel 13):** Product Owner telah secara resmi menyetujui pembaruan baseline arsitektur ke Laravel 13.x (PHP 8.3+) dan mencatat persetujuannya di `Approval Registry`.
- [ ] **PO-GATE-002 (Persetujuan ADR-001 s/d ADR-010):** Seluruh status usulan Architecture Decision Record (`PROPOSED`) di `DOC-15` telah ditinjau dan disetujui menjadi `APPROVED` oleh PO.
- [ ] **PO-GATE-003 (Persetujuan Penambahan Fitur FR-AUTH-004):** PO menyetujui penambahan antarmuka perubahan kata sandi admin (*Admin Password Change*) pada PRD dan Scope.
- [ ] **PO-GATE-004 (Validasi Model Monetisasi Adsterra):** PO mengonfirmasi pemahaman bahwa Adsterra masih berstatus usulan (*Candidate*) dan tidak mengizinkan klik popunder pada tombol aksi safelink.
- [x] **PO-GATE-005 (Direktif Operasional AI):** File panduan operasional AI (`AGENTS.md`) telah terpasang di root dan disepakati sebagai aturan wajib tanpa pengecualian.

### 2.2 Domain 2: Verifikasi Teknis & Infrastruktur Hosting
- [ ] **TECH-GATE-001 (Verifikasi CLI PHP 8.3 di SSH cPanel):** PO/DevOps telah login ke cPanel WarnaHost dan memverifikasi bahwa terminal SSH menjalankan PHP 8.3+ via perintah `php -v`. *(Status: NOT VERIFIED)*
- [ ] **TECH-GATE-002 (Prosedur Fallback Vendor Composer Disepakati):** Dokumen operasi `DOC-13` telah diperbarui dengan alur *Pre-Compiled Vendor Upload* untuk mencegah insiden kehabisan memori CloudLinux LVE. *(Temuan REV-OPS-001)*
- [x] **TECH-GATE-003 (Verifikasi Versi Basis Data):** MySQL 8.0+ / MariaDB 10.6+ telah terkonfirmasi aktif di cPanel dengan dukungan charset `utf8mb4_unicode_ci`.
- [ ] **TECH-GATE-004 (Konfigurasi Bypass Cache LiteSpeed):** Spesifikasi header `X-LiteSpeed-Cache-Control: no-cache` dan rule `.htaccess` untuk rute safelink telah disematkan pada dokumentasi teknis. *(Temuan REV-OPS-002)*
- [x] **TECH-GATE-005 (Ketiadaan Dependensi Runtime Asing):** Dipastikan tidak ada dependensi terhadap Node.js daemon, Redis in-memory store, Docker, atau root daemon.

### 2.3 Domain 3: Integritas Keamanan & Privasi
- [ ] **SEC-GATE-001 (Penyimpanan Hashed Session Token):** DDL basis data `visitor_sessions` telah diubah untuk menggunakan `token_hash` (`VARCHAR(64)`), bukan plaintext token. *(Temuan REV-SEC-001)*
- [x] **SEC-GATE-002 (Kerahasiaan URL Tujuan):** Terverifikasi bahwa tidak ada rute atau view Blade yang mengekspos destination URL ke client sebelum status 302 akhir diserahkan.
- [x] **SEC-GATE-003 (Countdown Timer Validasi Server-Side):** Logika verifikasi countdown murni mengandalkan timestamp server (`now() - created_at >= countdown_seconds`).
- [x] **SEC-GATE-004 (Transisi Status Atomik Sesi):** Query eksekusi redirect dipastikan atomik (`UPDATE visitor_sessions SET completed = 1 WHERE ... AND completed = 0`) untuk mematikan race condition.
- [ ] **SEC-GATE-005 (Switch Darurat Turnstile):** Kolom/setting `turnstile_enabled` telah didesain pada tabel `settings` untuk mitigasi saat API Cloudflare mengalami downtime. *(Temuan REV-SEC-002)*
- [x] **SEC-GATE-006 (Pseudonimisasi IP Pengunjung):** Alur pencatatan IP dipastikan menggunakan `hash_hmac('sha256', $ip, APP_KEY)` untuk mematuhi regulasi privasi data.

### 2.4 Domain 4: Kesiapan Penjaminan Kualitas (QA & RTM)
- [ ] **QA-GATE-001 (Rekonsiliasi RTM vs DOC-12):** Sebanyak 17 test case yang hilang telah didefinisikan ke dalam tabel spesifikasi `DOC-12`, dan 9 test case yatim telah dipetakan ke RTM. *(Temuan REV-QA-001)*
- [ ] **QA-GATE-002 (Test Plan Penggantian Password Admin):** Test case `TEST-AUTH-004` untuk validasi pergantian kata sandi admin telah terdaftar dengan skenario pengujian lengkap.
- [x] **QA-GATE-003 (Framework Pengujian):** Diputuskan penggunaan Pest PHP / PHPUnit bawaan Laravel 13 untuk pengujian otomatis.
- [x] **QA-GATE-004 (Kriteria Selesai / DoD):** Acceptance criteria untuk seluruh functional requirement telah memiliki target pengujian berstatus P0 dan P1.

---

## 3. Matriks Keputusan Gerbang (Gate Decision Matrix)

```
[ KONDISI SAAT INI ]
Terdapat 5 temuan P1 Major yang belum diselaraskan pada dokumen spesifikasi utama:
- REV-SEC-001 (Hashed token)
- REV-AUTH-001 (Admin password change)
- REV-QA-001 (17 missing test cases)
- REV-OPS-001 (Composer deploy fallback)
- REV-MON-001 (Adsterra button hijack ban)

-----------------------------------------------------------------------------------
KEPUTUSAN EVALUASI GERBANG:
[ X ] HOLD — LAKUKAN PENYELARASAN DOKUMENTASI TERLEBIH DAHULU (REVISI v1.2)
[   ] GO — LANJUT KE FASE KODING PHASE 1
-----------------------------------------------------------------------------------
```

### Rekomendasi Tindak Lanjut:
AI Coding Agent **DILARANG** memulai Phase 1 sampai Product Owner menyetujui pembaruan patch dokumentasi v1.2 yang menyelesaikan seluruh 5 temuan P1 di atas.

