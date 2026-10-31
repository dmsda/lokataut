# SEO & Web Performance Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-11 |
| **Versi** | 1.2.1 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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

## 1. Kebijakan Indeksasi Mesin Pencari (Multi-Layer Noindex Policy)

> ### 🛑 KEBIJAKAN SEO UTAMA: HALAMAN SAFELINK WAJIB BEBAS DARI INDEKSASI GOOGLE
> Halaman transisi safelink (`/s/{slug}`) adalah halaman utilitas perantara yang bersifat **Thin Content** (tanpa artikel orisinal atau nilai editorial). Mengizinkan Google merayapi dan mengindeks ribuan shortlink otomatis akan:
> 1. Membuang kuota perayapan (*Crawl Budget*) domain utama `lokataut.com`.
> 2. Memicu penalti algoritma Google atas kategori "Doorway Pages" atau "Thin Affiliate Content".
> 3. Merusak reputasi domain di mata mesin pencari.
> 
> **Kebijakan LokaTaut:** Seluruh rute `/s/{slug}` dan `/admin/*` **WAJIB DIKUNCI DARI INDEKSASI MESIN PENCARI MENGGUNAKAN 3 LAPIS PERLINDUNGAN**.

### 1.1 Tiga Lapis Perlindungan Noindex

1. **Lapis 1: HTTP Response Header (Paling Efektif)**  
   Middleware `SecurityHeadersMiddleware` menyuntikkan header berikut pada setiap request ke `/s/{slug}` dan `/admin/*`:
   ```http
   X-Robots-Tag: noindex, nofollow, noarchive, nosnippet
   ```
2. **Lapis 2: HTML Meta Tag**  
   Template Blade `resources/views/layouts/public.blade.php` menyertakan:
   ```html
   <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
   ```
3. **Lapis 3: File `robots.txt`**  
   Diletakkan pada `public/robots.txt`:
   ```txt
   User-agent: *
   Disallow: /s/
   Disallow: /admin/
   Disallow: /login
   Disallow: /logout
   Allow: /$
   ```

---

## 2. Target Core Web Vitals (Real-World Performance)

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

## 3. Strategi Optimasi Performa Shared Hosting

1. **Zero External Font Download:** Menggunakan *System Font Stack* murni, menghemat 1 request DNS dan ~150 KB transfer data.
2. **Zero Heavy JS Runtimes:** Tidak menggunakan jQuery, React, atau Vue pada safelink. Seluruh logika countdown ditangani Vanilla JS berukuran < 10 KB.
3. **Pencegahan CLS pada Slot Iklan:** Memberikan properti inline `min-height: 250px` pada container banner iklan agar tata letak halaman tidak melompat ketika banner selesai dimuat.
4. **Cloudflare Edge Proxy (Awan Jingga):** Kompresi Brotli aktif, caching aset statis (CSS/JS) di ratusan edge node, dan mitigasi serangan DDoS di level DNS.
5. **Database Indexing Kilat:** Seluruh pencarian slug menggunakan indeks B-Tree MySQL dengan waktu eksekusi < 5 milidetik.
6. **Cache Inode Pruning Management:** Penggunaan driver file cache untuk rate limiting berpotensi menghasilkan ribuan file lock di `storage/framework/cache/data`. Command terjadwal mingguan `lokataut:prune-cache` membersihkan file usang untuk menjaga batas kuota inode cPanel WarnaHost.
7. **LiteSpeed Server Cache Bypass:** Penyuntikan header respon `X-LiteSpeed-Cache-Control: no-cache` dan directive `.htaccess` memastikan LiteSpeed Web Server tidak meng-cache HTML halaman safelink yang memuat token sesi unik.

