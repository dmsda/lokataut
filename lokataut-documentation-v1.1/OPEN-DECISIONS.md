# Open Decisions Registry — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | OPEN-DECISIONS |
| **Versi** | 1.1 |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Status** | ACTIVE |

---

## 1. Daftar Keputusan Terbuka (Open Decisions Log)

Dokumen ini mencatat seluruh keputusan arsitektural dan bisnis yang memerlukan keputusan resmi dari **Product Owner (Dimas Aditya)**. AI Coding Agent **DILARANG** mengambil keputusan sepihak jika status masih `OPEN` atau `UNDER REVIEW`.

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
│ DEC-007 │ Pemilihan Format Adsterra        │ UNDER REVIEW │ 30 Okt 2026  │ Pending Ver.│
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

### DEC-007: Pemilihan Format Iklan Adsterra (Social Bar vs Popunder)
- **Konteks:** Popunder memiliki eCPM tinggi namun berisiko memicu peringatan browser jika pengiklan mengarahkan ke halaman mencurigakan.
- **Status:** `UNDER REVIEW` (Akan diputuskan saat pendaftaran akun resmi Adsterra selesai).

