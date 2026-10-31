# Register Risiko Arsitektur (Architectural Risk Register) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-RISK-01 |
| **Versi Dokumen** | 1.0.1 (Remediasi Konsistensi & Pemetaan DEC) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (DOC-09, DOC-11, DOC-13, DOC-15, & OPEN-DECISIONS) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Application Security Engineer & Lead DevOps Engineer |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Metodologi Penilaian Risiko

Tingkat keparahan risiko arsitektur dihitung menggunakan matriks $3 \times 3$ standar:

$$\text{Tingkat Keparahan (Severity)} = \text{Probabilitas (Likelihood)} \times \text{Dampak (Impact)}$$

- **Probabilitas:** Low (1), Medium (2), High (3)
- **Dampak:** Low (1), Medium (2), High (3)
- **Kategori Skor:**
  - Skor 6–9: **CRITICAL / HIGH** (Wajib memiliki mitigasi preventif sebelum fase koding dibuka).
  - Skor 3–4: **MEDIUM** (Wajib dipantau dan memiliki rencana kontinjensi).
  - Skor 1–2: **LOW** (Diterima dengan prosedur operasional standar).

---

## 2. Tabel Register Risiko Arsitektural (14 Risiko Utama v1.0.1)

| Risk ID | Deskripsi & Konteks Risiko | ADR / DEC Terkait | Probabilitas | Dampak | Tingkat Keparahan | Strategi Mitigasi Preventif | Mekanisme Deteksi & Monitoring | Rencana Kontinjensi / Fallback | Risiko Sisa |
|:---:|---|:---:|:---:|:---:|:---:|---|---|---|:---:|
| **RISK-01** | **Inkonsistensi Versi PHP CLI vs Web Handler cPanel**<br>Web server menjalankan PHP 8.3 via LiteSpeed, namun `/usr/bin/php` di CLI masih mengarah ke PHP 8.1 default, menyebabkan cron artisan crash. | ADR-001, ADR-009<br>*(DEC-001, DEC-003)* | High (3) | High (3) | **CRITICAL (9)** | Menentukan path binary absolut `/usr/local/bin/ea-php83` pada seluruh baris crontab cPanel. | Eksekusi pre-flight script `php -v` di terminal SSH sebelum setup. | Mengatur alias PHP 8.3 di file `.bashrc` pengguna cPanel. | Low |
| **RISK-02** | **Varian RDBMS MariaDB Menolak Kueri Khusus MySQL 8**<br>Hosting menjalankan MariaDB 10.5+ yang memiliki perbedaan tipikal pada fungsi JSON atau default mode kueri. | ADR-002<br>*(DEC-012 Scope)* | Medium (2) | High (3) | **HIGH (6)** | Mengaudit skema DDL agar 100% netral lintas MySQL 8 dan MariaDB 10.5+ tanpa sintaks eksklusif. | Eksekusi `SELECT VERSION();` pada pre-flight audit. | Penyesuaian konfigurasi koneksi `database.php` Laravel (`modes`, `strict`). | Low |
| **RISK-03** | **Penolakan Akun / Domain oleh Jaringan Iklan Adsterra**<br>Domain baru `lokataut.com` ditolak atau diverifikasi lama oleh tim compliance Adsterra. | ADR-004<br>*(DEC-002)* | Medium (2) | High (3) | **HIGH (6)** | Mendaftarkan akun dan memverifikasi domain lebih awal (Phase 0); menyediakan modularitas slot iklan agnostik. | Status review dashboard Adsterra. | Beralih sementara ke kandidat cadangan (Monetag / PropellerAds) tanpa ubah kode. | Medium |
| **RISK-04** | **Out-of-Memory (OOM SIGKILL) saat Live Composer di Hosting**<br>Proses `composer install` menghabiskan RAM fisik 2 GB CloudLinux LVE dan dimatikan paksa kernel OS. | ADR-013, ADR-009<br>*(DEC-010, DEC-003)* | High (3) | High (3) | **CRITICAL (9)** | Mengadopsi prosedur rilis cadangan resmi: build artifact `vendor.zip` di lingkungan lokal/CI (ADR-013 / DEC-010). | Monitor resource usage cPanel LVE Manager. | Unggah pre-built ZIP via cPanel File Manager/FTP; bypass composer di server. | Low |
| **RISK-05** | **Cloudflare Turnstile API Timeout / Outage Eksternal**<br>Koneksi cURL dari hosting ke endpoint `siteverify` Cloudflare mengalami latensi tinggi atau timeout > 5s. | ADR-006<br>*(DEC-005, DEC-011)* | Medium (2) | High (3) | **HIGH (6)** | Set timeout cURL maksimal 5 detik via `Http::timeout(5)`; tangani respon HTTP 503 yang ramah pengunjung. | Log error `security_logs` saat cURL error code 28. | Sakelar darurat bypass di `.env` (`turnstile_mode = emergency_bypass` via DEC-011). | Low |
| **RISK-06** | **Manipulasi Timer JavaScript oleh Pengunjung Safelink**<br>Pengunjung mengubah variabel countdown di browser devtools untuk mempercepat unduhan. | ADR-007<br>*(DEC-004)* | High (3) | Medium (2) | **HIGH (6)** | Validasi countdown dievaluasi secara server-side via timestamp server (`countdown_started_at`) berdurasi 10 detik. | Log penolakan bypass countdown di `security_logs` (`TEST-SAFE-006`). | Tolak request langsung dengan HTTP 422 Unprocessable Entity. | Negligible |
| **RISK-07** | **Race Condition pada Eksekusi Redirect Unduhan Ganda**<br>Dua request POST `/proceed` konkuren dikirim pada milidetik yang sama untuk token yang sama. | ADR-007, ADR-011<br>*(DEC-004, DEC-009)* | Medium (2) | Medium (2) | **MEDIUM (4)** | Transisi atomic update di DB: `UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`. | Affected rows = 0 pada request kedua. | Request kedua langsung ditolak dengan HTTP 422 Token Already Consumed. | Negligible |
| **RISK-08** | **Kehilangan Kunci Enkripsi `APP_KEY` Mengakibatkan Data Hilang**<br>`APP_KEY` terhapus atau diganti saat re-deploy, mematikan seluruh dekripsi URL tujuan. | ADR-008<br>*(DEC-012 Scope)* | Low (1) | High (3) | **MEDIUM (3)** | Larangan keras menjalankan `key:generate` pasca-instalasi awal; backup aman file `.env` di luar repositori. | Health-check script verifikasi dekripsi sample record. | Pemulihan `.env` dari arsip backup terisolasi. | Low |
| **RISK-09** | **Pembengkakan Disk MySQL Akibat Trafik Kunjungan Tinggi**<br>Trafik melonjak hingga 50.000 visits/hari, menyebabkan tabel `visits` melebihi 1,5 GB. | ADR-010, ADR-017<br>*(DEC-006, DEC-016)* | Medium (2) | Medium (2) | **MEDIUM (4)** | Scheduler retensi mingguan otomatis membersihkan baris `visits` yang lebih tua dari 90 hari setelah diringkas. | Alert kapasitas disk cPanel dan cron audit log. | Eksekusi manual command `php artisan lokataut:prune-visits --force` jika disk > 85%. | Low |
| **RISK-10** | **Kerusakan Data Analitik Historis Akibat Rotasi Salt IP**<br>Salt analitik tercampur dengan `APP_KEY`, sehingga rotasi enkripsi merusak hash IP unik masa lalu. | ADR-016<br>*(DEC-014)* | Low (1) | High (3) | **MEDIUM (3)** | Pemisahan mandatori `ANALYTICS_SALT` independen tanpa fallback implisit ke `APP_KEY`. | Boot failure `RuntimeException` di environment produksi jika salt tidak diset. | Dokumentasi audit trail rotasi salt terpisah di panduan devops. | Low |
| **RISK-11** | **Deteksi Invalid Traffic (IVT) oleh Tim Anti-Fraud Jaringan Iklan**<br>Skrip iklan eksternal membajak event klik tombol proceed atau iklan terlalu dekat dengan tombol. | ADR-014<br>*(DEC-007)* | Medium (2) | High (3) | **HIGH (6)** | Isolasi mutlak `#proceed-btn` dari skrip iklan; jarak visual 30px (safeguard internal WCAG); penangguhan popunder. | Monitor rasio IVT pada dashboard analitik Adsterra. | Penonaktifan sementara slot banner bermasalah via toggle panel admin. | Low |
| **RISK-12** | **Duplikasi Baris Agregasi Akibat Hard Delete Tautan Sebelum Cron**<br>MySQL mengizinkan baris duplikat saat `link_id` menjadi NULL jika menggunakan unique index komposit nullable. | ADR-015, ADR-017<br>*(DEC-013, DEC-015, DEC-016)* | High (3) | High (3) | **CRITICAL (9)** | Menerapkan `aggregate_key VARCHAR(100) NOT NULL` (DEC-015) dan Idempotensi 3-Pilar (DB Transaction, Row Lock, withoutOverlapping). | Test otomatis `TEST-ANA-004` dan `TEST-LINK-007`. | Eksekusi command agregasi harian bersifat idempoten matematis. | Negligible |
| **RISK-13** | **Serangan Brute Force terhadap Endpoint Login / Profil Admin**<br>Penyerang menembakkan ribuan request autentikasi untuk membobol akun admin tunggal. | ADR-010, ADR-012<br>*(DEC-008)* | High (3) | High (3) | **CRITICAL (9)** | Rate limiting ketat: 5 percobaan login/menit; proteksi CSRF; password minimal 12 karakter. | Pencatatan otomatis kegagalan login di `security_logs` (`TEST-AUTH-003`). | Pemblokiran IP otomatis selama 15 menit via middleware throttling. | Low |
| **RISK-14** | **Penyuntikan Skrip Berbahaya (Stored XSS) pada Kolom Kode Iklan**<br>Kode iklan pihak ketiga menyuntikkan script berbahaya yang mencuri cookie sesi admin. | ADR-004, ADR-014<br>*(DEC-002, DEC-007)* | Low (1) | High (3) | **MEDIUM (3)** | Akses input `ad_code` dibatasi hanya untuk Super Admin terautentikasi; penegakan Content Security Policy (CSP). | Audit log setiap perubahan kode iklan di `security_logs`. | Fitur darurat penonaktifan iklan global di panel admin. | Low |

---

## 3. Matriks Distribusi Risiko Arsitektur

```
    Dampak Tinggi (3) │  [RISK-08, 10, 14]   │  [RISK-02, 03, 05, 11]  │  [RISK-01, 04, 12, 13]  │
                      │       (MEDIUM)       │         (HIGH)          │       (CRITICAL)        │
    Dampak Sedang (2) │                      │      [RISK-07, 09]      │        [RISK-06]        │
                      │        (LOW)         │        (MEDIUM)         │         (HIGH)          │
    Dampak Rendah (1) │                      │                         │                         │
                      │        (LOW)         │         (LOW)           │        (MEDIUM)         │
                      └──────────────────────┴─────────────────────────┴─────────────────────────┘
                           Rendah (1)               Sedang (2)                 Tinggi (3)
                                              P R O B A B I L I T A S
```

### Rekapitulasi Tingkat Risiko:
- **Risiko Kritis (CRITICAL):** 4 Risiko (`RISK-01`, `RISK-04`, `RISK-12`, `RISK-13`) — *Seluruhnya telah memiliki mitigasi arsitektur definitif*.
- **Risiko Tinggi (HIGH):** 5 Risiko (`RISK-02`, `RISK-03`, `RISK-05`, `RISK-06`, `RISK-11`).
- **Risiko Sedang (MEDIUM):** 5 Risiko (`RISK-07`, `RISK-08`, `RISK-09`, `RISK-10`, `RISK-14`).
- **Risiko Rendah (LOW):** 0 Risiko.

---

## 4. Tindakan Segera Sebelum Masuk Fase Koding (Pre-Coding Gates)

Tiga tindakan mitigasi kritis yang wajib diselesaikan sebelum baris kode aplikasi pertama ditulis:
1. **Verifikasi CLI Hosting:** Eksekusi 7 perintah verifikasi terminal cPanel pada akun WarnaHost riil pengguna untuk menutup `RISK-01` dan `RISK-02`.
2. **Pendaftaran Domain & Akun Adsterra:** Pengajuan akun dan domain `lokataut.com` untuk menutup `RISK-03`.
3. **Persetujuan Formal DEC-012 & DEC-016:** Pengesahan tata kelola ADR dan desain integritas basis data untuk menutup `RISK-12`.

