# Analisis Kesenjangan Kebutuhan & Keterlacakan Pengujian LokaTaut v1.1

> **Status Analisis:** `REMEDIATION REQUIRED PRIOR TO CODING`  
> **Tanggal Audit:** 31 Oktober 2026  
> **Auditor:** Senior Business Analyst, Product Manager, & QA Lead  
> **Dokumen Rujukan:** `01-product/02-prd.md` (DOC-02), `01-product/03-scope-requirements.md` (DOC-03), `06-quality/12-testing-qa.md` (DOC-12), dan `REQUIREMENT-TRACEABILITY-MATRIX.md` (RTM)

---

## 1. Executive Summary Kesenjangan

Audit kebutuhan (*requirement gap analysis*) memvalidasi apakah seluruh spesifikasi fungsional (FR) dan non-fungsional (NFR) telah memiliki deskripsi yang cukup, batasan ruang lingkup yang jelas, kriteria penerimaan (*acceptance criteria*), dan pemetaan keterlacakan pengujian (*traceability*) dua arah tanpa celah.

Hasil analisis menemukan dua kesenjangan utama:
1. **Kesenjangan Requirement Fungsional (REV-AUTH-001 - P1):** Ketiadaan spesifikasi fitur penggantian password dan pembaruan profil administrator tunggal.
2. **Kesenjangan Keterlacakan Pengujian Dua Arah (REV-QA-001 - P1):** Diskoneksi fisik antara `REQUIREMENT-TRACEABILITY-MATRIX.md` (RTM) dan `06-quality/12-testing-qa.md` (DOC-12) yang melibatkan 17 test case hilang di DOC-12 dan 9 test case yatim di DOC-12.

---

## 2. Analisis Cakupan Kebutuhan Fungsional (FR Coverage)

| Modul Produk | Rentang ID FR | Status Kelengkapan | Evaluasi & Temuan Kesenjangan |
|---|---|:---:|---|
| **Autentikasi Admin** | `FR-AUTH-001` s/d `FR-AUTH-003` | **TIDAK LENGKAP** | **GAP BESAR (REV-AUTH-001):** Tidak ada requirement `FR-AUTH-004` (Admin Password Change & Profile Management). Admin tidak bisa ganti password via UI. |
| **Manajemen Tautan** | `FR-LINK-001` s/d `FR-LINK-006` | **LENGKAP** | Mencakup CRUD link, slug custom/auto, expiration date, active/inactive toggle, tracking source tag. |
| **Mesin Safelink** | `FR-SL-001` s/d `FR-SL-007` | **LENGKAP (Bersyarat)** | Mencakup alur token sesi, countdown server-side, integrasi Turnstile, one-time redirect. Butuh penyesuaian hashing token (`REV-SEC-001`). |
| **Monetisasi & Iklan** | `FR-MON-001` s/d `FR-MON-004` | **LENGKAP (Bersyarat)** | Manajemen slot iklan (header, sidebar, content, popunder). Butuh larangan tegas pemicu iklan pada tombol proceed (`REV-MON-001`). |
| **Analitik & Metrik** | `FR-ANAL-001` s/d `FR-ANAL-004` | **LENGKAP** | Metrik total klik, pengunjung unik harian, sebaran referer, peranti, dan negara. |
| **Pengaturan Sistem** | `FR-SET-001` s/d `FR-SET-003` | **LENGKAP (Bersyarat)** | Konfigurasi countdown default, kunci Turnstile, site meta. Butuh switch emergency bypass Turnstile (`REV-SEC-002`). |
| **Dashboard Ringkasan**| `FR-DASH-001` s/d `FR-DASH-002` | **LENGKAP** | Kartu ringkasan metrik 30 hari dan tabel tautan terbaru. |

---

## 3. Analisis Kesenjangan Spesifikasi Penggantian Password (REV-AUTH-001)

### 3.1 Identifikasi Gap
Pada sistem yang dirancang beroperasi di shared hosting produksi, administrator wajib memiliki kemampuan mandiri untuk mengganti kata sandi. Mengandalkan `DatabaseSeeder` dengan kredensial default atau query manual SQL cPanel melanggar kaidah operasional aman (*Operational Security Hazard*).

### 3.2 Proposal Spesifikasi Requirement Baru: `FR-AUTH-004`
- **ID Kebutuhan:** `FR-AUTH-004`
- **Nama Fitur:** Admin Password & Profile Management
- **Prioritas:** `P1 (High / Mandatory Baseline)`
- **Deskripsi:** Administrator dapat mengubah password akun administrator tunggal melalui antarmuka web yang aman setelah mengonfirmasi password lama.
- **Kriteria Penerimaan (Acceptance Criteria):**
  1. *Given* administrator telah login ke dasbor, *When* mengakses menu `/admin/profile`, *Then* sistem menampilkan formulir ubah kata sandi.
  2. *Given* admin mengisi formulir, *When* `current_password` yang dimasukkan salah, *Then* sistem menolak perubahan dengan pesan error validasi dan mencatat insiden di `security_logs`.
  3. *Given* admin memasukkan password baru kurang dari 12 karakter atau tidak memiliki kombinasi karakter kompleks, *Then* sistem menolak dengan pesan validasi yang jelas.
  4. *Given* admin memasukkan konfirmasi password yang tidak cocok, *Then* validasi gagal.
  5. *Given* seluruh input valid, *When* form disubmit via `PUT /admin/profile/password`, *Then* sistem meng-hash password baru dengan Bcrypt, memperbarui database, merotasi sesi login, dan menampilkan notifikasi sukses.

---

## 4. Analisis Diskoneksi RTM vs Dokumen Pengujian (REV-QA-001)

### 4.1 Sebanyak 17 Test Case pada RTM Hilang dari Registry DOC-12
Pemeriksaan silang mendalam mengungkap bahwa dokumen `REQUIREMENT-TRACEABILITY-MATRIX.md` memetakan requirement terhadap 17 ID pengujian yang tabel definisinya tidak pernah ditulis di dalam `06-quality/12-testing-qa.md`:

| No | Test Case ID pada RTM | Kebutuhan yang Dipetakan | Dampak Ketiadaan Definisi di DOC-12 |
|:---:|---|---|---|
| 1 | `TEST-DASH-001` | `FR-DASH-001` (Dashboard Summary Metrics) | Kriteria uji agregasi kartu metrik dashboard tidak terdefinisi. |
| 2 | `TEST-DASH-002` | `FR-DASH-002` (Recent Links Listing) | Kriteria uji daftar 10 tautan terbaru tidak terdefinisi. |
| 3 | `TEST-ANAL-001` | `FR-ANAL-001` (Date Range Filter) | Kriteria uji pemfilteran rentang tanggal analitik tidak terdefinisi. |
| 4 | `TEST-ANAL-002` | `FR-ANAL-002` (Referrer & Device Breakdown)| Kriteria uji pelaporan breakdown perangkat & referer tidak terdefinisi. |
| 5 | `TEST-SET-001` | `FR-SET-001` (General Site Settings) | Kriteria uji update konfigurasi umum tidak terdefinisi. |
| 6 | `TEST-SET-002` | `FR-SET-002` (Turnstile Settings Form) | Kriteria uji update credentials Turnstile tidak terdefinisi. |
| 7 | `TEST-PERF-001` | `NFR-PERF-001` (Response Time < 200ms) | Metrik acuan dan skenario uji beban response time tidak ada di DOC-12. |
| 8 | `TEST-PERF-002` | `NFR-PERF-002` (LiteSpeed Non-Cache Test)| Skenario pengujian header anti-cache tidak ada di DOC-12. |
| 9 | `TEST-PERF-003` | `NFR-PERF-003` (Cron Memory Footprint) | Kriteria uji konsumsi memori command harian tidak ada di DOC-12. |
| 10 | `TEST-UX-001` | `NFR-UX-001` (Mobile Viewport 360px) | Checkpoint pengujian responsivitas layar sempit tidak ada di DOC-12. |
| 11 | `TEST-UX-002` | `NFR-UX-002` (Accessibility & Contrast) | Checkpoint uji kontras warna dan accessibility tidak ada di DOC-12. |
| 12 | `TEST-MAINT-001` | `NFR-REL-002` (Maintenance Mode Behavior) | Skenario uji maintenance mode tidak ada di DOC-12. |
| 13 | `TEST-REG-001` | Regresi Sprint Pengujian Menyeluruh | Rencana regresi otomatis tidak ada di tabel DOC-12. |
| 14 | `TEST-REG-002` | Database Migration & Rollback Integrity| Skenario uji `migrate:rollback` tidak ada di DOC-12. |
| 15 | `TEST-CLI-001` | Scheduled Daily Aggregation Command | Pengujian command konsol harian tidak ada di DOC-12. |
| 16 | `TEST-CLI-002` | Scheduled Session Pruning Command | Pengujian command pembersihan sesi tidak ada di DOC-12. |
| 17 | `TEST-SEC-006` | `NFR-SEC-004` (IP Rate Limiting Throttling)| Skenario uji respons 429 Too Many Requests tidak ada di DOC-12. |

### 4.2 Sebanyak 9 Test Case di DOC-12 Belum Terpetakan di RTM
Sebaliknya, dokumen `DOC-12` mendefinisikan 9 test case yang belum dimasukkan ke dalam matriks RTM:
- `TEST-SL-004` (Safelink Destination Redirection Flow)
- `TEST-MON-003` (Ad Slot Status Toggle)
- `TEST-SEC-004` (Single Session Enforcement)
- `TEST-SEC-005` (Audit Log Verification)
- `TEST-RATE-003` (Admin Login Rate Limiter)
- `TEST-UNIT-003` (Slug Generator Unit Test)
- `TEST-UNIT-004` (IP Pseudonymization Unit Test)
- `TEST-UNIT-005` (Countdown Calculator Unit Test)
- `TEST-UNIT-006` (Ad Code Sanitizer Unit Test)

---

## 5. Rencana Tindakan Perbaikan (Remediation Plan)

Untuk mencapai status keterlacakan 100% (*100% Traceability Gate*), tahapan koreksi berikut wajib dieksekusi:

```
[ Tindakan Remediasi 1 ]
Tambahkan FR-AUTH-004 pada DOC-02 (PRD) dan DOC-03 (Scope).
       |
[ Tindakan Remediasi 2 ]
Tambahkan rute profil/password dan ProfileController pada DOC-07 (Tech Spec).
       |
[ Tindakan Remediasi 3 ]
Tuliskan 17 tabel spesifikasi test case yang hilang ke dalam DOC-12 (Testing & QA).
       |
[ Tindakan Remediasi 4 ]
Sinkronkan matriks RTM agar memuat pemetaan FR-AUTH-004 dan 9 test case yang sebelumnya yatim.
```

Dengan mengeksekusi rencana perbaikan di atas, seluruh persyaratan produk akan terhubung secara teratur dari PRD hingga baris kode pengujian otomatis.

