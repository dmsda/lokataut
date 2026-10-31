# Product Vision & Strategy — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-01 |
| **Versi** | 1.2 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Principal Product Manager & System Analyst |
| **Target Pembaca** | Product Owner, AI Coding Agent, Technical Lead |
| **Tujuan** | Menetapkan visi produk, model bisnis, positioning pasar, use cases, metrik keberhasilan, dan batas strategis platform LokaTaut. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](./02-prd.md) | Dekomposisi visi ke kebutuhan fungsional (FR) termasuk FR-AUTH-004 |
| [DOC-03 — Scope & Requirements Specification](./03-scope-requirements.md) | Batasan fitur MVP vs Post-MVP dan batasan Out-of-Scope |
| [DOC-10 — Monetization Specification](../05-security-monetization/10-monetization-specification.md) | Strategi dan model periklanan banner pendukung monetisasi |
| [DOC-15 — Architecture Decision Records](../09-decisions/15-architecture-decisions.md) | Rekam keputusan strategis (ADR-001 s/d ADR-014) |

---

## 1. Product Overview

**LokaTaut** adalah platform perantara dan pengelola tautan unduhan privat (*Private Link Management & Safelink Platform*) yang dikembangkan secara custom untuk kebutuhan pribadi seorang pemilik tunggal (single owner/administrator). Platform ini berfungsi mengelola, melindungi, dan memonetisasi tautan unduhan dari website-website milik sendiri (seperti file software aplikasi, APK Android, template/aset kreatif digital, dokumen, dsb.).

Setiap tautan unduhan yang diklik oleh pengunjung diarahkan terlebih dahulu melewati halaman transisi safelink yang memuat elemen informasi unduhan, verifikasi bot Cloudflare Turnstile, hitung mundur (countdown timer 10 detik), serta penempatan unit iklan dari jaringan periklanan resmi. Begitu seluruh tahapan selesai, pengunjung secara otomatis dialihkan ke URL unduhan final via HTTP 302 Found. Seluruh pendapatan iklan 100% menjadi milik pemilik platform.

### LokaTaut BUKAN:
- ❌ Public URL shortener network (seperti Bitly atau TinyURL publik).
- ❌ Publisher marketplace / platform periklanan publik.
- ❌ Platform bagi hasil pendapatan (*revenue sharing*) atau referral/affiliate.
- ❌ Layanan SaaS multi-tenant dengan registrasi pengguna publik.

---

## 2. Product Vision & Mission

### 2.1 Visi Produk
> Menjadi infrastruktur perantara unduhan mandiri (*self-hosted safelink engine*) yang aman, berkinerja tinggi, berbiaya operasional mendekati nol, dan memberikan kontrol penuh 100% atas monetisasi trafik unduhan pribadi tanpa ketergantungan pada script/plugin berbayar pihak ketiga.

### 2.2 Misi Produk
1. **Kepemilikan Penuh:** Memiliki kendali mutlak atas kode sumber, basis data, dan data analitik trafik.
2. **Monetisasi Tanpa Bagi Hasil:** Menghilangkan potongan komisi pihak ketiga sehingga 100% pendapatan iklan dinikmati pemilik.
3. **Efisiensi Anggaran Maksimal:** Berjalan stabil di lingkungan shared hosting ekonomis (WarnaHost Starter Unlimited) dengan biaya operasional < Rp 100.000 / bulan.
4. **Keamanan Tanpa Kompromi:** Melindungi URL tujuan unduhan dari bot scraper dan manipulasi JavaScript client-side.
5. **Pengalaman Pengguna Bersih:** Menghindari pola visual menjebak (*dark patterns*), tombol download palsu, dan script iklan agresif yang merusak reputasi website sumber.

---

## 3. Problem Statement & Value Proposition

### 3.1 Masalah yang Dihadapi Pengelola Web Unduhan
1. **Biaya & Risiko Script Pihak Ketiga:** Plugin safelink berbayar (seperti WP Safelink) memiliki biaya lisensi tahunan, rawan ditinggalkan pengembangnya (*abandoned*), dan sering memiliki celah keamanan (*backdoor*).
2. **Kecurangan Jaringan Pemendek Tautan Publik:** Layanan shortlink publik sering kali memotong rasio klik valid (*click shave*), menampilkan iklan pop-up berbahaya, dan membagi sebagian besar pendapatan untuk mereka sendiri.
3. **Bahaya Pemblokiran Google AdSense:** Penempatan AdSense pada halaman safelink/countdown adalah pelanggaran keras terhadap *Google Publisher Policies* (masalah *thin content* dan *forced clicks*), yang berakibat penutupan akun secara permanen tanpa hak banding.
4. **Scraping Tautan Langsung:** Tautan unduhan langsung mudah di-scrape oleh bot tanpa mendatangkan kunjungan halaman yang dapat dimonetisasi.

### 3.2 Value Proposition LokaTaut

| Dimensi Nilai | Solusi Publik / Plugin Pihak Ketiga | LokaTaut Private Platform |
|---|---|---|
| **Bagi Hasil Revenue** | Dipotong 30% – 60% oleh jaringan publik | **100% Milik Pemilik Platform** |
| **Kepemilikan Kode** | Tertutup, terenkripsi ionCube, lisensi berkala | **Open & 100% Milik Sendiri** |
| **Risiko Keamanan** | URL tujuan sering terekspos di JavaScript | **Terenkripsi AES-256, Validasi Server-Side** |
| **Pengalaman Pengguna** | Penuh jebakan popunder agresif & malware | **Bersih, Transparan, Bebas Dark Patterns** |
| **Kepatuhan Iklan** | Sering memicu ban AdSense | **Menggunakan Ad Network Sesuai Kebijakan** |
| **Biaya Operasional** | Biaya lisensi berulang + server terpisah | **Cukup Shared Hosting Rp 79.000/bln** |

---

## 4. Target Audience & Primary Use Cases

### 4.1 Target Pengguna
1. **Primary User (Administrator Tunggal):** Dimas Aditya (Pemilik platform yang mengelola tautan unduhan dari website-website miliknya).
2. **Secondary User (Visitor / Pengunjung):** Pengguna internet yang mengklik link unduhan file dari website milik pemilik.

### 4.2 Primary Use Cases

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Primary Use Cases Summary                       │
├────────┬──────────────────────────┬─────────────┬──────────────────────┤
│ UC ID  │ Nama Use Case            │ Aktor Utama │ Prioritas            │
├────────┼──────────────────────────┼─────────────┼──────────────────────┤
│ UC-001 │ Buat Shortlink Unduhan   │ Admin       │ P0 (Critical)        │
│ UC-002 │ Akses Halaman Safelink   │ Pengunjung  │ P0 (Critical)        │
│ UC-003 │ Verifikasi Bot Turnstile │ Pengunjung  │ P0 (Critical)        │
│ UC-004 │ Validasi & Redirect 302  │ Pengunjung  │ P0 (Critical)        │
│ UC-005 │ Pantau Analitik Trafik   │ Admin       │ P1 (High)            │
│ UC-006 │ Kelola Penempatan Iklan  │ Admin       │ P1 (High)            │
│ UC-007 │ Kelola Status Tautan     │ Admin       │ P0 (Critical)        │
│ UC-008 │ Kelola Profil & Password │ Admin       │ P1 (High)            │
└────────┴──────────────────────────┴─────────────┴──────────────────────┘
```

---

## 5. Model Bisnis & Prinsip Monetisasi

- **Model:** *Single Owner Monetization via Ad Networks*.
- **Mitra Jaringan Iklan Kandidat:** **Adsterra** (*Candidate - Proposed*), dengan alternatif Monetag dan PropellerAds (seluruhnya berstatus kandidat sampai akun dan domain diverifikasi).
- **Larangan Google AdSense:** Sesuai kebijakan resmi Google Publisher Policies (*thin content* dan *forced interaction*), AdSense **DILARANG KERAS** dipasang pada halaman safelink/countdown.
- **Format Iklan Prioritas:** Banner display standar terlabel jelas (728x90, 300x250 native banner) dengan penempatan terpisah minimal 30px dari elemen aksi.
- **Penangguhan Format Agresif:** Format Popunder ditangguhkan (*suspended*) sampai ada verifikasi kepatuhan tertulis dari jaringan iklan; tombol aksi unduhan (*Proceed button*) steril mutlak dari pemicu iklan.
- **Pemisahan Pelaporan:** Sistem membedakan secara tegas antara metrik analitik internal (tayangan, verifikasi, selesai) dengan pelaporan konversi pendapatan riil (\$) yang dipantau langsung di portal jaringan iklan.

---

## 6. Prinsip Produk & Batasan Strategis

1. **Simplicity over Complexity:** Menghindari arsitektur berlebih (microservices, Docker di produksi, Redis wajib). Menggunakan **Laravel 13.x**, PHP 8.3+, Blade, Native CSS, Vanilla JS, dan MySQL 8.0+.
2. **Security by Design:** Menyembunyikan destination URL selama proses safelink, validasi countdown di server-side, token satu kali pakai (*atomic update*), dan penyamaran IP via keyed HMAC.
3. **No Overpromising:** Mengakui keterbatasan teknis secara jujur (misal: redirect 302 mengirimkan URL tujuan ke browser pada akhir alur; analitik IP hash adalah pendekatan estimasi).
4. **Cost Efficiency:** Berjalan optimal pada alokasi 1 vCPU dan 2 GB RAM shared hosting WarnaHost Starter Unlimited.

