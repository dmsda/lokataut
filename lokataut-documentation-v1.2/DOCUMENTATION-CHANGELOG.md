# Documentation Changelog — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | CHANGELOG |
| **Versi** | 1.2 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |

---

## Versi 1.2 (31 Oktober 2026) — Post-Independent Review Remediation

### 1. Keamanan & Kriptografi (Security Remediation)
- **DOC-02, DOC-03, DOC-04, DOC-05, DOC-06, DOC-07, DOC-09, DOC-12, DOC-14, DOC-15, DOC-16, RTM (REV-SEC-001):**
  - Mengubah penyimpanan token sesi safelink dari plaintext `VARCHAR(64)` menjadi `token_hash VARCHAR(64) UNIQUE` (SHA-256) sesuai OWASP ASVS v4.0 Section 3.2.4. Raw token acak 32-byte hanya dipegang oleh browser pengunjung.
  - Memperbarui query atomic transition menjadi: `UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`.
- **DOC-06, DOC-07, DOC-09 (Kritik Audit - Key Separation):**
  - Mengisolasi salt pseudonimisasi IP pengunjung ke variabel `ANALYTICS_SALT` (`config('services.analytics.salt')`) agar rotasi kunci `APP_KEY` tidak merusak konsistensi analitik historis (NIST SP 800-57 Part 1).
- **DOC-02, DOC-03, DOC-04, DOC-06, DOC-07, DOC-09, DOC-12, DOC-16 (REV-SEC-002):**
  - Menambahkan setting mode darurat `turnstile_mode` (`enabled`, `disabled`, `emergency_bypass`) pada tabel `settings` dan panel admin dengan audit logging di `security_logs`.

### 2. Autentikasi & Manajemen Akun (Admin Management)
- **DOC-01, DOC-02, DOC-03, DOC-04, DOC-05, DOC-07, DOC-08, DOC-09, DOC-12, DOC-14, DOC-15, DOC-16, RTM (REV-AUTH-001):**
  - Menambahkan kebutuhan fungsional `FR-AUTH-004: Admin Password & Profile Management`.
  - Menambahkan spesifikasi rute `GET /admin/profile` dan `PUT /admin/profile/password`, `ProfileController`, `UpdatePasswordRequest` (validasi `current_password`, password baru kompleks 12+ char), rotasi session ID, dan wireframe UI.

### 3. Penjaminan Kualitas & Matriks Keterlacakan (QA & Traceability)
- **DOC-03, DOC-12, RTM (REV-QA-001):**
  - Menuliskan tabel spesifikasi pengujian lengkap (Preconditions, Steps, Expected Results, Priority) untuk seluruh 17 test case yang sebelumnya hilang di `DOC-12`.
  - Mendaftarkan dan memetakan 9 test case yang sebelumnya yatim ke dalam RTM.
  - Menambahkan test case pergantian kata sandi (`TEST-AUTH-006`, `TEST-AUTH-007`) dan hashed token (`TEST-SEC-002`).
  - Total test suite terstruktur mencapai **36 Test Cases terpetakan 100% dua arah tanpa orphan**.

### 4. Infrastruktur, Kinerja & Deployment (DevOps & Hosting)
- **DOC-03, DOC-05, DOC-07, DOC-13, DOC-15, DOC-16 (REV-OPS-001):**
  - Mendokumentasikan prosedur *Fallback Pre-Built Vendor Deployment* pada `DOC-13` (kompilasi lokal/CI dengan PHP 8.3 matching, package ZIP release, upload & extract di server cPanel) serta prosedur rollback cepat.
  - Mengoreksi klaim overstatement auditor mengenai eliminasi risiko 100% dengan evaluasi risiko realistis.
- **DOC-05, DOC-07, DOC-11, DOC-12, DOC-13, DOC-16 (REV-OPS-002):**
  - Menyuntikkan header respon `X-LiteSpeed-Cache-Control: no-cache` pada rute publik safelink dan menyematkan directive `.htaccess` khusus LiteSpeed untuk mencegah caching server-side atas halaman dinamis ber-token.
- **DOC-02, DOC-04, DOC-05, DOC-07, DOC-11, DOC-12, DOC-13, DOC-16 (REV-PERF-001):**
  - Menambahkan scheduled artisan command harian `lokataut:prune-cache` (pukul 03:00 WIB) untuk membersihkan file cache usang di `storage/framework/cache/data`, menjaga kuota inode cPanel WarnaHost.

### 5. Monetisasi & Kebijakan Periklanan (Ad Network Compliance)
- **DOC-01, DOC-02, DOC-03, DOC-04, DOC-08, DOC-10, DOC-14, DOC-15 (REV-MON-001):**
  - Mensterilkan tombol aksi unduhan (`#proceed-btn`) secara mutlak dari skrip atau pemicu iklan pihak ketiga (Anti-IVT).
  - Mengoreksi rekomendasi auditor dengan **menangguhkan (*suspend*) format popunder** pada MVP hingga ada persetujuan akun riil dan kepatuhan tertulis dari jaringan iklan.
  - Mengklarifikasi bahwa jarak 30px adalah standar rekayasa internal UI/UX untuk mencegah sentuhan tidak sengaja pada ponsel.

### 6. Integritas Basis Data & Konsistensi Dokumen (DB & Consistency)
- **DOC-06, DOC-07, DOC-15 (REV-DB-001):**
  - Mengubah kolom `visits.link_id` menjadi `BIGINT UNSIGNED NULL` dengan konstrain `ON DELETE SET NULL` guna mempertahankan data analitik historis jika tautan dihapus.
- **DOC-02, DOC-04, DOC-07, DOC-12, DOC-16, RTM (REV-DB-002):**
  - Menetapkan bahwa `AggregateDailyStatsCommand` mengeksekusi kueri `Link::withTrashed()->chunk(...)`, menjamin tautan yang di-soft-delete pada hari yang sama tetap terhitung di `daily_stats`.
- **DOC-03, DOC-07, DOC-12, DOC-13, DOC-14, DOC-16, RTM (REV-DOC-001):**
  - Menyeragamkan seluruh dokumen menggunakan penamaan kelas console berakhiran `Command` (`AggregateDailyStatsCommand.php`, `PruneCacheCommand.php`).

---

## Versi 1.1 (30 Oktober 2026) — Audit & Consistency Revision
- Pembaruan baseline framework ke Laravel 13.x (PHP 8.3+).
- Pengembalian status ADR ke PROPOSED dan perbaikan 100% broken links Markdown.
- Formalisasi klasifikasi model bisnis private single-owner dan larangan Google AdSense.

---

## Versi 1.0 (30 Oktober 2026) — Rilis Draf Awal
- Pembuatan draf awal 16 dokumen spesifikasi teknis dan operasional.
