# Source Verification Register — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | SOURCE-VERIFICATION-REGISTER |
| **Versi** | 1.2.2 |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Status** | ACTIVE (Ready for Product Owner Final Review) |

---

## 1. Register Verifikasi Sumber Resmi

Untuk memastikan integritas teknis dan mencegah halusinasi spesifikasi, seluruh klaim teknologi, batas framework, standar keamanan, dan kebijakan jaringan periklanan diverifikasi ke dokumentasi resmi menggunakan 3 klasifikasi bukti:
- `PROVIDER CLAIM VERIFIED`: Terverifikasi dari klaim spesifikasi resmi penyedia layanan/vendor.
- `ACCOUNT ENVIRONMENT VERIFIED`: Terverifikasi melalui pengujian riil pada akun aktif pengguna.
- `NOT VERIFIED`: Belum diuji atau belum disetujui pada akun aktual pengguna.

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Source Verification Matrix                                           │
├─────────┬──────────────────────────┬─────────────────────────────┬──────────────┬──────────────────────┤
│ Ref ID  │ Nama Sumber              │ URL / Dokumen Acuan         │ Tanggal Cek  │ Klasifikasi Status   │
├─────────┼──────────────────────────┼─────────────────────────────┼──────────────┼──────────────────────┤
│ SRC-001 │ Laravel Release Policy   │ laravel.com/docs/releases   │ 30 Okt 2026  │ PROVIDER CLAIM VERIF.│
│ SRC-002 │ Cloudflare Turnstile API │ developers.cloudflare.com   │ 30 Okt 2026  │ PROVIDER CLAIM VERIF.│
│ SRC-003 │ Google Publisher Policy  │ support.google.com/adsense  │ 30 Okt 2026  │ OFFICIAL POLICY      │
│ SRC-004 │ Adsterra Terms & Cond.   │ adsterra.com/terms/         │ 31 Okt 2026  │ CANDIDATE / PENDING  │
│ SRC-005 │ WarnaHost Shared Hosting │ warnahost.com               │ 31 Okt 2026  │ PARTIAL CLAIM VERIF. │
│ SRC-006 │ PHP 8.3 Release Timeline │ php.net/supported-versions  │ 30 Okt 2026  │ PROVIDER CLAIM VERIF.│
│ SRC-007 │ OWASP ASVS v4.0 Standard │ owasp.org/www-project-asvs  │ 31 Okt 2026  │ VERIFIED STANDARD    │
│ SRC-008 │ NIST SP 800-57 Part 1    │ csrc.nist.gov/publications  │ 31 Okt 2026  │ VERIFIED STANDARD    │
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

### SRC-003: Kebijakan Google AdSense untuk Halaman Safelink & Countdown (Official Policy)
- **Sumber:** [support.google.com/adsense/answer/1346295](https://support.google.com/adsense/answer/1346295) (Google Publisher Policies: Thin Content & Encouraging Clicks)
- **Fakta Tervalidasi:**
  - Google melarang unit iklan pada halaman perantara tanpa konten substansial (*thin content*).
  - Countdown timer yang memaksa pengguna menunggu di halaman safelink berpotensi memicu *accidental clicks* / Invalid Traffic.
  - Sanksi pelanggaran adalah pembatasan penayangan iklan (*ad serving limit*) hingga penonaktifan akun (*account suspension*).
- **Kesimpulan Dokumen:** Google AdSense tidak digunakan pada halaman safelink LokaTaut demi kepatuhan kebijakan platform penerbit Google.

### SRC-004: Kebijakan Periklanan Adsterra (Candidate Ad Network)
- **Sumber:** [adsterra.com/terms/](https://adsterra.com/terms/)
- **Fakta Tervalidasi:**
  - Mengizinkan penempatan iklan pada landing page & download link selama trafik nyata dan transparan.
  - **Dilarang Keras:** *Incentivized traffic* (memaksa klik untuk mengunduh), overlapping iklan di atas tombol download, auto-refresh script, click-hijacking, dan konten malware.
  - **Status Akun:** `NOT VERIFIED` (belum ada akun/domain LokaTaut yang didaftarkan ke Adsterra).
  - **Jarak 30px:** Bukan klausul resmi kontrak Adsterra, melainkan **Internal Engineering Safeguard** LokaTaut (mengacu WCAG 2.5.5) untuk mencegah sentuhan tidak sengaja pada ponsel.
- **Kesimpulan Dokumen:** Adsterra ditetapkan sebagai kandidat monetisasi utama. Format popunder ditangguhkan (*suspended*) pada MVP untuk menghindari risiko fraud IVT otomatis; tombol unduhan steril mutlak dari pemicu iklan.

### SRC-005: Spesifikasi Shared Hosting WarnaHost Starter Unlimited
- **Sumber:** [warnahost.com/cloud-hosting-indonesia/](https://warnahost.com)
- **Fakta Tervalidasi:**
  - Alokasi Paket Publik: 1 vCPU Core, 2 GB RAM fisik LVE, NVMe SSD Storage (`PROVIDER CLAIM VERIFIED`).
  - Fitur: cPanel, LiteSpeed Web Server, MultiPHP Selector (PHP 8.3 tersedia di marketing cPanel).
  - **Status Lingkungan Akun Aktual:** `NOT VERIFIED` (ketersediaan PHP 8.3 di SSH CLI, Composer v2, modul ekstensi, dan akses shell SSH belum diuji secara langsung pada akun pengguna aktif).
- **Kesimpulan Dokumen:** Wajib menjalankan *Pre-Flight Verification Commands* sebelum deployment, dan menetapkan *Fallback Pre-Built Vendor Release* sebagai mitigasi risiko kehabisan memori saat kompilasi.

### SRC-006: PHP 8.3 Release Timeline
- **Sumber:** [php.net/supported-versions.php](https://www.php.net/supported-versions.php)
- **Fakta Tervalidasi:** PHP 8.3 menerima dukungan perbaikan bug aktif dan dukungan keamanan resmi hingga akhir 2026+. Kompatibel penuh dengan framework Laravel 13.

### SRC-007: Standar OWASP Application Security Verification Standard (ASVS) v4.0
- **Sumber:** [owasp.org/www-project-application-security-verification-standard/](https://owasp.org/www-project-application-security-verification-standard/)
- **Fakta Tervalidasi:**
  - Section 2.1 (Credential Management): Mempersyaratkan kemampuan verifikasi kredensial lama sebelum mengizinkan rotasi kata sandi, serta panjang minimal password 12 karakter.
  - Section 3.2.4 (Session Token Storage): Mempersyaratkan penyimpanan token sesi pada persistent storage dalam bentuk cryptographic digest/hash (misal: SHA-256) untuk mencegah eksploitasi instan saat terjadi kebocoran basis data (*Defense-in-Depth*).

### SRC-008: Standar NIST SP 800-57 Part 1 Rev. 5 (Recommendation for Key Management)
- **Sumber:** [csrc.nist.gov/publications/detail/sp/800-57-part-1/rev-5/final](https://csrc.nist.gov/publications/detail/sp/800-57-part-1/rev-5/final)
- **Fakta Tervalidasi:** Prinsip Cryptographic Key Separation: Kunci yang digunakan untuk mengenkripsi data (misal: `APP_KEY` untuk enkripsi URL sesi dan cookies) tidak boleh digunakan bersamaan sebagai salt persisten untuk data analitik jangka panjang. Rotasi satu kunci tidak boleh merusak integritas kunci atau sistem lain.
