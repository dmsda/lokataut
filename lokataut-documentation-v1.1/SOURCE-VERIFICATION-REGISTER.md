# Source Verification Register — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | SOURCE-VERIFICATION-REGISTER |
| **Versi** | 1.1 |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Status** | ACTIVE |

---

## 1. Register Verifikasi Sumber Resmi

Untuk memastikan integritas teknis dan mencegah halusinasi spesifikasi, seluruh klaim teknologi, batas framework, dan kebijakan jaringan periklanan diverifikasi ke dokumentasi resmi:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Source Verification Matrix                                           │
├─────────┬──────────────────────────┬─────────────────────────────┬──────────────┬──────────────────────┤
│ Ref ID  │ Nama Sumber              │ URL / Domain                │ Tanggal Cek  │ Status Verifikasi    │
├─────────┼──────────────────────────┼─────────────────────────────┼──────────────┼──────────────────────┤
│ SRC-001 │ Laravel Release Policy   │ laravel.com/docs/releases   │ 30 Okt 2026  │ VERIFIED             │
│ SRC-002 │ Cloudflare Turnstile API │ developers.cloudflare.com   │ 30 Okt 2026  │ VERIFIED             │
│ SRC-003 │ Google Publisher Policy  │ support.google.com/adsense  │ 30 Okt 2026  │ VERIFIED (AdSense Ban)│
│ SRC-004 │ Adsterra Terms & Cond.   │ adsterra.com/terms/         │ 30 Okt 2026  │ VERIFIED (Restrictions│
│ SRC-005 │ WarnaHost Shared Hosting │ warnahost.com               │ 30 Okt 2026  │ NEEDS PRE-DEPLOY VER.│
│ SRC-006 │ PHP 8.3 Release Timeline │ php.net/supported-versions  │ 30 Okt 2026  │ VERIFIED             │
└─────────┴──────────────────────────┴─────────────────────────────┴──────────────┴──────────────────────┘
```

---

## 2. Rincian Temuan & Bukti Sumber

### SRC-001: Kebijakan Rilis & Dukungan Laravel
- **Sumber:** [laravel.com/docs/releases](https://laravel.com/docs/releases)
- **Fakta Tervalidasi:**
  - Laravel 11 mencapai *End of Life* pada **12 Maret 2026**.
  - Laravel 12 hanya menerima *Security Fixes* hingga **24 Februari 2027**.
  - Laravel 13 dirilis **17 Maret 2026**, membutuhkan PHP minimal 8.3, dan menerima dukungan keamanan penuh hingga **17 Maret 2028**.
- **Kesimpulan Dokumen:** Menetapkan Laravel 13 sebagai baseline proyek baru adalah keputusan yang benar dan aman.

### SRC-002: Cloudflare Turnstile Server-Side SiteVerify API
- **Sumber:** [developers.cloudflare.com/turnstile/get-started/server-side-validation/](https://developers.cloudflare.com/turnstile/get-started/server-side-validation/)
- **Fakta Tervalidasi:**
  - Endpoint validasi: `POST https://challenges.cloudflare.com/turnstile/v0/siteverify`.
  - Parameter wajib: `secret`, `response`. Parameter opsional: `remoteip`.
  - Respon JSON: `{"success": true, ...}`.
  - Kuota: Gratis tanpa batas request (unlimited verification).
- **Kesimpulan Dokumen:** Integrasi Turnstile di backend Laravel melalui `Http::asForm()->post(...)` 100% kompatibel dan tidak memerlukan package pihak ketiga yang berat.

### SRC-003: Kebijakan Google AdSense untuk Halaman Safelink & Countdown
- **Sumber:** [support.google.com/adsense/answer/1346295](https://support.google.com/adsense/answer/1346295) (Google Publisher Policies: Thin Content & Encouraging Clicks)
- **Fakta Tervalidasi:**
  - Google melarang unit iklan pada halaman perantara tanpa konten substansial (*thin content*).
  - Countdown timer yang memaksa pengguna menunggu dianggap memicu *accidental clicks* dan memicu pemblokiran akun permanen tanpa opsi banding.
- **Kesimpulan Dokumen:** Larangan mutlak Google AdSense pada safelink LokaTaut adalah keharusan hukum yang mutlak.

### SRC-004: Kebijakan Periklanan Adsterra
- **Sumber:** [adsterra.com/terms/](https://adsterra.com/terms/)
- **Fakta Tervalidasi:**
  - Mengizinkan penempatan iklan pada landing page & download link selama trafik nyata dan transparan.
  - **Dilarang Keras:** *Incentivized traffic* (memaksa klik untuk mengunduh), overlapping iklan di atas tombol download, auto-refresh script, dan konten malware/copyright infringement.
  - Minimum payout: \$5 untuk WebMoney, Paxum, USDT (Tercatat \$100+ untuk Wire Transfer & PayPal).
- **Kesimpulan Dokumen:** Adsterra dapat digunakan dengan catatan tata letak iklan harus memberi jarak aman (minimal 30px) dari tombol download agar tidak memicu deteksi Invalid Traffic (IVT).

### SRC-005: Spesifikasi Shared Hosting WarnaHost Starter Unlimited
- **Sumber:** [warnahost.com/cloud-hosting-indonesia/](https://warnahost.com)
- **Fakta Tervalidasi:**
  - Alokasi: 1 vCPU Core, 2 GB RAM, NVMe SSD Storage.
  - Fitur: cPanel, LiteSpeed Web Server, MultiPHP Selector (PHP 8.3 tersedia), Imunify360, Terminal/SSH cPanel.
  - **Catatan yang Perlu Diverifikasi Saat Pre-Deployment:** Akses command line Composer melalui cPanel Terminal dan batas interval minimum Cron Job (1 menit vs 5 menit).
- **Kesimpulan Dokumen:** Arsitektur modular monolith tanpa Redis dan penempatan source code di luar `public_html` kompatibel dengan hosting ini.

