# Monetization & Advertising Specification — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DOC-10 |
| **Versi** | 1.2.1 |
| **Status** | UNDER REVIEW (Ready for Product Owner Final Review) |
| **Terakhir Diperbarui** | 31 Oktober 2026 |
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

## 1. Klasifikasi Kebijakan: Google AdSense Tidak Digunakan

> ### ⚠️ KEBIJAKAN RESMI: JANGAN GUNAKAN GOOGLE ADSENSE PADA SAFELINK (Official Policy)
> Berdasarkan pedoman resmi **Google Publisher Policies**, penempatan unit iklan Google AdSense pada halaman safelink, shortlink perantara, atau halaman dengan countdown timer bertentangan dengan kebijakan kualitas inventaris:
> 1. **Thin / Low-Value Content:** Halaman safelink tidak memiliki konten editorial yang substansial.
> 2. **Forced Waiting & Encouraging Clicks:** Memaksa pengunjung menunggu countdown di halaman sarat iklan berpotensi memicu klik tidak disengaja (*accidental clicks* / *Invalid Traffic*).
> 
> **Potensi Sanksi Platform:** Kebijakan Google menetapkan risiko pembatasan penayangan iklan (*ad serving limit*) hingga penonaktifan akun penayang (*account suspension*).
> 
> **Kebijakan Internal LokaTaut:** Platform LokaTaut secara tegas menetapkan **TIDAK MENGGUNAKAN** Google AdSense pada seluruh rute `/s/{slug}`.

---

## 2. Jaringan Periklanan Kandidat & Regulasi Kepatuhan

Monetisasi safelink dialihkan ke jaringan periklanan pihak ketiga yang mengizinkan trafik perantara unduhan utilitas digital, dengan status **`CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT & DOMAIN VERIFICATION)`**:

> **Catatan Status Verifikasi:**  
> Adsterra belum menyetujui model bisnis atau domain LokaTaut secara spesifik (status akun aktual adalah `NOT VERIFIED`, menunggu pendaftaran domain riil oleh Product Owner pasca deployment). Model integrasi slot iklan dirancang 100% agnostik agar fleksibel terhadap ad network manapun.

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                           Matriks Jaringan Iklan Kandidat                                       │
├────────────────┬──────────────┬─────────────┬───────────────────────────┬───────────────────────┤
│ Ad Network     │ Status       │ Min. Payout │ Format Iklan Direncankan  │ Kebijakan Kunci       │
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────────────┤
│ **Adsterra**   │ CANDIDATE    │ $5 (Crypto/ │ Display Banner 728x90,    │ Melarang forced click,│
│                │ (Kandidat #1)│ Paxum/WMZ)  │ Native Banner 300x250     │ misleading download   │
│                │              │             │ (Popunder DITANGGUHKAN)   │ button & button hijack│
├────────────────┼──────────────┼─────────────┼───────────────────────────┼───────────────────────┤
│ **Monetag**    │ CANDIDATE    │ $5          │ In-Page Push,             │ Alternatif resmi      │
│ (PropellerAds) │ (Kandidat #2)│             │ Display Banner            │ untuk trafik global   │
└────────────────┴──────────────┴─────────────┴───────────────────────────┴───────────────────────┘
```

### 2.1 Kebijakan Anti-Invalid Traffic (IVT) & Larangan Keras Adsterra
1. **Sterilisasi Tombol Navigasi (`#proceed-btn`):** Dilarang keras mengaitkan script iklan, popunder, direct-link, atau event bubbling pada tombol aksi unduhan. Tombol ini wajib steril 100% dari kode pihak ketiga. Pelanggaran aturan ini menyebabkan deteksi fraud instan oleh sistem anti-cheat Adsterra dan pemblokiran domain/saldo.
2. **Larangan Penipuan Visual (No Fake Buttons):** Dilarang menempatkan iklan atau banner yang meniru tombol "Download", "Play", atau "Next" bawaan website.
3. **Pemberian Label Transparan:** Setiap slot banner wajib memiliki teks label disclaimer yang jelas: `"Advertisement / Iklan Sponsor"`.
4. **Jarak Aman Elemen Interaktif (Internal Engineering Safeguard $\ge 30\text{px}$):** Banner iklan dan tombol interaktif formulir dipisahkan jarak vertikal minimal 30px sebagai standar rekayasa UI internal (mengacu pada pedoman WCAG 2.5.5 Touch Target) untuk meminimalkan sentuhan tidak sengaja pada ponsel (*accidental mobile taps*). Ini merupakan kebijakan rekayasa internal LokaTaut, bukan klausul kontrak baku jaringan iklan.
5. **Penangguhan Format Popunder (Internal Engineering Safeguard):** Format popunder ditangguhkan (*suspended*) pada fase MVP untuk meminimalkan risiko deteksi Invalid Traffic (IVT) otomatis dan menjaga kenyamanan pengunjung. Evaluasi format popunder hanya dapat dipertimbangkan pasca-MVP setelah akun resmi terverifikasi dan mendapat izin eksplisit Product Owner.

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

---

## 5. Checklist Verifikasi Akun Nyata (Sebelum Go-Live Monetisasi)

Sebelum kode tag iklan aktif diinjeksikan pada sistem produksi:
1. [ ] **Registrasi Akun Publisher:** Product Owner mendaftarkan domain `lokataut.com` ke portal publisher Adsterra / Monetag.
2. [ ] **Verifikasi Kepemilikan Domain:** Melengkapi instruksi verifikasi DNS record atau meta tag verifikasi.
3. [ ] **Approval Status Domain:** Menunggu status domain resmi berubah menjadi `Approved / Active`.
4. [ ] **Pengambilan Tag Banner Resmi:** Mengambil script tag resmi khusus Banner 728x90 dan 300x250 (tanpa mengaktifkan popunder).
5. [ ] **Uji Tampilan Non-Klik:** Memastikan banner tampil rapi dengan label disclaimer tanpa melakukan self-clicks untuk menjaga reputasi awal domain.

