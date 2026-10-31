# UI/UX Design Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-08 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Senior UI/UX Architect |
| **Target Pembaca** | AI Coding Agent, Frontend Engineer, QA Specialist |
| **Tujuan** | Memberikan spesifikasi visual, tata letak, hierarki komponen, state interaksi, dan pedoman aksesibilitas untuk dua antarmuka: Public Safelink dan Administrator Dashboard. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan antarmuka fitur (FR) dan user personas |
| [DOC-04 — User Flow & System Flow](../02-analysis/04-user-system-flow.md) | Alur navigasi dan state transisi UI |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Batasan teknis frontend (Native CSS, Vanilla JS) |
| [DOC-10 — Monetization Specification](../05-security-monetization/10-monetization-specification.md) | Dimensi dan penempatan slot iklan yang tidak merusak UX |

---

## 1. Design Philosophy & Brand Personality

### 1.1 Filosofi Desain
1. **Trust-Building over Aggression:** Halaman safelink konvensional sering dicap sebagai situs "spam", "jebakan", atau "phishing" karena tombol unduhan palsu dan iklan pop-up yang liar. LokaTaut menerapkan filosofi **Clean Utility** — tampilan bersih, instruksi gamblang, tanpa tombol jebakan, dan bebas dari tipuan visual (*zero deceptive dark patterns*).
2. **Minimal & Neutral Branding:** Halaman publik safelink tidak menonjolkan branding "LokaTaut" yang berlebihan. Fokusnya adalah membantu pengunjung menjangkau unduhan mereka secara transparan.
3. **Efficiency-First Admin Dashboard:** Panel admin dirancang untuk kemudahan navigasi, pemuatan data kilat, dan manipulasi tautan cepat (< 30 detik).
4. **Mobile-First Responsiveness:** Lebih dari 70% trafik unduhan berasal dari perangkat mobile (Android/iOS). Seluruh elemen dioptimalkan untuk navigasi satu jempol (*thumb-friendly*).

### 1.2 Kepribadian Produk (Brand Personality)
- **Profesional & Andal:** Menggunakan tipografi sistem yang tajam, kontras tinggi, dan tata letak terstruktur rapi.
- **Transparan:** Menjelaskan secara jujur kepada pengunjung: "Tautan unduhan Anda sedang disiapkan".
- **Ringan:** Tidak ada animasi berat atau script pihak ketiga yang menghambat rendering konten utama.

---

## 2. Design System Tokens (Color, Typography, Spacing)

### 2.1 Palet Warna (Color Palette)

```
┌────────────────────────────────────────────────────────────────────────┐
│                        Color Token Registry                            │
├─────────────────┬───────────┬──────────────┬───────────────────────────┤
│ Token Name      │ Hex Code  │ Tailwind/CSS │ Penggunaan                │
├─────────────────┼───────────┼──────────────┼───────────────────────────┤
│ --color-primary │ #2563EB   │ Blue 600     │ CTA utama, link aktif     │
│ --primary-hover │ #1D4ED8   │ Blue 700     │ State hover button primary│
│ --primary-light │ #EFF6FF   │ Blue 50      │ Background aksen/badge    │
│ --color-success │ #10B981   │ Emerald 500  │ Status active, lolos cek  │
│ --color-warning │ #F59E0B   │ Amber 500    │ Link expired, peringatan  │
│ --color-danger  │ #EF4444   │ Red 500      │ Status error, delete action│
│ --bg-page       │ #F8FAFC   │ Slate 50     │ Latar belakang umum       │
│ --bg-card       │ #FFFFFF   │ White        │ Container card utama      │
│ --border-color  │ #E2E8F0   │ Slate 200    │ Border input & card       │
│ --text-primary  │ #0F172A   │ Slate 900    │ Teks judul & label utama  │
│ --text-secondary│ #475569   │ Slate 600    │ Teks deskripsi & bantuan  │
│ --text-muted    │ #94A3B8   │ Slate 400    │ Placeholder & caption     │
└─────────────────┴───────────┴──────────────┴───────────────────────────┘
```

> **Aksesibilitas Kontras (WCAG 2.1 AA):**  
> Rasio kontras teks `--text-primary` (#0F172A) terhadap background putih (#FFFFFF) adalah **15.8:1** (jauh melampaui standar minimal 4.5:1). Rasio kontras tombol `--color-primary` (#2563EB) dengan teks putih adalah **4.6:1** (lolos level AA).

### 2.2 Sistem Tipografi (Typography System)
Menggunakan **System Font Stack** untuk menghindari download webfont eksternal (~100-300 KB) demi kecepatan muat maksimal:
```css
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

| Tingkatan Teks | Ukuran (px / rem) | Berat (Weight) | Line Height | Penggunaan |
|---|---|---|---|---|
| Heading 1 (h1) | 28px / 1.75rem | 700 (Bold) | 1.25 | Judul halaman admin |
| Heading 2 (h2) | 22px / 1.375rem | 600 (SemiBold) | 1.3 | Judul kartu safelink / section |
| Heading 3 (h3) | 18px / 1.125rem | 600 (SemiBold) | 1.4 | Subjudul widget |
| Body Regular   | 15px / 0.9375rem | 400 (Regular) | 1.5 | Teks isi dan deskripsi |
| Body Medium    | 15px / 0.9375rem | 500 (Medium) | 1.5 | Label form & navigasi |
| Small Text     | 13px / 0.8125rem | 400 (Regular) | 1.4 | Metadata tabel, help text |
| Timer Display  | 48px / 3.0rem | 700 (Bold) | 1.0 | Angka hitung mundur countdown |

### 2.3 Skala Spacing & Grid
- **Base Unit:** 4px
- **Skala Padding/Margin:** 4px (`xs`), 8px (`sm`), 12px (`md`), 16px (`lg`), 24px (`xl`), 32px (`2xl`).
- **Container Max-Width:**
  - Halaman Public Safelink: **640px** (fokus di tengah, optimal untuk satu kolom vertikal).
  - Dashboard Admin: **1280px** (lebar maksimal dengan layout grid 2 atau 3 kolom).
- **Responsive Breakpoints:**
  - Mobile: `< 640px`
  - Tablet: `640px – 1023px`
  - Desktop: `≥ 1024px`

---

## 3. Komponen Antarmuka (Component Library)

### 3.1 Tombol (Button Specifications)
- **Tinggi Elemen (Touch Target):** Minimal **44px** pada mobile untuk kepatuhan navigasi jempol.
- **Border Radius:** `8px`.
- **States:**
  - `Default`: Background `--color-primary`, warna teks putih, bayangan halus.
  - `Hover`: Background `--primary-hover`, pointer kursor.
  - `Active/Pressed`: Sedikit scaling `transform: scale(0.98)`.
  - `Disabled`: Background `#CBD5E1`, teks `#64748B`, kursor `not-allowed`.
  - `Loading`: Teks digantikan dengan spinner CSS putih berputar.

### 3.2 Kotak Verifikasi Turnstile
- Container berukuran lebar 300px tinggi 65px yang diposisikan di tengah (*centered*).
- Menampilkan skeleton loader abu-abu halus sebelum script widget selesai dimuat dari Cloudflare.

### 3.3 Kartu Countdown Timer
- Lingkaran atau box modern dengan latar belakang `--primary-light` (#EFF6FF).
- Menampilkan teks status dinamis:
  - State 1: "Harap selesaikan verifikasi di atas" (sebelum Turnstile selesai).
  - State 2: "Menyiapkan tautan unduhan dalam..." + angka timer besar (saat countdown berjalan).
  - State 3: "Tautan unduhan siap!" (saat mencapai 0 detik).

---

## 4. Spesifikasi Halaman Publik (Public Safelink Wireframes)

### 4.1 Halaman Transisi Safelink Utama (`/s/{slug}`)

```
+-------------------------------------------------------------+
|                     [Ad Slot: Header 728x90]                |
+-------------------------------------------------------------+
|                                                             |
|              +-------------------------------+              |
|              |         LokaTaut Card         |              |
|              |        (Max-width 640px)      |              |
|              +-------------------------------+              |
|              | [Icon File Download]          |              |
|              | Judul File: Photoshop 2026    |              |
|              | Website Asal: softwaredown.id |              |
|              |                               |              |
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
|              |  [ Tombol: Menunggu Timer... ]|              |
|              |  (Disabled State - Abu-abu)   |              |
|              |                               |              |
|              | +---------------------------+ |              |
|              | |  [Ad Slot: Below Timer]   | |              |
|              | |      Banner 300x250       | |              |
|              | +---------------------------+ |              |
|              +-------------------------------+              |
|                                                             |
+-------------------------------------------------------------+
|                     [Ad Slot: Footer 728x90]                |
+-------------------------------------------------------------+
```

### 4.2 State Transisi Tombol Lanjutkan Unduhan
1. **Initial State (Turnstile Pending):**  
   Tombol bertuliskan: `Selesaikan Verifikasi di Atas` (Warna Abu-abu, disabled).
2. **Counting State (Turnstile Verified):**  
   Widget Turnstile disembunyikan via CSS. Box timer aktif berdetak.  
   Tombol bertuliskan: `Menyiapkan Tautan (10s...)` (Warna Abu-abu, disabled).
3. **Ready State (Countdown 0s):**  
   Box timer menampilkan ikon centang hijau.  
   Tombol berubah warna menjadi biru cerah (`--color-primary`), bertuliskan:  
   **`👉 Lanjutkan ke Unduhan (Download Sekarang)`** (Aktif, pointer).

### 4.3 Halaman Tautan Kedaluwarsa (`expired.blade.php`)
- Status HTTP: 410 Gone / 200 OK informatif.
- Visual: Ikon kalender / jam pasir berwarna amber.
- Teks: "Tautan ini telah kedaluwarsa atau tidak lagi tersedia."
- CTA: Tombol "Kembali ke Beranda Situs Asal".

### 4.4 Halaman Tautan Tidak Ditemukan (`invalid.blade.php`)
- Status HTTP: 404 Not Found.
- Visual: Ikon tanda seru merah minimalis.
- Teks: "Tautan unduhan tidak valid atau telah dihapus oleh pengelola."

---

## 5. Spesifikasi Halaman Admin Dashboard (Admin Wireframes)

### 5.1 Dashboard Utama (`/admin`)

```
+-------------------------------------------------------------------------+
| [LOGO] LokaTaut Admin         [Search...]       Admin (Dimas) [Logout]  |
+--------------+----------------------------------------------------------+
|  Sidebar     |  Ringkasan Metrik Hari Ini                               |
|              |  +--------------+--------------+--------------+          |
|  - Dashboard |  | Total Links  | Kunjungan    | Selesai (302)| Rasio %  |
|  - Links     |  | 142 Link     | 18.420 Klik  | 14.190 Seles | 77.03%   |
|  - Ad Slots  |  +--------------+--------------+--------------+          |
|  - Analytics |                                                          |
|  - Settings  |  Tren Kunjungan 7 Hari Terakhir (Bar Chart / SVG)        |
|  - Sec Logs  |  [|||||||||||||||||||||||||||||||||||||||||||||||||||]   |
|              |                                                          |
|              |  Tautan Terbaru Ditambahkan        [ + Buat Tautan Baru ]|
|              |  +--------------------+-------------+---------+--------+ |
|              |  | Slug               | Website     | Klik    | Status | |
|              |  +--------------------+-------------+---------+--------+ |
|              |  | /s/coreldraw-2026  | softwaredown| 1.250   | Active | |
|              |  | /s/subway-mod-apk  | apkzone.net | 4.200   | Active | |
|              |  +--------------------+-------------+---------+--------+ |
+--------------+----------------------------------------------------------+
```

### 5.2 Form Buat / Ubah Tautan (`/admin/links/create`)
- **Card Input Vertikal:**
  1. *URL Tujuan Asli (Destination URL)*: Input teks panjang dengan validasi protokol `https://`.
  2. *Judul Tautan*: Label deskriptif untuk inventaris admin.
  3. *Slug Kustom (Opsional)*: Input dengan prefix `lokataut.com/s/` + indikator ketersediaan slug real-time. Jika kosong, sistem otomatis generate slug acak.
  4. *Website Sumber*: Dropdown / autocomplete daftar domain milik admin.
  5. *Durasi Hitung Mundur (Detik)*: Default 10 detik.
  6. *Tanggal Kedaluwarsa (Opsional)*: Input date-time picker.
  7. *Status*: Radio button (Active / Inactive).
- **Aksi:** Tombol "Simpan Tautan" (Primary) dan "Batal" (Ghost).

### 5.3 Manajemen Slot Iklan (`/admin/ads`)
- Tabel konfigurasi slot per posisi: Header, Above Timer, Below Timer, Footer.
- Fitur live toggle switch (On/Off) tanpa reload halaman menggunakan AJAX.
- Editor kode script iklan dengan syntax container aman.

---

## 6. Pedoman Aksesibilitas & Responsivitas Mobile

1. **Touch Targets:** Seluruh link, tombol, dan checkbox memiliki area sentuh minimal 44x44 pixel.
2. **Keyboard Navigation:** Setiap interaksi form dapat dicapai via tombol `Tab` dan dieksekusi dengan `Enter` / `Space`. Focus indicator warna biru (`outline: 2px solid #2563EB`) tidak boleh dinonaktifkan.
3. **No Horizontal Scroll:** Tidak ada elemen yang memicu horizontal scrollbar pada layar terkecil (320px).
4. **Ad Isolation:** Container iklan wajib memiliki min-height (contoh: `min-height: 250px` untuk slot 300x250) agar halaman tidak mengalami pergeseran tata letak mendadak saat banner iklan selesai dimuat.

