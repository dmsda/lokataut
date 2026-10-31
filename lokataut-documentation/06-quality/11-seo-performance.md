# SEO & Web Performance Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-11 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | SEO Strategist & Web Performance Specialist |
| **Target Pembaca** | AI Coding Agent, Fullstack Developer, DevOps |
| **Tujuan** | Menetapkan strategi teknis SEO (pengendalian indeksasi bot mesin pencari) dan metrik performa web (Core Web Vitals & optimasi resource shared hosting). |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan non-fungsional performa (NFR-PERF-*) |
| [DOC-05 — Software Architecture](../03-architecture/05-system-architecture.md) | Konfigurasi layer caching dan Cloudflare CDN |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Implementasi asset CSS/JS murni tanpa framework berat |
| [DOC-08 — UI/UX Specification](../04-design/08-ui-ux-specification.md) | Pencegahan pergeseran tata letak (Cumulative Layout Shift) |

---

## 1. Strategi Indeksasi & Technical SEO

> ### 🛑 PRINSIP SEO UTAMA: HALAMAN SAFELINK DILARANG DIINDEKS MESIN PENCARI
> Halaman transisi safelink (`/s/{slug}`) adalah halaman utilitas perantara yang bersifat **Thin Content** (tanpa artikel jurnalistik atau konten editorial panjang). Mengizinkan Google mengindeks ribuan URL shortlink otomatis akan:
> 1. Membuang kuota perayapan (*Crawl Budget*) domain utama `lokataut.com`.
> 2. Memicu penalti algoritma Google atas kategori "Doorway Pages" atau "Thin Affiliate Content".
> 3. Merusak otoritas SEO dari website sumber milik pemilik.
> 
> **Kebijakan Indeksasi LokaTaut:** Seluruh rute `/s/{slug}` dan `/admin/*` **WAJIB DIKUNCI DARI INDEKSASI MESIN PENCARI**.

### 1.1 Penerapan Tag Meta Robots & HTTP Header
Setiap respon HTTP dari rute `/s/{slug}` wajib menyertakan tag instruksi bot:

1. **HTML Meta Tag:**
```html
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
```

2. **HTTP Header Response (via Middleware `SecurityHeadersMiddleware`):**
```http
X-Robots-Tag: noindex, nofollow, noarchive, nosnippet
```

### 1.2 Konfigurasi `robots.txt`
File `public/robots.txt` disederhanakan untuk memblokir perayap dari jalur safelink dan panel admin:

```txt
# robots.txt for lokataut.com
User-agent: *
Disallow: /s/
Disallow: /admin/
Disallow: /login
Disallow: /logout

# Izinkan perayap hanya ke halaman beranda root (jika ada landing page statis)
Allow: /$
```

### 1.3 Sitemap Strategy
LokaTaut **TIDAK MEMERLUKAN SITEMAP XML** untuk link safelink. Tautan unduhan adalah ranah privat yang disebarkan dari website sumber milik pemilik, bukan untuk dicari pengunjung melalui Google Search.

---

## 2. Target Core Web Vitals (Real-World Performance)

Untuk memastikan pengalaman pengunjung yang mulus dan mencegah pengunjung menutup tab sebelum iklan termuat (*bounce rate* rendah), sistem menargetkan ambang batas **Google Core Web Vitals**:

```
┌────────────────────────────────────────────────────────────────────────┐
│                      Core Web Vitals Thresholds                        │
├──────────────────────────┬───────────┬──────────────┬──────────────────┤
│ Metrik Kinerja           │ Simbol    │ Ambang Baik  │ Metrik LokaTaut  │
├──────────────────────────┼───────────┼──────────────┼──────────────────┤
│ Largest Contentful Paint │ LCP       │ ≤ 2.5 detik  │ **< 1.2 detik**  │
│ Interaction to Next Paint│ INP       │ ≤ 200 ms     │ **< 50 ms**      │
│ Cumulative Layout Shift  │ CLS       │ ≤ 0.10       │ **< 0.02**       │
│ Time to First Byte       │ TTFB      │ ≤ 800 ms     │ **< 250 ms**     │
│ First Contentful Paint   │ FCP       │ ≤ 1.8 detik  │ **< 0.8 detik**  │
└──────────────────────────┴───────────┴──────────────┴──────────────────┘
```

---

## 3. Strategi Optimasi Performa (Performance Engineering)

### 3.1 Eliminasi Ketergantungan Eksternal (Asset Zero-Bloat)
1. **No External Fonts:** Menghindari pemanggilan Google Fonts (`fonts.googleapis.com`). Menggunakan System Font Stack murni menghemat 1 request DNS, 1 koneksi TLS, dan ~150 KB transfer data.
2. **No Heavy JS Frameworks:** Tidak menyertakan jQuery (85 KB), Bootstrap JS (60 KB), atau React/Vue runtime (130+ KB). Semua interaktivitas ditangani Vanilla JS berukuran < 10 KB.
3. **No Heavy CSS Frameworks:** Menolak framework CSS monolitik pada antarmuka publik safelink. File `public/css/safelink.css` murni memiliki ukuran < 8 KB.

### 3.2 Penanganan Cumulative Layout Shift (CLS)
Salah satu penyebab skor CLS buruk pada halaman beriklan adalah kemunculan banner yang mendorong elemen di bawahnya secara tiba-tiba.
- **Solusi LokaTaut:** Seluruh slot iklan dibungkus dalam container `<div class="ad-slot-wrapper">` dengan properti `min-height` tetap (misal: 250px untuk banner 300x250). Ruang kosong telah disiapkan sejak detik pertama, sehingga saat script iklan Adsterra selesai dimuat, posisi tombol countdown dan Turnstile tidak bergeser sama sekali.

### 3.3 Akselerasi Edge & HTTP/3 via Cloudflare
- **Proxy Status Active (Orange Cloud):** Seluruh aset statis (CSS, JS, logo) di-cache di ratusan data center Cloudflare terdekat dari pengunjung.
- **Brotli Compression:** Kompresi otomatis teks HTML, CSS, dan JS hingga 20% lebih padat dibanding Gzip standar.
- **HTTP/3 (QUIC Protocol):** Pengiriman paket data tanpa *head-of-line blocking* pada jaringan mobile seluler 4G/3G yang tidak stabil.

### 3.4 Optimasi Basis Data & LiteSpeed Server
- **LiteSpeed LSCache Compatibility:** Header `Cache-Control: no-store, no-cache, must-revalidate` dikirimkan secara eksplisit pada halaman `/s/{slug}` agar web server LiteSpeed tidak menyimpan cache HTML safelink dinamis antar pengunjung berbeda.
- **Query Index Optimization:** Setiap kueri pencarian link menggunakan `slug` didukung index unik MySQL b-tree dengan kompleksitas O(log N). Waktu eksekusi query kueri link berada di bawah **5 milidetik**.
- **Prepared Execution:** Memanfaatkan PDO persistent connection jika didukung konfigurasi hosting untuk memangkas waktu jabat tangan (*handshake*) basis data.

---

## 4. Metodologi Pengujian & Validasi Lighthouse

1. Pengujian performa dilakukan menggunakan **Google Chrome Lighthouse (Mobile Simulation: Moto G4, Slow 4G)**.
2. Target Skor Lighthouse pada Lingkungan Shared Hosting:
   - **Performance:** ≥ 90 / 100 (di luar script jaringan iklan pihak ketiga yang berada di luar kendali server).
   - **Accessibility:** 100 / 100.
   - **Best Practices:** 100 / 100.
   - **SEO:** 100 / 100 (memvalidasi keberadaan tag noindex, viewport, dan semantic elements).

