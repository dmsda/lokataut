# UI/UX Design Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-08 |
| **Versi** | 1.2.2 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
| **Penulis** | Senior UI/UX Architect & Accessibility Specialist |
| **Target Pembaca** | AI Coding Agent, Frontend Engineer, QA Specialist |
| **Tujuan** | Memberikan spesifikasi visual, tata letak, hierarki komponen, pencegahan dark pattern periklanan, dan pedoman aksesibilitas untuk Public Safelink dan Administrator Dashboard. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan antarmuka fitur (FR) dan user personas |
| [DOC-04 — User Flow & System Flow](../02-analysis/04-user-system-flow.md) | Alur navigasi dan state transisi UI |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Batasan teknis frontend (Native CSS, Vanilla JS) |
| [DOC-10 — Monetization Specification](../05-security-monetization/10-monetization-specification.md) | Dimensi dan penempatan slot iklan yang tidak merusak UX |

---

## 1. Design Philosophy & Anti-Deceptive Ad Patterns

### 1.1 Prinsip Desain
1. **Pemberantasan Dark Patterns (Anti-Clickbait):** Halaman perantara download sering kali menipu pengguna dengan menampilkan banner iklan yang menyerupai tombol download sistem. LokaTaut **mewajibkan** pemberian label visual yang jelas di atas setiap slot iklan: `"Advertisement / Iklan Sponsor"`.
2. **Jarak Aman Elemen Interaktif (Safe Spacing):** Tombol resmi unduhan LokaTaut (`#proceed-btn`) wajib diberi jarak aman minimal **30 pixel** dari batas slot iklan terdekat. Hal ini menjadi standar rekayasa internal (*internal engineering standard*) untuk mencegah salah klik (*accidental click*) pada layar sentuh ponsel yang dapat memicu penalti Invalid Traffic (IVT) dari jaringan iklan.
3. **Sterilisasi Tombol Navigasi:** Tombol aksi unduhan (`#proceed-btn`) adalah elemen submit formulir murni yang terisolasi 100% dari skrip atau pemicu iklan pihak ketiga. Dilarang keras mengaitkan klik tombol dengan popunder atau direct link.
4. **Clean Utility & Minimal Branding:** Tampilan safelink difokuskan pada kejelasan informasi file dan progress hitung mundur tanpa visual spam.
5. **Mobile-First Responsiveness:** Touch target untuk seluruh tombol dan interaksi input minimal **44x44 pixel** untuk kenyamanan navigasi satu jempol pada layar smartphone.

---

## 2. Design Tokens (Color, Typography, Spacing)

### 2.1 Palet Warna (Color Palette)

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Color Token Registry                            │
├─────────────────┬───────────┬──────────────┬───────────────────────────┤
│ Token Name      │ Hex Code  │ Tailwind/CSS │ Penggunaan                │
├─────────────────┼───────────┼──────────────┼───────────────────────────┤
│ --color-primary │ #2563EB   │ Blue 600     │ CTA utama, link aktif     │
│ --primary-hover │ #1D4ED8   │ Blue 700     │ State hover button primary│
│ --primary-light │ #EFF6FF   │ Blue 50      │ Background aksen timer    │
│ --color-success │ #10B981   │ Emerald 500  │ Status active, centang ok │
│ --color-warning │ #F59E0B   │ Amber 500    │ Link expired, peringatan  │
│ --color-danger  │ #EF4444   │ Red 500      │ Status error, delete action│
│ --bg-page       │ #F8FAFC   │ Slate 50     │ Latar belakang umum       │
│ --bg-card       │ #FFFFFF   │ White        │ Container card utama      │
│ --border-color  │ #E2E8F0   │ Slate 200    │ Border input & card       │
│ --text-primary  │ #0F172A   │ Slate 900    │ Teks judul & label utama  │
│ --text-secondary│ #475569   │ Slate 600    │ Teks deskripsi & bantuan  │
│ --text-muted    │ #94A3B8   │ Slate 400    │ Label "Advertisement"     │
└─────────────────┴───────────┴──────────────┴───────────────────────────┘
```

> **Aksesibilitas Kontras (WCAG 2.1 AA):**  
> Rasio kontras teks `--text-primary` (#0F172A) terhadap background putih adalah **15.8:1**. Rasio kontras tombol primary dengan teks putih adalah **4.6:1** (lolos level AA).

### 2.2 Sistem Tipografi
Menggunakan **System Font Stack** murni untuk memangkas waktu muat:
```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

---

## 3. Spesifikasi Wireframe Halaman Publik Safelink (`/s/{slug}`)

```
+-------------------------------------------------------------+
|                      [ Iklan Sponsor ]                      |
|                   [Ad Slot: Header 728x90]                  |
+-------------------------------------------------------------+
|                                                             |
|              +-------------------------------+              |
|              |         LokaTaut Card         |              |
|              |        (Max-width 640px)      |              |
|              +-------------------------------+              |
|              | [Icon File Download]          |              |
|              | Judul File: Photoshop 2026    |              |
|              | Sumber: softwaredown.id       |              |
|              |                               |              |
|              | <small>Advertisement</small>  |              |
|              | +---------------------------+ |              |
|              | |  [Ad Slot: Above Timer]   | |              |
|              | |      Banner 300x250       | |              |
|              | +---------------------------+ |              |
|              |                               |              |
|              |  [ Cloudflare Turnstile ]     |              |
|              |  [ ] Verifikasi Pengunjung    |              |
|              |                               |              |
|              |  +-------------------------+  |              |
|              |  |   Countdown Box         |  |              |
|              |  |   Angka Besar: 10       |  |              |
|              |  |   "Detik tersisa..."    |  |              |
|              |  +-------------------------+  |              |
|              |                               |              |
|              |  (Jarak Aman Minimal 30px)    |              |
|              |  [ Tombol: Menunggu Timer... ]|              |
|              |  (Disabled State - Abu-abu)   |              |
|              |                               |              |
|              | <small>Advertisement</small>  |              |
|              | +---------------------------+ |              |
|              | |  [Ad Slot: Below Timer]   | |              |
|              | |      Banner 300x250       | |              |
|              | +---------------------------+ |              |
|              +-------------------------------+              |
|                                                             |
+-------------------------------------------------------------+
|                      [ Iklan Sponsor ]                      |
|                   [Ad Slot: Footer 728x90]                  |
+-------------------------------------------------------------+
```

### 3.1 State Transisi Tombol "Lanjutkan Unduhan"
1. **State 1 (Turnstile Pending):** Teks: `Selesaikan Verifikasi di Atas` (Background `#E2E8F0`, kursor `not-allowed`).
2. **State 2 (Countdown Berjalan):** Teks: `Menyiapkan Tautan Unduhan (10s...)` (Background `#E2E8F0`, kursor `not-allowed`).
3. **State 3 (Countdown 0s & Ready):** Teks: `👉 Lanjutkan ke Unduhan (Download Sekarang)` (Background `--color-primary` #2563EB, kursor `pointer`, ikon panah download).

---

## 4. Spesifikasi Halaman Administrator Dashboard (`/admin`)

- **Top Navbar:** Logo LokaTaut Admin, status koneksi, profil admin (Dimas), dan tombol Logout.
- **Sidebar Kiri (Desktop: 240px Fixed, Mobile: Drawer):**
  - Dashboard
  - Manajemen Tautan (Daftar, Buat Baru)
  - Manajemen Slot Iklan
  - Analitik Trafik & Agregasi
  - Pengaturan Sistem
  - Profil & Keamanan Admin (Ubah Password)
  - Log Keamanan (Audit Insiden)
- **Konten Utama:** Kartu metrik (Total Links, Tayangan Hari Ini, Unduhan Selesai, Rasio %), grafik tren 7 hari, dan tabel tautan terbaru.

---

### 4.1 Spesifikasi Antarmuka Profil & Pergantian Password Admin (`/admin/profile`)

Antarmuka terproteksi yang memungkinkan administrator tunggal memperbarui profil dan merotasi kata sandi akun secara mandiri:

```
+-------------------------------------------------------------+
| Profil & Keamanan Akun Administrator                        |
| Kelola informasi profil dan rotasi kata sandi akun          |
+-------------------------------------------------------------+
|                                                             |
| +---------------------------------------------------------+ |
| | Kartu 1: Informasi Profil Akun                          | |
| |                                                         | |
| | Nama Admin:     [ Dimas Aditya                 ]        | |
| | Alamat Email:   [ dimas@lokataut.com           ]        | |
| |                                                         | |
| | [ Tombol: Simpan Profil ]                               | |
| +---------------------------------------------------------+ |
|                                                             |
| +---------------------------------------------------------+ |
| | Kartu 2: Pergantian Kata Sandi                          | |
| |                                                         | |
| | Password Saat Ini:       [ ************               ] | |
| | Password Baru:           [ ************               ] | |
| |                          (Min. 12 karakter, kombinasi   | |
| |                           huruf besar, kecil, angka,    | |
| |                           dan simbol)                   | |
| | Konfirmasi Password Baru:[ ************               ] | |
| |                                                         | |
| | [ Tombol Primary: Perbarui Kata Sandi ]                 | |
| +---------------------------------------------------------+ |
|                                                             |
+-------------------------------------------------------------+
```

- **Validasi Visual:**
  - Password strength meter (indikator warna: merah, kuning, hijau) berdasarkan panjang dan variasi karakter.
  - Pesan error inline jika `current_password` tidak valid atau konfirmasi password baru tidak cocok.
  - Flash notification hijau: *"Kata sandi berhasil diperbarui. Sesi login telah dirotasi."*

