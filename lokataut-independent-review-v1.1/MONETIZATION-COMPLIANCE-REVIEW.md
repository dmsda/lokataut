# Tinjauan Kepatuhan Kebijakan Monetisasi & Jaringan Iklan LokaTaut v1.1

> **Status Kepatuhan:** `CONDITIONALLY COMPLIANT (ACCOUNT VERIFICATION REQUIRED)`  
> **Tanggal Audit:** 31 Oktober 2026  
> **Auditor:** Digital Monetization Specialist & Compliance Auditor  
> **Jaringan Iklan yang Ditinjau:** Adsterra (Primary Proposed), Monetag (Secondary), PropellerAds (Tertiary), Google AdSense (Prohibited Baseline)

---

## 1. Executive Summary Monetisasi

Model bisnis LokaTaut adalah **Private Single-Owner Monetization**, di mana seluruh pendapatan iklan dari halaman safelink menjadi milik penuh pemilik platform.

Audit independen terhadap dokumen `05-security-monetization/10-monetization-specification.md` mengonfirmasi bahwa arah kebijakan monetisasi LokaTaut telah mengambil keputusan yang sangat tepat dengan **MELARANG KERAS PENGGUNAAN GOOGLE ADSENSE** pada halaman safelink. Google AdSense memiliki regulasi ketat (*Publisher Policies on Thin Content and Forced Interaction*) yang secara eksplisit melarang penempatan unit iklan pada halaman perantara dengan hitung mundur (*forced waiting*).

Namun demikian, audit menemukan bahwa integrasi jaringan iklan alternatif yang direncanakan (**Adsterra**) masih berstatus **`CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT VERIFICATION)`** dan memiliki risiko kepatuhan serius terkait implementasi format **Popunder / Direct Link (Temuan REV-MON-001 - P1)** jika tidak diatur dengan batasan ketat pada antarmuka safelink.

---

## 2. Analisis Kebijakan Jaringan Iklan Resmi

### 2.1 Tolok Ukur Larangan Google AdSense (Benchmark)
- **Aturan AdSense:** Kebijakan Konten Bernilai Rendah (*Thin Content*) & Manipulasi Navigasi (*Unnatural Attention to Ads*).
- **Alasan Larangan:** Halaman safelink yang hanya menampilkan ringkasan tautan dan timer hitung mundur 10 detik dikategorikan sebagai *Low-value / Navigation intermediary*. Akun penayang yang memasang AdSense pada safelink dipastikan terkena sanksi pembatasan iklan (*Ad Serving Limit*) hingga penutupan akun permanen.
- **Kesimpulan Dokumen v1.1:** Penetapan larangan AdSense pada `DOC-10` dan `AGENTS.md` **100% TEPAT DAN COMPLIANT**.

### 2.2 Kebijakan Adsterra Publisher Terms & Batasan IVT
Adsterra merupakan jaringan periklanan independen yang ramah terhadap website download, tools, dan utilitas digital. Meskipun demikian, Adsterra memiliki sistem deteksi **Invalid Traffic (IVT)** dan pedoman anti-fraud yang ketat:

| Kebijakan Adsterra | Ketentuan Resmi | Status di LokaTaut v1.1 |
|---|---|:---:|
| **Forced / Incentivized Clicks** | Dilarang memaksa atau mendorong pengunjung mengklik iklan untuk mendapatkan file. | **COMPLIANT** (Tautan unduhan tidak mensyaratkan klik iklan). |
| **Misleading Layout** | Dilarang menempatkan iklan menyerupai tombol download palsu (*fake download buttons*). | **COMPLIANT** (Label `"Advertisement / Iklan Sponsor"` wajib terpasang). |
| **Safe Margin Distance** | Unit iklan banner wajib berjarak minimal 30px dari elemen tombol aksi navigasi. | **COMPLIANT** (Margin 30px telah didefinisikan di `DOC-08` dan `DOC-10`). |
| **Click-Hijacking pada Tombol** | Dilarang memasang pemicu popunder/direct-link pada tombol aksi utama situs (*Proceed button*). | **GAP (REV-MON-001 - P1)** (Belum dilarang secara kaku di dokumentasi). |
| **Direct Link Overlays** | Dilarang membuat overlay transparan di atas seluruh layar yang menangkap klik sembarangan. | **COMPLIANT** (Tidak ada rancangan transparent click overlay). |

---

## 3. Evaluasi Format Iklan yang Diusulkan

### 3.1 Format Banner Display (Native Banner 300x250, 728x90)
- **Tingkat Risiko:** **RENDAH (Aman)**
- **Kepatuhan:** Penempatan di area atas judul (*Above the Fold*) dan di bawah kotak hitung mundur (*Below the Counter*) memenuhi standar tata letak responsif, dengan catatan diberi label pembeda yang jelas agar tidak mengecoh pengunjung.
- **Rekomendasi Teknis:** Gunakan kontainer CSS tetap (*fixed aspect ratio wrapper*) untuk mencegah *Cumulative Layout Shift (CLS)* saat script banner terlambat memuat iklan.

### 3.2 Format Social Bar / Push Notification Widget
- **Tingkat Risiko:** **SEDANG**
- **Kepatuhan:** Social Bar Adsterra berupa floating widget kecil di pojok layar.
- **Ketentuan Wajib:** Widget tidak boleh menutupi tombol countdown `#proceed-btn` atau widget captcha Turnstile pada resolusi layar mobile (viewport lebar 320px–375px).

### 3.3 Format Popunder / Direct Link (Fokus Temuan REV-MON-001)
- **Tingkat Risiko:** **TINGGI (Kritis terhadap IVT)**
- **Masalah Potensial:** Banyak pengembang safelink pemula mengaitkan script popunder langsung pada event listener tombol *Proceed* (`#proceed-btn.addEventListener('click', ...)`). Ketika pengunjung menekan tombol unduh, tab baru terbuka menampilkan iklan popunder sementara tab lama membuka file unduhan.
- **Bahaya Sanksi:**
  1. Detektor bot Adsterra mendeteksi 100% klik popunder berasal dari koordinat tombol aksi yang sama persis (*unnatural click distribution*).
  2. Adsterra menandai akun sebagai *Incentivized / Hijacked Traffic*.
  3. Konsekuensi: Pembekuan saldo pendapatan (*hold payout*), rasio eCPM anjlok ke \$0.01, dan risiko domain `lokataut.com` di-*blacklist* permanen.

---

## 4. Pedoman Wajib Implementasi Antarmuka Bebas Pelanggaran

Untuk memastikan akun Adsterra / Monetag tetap bereputasi tinggi (*clean compliance record*), aturan-aturan baku berikut **WAJIB dimasukkan ke dalam revisi `DOC-10`**:

1. **Sterilisasi Tombol Navigasi (`#proceed-btn`):**
   Tombol navigasi countdown wajib murni berupa elemen HTML `<button type="submit">` di dalam `<form method="POST" action="/sl/{token}/proceed">`. Tidak boleh ada script iklan pihak ketiga yang disematkan atau menempel pada elemen tombol ini.
2. **Pemicu Popunder Terisolasi:**
   Jika script popunder aktif, script tersebut hanya boleh diinjeksikan secara independen oleh SDK resmi Adsterra dan hanya terpicu oleh klik pertama pada area netral halaman (*background body*), bukan pada area elemen interaktif formulir.
3. **Pemberian Label Transparan:**
   Setiap slot banner wajib dibungkus dalam kontainer semantic dengan label teks abu-abu berukuran minimal 11px:
   ```html
   <div class="ad-container ad-banner-300x250">
       <span class="ad-disclaimer">Advertisement / Iklan Sponsor</span>
       {!! $adSlot->rendered_code !!}
   </div>
   ```

---

## 5. Checklist Verifikasi Akun Nyata (Sebelum Go-Live)

Karena saat ini jaringan iklan masih berstatus usulan (*candidate*), Product Owner wajib menjalankan tahapan verifikasi akun sebelum sistem memasuki Phase 4 (Monetisasi):

- [ ] **Pendaftaran Akun Publisher:** Mendaftarkan domain `lokataut.com` ke dashboard publisher Adsterra / Monetag.
- [ ] **Verifikasi Kepemilikan Domain:** Memasang meta tag atau file verifikasi DNS/HTML yang diminta jaringan iklan.
- [ ] **Persetujuan Domain Resmi:** Menunggu konfirmasi status domain menjadi `Active / Approved` di dashboard jaringan iklan.
- [ ] **Generasi Placement ID:** Mengambil Script Tag resmi untuk Banner 300x250, Social Bar, dan Popunder resmi dari dashboard.
- [ ] **Pengujian Sandbox Tanpa Klik:** Memastikan iklan tayang di staging/local tanpa melakukan klik sendiri (*no self-clicks*) untuk mencegah deteksi fraud awal.

---
*Tinjauan kepatuhan monetisasi ini melindungi platform LokaTaut dari penalti jaringan periklanan dan menjamin keberlanjutan pendapatan jangka panjang.*

