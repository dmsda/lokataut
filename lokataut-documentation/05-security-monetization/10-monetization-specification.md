# Monetization & Advertising Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-10 |
| **Versi** | 1.0 |
| **Status** | DRAFT |
| **Terakhir Diperbarui** | 2026-10-30 |
| **Penulis** | Monetization Strategist & Compliance Specialist |
| **Target Pembaca** | Product Owner, AI Coding Agent, Frontend Engineer |
| **Tujuan** | Merumuskan strategi monetisasi trafik unduhan, arsitektur penempatan slot iklan, integrasi ad network yang mematuhi kebijakan, serta mitigasi invalid traffic. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-01 — Product Vision & Strategy](../01-product/01-product-vision.md) | Model bisnis kepemilikan tunggal (Single Owner Monetization) |
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan fungsional manajemen slot iklan (FR-ADS-*) |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Skema tabel `ad_slots` |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Komponen Blade `<x-ad-slot>` dan injeksi script |
| [DOC-08 — UI/UX Specification](../04-design/08-ui-ux-specification.md) | Penempatan visual dan dimensi slot iklan |

---

## 1. Executive Summary & Policy Compliance Alert

> ### ⚠️ PERINGATAN KEBIJAKAN UTAMA: GOOGLE ADSENSE DILARANG KERAS
> Berdasarkan **Google AdSense Program Policies** dan **Google Publisher Policies**, penempatan unit iklan AdSense pada halaman perantara unduhan (*safelink*), pemendek tautan (*URL shortener*), atau halaman ber-timer (*countdown redirect*) adalah **PELANGGARAN BERAT**.
> 
> **Penyebab Pelanggaran:**
> 1. **Thin / Low-Value Content:** Halaman safelink tidak memiliki konten editorial/artikel asli yang substansial.
> 2. **Forced Ad Viewing & Accidental Clicks:** Memaksa pengunjung menunggu countdown di halaman penuh iklan meningkatkan risiko klik tidak disengaja (*Invalid Traffic / IVT*).
> 
> **Konsekuensi Pelanggaran:** Penangguhan iklan (*ad serving limitation*) seketika hingga **pemblokiran akun Google AdSense permanen tanpa hak banding**.
> 
> **Kebijakan LokaTaut:** Platform LokaTaut **DILARANG MENGGUNAKAN GOOGLE ADSENSE** pada rute `/s/{slug}`. Monetisasi safelink dialihkan 100% ke jaringan periklanan resmi yang secara spesifik mendukung dan mengizinkan format safelink/redirect.

---

## 2. Jaringan Iklan Rekomendasi (Verified Ad Networks)

Berdasarkan riset pasar dan kompatibilitas kebijakan trafik download, berikut adalah jaringan iklan pihak ketiga yang didukung:

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                      Perbandingan Jaringan Iklan Safelink                               │
├────────────────┬──────────────┬─────────────┬───────────────────────────┬───────────────┤
│ Ad Network     │ Approval     │ Min. Payout │ Format Iklan Terbaik      │ Rekomendasi   │
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────┤
│ **Adsterra**   │ Instan (<1h) │ $5          │ Social Bar, Banner 300x250│ ⭐ PRIMER     │
│                │              │             │ Popunder / Direct Link    │ (Paling Pas)  │
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────┤
│ **Monetag**    │ Instan       │ $5          │ Smartlink, In-Page Push,  │ Alternatif #1 │
│ (PropellerAds) │              │             │ Vignette Banner           │ (Trafik Global)│
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────┤
│ **HilltopAds** │ Cepat        │ $50         │ Popunder, Video Slider,   │ Alternatif #2 │
│                │              │             │ Display Banner            │ (Volume Besar)│
└────────────────┴──────────────┴─────────────┴───────────────────────────┴───────────────┘
```

### Mengapa Adsterra Dipilih sebagai Jaringan Primer?
1. **Dukungan Penuh untuk Safelink & Download Traffic:** Adsterra secara resmi memperbolehkan penempatan iklan pada halaman transisi unduhan.
2. **Ambang Pembayaran Sangat Terjangkau:** Minimum penarikan dana hanya **$5** (tersedia opsi Paxum, WebMoney, Crypto USDT, PayPal).
3. **Format Iklan Berkinerja Tinggi:**
   - **Social Bar:** Widget interaktif modern mirip notifikasi chat/OS dengan rasio klik (CTR) tinggi tanpa mengganggu elemen countdown.
   - **Display Banners (728x90 & 300x250):** Banner visual standar yang dapat disematkan rapi di atas dan di bawah timer.
   - **Anti-AdBlock Solution:** Adsterra menyediakan kode script yang resisten terhadap pemblokir iklan standar.

---

## 3. Arsitektur Slot Iklan (Ad Slot Architecture)

LokaTaut menyediakan 4 posisi slot standar pada antarmuka publik safelink:

```
┌─────────────────────────────────────────────────────────────┐
│                 POSISI SLOT IKLAN SAFELINK                  │
├─────────────────────┬───────────────────┬───────────────────┤
│ Posisi Slot         │ Dimensi Standar   │ Target Perangkat  │
├─────────────────────┼───────────────────┼───────────────────┤
│ `header`            │ 728x90 / 468x60   │ Desktop & Tablet  │
│ `above_timer`       │ 300x250 / 336x280 │ Mobile & Desktop  │
│ `below_timer`       │ 300x250 / 336x280 │ Mobile & Desktop  │
│ `footer`            │ 728x90 / 320x50   │ Semua Perangkat   │
└─────────────────────┴───────────────────┴───────────────────┘
```

### 3.1 Komponen Render Blade (`<x-public.ad-slot>`)
Kode iklan disimpan dalam bentuk teks mentah pada database (`ad_slots.ad_code`). Komponen Blade merendernya secara terisolasi:

```html
@props(['position'])

@php
    $slot = \App\Models\AdSlot::where('position', $position)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->first();
@endphp

@if($slot && !empty($slot->ad_code))
    <div class="ad-slot-wrapper ad-slot-{{ $position }}" 
         data-slot-id="{{ $slot->id }}"
         style="min-height: {{ $position === 'header' || $position === 'footer' ? '90px' : '250px' }}; display: flex; justify-content: center; align-items: center; margin: 12px 0;">
        {!! $slot->ad_code !!}
    </div>
@endif
```

> **Catatan Pencegahan Layout Shift (CLS):**  
> Properti `min-height` diatur secara inline pada wrapper div untuk memastikan browser telah mengalokasikan ruang sebelum script iklan selesai diunduh. Hal ini mencegah pergeseran tombol countdown yang dapat merusak pengalaman pengguna.

---

## 4. Strategi Pemuatan Iklan (Ad Loading Strategy)

1. **Pemuatan Asinkron (Asynchronous Execution):** Seluruh tag script iklan Adsterra disajikan dengan atribut `async` atau dimuat secara dinamis setelah dokumen HTML utama selesai diparsing (`DOMContentLoaded`).
2. **Isolasi Kegagalan (Failure Resilience):** Kegagalan server jaringan iklan (misal: koneksi iklan timeout atau diblokir adblocker) **TIDAK BOLEH** menghentikan hitung mundur timer atau menghalangi tombol unduhan. Logika countdown dan verifikasi Turnstile berjalan independen dari script iklan.
3. **Respon Ramah Pengguna Terhadap AdBlocker:** Jika pengunjung menggunakan ekstensi pemblokir iklan, LokaTaut tidak akan menampilkan script anti-adblock agresif yang mengunci layar secara total. Pengunjung tetap diizinkan menyelesaikan hitung mundur secara wajar demi menjaga integritas reputasi situs utama.

---

## 5. Batasan Pelaporan & Pemisahan Metrik Analitik

Untuk menjaga kejujuran dan akurasi sistem, LokaTaut **memisahkan secara tegas** antara metrik analitik internal dengan pelaporan pendapatan eksternal:

```
┌─────────────────────────────────────────────────────────────┐
│              Pemisahan Metrik Internal vs Eksternal         │
├──────────────────────────────┬──────────────────────────────┤
│ Metrik Internal LokaTaut     │ Laporan Jaringan Iklan Luar  │
│ (Tercatat di Basis Data)     │ (Dilihat di Dashboard Adsterra)│
├──────────────────────────────┼──────────────────────────────┤
│ • Total Tayangan (Impressions)│ • Total Impresi Iklan Valid  │
│ • Selesai Verifikasi Turnstile│ • Klik Iklan Terverifikasi   │
│ • Redirect Selesai (302)     │ • eCPM (Pendapatan per 1.000)│
│ • Completion Rate (%)        │ • Saldo Pendapatan Riil ($)  │
│ • Distribusi Negara & Device │ • Fill Rate & Potongan IVT   │
└──────────────────────────────┴──────────────────────────────┘
```

> **DISCLAIMER TEKNIS:**  
> Sistem LokaTaut **TIDAK MENGKLAIM** dapat mengetahui saldo pendapatan riil secara otomatis tanpa integrasi API resmi dari masing-masing ad network. Dashboard LokaTaut fokus pada **performa konversi alur link**, sedangkan konversi moneter ($) dipantau langsung oleh pemilik melalui portal resmi Adsterra.

---

## 6. Pencegahan Trafik Tidak Valid (Invalid Traffic / IVT Prevention)

Untuk memastikan akun Adsterra pemilik tetap bereputasi tinggi (*high trust score*) dan menghasilkan eCPM optimal:

1. **Dilarang Memanipulasi Referrer:** Sistem **TIDAK AKAN** merekayasa header HTTP Referrer seolah-olah trafik berasal dari pencarian organik Google (`google.com`). Header referrer disajikan apa adanya sesuai asal klik pengunjung dari website pemilik.
2. **Dilarang Melakukan Click-Jacking / Forced Click:** Tombol countdown diletakkan pada jarak aman (minimal 20px) dari banner iklan terdekat untuk menghindari klik iklan yang tidak disengaja.
3. **Penyaringan Bot via Cloudflare Turnstile:** Bot otomatis, crawler agresif, dan script scraper disaring sebelum timer berjalan. Dengan demikian, impresi iklan hanya disajikan kepada pengunjung manusia asli.

