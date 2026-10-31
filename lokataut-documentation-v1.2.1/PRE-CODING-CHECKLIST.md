# Checklist Kesiapan Pra-Koding LokaTaut v1.2.1 (Pre-Coding Readiness Gate)

> **Status Gerbang Kesiapan:** `READY FOR PRODUCT OWNER FINAL REVIEW`  
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
| 1. Product Owner & Governance     | 5 Item     | 3 Verified / 2  | MENUNGGU PO    |
| 2. Arsitektur Teknis & Hosting    | 5 Item     | 4 Verified / 1  | PRE-FLIGHT SSH |
| 3. Integritas Keamanan & Privasi  | 6 Item     | 6/6 Verified    | SIAP (SECURE)  |
| 4. Penjaminan Kualitas & RTM      | 4 Item     | 4/4 Verified    | SIAP (QA 100%) |
+-----------------------------------+:----------:+:---------------:+:--------------:+
| TOTAL KESIAPAN PRA-KODING         | 20 Item    | 17/20 Verified  | PENDING PO & SSH
+-----------------------------------------------------------------------------------+
```

---

## 2. Checklist Detail Berdasarkan Domain

### 2.1 Domain 1: Persetujuan Product Owner & Tata Kelola (PO Sign-Off)
- [x] **PO-GATE-001 (Persetujuan Baseline Framework & Scope Awal):** Product Owner telah secara resmi menyetujui keputusan DEC-001 (Laravel 13), DEC-002 (Adsterra kandidat), DEC-003 (Hosting target WarnaHost), DEC-004 (Countdown 10s), DEC-005 (Turnstile sebelum countdown), dan DEC-006 (Retensi raw 90 hari) pada 30 Oktober 2026.
- [x] **PO-GATE-002 (Direktif Operasional AI):** File panduan operasional AI ([`AGENTS.md`](../AGENTS.md)) telah terpasang di root repository dan disepakati sebagai aturan kerja baku.
- [ ] **PO-GATE-003 (Persetujuan Usulan Baru DEC-007 s/d DEC-014):** Menunggu peninjauan dan tanda tangan formal Product Owner terhadap usulan baru pada [`OPEN-DECISIONS.md`](./OPEN-DECISIONS.md) (Penangguhan Popunder, Ganti Password Admin, Hashed Session Token, Fallback Vendor Deploy, Turnstile Emergency Mode Switch, Kebijakan Soft/Hard Delete, dan Mandatori ANALYTICS_SALT).
- [ ] **PO-GATE-004 (Persetujuan Formal ADR-001 s/d ADR-016):** Seluruh status Architecture Decision Record (`PROPOSED`) di [`15-architecture-decisions.md`](./09-decisions/15-architecture-decisions.md) menunggu persetujuan formal PO menjadi `APPROVED`.
- [x] **PO-GATE-005 (Integritas Approval Tanpa Pemalsuan):** Terverifikasi bahwa AI Coding Agent tidak memberikan persetujuan fiktif atas nama Product Owner. Seluruh dokumen baru berstatus `UNDER REVIEW (Ready for Product Owner Final Review)`.

### 2.2 Domain 2: Verifikasi Teknis & Infrastruktur Hosting
- [ ] **TECH-GATE-001 (Verifikasi Pre-Flight Terminal SSH cPanel):** Menunggu eksekusi nyata dari 7 perintah pre-flight verification di cPanel Terminal/SSH WarnaHost (DOC-13 Bagian 1.1) untuk memastikan PHP 8.3 CLI aktif, alokasi memori, ketersediaan Composer, dan kuota Inode akun aktual. *(Status: NOT VERIFIED / PENDING TEST)*
- [x] **TECH-GATE-002 (Prosedur Fallback Vendor Composer Disepakati):** Dokumen operasi `DOC-13` telah menetapkan alur *Fallback Pre-Compiled Vendor Deployment* via arsip ZIP untuk memitigasi risiko pembatasan memori CloudLinux LVE.
- [x] **TECH-GATE-003 (Spesifikasi Basis Data & Backup Mandatori):** Disepakati penggunaan MySQL 8.0 / MariaDB 10.5+ dengan mitigasi *disaster recovery* berupa pembuatan snapshot `mysqldump` sebelum rilis/migrasi.
- [x] **TECH-GATE-004 (Konfigurasi Bypass Cache LiteSpeed):** Header `X-LiteSpeed-Cache-Control: no-cache` dan direktif `.htaccess` khusus rute dinamis safelink telah ditetapkan.
- [x] **TECH-GATE-005 (Ketiadaan Dependensi Runtime Asing):** Terverifikasi 100% bebas dari Node.js backend, supervisor root daemon, dan in-memory store wajib Redis.

### 2.3 Domain 3: Integritas Keamanan & Privasi
- [x] **SEC-GATE-001 (Penyimpanan Hashed Session Token):** Skema `visitor_sessions` menggunakan `token_hash` (SHA-256 `VARCHAR(64)`), sedangkan raw token hanya dikirim ke peramban.
- [x] **SEC-GATE-002 (Kerahasiaan Mutlak Destination URL):** URL tujuan tidak pernah dikirimkan ke DOM atau JSON sebelum verifikasi Turnstile dan countdown selesai (HTTP 302 Location header).
- [x] **SEC-GATE-003 (Countdown Timer Validasi Server-Side):** Evaluasi countdown murni didasarkan pada perbandingan timestamp server (`now() - countdown_started_at >= countdown_seconds`).
- [x] **SEC-GATE-004 (Transisi Status Atomik Sesi):** Query eksekusi redirect bersifat atomik (`UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`) untuk mematikan race condition.
- [x] **SEC-GATE-005 (Switch Darurat Turnstile):** Setting `turnstile_mode` (`enabled`, `disabled`, `emergency_bypass`) didesain di database dan admin panel dengan audit log di `security_logs`.
- [x] **SEC-GATE-006 (Mandatori ANALYTICS_SALT Tanpa Fallback):** Keyed HMAC IP pengunjung menggunakan `ANALYTICS_SALT` yang terisolasi dari `APP_KEY` (NIST SP 800-57) dan melempar `RuntimeException` jika tidak dikonfigurasi di production.

### 2.4 Domain 4: Kesiapan Penjaminan Kualitas (QA & RTM)
- [x] **QA-GATE-001 (Traceability RTM vs DOC-12 100% Sinkron):** Sebanyak 55 test cases terdefinisi lengkap dengan Preconditions, Steps, Expected Results, dan Priority. 0 orphan requirements dan 0 orphan tests.
- [x] **QA-GATE-002 (Test Plan Penggantian Password Admin):** Skenario `TEST-AUTH-006` dan `TEST-AUTH-007` untuk validasi pergantian kata sandi admin telah terdaftar.
- [x] **QA-GATE-003 (Edge Cases Terpetakan Lengkap):** Skenario penolakan bypass JS, replay token, race condition konkuren, turnstile timeout/invalid, link soft & hard delete, agregasi retry, scheduler overlap prevention, dan deployment rollback telah terdaftar.
- [x] **QA-GATE-004 (Status Pre-Coding Akurat):** Seluruh test case berstatus `DOCUMENTED` dan `NOT RUN` (tidak ada klaim palsu sebelum implementasi kode).

---

## 3. Matriks Keputusan Gerbang (Gate Decision Matrix)

```
[ KONDISI SAAT INI ]
Seluruh perbaikan konsistensi v1.2.1 telah diselesaikan secara tuntas:
- FIX-001: Kebijakan Deletion & Retensi (Soft Delete vs Hard Delete) -> SELESAI
- FIX-002: Pemisahan ANALYTICS_SALT tanpa fallback ke APP_KEY -> SELESAI
- FIX-003: Klasifikasi bukti hosting & Perintah Pre-flight -> SELESAI
- FIX-004: Tata kelola approval & Registri keputusan terbuka -> SELESAI
- FIX-005: Verifikasi sumber monetisasi & Isolasi tombol proceed -> SELESAI
- FIX-006: Matriks RTM 100% sinkron & Status uji NOT RUN -> SELESAI
- FIX-007: Prosedur deployment, rollback safety & backup mysqldump -> SELESAI
- FIX-008: Standarisasi jadwal agregasi harian & cron pruning -> SELESAI

-----------------------------------------------------------------------------------
KEPUTUSAN EVALUASI GERBANG:
[   ] BLOCKED (Ada kontradiksi teknis / kekurangan dokumen)
[   ] NEEDS REVISION (Perlu perbaikan arsitektur lanjutan)
[ X ] READY FOR PRODUCT OWNER FINAL REVIEW (Menunggu Tanda Tangan PO & SSH Pre-Flight)
-----------------------------------------------------------------------------------
```
