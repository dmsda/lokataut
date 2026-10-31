# Register Keputusan Resmi Product Owner (PO Decision Register) — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | PO-DEC-REG-01 |
| **Versi Dokumen** | 1.0.2 (Pemutakhiran Pemetaan Kanonikal & Pemisahan Otorisasi) |
| **Status Dokumen** | **ACTION REQUIRED (Menunggu Keputusan Dimas Aditya atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md)) |
| **Tanggal Terbit** | 31 Oktober 2026 |
| **Penyusun** | Tim Peninjau Teknis Multidisiplin LokaTaut |
| **Otoritas Tunggal** | **Product Owner (Dimas Aditya)** |

---

## 1. Status Terkini Keputusan Produk (16 DEC Kanonikal)

Sesuai dokumen kanonikal [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) dan konfirmasi Product Owner pada 31 Oktober 2026, status dari 16 keputusan produk adalah sebagai berikut:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                   Open Decisions Summary Matrix                                        │
├─────────┬──────────────────────────────────┬──────────────┬──────────────┬─────────────┬───────────────┤
│ DEC ID  │ Topik Keputusan Kanonikal        │ Status       │ Tanggal      │ Otoritas PO │ Bukti Otoritas│
├─────────┼──────────────────────────────────┼──────────────┼──────────────┼─────────────┼───────────────┤
│ DEC-001 │ Migrasi Framework ke Laravel 13  │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-002 │ Status Kandidat Jaringan Iklan   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-003 │ Verifikasi Runtime WarnaHost     │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-004 │ Durasi Default Countdown (10s)   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-005 │ Turnstile Placement (Sebelum)    │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-006 │ Retensi Data Mentah (90 Hari)    │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-007 │ Penangguhan Format Popunder      │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-008 │ Fitur Ganti Password (FR-AUTH-004│ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-009 │ Hashed Token Sesi (token_hash)   │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-010 │ Fallback Vendor Deploy (DOC-13)  │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-011 │ Turnstile Emergency Bypass Switch│ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-012 │ Approval Formal ADR-001 s/d 017  │ UNDER REVIEW │ -            │ Pending PO  │ Butuh TTD PO  │
│ DEC-013 │ Kebijakan Soft & Hard Delete     │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-014 │ Mandatori ANALYTICS_SALT Mandiri │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-015 │ Dedicated Aggregate Key NOT NULL │ APPROVED     │ 2026-10-31   │ Dimas A.    │ Explicit PO   │
│ DEC-016 │ Snapshot Identitas Tautan Kunj.  │ UNDER REVIEW │ -            │ Pending PO  │ Butuh TTD PO  │
└─────────┴──────────────────────────────────┴──────────────┴──────────────┴─────────────┴───────────────┘
```

**Rekapitulasi:**
- **14 DEC Berstatus `APPROVED`:** Memiliki keabsahan legalitas dan arsitektur produk yang sah.
- **2 DEC Berstatus `UNDER REVIEW`:** Menunggu keputusan mandiri Product Owner pada lembar ini.

---

## 2. Formulir Keputusan Eksekutif: DEC-012 (Tata Kelola ADR)

### Ringkasan Eksekutif Ruang Lingkup DEC-012:
DEC-012 **hanya mengatur kerangka tata kelola, aturan kewenangan AI, dan prosedur pengesahan ADR**.
- AI Agent memiliki wewenang nol untuk menyetujui ADR.
- Menyetujui DEC-012 **TIDAK secara otomatis menyetujui ke-17 ADR**.
- Setiap ADR dari ADR-001 s/d ADR-017 wajib ditinjau dan disetujui satu per satu secara granular.

### Rekomendasi Tim Peninjau:
Setujui (APPROVE) kerangka tata kelola DEC-012 untuk meresmikan aturan main persetujuan ADR.

---

### Lembar Isian Keputusan Product Owner untuk DEC-012:

Silakan tentukan pilihan Anda untuk **DEC-012**:

- [ ] **SETUJU PENUH (APPROVE):** Menyetujui kerangka tata kelola dan prosedur pengesahan ADR DEC-012. (Persetujuan individual untuk 17 ADR dilakukan secara terpisah pada Bagian 4).
- [ ] **SETUJU BERSYARAT (APPROVE WITH CONDITIONS):** Menyetujui dengan klausul tambahan.
- [ ] **TOLAK / PERLU REVISI (REJECT / REVISE):** Meminta perubahan aturan tata kelola.

**Catatan Khusus Product Owner untuk DEC-012:**
```
(Tuliskan catatan atau instruksi tambahan di sini)


```

---

## 3. Formulir Keputusan Eksekutif: DEC-016 (Snapshot Identitas Tautan)

### Ringkasan Eksekutif Ruang Lingkup DEC-016:
DEC-016 berfokus murni pada **tabel sumber `visits`**:
1. **Penambahan Kolom Snapshot:** `link_id_snapshot BIGINT UNSIGNED NOT NULL` dan `link_slug_snapshot VARCHAR(50) NOT NULL`.
2. **Dampak Penyimpanan:** Simulasi matematis menunjukkan pertambahan +63,9 MB disk selama siklus retensi 90 hari pada trafik normal (10.000 visits/hari), aman pada alokasi disk NVMe WarnaHost.
3. **Penyajian UI Dashboard:** Tim merekomendasikan **Opsi 2 (Hidden by Default dengan Checkbox Toggle "Tampilkan Tautan Terhapus")** agar dasbor operasional harian tetap ringkas tanpa menghilangkan data audit historis.
4. **Taksonomi Trafik Tautan Terhapus:** Kunjungan dari tautan yang telah di-hard delete **tetap menggunakan `aggregate_key = 'link:{snapshot_id}'`** sehingga riwayat statistiknya tetap teratribusi ke tautan tersebut.
5. **Idempotensi Agregasi:** Didukung Arsitektur Idempotensi Berbasis Watermark & Checkpoint (menguji 6 skenario kegagalan).

### Rekomendasi Tim Peninjau:
Setujui (APPROVE) implementasi snapshot identitas DEC-016 dengan parameter Opsi 2 dan Idempotensi Watermark.

---

### Lembar Isian Keputusan Product Owner untuk DEC-016:

Silakan tentukan pilihan Anda untuk **DEC-016**:

- [ ] **SETUJU PENUH (APPROVE):** Menyetujui penambahan snapshot pada tabel `visits`, penyajian UI Opsi 2 (Checkbox Toggle), atribusi partisi `link:{id}`, dan Idempotensi Watermark.
- [ ] **PILIH OPSI ANTARMUKA LAIN:**
  - [ ] Opsi 1: Tampilkan langsung secara inline dengan badge abu-abu.
  - [ ] Opsi 3: Pisahkan ke dalam tab khusus "Arsip Tautan Terhapus".
- [ ] **TOLAK / PERLU REVISI:** Meminta perubahan desain basis data alternatif.

**Catatan Khusus Product Owner untuk DEC-016:**
```
(Tuliskan catatan atau instruksi tambahan di sini)


```

---

## 4. Lembar Peninjauan & Otorisasi Individual 17 ADR

> **PANDUAN BAGI PRODUCT OWNER:**  
> Kolom **Keputusan PO** sengaja dikosongkan. Dimas Aditya dapat mengisi status per ADR (*Setuju / Tolak / Bersyarat*) atau memberikan konfirmasi tertulis pada percakapan proyek.

| ADR ID | Judul Keputusan Arsitektur | Relasi DEC Kanonikal | Rekomendasi Reviewer | Keputusan PO *(Setuju/Tolak/Syarat)* | Catatan Khusus PO | Status Formal |
|:---:|---|:---:|:---:|:---:|:---:|:---:|
| **ADR-001** | Modular Monolith Laravel 13.x | DEC-001 (Approved) | `SOUND WITH CONDITIONS` | | | `UNDER REVIEW` |
| **ADR-002** | MySQL 8.0 sebagai RDBMS Tunggal | *No dedicated DEC* | `BLOCKED BY MISSING EVIDENCE` | | | `UNDER REVIEW` |
| **ADR-003** | Blade + Native CSS + Vanilla JS | *No dedicated DEC* | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-004** | Penolakan AdSense & Adsterra Candidate | DEC-002 (Approved) | `SOUND WITH CONDITIONS` | | | `UNDER REVIEW` |
| **ADR-005** | Tabel `visitor_sessions` & UA Hash | *No dedicated DEC* | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-006** | Cloudflare Turnstile Bot Mitigation | DEC-005, DEC-011 | `SOUND WITH CONDITIONS` | | | `UNDER REVIEW` |
| **ADR-007** | Countdown Server & Atomic Token | DEC-004 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-008** | Enkripsi AES-256 Destination URL | *No dedicated DEC* | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-009** | Target WarnaHost Starter Unlimited | DEC-003 (Approved) | `BLOCKED BY MISSING EVIDENCE` | | | `UNDER REVIEW` |
| **ADR-010** | Retensi 90 Hari & Keyed HMAC IP Hash | DEC-006 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-011** | Hashed Session Token (SHA-256) | DEC-009 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-012** | Antarmuka Ganti Password Admin | DEC-008 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-013** | Pre-Built Vendor ZIP Deployment | DEC-010 (Approved) | `SOUND WITH CONDITIONS` | | | `UNDER REVIEW` |
| **ADR-014** | Suspend Popunder & Tombol Steril | DEC-007 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-015** | Kebijakan Hapus & Retensi Analitik | DEC-013 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-016** | Mandatori ANALYTICS_SALT Mandiri | DEC-014 (Approved) | `TECHNICALLY SOUND` | | | `UNDER REVIEW` |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot | DEC-015, DEC-016 | `SOUND WITH CONDITIONS` | | | `UNDER REVIEW` |

---

## 5. Lembar Pengesahan Formal (Product Owner Sign-Off Block)

Dengan menandatangani lembar ini atau memberikan konfirmasi eksplisit pada percakapan proyek, Product Owner meresmikan arahan bagi seluruh AI Coding Agent yang bertugas di repository LokaTaut:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                         LEMBAR PENGESAHAN PRODUCT OWNER                                │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│ Nama Lengkap       : Dimas Aditya                                                      │
│ Peran Otoritas     : Single Product Owner & System Proprietor                          │
│ Tanggal Keputusan  : ____________________ 2026                                         │
│ Status Gerbang     : READY FOR PRODUCT OWNER ADR REVIEW                                │
│                                                                                        │
│ Pernyataan:                                                                            │
│ "Saya telah meninjau hasil evaluasi arsitektur independen, register risiko, dan        │
│ analisis teknis yang disajikan pada lokataut-adr-review-v1.0.2/. Dengan ini saya       │
│ menetapkan keputusan resmi atas DEC-012, DEC-016, serta ADR sebagaimana tercantum."    │
│                                                                                        │
│ Tanda Tangan Konfirmasi:                                                               │
│                                                                                        │
│ __________________________________________                                             │
│ (Dimas Aditya)                                                                         │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

> **PERINGATAN OPERASIONAL KEPADA AI CODING AGENT:**  
> Dilarang mengisi tanda tangan atau mencentang opsi di atas atas nama Product Owner. AI Agent wajib menunggu respon otentik dari Dimas Aditya. Gerbang pengembangan tetap berstatus **`READY FOR PRODUCT OWNER ADR REVIEW`** dan **TIDAK BOLEH MEMULAI KODING APLIKASI**.

