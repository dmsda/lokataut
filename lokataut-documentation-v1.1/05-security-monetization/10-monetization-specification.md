# Monetization & Advertising Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-10 |
| **Versi** | 1.1 |
| **Status** | UNDER REVIEW |
| **Terakhir Diperbarui** | 30 Oktober 2026 |
| **Penulis** | Monetization Strategist & Compliance Specialist |
| **Target Pembaca** | Product Owner, AI Coding Agent, Frontend Engineer |
| **Tujuan** | Merumuskan strategi monetisasi trafik unduhan, batasan kebijakan resmi jaringan periklanan, mitigasi invalid traffic, serta arsitektur slot iklan agnostik. |

---

## Dependensi Dokumen

| Dokumen | Relasi |
|---|---|
| [DOC-01 — Product Vision & Strategy](../01-product/01-product-vision.md) | Model bisnis kepemilikan tunggal (Single Owner Monetization) |
| [DOC-02 — Product Requirements Document](../01-product/02-prd.md) | Kebutuhan fungsional manajemen slot iklan (FR-ADS-*) |
| [DOC-06 — Database Design](../03-architecture/06-database-design.md) | Skema tabel `ad_slots` |
| [DOC-07 — Technical Specification](../03-architecture/07-technical-specification.md) | Komponen Blade `<x-ad-slot>` dan injeksi script |
| [DOC-08 — UI/UX Specification](../04-design/08-ui-ux-specification.md) | Penempatan visual, label sponsor, dan jarak aman 30px |
| [SOURCE-VERIFICATION-REGISTER.md](../SOURCE-VERIFICATION-REGISTER.md) | Bukti verifikasi ketentuan resmi Adsterra & Google Policy |

---

## 1. Peringatan Kebijakan: Google AdSense Dilarang Keras

> ### ⚠️ LARANGAN MUTLAK: JANGAN GUNAKAN GOOGLE ADSENSE PADA SAFELINK
> Berdasarkan **Google Publisher Policies**, penempatan unit iklan Google AdSense pada halaman safelink, shortlink redirect, atau halaman dengan countdown timer adalah **PELANGGARAN KERAS**:
> 1. **Thin / Low-Value Content:** Halaman safelink tidak memiliki konten artikel editorial orisinal yang memadai.
> 2. **Forced Waiting & Accidental Clicks:** Memaksa pengunjung menunggu countdown di halaman sarat iklan secara artifisial meningkatkan rasio klik tidak disengaja (*Invalid Traffic / IVT*).
> 
> **Sanksi Google:** Pembatasan penayangan iklan (*ad limit*) seketika hingga **penutupan akun Google AdSense permanen tanpa opsi banding**.
> 
> **Kebijakan LokaTaut:** Platform LokaTaut secara tegas **MELARANG** integrasi script Google AdSense pada seluruh rute `/s/{slug}`.

---

## 2. Jaringan Periklanan Kandidat (Candidate Ad Networks)

Monetisasi safelink dialihkan ke jaringan periklanan pihak ketiga yang secara eksplisit memperbolehkan trafik perantara unduhan:

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                           Matriks Jaringan Iklan Kandidat                                       │
├────────────────┬──────────────┬─────────────┬───────────────────────────┬───────────────────────┤
│ Ad Network     │ Status       │ Min. Payout │ Format Iklan Direncankan  │ Kebijakan Kunci       │
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────────────┤
│ **Adsterra**   │ PROPOSED     │ $5 (Crypto/ │ Social Bar, Display Banner│ Melarang incentivized │
│                │ (Kandidat #1)│ Paxum/WMZ)  │ 300x250, Banner 728x90    │ traffic & dark patterns│
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────────────┤
│ **Monetag**    │ PROPOSED     │ $5          │ Smartlink, In-Page Push,  │ Efektif untuk trafik  │
│ (PropellerAds) │ (Kandidat #2)│             │ Vignette Banner           │ internasional Tier 2/3│
└────────────────┴──────────────┴─────────────┴───────────────────────────┴───────────────────────┘
```

### 2.1 Verifikasi Kebijakan Resmi Adsterra (Terms & Conditions)
Sesuai hasil audit kepatuhan (Ref: `SRC-004`):
1. **Incentivized Traffic Dilarang:** Dilarang membuat teks instruksi yang memaksa pengunjung mengklik iklan untuk membuka link download (*"Klik iklan ini untuk mengunduh"*).
2. **Pencegahan Accidental Click:** Banner iklan dilarang tumpang-tindih (*overlap*) atau diletakkan terlalu rapat dengan tombol download. LokaTaut menetapkan jarak minimal **30 pixel** antara slot iklan dan tombol countdown.
3. **Kualitas Trafik:** Trafik bot, perayap otomatis, dan manipulasi klik akan diblokir oleh sistem GeoEdge Adsterra yang dapat berakibat penahanan dana. Cloudflare Turnstile pada LokaTaut berfungsi menyaring bot sebelum tayangan iklan dimuat.

---

## 3. Arsitektur Slot Iklan Agnostik (Ad-Network Agnostic)

Backend LokaTaut tidak mengunci sistem pada satu vendor iklan tertentu (*vendor lock-in*). Kode iklan disimpan dalam bentuk teks mentah pada database (`ad_slots.ad_code`) dan disajikan melalui komponen Blade terstandarisasi:

```html
@props(['position'])

@php
    $slot = \App\Models\AdSlot::where('position', $position)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->first();
@endphp

@if($slot && !empty($slot->ad_code))
    <div class="ad-slot-container" style="margin: 16px 0; text-align: center;">
        <span style="font-size: 11px; color: #94A3B8; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
            Advertisement / Iklan Sponsor
        </span>
        <div class="ad-slot-wrapper ad-slot-{{ $position }}" 
             style="min-height: {{ in_array($position, ['header', 'footer']) ? '90px' : '250px' }}; display: flex; justify-content: center; align-items: center;">
            {!! $slot->ad_code !!}
        </div>
    </div>
@endif
```

> **Mitigasi Cumulative Layout Shift (CLS):**  
> Container slot iklan wajib memiliki properti `min-height` (90px untuk banner horizontal, 250px untuk banner box) agar browser telah mengalokasikan ruang visual sebelum script iklan selesai diunduh.

---

## 4. Pemisahan Pelaporan Metrik Internal vs Eksternal

LokaTaut menegaskan pemisahan antara data analitik lokal dan metrik finansial:
- **Tercatat di Basis Data LokaTaut:** Tayangan halaman safelink (*impressions*), kelulusan verifikasi Turnstile (*verified*), dan pengalihan sukses (*completions*).
- **Tercatat di Portal Ad Network Eksternal:** Impresi iklan valid menurut filter ad network, klik iklan yang disetujui, nilai eCPM, dan saldo pendapatan riil (\$).

Sistem LokaTaut tidak menjanjikan pelaporan pendapatan otomatis tanpa integrasi API resmi reporting dari masing-masing ad network.

