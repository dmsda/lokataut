# Open Decisions Registry — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | OPEN-DECISIONS |
| **Versi** | 1.2.1 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Status** | ACTIVE (Ready for Product Owner Final Review) |

---

## 1. Daftar Keputusan Terbuka (Open Decisions Log)

Dokumen ini mencatat seluruh keputusan arsitektural dan bisnis yang memerlukan keputusan resmi dari **Product Owner (Dimas Aditya)**. AI Coding Agent **DILARANG** mengambil keputusan sepihak jika status masih `PROPOSED` atau `UNDER REVIEW`.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                               Open Decisions Summary                                   │
├─────────┬──────────────────────────────────┬──────────────┬──────────────┬─────────────┤
│ DEC ID  │ Topik Keputusan                  │ Status       │ Tanggal      │ Approval PO │
├─────────┼──────────────────────────────────┼──────────────┼──────────────┼─────────────┤
│ DEC-001 │ Migrasi Framework ke Laravel 13  │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-002 │ Status Kandidat Jaringan Iklan   │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-003 │ Verifikasi Runtime WarnaHost     │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-004 │ Durasi Default Countdown (10s)   │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-005 │ Turnstile Placement (Sebelum)    │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-006 │ Retensi Data Mentah (90 Hari)    │ APPROVED     │ 30 Okt 2026  │ Dimas A.    │
│ DEC-007 │ Penangguhan Format Popunder      │ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-008 │ Fitur Ganti Password (FR-AUTH-004│ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-009 │ Hashed Token Sesi (token_hash)   │ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-010 │ Fallback Vendor Deploy (DOC-13)  │ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-011 │ Turnstile Emergency Bypass Switch│ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-012 │ Approval Formal ADR-001 s/d 016  │ UNDER REVIEW │ 31 Okt 2026  │ Pending PO  │
│ DEC-013 │ Kebijakan Soft & Hard Delete     │ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
│ DEC-014 │ Mandatori ANALYTICS_SALT Mandiri │ PROPOSED     │ 31 Okt 2026  │ Pending PO  │
└─────────┴──────────────────────────────────┴──────────────┴──────────────┴─────────────┘
```

---

## 2. Rincian & Catatan Keputusan

### DEC-001: Migrasi Framework ke Laravel 13.x
- **Konteks:** Laravel 11 telah melewati masa dukungan keamanan (EOL) pada 12 Maret 2026.
- **Keputusan:** Mengadopsi **Laravel 13.x** dengan runtime **PHP 8.3+** sebagai baseline baku untuk menjamin dukungan keamanan hingga Maret 2028.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-002: Status Kandidat Jaringan Iklan (Adsterra)
- **Konteks:** Menghindari asumsi sepihak mengenai persetujuan format iklan safelink tanpa verifikasi akun riil.
- **Keputusan:** Menetapkan Adsterra sebagai `CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT VERIFICATION)`. Desain slot iklan dibuat agnostik sehingga dapat memuat script jaringan lain jika diperlukan.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-003: Verifikasi Runtime Hosting WarnaHost Starter
- **Konteks:** Keterbatasan resource 1 vCPU dan 2 GB RAM pada shared hosting.
- **Keputusan:** Menetapkan WarnaHost Starter Unlimited sebagai `TARGET HOSTING (NEEDS PRE-DEPLOYMENT RUNTIME VERIFICATION)`. Wajib memverifikasi ketersediaan PHP 8.3 CLI dan Composer 2 di cPanel sebelum instalasi final.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-004: Durasi Default Countdown Timer
- **Konteks:** Keseimbangan antara pengalaman pengunjung dan waktu tayang impresi iklan.
- **Keputusan:** 10 detik ditetapkan sebagai durasi default global, dengan fleksibilitas dikonfigurasi per tautan melalui panel admin.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-005: Titik Penempatan Cloudflare Turnstile
- **Konteks:** Menentukan kapan tantangan verifikasi bot dimunculkan kepada pengunjung.
- **Keputusan:** Turnstile ditempatkan **sebelum countdown dimulai**. Setelah verifikasi lolos via AJAX, timer 10 detik mulai berdetak.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-006: Kebijakan Retensi Data Kunjungan
- **Konteks:** Mengontrol ukuran tabel `visits` agar tidak melampaui kapasitas disk shared hosting.
- **Keputusan:** Data mentah disimpan maksimal **90 hari** sebelum dibersihkan via scheduler mingguan, setelah diringkas permanen ke `daily_stats`.
- **Status:** `APPROVED by Product Owner (30 Okt 2026)`.

### DEC-007: Penangguhan Format Popunder & Isolasi Tombol Navigasi
- **Konteks:** Menghindari penalti Invalid Traffic (IVT) Adsterra atas pelanggaran *forced/incentivized clicks*.
- **Rekomendasi:** Menangguhkan format popunder pada MVP dan memfokuskan monetisasi pada banner display terlabel jelas dengan jarak aman 30px dari tombol aksi.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-008: Penambahan Fitur Ganti Kata Sandi Admin (`FR-AUTH-004`)
- **Konteks:** Menyediakan antarmuka mandiri bagi admin tunggal untuk merotasi kata sandi akun secara aman.
- **Rekomendasi:** Menyetujui implementasi `FR-AUTH-004`, rute `/admin/profile`, dan validasi kata sandi lama/baru.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-009: Penyimpanan Hashed Session Token (`token_hash`)
- **Konteks:** Mematuhi prinsip *Defense-in-Depth* OWASP ASVS v4.0 Section 3.2.4.
- **Rekomendasi:** Menyetujui skema `visitor_sessions.token_hash` (SHA-256) untuk melindungi token dari kebocoran basis data.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-010: Prosedur Fallback Pre-Built Vendor Deployment
- **Konteks:** Mitigasi risiko *Process Killed / Out Of Memory* saat live `composer install` di CloudLinux WarnaHost.
- **Rekomendasi:** Menyetujui opsi rilis paket vendor pre-built via ZIP archive sebagai cadangan utama.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-011: Mode Darurat Turnstile (`turnstile_mode`)
- **Konteks:** Menjamin kelangsungan akses unduhan pengunjung jika API Cloudflare Turnstile mengalami outage.
- **Rekomendasi:** Menyetujui penambahan setting switch darurat `turnstile_mode = emergency_bypass` dengan audit log.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-012: Persetujuan Formal Dokumen ADR-001 s/d ADR-016
- **Konteks:** Memberikan otorisasi formal pada berkas arsitektur sebelum fase implementasi kode dibuka.
- **Rekomendasi:** PO mengubah status seluruh ADR dari `PROPOSED` menjadi `APPROVED` pada `Approval Registry`.
- **Status:** `UNDER REVIEW by Product Owner`.

### DEC-013: Kebijakan Penghapusan Data (Soft & Hard Delete) dan Snapshot Slug
- **Konteks:** Menjaga keutuhan analitik historis saat tautan dihapus permanen.
- **Rekomendasi:** Menyetujui kebijakan: Soft Delete sebagai default operasional; Hard Delete menyetel `visits.link_id` dan `daily_stats.link_id` menjadi `NULL` (`ON DELETE SET NULL`) dengan snapshot `link_slug` historis tersimpan pada `daily_stats`.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.

### DEC-014: Mandatori Pemisahan Kunci ANALYTICS_SALT Tanpa Fallback
- **Konteks:** Menjamin isolasi siklus kunci analitik dari `APP_KEY` sesuai standar NIST SP 800-57 Part 1.
- **Rekomendasi:** Menyetujui penghapusan mutlak fallback `ANALYTICS_SALT` ke `APP_KEY`, penegakan exception jika tidak ada di production, dan dokumentasi batas kontinuitas unique visitor saat rotasi kunci.
- **Status:** `PROPOSED (Menunggu Tanda Tangan PO)`.
