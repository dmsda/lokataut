# Matriks Konsistensi Silang Dokumentasi LokaTaut v1.1

> **Status Audit:** `NEEDS REVISION`  
> **Tanggal Audit:** 31 Oktober 2026  
> **Cakupan Dokumen:** DOC-01 s/d DOC-16, RTM, dan Direktif Operasional (`AGENTS.md`)

---

## 1. Ikhtisar Evaluasi Konsistensi

Audit ini melakukan perbandingan silang (*cross-referencing*) antara seluruh dokumen spesifikasi untuk mendeteksi kontradiksi, ambiguitas, atau kesenjangan (*information drift*) di antara dimensi produk, arsitektur, basis data, keamanan, monetisasi, dan pengujian.

```
+---------------------------------------------------------------------------------------------------------+
| DIMENSI SPESIFIKASI           | DOKUMEN YANG TERLIBAT       | KONSISTENSI | CATATAN TEMUAN / STATUS     |
+-------------------------------+-----------------------------+-------------+-----------------------------+
| 1. Model Bisnis & Scope       | DOC-01, 02, 03, AGENTS.md   | 100% MATCH  | Konsisten Private Owner     |
| 2. Tech Stack & Runtime       | DOC-05, 07, 13, 15, AGENTS  | 100% MATCH  | Laravel 13 & PHP 8.3+ sinkron|
| 3. Destination URL Protection | DOC-02, 04, 07, 09          | 100% MATCH  | Rahasia s/d 302 Redirect    |
| 4. Session Token Lifecycle    | DOC-06, 07, 09              | DISKREPANSI | REV-SEC-001 (Plain vs Hash) |
| 5. Autentikasi & Profil Admin | DOC-02, 07, RTM             | KESENJANGAN | REV-AUTH-001 (No Pwd Change)|
| 6. Basis Data & Integritas FK | DOC-06, 07                  | DISKREPANSI | REV-DB-001 (Cascade Delete) |
| 7. Agregasi Analitik Harian   | DOC-06, 07, 14              | DISKREPANSI | REV-DB-002 & REV-DOC-001    |
| 8. Kebijakan Jaringan Iklan   | DOC-03, 10, AGENTS.md       | DISKREPANSI | REV-MON-001 (Click Hijack)  |
| 9. Kesiapan Pengujian & RTM   | DOC-12, RTM                 | DISKREPANSI | REV-QA-001 (17 Missing Tests|
| 10. Infrastruktur Shared Host | DOC-05, 11, 13              | KESENJANGAN | REV-OPS-001 & REV-OPS-002   |
+---------------------------------------------------------------------------------------------------------+
```

---

## 2. Rincian Analisis Konsistensi Silang Antar Dokumen

### 2.1 Model Bisnis, Scope, & Direktif AI (DOC-01, DOC-02, DOC-03, AGENTS.md)
- **Status:** **KONSISTEN (100% Sesuai)**
- **Analisis:**
  - Seluruh dokumen menegaskan bahwa LokaTaut adalah platform **PRIVATE / SINGLE OWNER**.
  - Aturan di `AGENTS.md` selaras dengan `DOC-01` dan `DOC-03`: melarang keras fitur pendaftaran publik, dompet digital, gateway pembayaran, marketplace advertiser/publisher, referral, dan SaaS multi-tenant.
  - Batasan akses administrator tunggal ditegakkan di seluruh hierarki dokumen.

### 2.2 Tech Stack & Batasan Infrastruktur (DOC-05, DOC-07, DOC-13, DOC-15, AGENTS.md)
- **Status:** **KONSISTEN (100% Sesuai)**
- **Analisis:**
  - Seluruh file telah diperbarui ke baseline **Laravel 13.x (PHP 8.3+)** dan MySQL 8.0+.
  - Larangan pengenalan runtime baru (Node.js backend, Python, Go), supervisor daemon, atau Redis diatur konsisten di `DOC-05`, `DOC-07`, dan `AGENTS.md`.
  - Frontend publik disepakati murni Semantic HTML5, Native CSS (no Tailwind/Bootstrap di safelink), dan Vanilla JS ES6+.

### 2.3 Perlindungan Destination URL (DOC-02, DOC-04, DOC-07, DOC-09)
- **Status:** **KONSISTEN (100% Sesuai)**
- **Analisis:**
  - Seluruh dokumen sepakat bahwa URL tujuan akhir **tidak pernah dibocorkan ke browser** sebelum pengunjung berhasil menyelesaikan verifikasi Turnstile dan countdown server-side.
  - Mekanisme penyerahan URL tujuan via HTTP 302 Location header pada endpoint final redirect (`POST /sl/{token}/proceed` -> `302 Found`) konsisten antara diagram alur `DOC-04`, spesifikasi controller `DOC-07`, dan dokumen keamanan `DOC-09`.

### 2.4 Token Sesi Safelink & Penyimpanan Database (DOC-06, DOC-07, DOC-09)
- **Status:** **DISKREPANSI (Perlu Penyesuaian — REV-SEC-001)**
- **Analisis:**
  - `DOC-07` dan `DOC-09` menjelaskan token sesi memiliki entropi 256-bit (`bin2hex(random_bytes(32))`), berbatas waktu 30 menit, dan terikat hash session + user-agent.
  - Namun di `DOC-06` (Tabel `visitor_sessions`), token disimpan dalam teks terbuka (plaintext).
  - Standar arsitektur keamanan menuntut token disimpan dalam bentuk hash kriptografis (`token_hash = sha256($token)`) untuk mencegah *token hijacking* jika basis data bocor.

### 2.5 Modul Autentikasi & Manajemen Kredensial (DOC-02, DOC-03, DOC-07, RTM)
- **Status:** **KESENJANGAN REQUIREMENT (Perlu Penambahan — REV-AUTH-001)**
- **Analisis:**
  - `DOC-02` hanya mencantumkan `FR-AUTH-001` (Login), `FR-AUTH-002` (Single-session enforcement), dan `FR-AUTH-003` (Logout).
  - `DOC-07` mendefinisikan rute login dan logout, namun **tidak menyediakan rute maupun controller untuk ubah password admin**.
  - `RTM` tidak memetakan requirement ubah kredensial admin. Fitur ini merupakan *mandatory baseline* untuk sistem produksi.

### 2.6 Integritas Relasi Basis Data (DOC-06, DOC-07)
- **Status:** **DISKREPANSI (Perlu Penyesuaian — REV-DB-001)**
- **Analisis:**
  - `DOC-06` mendefinisikan relasi `FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE` pada tabel `visits`.
  - `DOC-02` dan `DOC-07` menghendaki pelaporan analitik historis yang permanen. Jika link terhapus secara fisik, riwayat kunjungan di tabel `visits` ikut musnah akibat klausa `ON DELETE CASCADE`.

### 2.7 Logika Agregasi Data Harian (DOC-06, DOC-07, DOC-14)
- **Status:** **DISKREPANSI (Perlu Penyesuaian — REV-DB-002 & REV-DOC-001)**
- **Analisis:**
  - `DOC-07` Section 6.2 mendeskripsikan `AggregateDailyStatsCommand` tanpa penanganan tautan yang di-soft-delete pada hari yang sama.
  - Terdapat perbedaan penamaan kelas: `AggregateDailyStatsCommand` di `DOC-07` vs `AggregateDailyStats` di `DOC-14`.

### 2.8 Kebijakan Jaringan Iklan (DOC-03, DOC-10, AGENTS.md)
- **Status:** **DISKREPANSI (Perlu Penyesuaian — REV-MON-001)**
- **Analisis:**
  - `AGENTS.md` Rule 4 melarang manipulasi klik palsu (*no forced clicks, no accidental clicks layout*).
  - `DOC-10` menyebutkan integrasi Adsterra Popunder pada interaksi pertama pengunjung.
  - Belum ada larangan eksplisit yang mencegah pengikatan popunder pada tombol aksi utama safelink (*Proceed Button*), yang berpotensi memicu sanksi invalid traffic (IVT) dari Adsterra.

### 2.9 Sinkronisasi Kualitas & Keterlacakan Pengujian (DOC-12, RTM)
- **Status:** **KESENJANGAN BESAR (Wajib Rekonsiliasi — REV-QA-001)**
- **Analisis:**
  - Terdapat **17 test ID** di `REQUIREMENT-TRACEABILITY-MATRIX.md` yang tidak memiliki blok tabel detail pada `DOC-12`:
    - `TEST-DASH-001`, `TEST-DASH-002` (Modul Dashboard)
    - `TEST-ANAL-001`, `TEST-ANAL-002` (Modul Analitik)
    - `TEST-SET-001`, `TEST-SET-002` (Modul Pengaturan)
    - `TEST-PERF-001`, `TEST-PERF-002`, `TEST-PERF-003` (Pengujian Kinerja)
    - `TEST-UX-001`, `TEST-UX-002` (Pengujian Responsivitas UI/UX)
    - `TEST-MAINT-001` (Maintenance Mode)
    - `TEST-REG-001`, `TEST-REG-002` (Regression Testing)
  - Sebaliknya, terdapat **9 test ID** di `DOC-12` yang tidak dicatat pada RTM.

### 2.10 Keterbatasan Shared Hosting & Deployment (DOC-05, DOC-07, DOC-11, DOC-13)
- **Status:** **KESENJANGAN OPERASIONAL (Perlu Prosedur Fallback — REV-OPS-001 & REV-OPS-002)**
- **Analisis:**
  - `DOC-13` hanya mengandalkan live `composer install` via SSH, tanpa memperhitungkan batas alokasi memori CloudLinux LVE WarnaHost Starter (2 GB RAM).
  - Dokumen belum menspesifikasikan header bypass cache khusus web server LiteSpeed (`X-LiteSpeed-Cache-Control: no-cache`) untuk rute safelink dinamis ber-token.

---

## 3. Matriks Tindak Lanjut Penyelarasan

| Temuan | Tindakan Penyelarasan Dokumen | Dokumen Target | Penanggung Jawab |
|---|---|---|---|
| **REV-SEC-001** | Ubah kolom `token` menjadi `token_hash` di DDL dan perbarui alur pembuatan/validasi token sesi. | `DOC-06`, `DOC-07`, `DOC-09` | Security & DB Architect |
| **REV-AUTH-001** | Tambahkan spesifikasi requirement `FR-AUTH-004`, rute, controller, dan form ganti password admin. | `DOC-02`, `DOC-03`, `DOC-07`, RTM | Product Manager & Laravel Arch |
| **REV-QA-001** | Masukkan 17 tabel test case yang hilang ke dalam DOC-12 dan daftarkan 9 test case yatim ke RTM. | `DOC-12`, RTM | QA Lead |
| **REV-OPS-001** | Cantumkan strategi deployment fallback (Pre-compiled vendor bundle via CI/Lokal). | `DOC-13` | DevOps Architect |
| **REV-MON-001** | Tegaskan larangan event click-hijacking pada tombol navigasi safelink. | `DOC-10` | Monetization Specialist |
| **REV-DB-001** | Ubah kebijakan penghapusan link menjadi pure soft-delete atau sesuaikan FK visits `ON DELETE SET NULL`. | `DOC-06` | Database Architect |
| **REV-DB-002** | Perjelas pemanggilan `Link::withTrashed()` pada command agregasi harian. | `DOC-07` | Laravel Architect |
| **REV-SEC-002** | Tambahkan key `turnstile_enabled` di tabel settings sebagai switch darurat saat API outage. | `DOC-06`, `DOC-09` | Security Engineer |
| **REV-PERF-001** | Tentukan job pruning file cache harian untuk menghindari kehabisan kuota inode hosting. | `DOC-07`, `DOC-11` | DevOps / System Architect |
| **REV-OPS-002** | Tambahkan header anti-cache LiteSpeed pada middleware safelink dan directive `.htaccess`. | `DOC-07`, `DOC-13` | DevOps / Backend Engineer |
| **REV-DOC-001** | Standarkan penamaan command ke `AggregateDailyStatsCommand.php`. | `DOC-14` | Technical Documentation Lead |

---
*Matriks konsistensi ini memastikan tidak ada celah logika tersembunyi yang dapat membingungkan AI Coding Agent.*

