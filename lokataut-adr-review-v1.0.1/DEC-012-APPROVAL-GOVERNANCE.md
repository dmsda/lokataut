# Tata Kelola & Prosedur Pengesahan ADR: DEC-012 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DEC-012-GOV-01 |
| **Versi Dokumen** | 1.0.1 (Isolasi Murni Tata Kelola & Pemisahan Otorisasi ADR) |
| **Status Tata Kelola** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md), [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md), & [AGENTS.md](../AGENTS.md)) |
| **Tanggal Terbit** | 31 Oktober 2026 |
| **Penyusun** | Lead Software Architect & Technical Documentation Auditor |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Prinsip Fundamental Tata Kelola ADR (Governance Principles)

Architecture Decision Record (ADR) adalah dokumen kontrak teknis permanen yang mengikat arah pengembangan sistem LokaTaut. Pada versi 1.0.1, **ruang lingkup DEC-012 dipertegas murni sebagai kerangka tata kelola (*governance framework*) dan prosedur pengesahan ADR**.

Prinsip tata kelola yang berlaku secara mutlak:

1. **Otoritas Tunggal Product Owner (Sole Approval Authority):**  
   Hanya Product Owner (Dimas Aditya) yang memiliki hak dan wewenang sah untuk menyetujui (*Approve*), menolak (*Reject*), atau meminta revisi (*Revise*) terhadap setiap ADR.
2. **Larangan Mutlak Persetujuan Otomatis oleh AI (Zero AI Authority):**  
   AI Coding Agent, Documentation Agent, maupun Review Agent **DILARANG KERAS** menganggap, menyimpulkan, atau menandai sebuah ADR sebagai `APPROVED` atas inisiatif sendiri. Segala bentuk status persetujuan yang disematkan tanpa pernyataan eksplisit dari Product Owner dianggap **batal demi hukum arsitektur (*void*)**.
3. **Pemisahan Tegas Tata Kelola dari Persetujuan Arsitektur:**  
   Persetujuan Product Owner atas DEC-012 **HANYA meresmikan aturan dan prosedur tata kelola**. Persetujuan DEC-012 **TIDAK SECARA OTOMATIS menyetujui ke-17 ADR**.
4. **Persetujuan Granular Individual per ADR (ADR-by-ADR Review):**  
   Setiap keputusan arsitektur (ADR-001 s/d ADR-017) wajib ditinjau dan disetujui satu per satu secara granular oleh Product Owner berdasarkan konsekuensi, trade-off, dan pemenuhan kondisi prasyaratnya.
5. **Pemisahan Status Rekomendasi Peninjau dari Otorisasi PO:**  
   Predikat seperti `TECHNICALLY SOUND`, `SOUND WITH CONDITIONS`, dan `BLOCKED BY MISSING EVIDENCE` adalah **rekomendasi profesional tim peninjau teknis**. Predikat ini **BUKAN status persetujuan**. Seluruh 17 ADR berstatus formal `PROPOSED / UNDER REVIEW` hingga Product Owner memberikan keputusan definitif.
6. **Kewajiban Bukti Persetujuan (Approval Evidence Trail):**  
   Setiap ADR yang disetujui wajib mencantumkan tanggal persetujuan (YYYY-MM-DD), otoritas pemberi persetujuan (Dimas Aditya), dan kutipan bukti persetujuan (percakapan proyek atau tanda tangan registri).
7. **Kekekalan Rekam Keputusan (Immutability):**  
   ADR yang telah berstatus `APPROVED` tidak boleh diubah secara diam-diam. Setiap perubahan kebutuhan arsitektur di masa depan wajib dicatat melalui ADR baru berstatus `SUPERSEDES [ADR-XXX]`.

---

## 2. Lembar Inventaris & Rekomendasi ADR untuk Evaluasi PO

> **PETUNJUK BAGI PRODUCT OWNER:**  
> Kolom **Keputusan Product Owner (PO Decision)** dan **Catatan Khusus PO** sengaja dikosongkan. Kolom ini disediakan khusus bagi Dimas Aditya untuk menentukan keputusan individual per ADR secara bertahap.

| ADR ID | Judul Keputusan Arsitektur | Relasi DEC Kanonikal (v1.2.2) | Rekomendasi Reviewer | Risiko Arsitektur Kunci | Keputusan PO *(Isi: Setuju / Tolak / Bersyarat)* | Catatan Khusus PO | Status Formal Saat Ini |
|:---:|---|:---:|:---:|---|:---:|:---:|:---:|
| **ADR-001** | Modular Monolith Laravel 13.x | DEC-001 (Approved) | `SOUND WITH CONDITIONS` | Inkonsistensi versi PHP CLI cPanel | | | `PROPOSED / UNDER REVIEW` |
| **ADR-002** | MySQL 8.0 sebagai RDBMS Tunggal | *Arsitektur Umum* | `BLOCKED BY MISSING EVIDENCE` | Varian MariaDB di cPanel belum diverifikasi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-003** | Blade + Native CSS + Vanilla JS | *Arsitektur Umum* | `TECHNICALLY SOUND` | Disiplin penulisan CSS tanpa utility classes | | | `PROPOSED / UNDER REVIEW` |
| **ADR-004** | Penolakan AdSense & Adsterra Candidate | DEC-002 (Approved) | `SOUND WITH CONDITIONS` | Akun/domain Adsterra belum terverifikasi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-005** | Tabel `visitor_sessions` & UA Hash | *Arsitektur Umum* | `TECHNICALLY SOUND` | Pertumbuhan baris tabel sesi aktif | | | `PROPOSED / UNDER REVIEW` |
| **ADR-006** | Cloudflare Turnstile Bot Mitigation | DEC-005 & DEC-011 (Approved) | `SOUND WITH CONDITIONS` | Ketergantungan jaringan eksternal (timeout 5s) | | | `PROPOSED / UNDER REVIEW` |
| **ADR-007** | Countdown Server & Atomic Token | DEC-004 (Approved) | `TECHNICALLY SOUND` | Ketergantungan sinkronisasi NTP server | | | `PROPOSED / UNDER REVIEW` |
| **ADR-008** | Enkripsi AES-256 Destination URL | *Arsitektur Umum* | `TECHNICALLY SOUND` | Kehilangan `APP_KEY` mematikan dekripsi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-009** | Target WarnaHost Starter Unlimited | DEC-003 (Approved) | `BLOCKED BY MISSING EVIDENCE` | 7 perintah pre-flight belum dieksekusi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-010** | Retensi 90 Hari & Keyed HMAC IP Hash | DEC-006 (Approved) | `TECHNICALLY SOUND` | Kehilangan log forensik mentah > 90 hari | | | `PROPOSED / UNDER REVIEW` |
| **ADR-011** | Hashed Session Token (SHA-256) | DEC-009 (Approved) | `TECHNICALLY SOUND` | Format heksadesimal harus konsisten | | | `PROPOSED / UNDER REVIEW` |
| **ADR-012** | Antarmuka Ganti Password Admin | DEC-008 (Approved) | `TECHNICALLY SOUND` | Potensi brute force jika rate limit jebol | | | `PROPOSED / UNDER REVIEW` |
| **ADR-013** | Pre-Built Vendor ZIP Deployment | DEC-010 (Approved) | `SOUND WITH CONDITIONS` | Perbedaan ekstensi mesin build lokal & host | | | `PROPOSED / UNDER REVIEW` |
| **ADR-014** | Suspend Popunder & Tombol Steril | DEC-007 (Approved) | `TECHNICALLY SOUND` | Jarak 30px adalah internal safeguard WCAG | | | `PROPOSED / UNDER REVIEW` |
| **ADR-015** | Kebijakan Hapus & Retensi Analitik | DEC-013 (Approved) | `TECHNICALLY SOUND` | Penanganan relasi bernilai NULL di Eloquent | | | `PROPOSED / UNDER REVIEW` |
| **ADR-016** | Mandatori ANALYTICS_SALT Mandiri | DEC-014 (Approved) | `TECHNICALLY SOUND` | Crash fail-fast jika lupa pasang di `.env` | | | `PROPOSED / UNDER REVIEW` |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot | DEC-015 (Appr) & DEC-016 (Review) | `SOUND WITH CONDITIONS` | Butuh Idempotensi 3-Pilar & TTD DEC-016 | | | `PROPOSED / UNDER REVIEW` |

---

## 3. Prosedur Dua Tahap bagi Product Owner

Untuk menjaga pemisahan yang bersih, pengesahan dilakukan dalam **dua tahap terpisah**:

### Tahap 1: Pengesahan Kerangka Tata Kelola (DEC-012)
Product Owner memberikan konfirmasi tertulis untuk mengesahkan tata kelola DEC-012. Contoh konfirmasi yang sah:
> *"Saya menyetujui DEC-012 mengenai kerangka tata kelola dan mekanisme persetujuan ADR. Saya memahami bahwa persetujuan ini meresmikan aturan tata kelola, dan tidak otomatis menyetujui ke-17 ADR."*

### Tahap 2: Peninjauan & Persetujuan Individual 17 ADR
Setelah DEC-012 disetujui, Product Owner dapat meninjau ke-17 ADR satu per satu atau berkelompok sesuai pertimbangan mandiri. Contoh konfirmasi:
> *"Untuk ADR, saya menyetujui ADR-003, ADR-005, ADR-007, ADR-008, ADR-010, ADR-011, ADR-012, ADR-014, ADR-015, dan ADR-016 sebagai APPROVED. Untuk ADR-001, ADR-004, ADR-006, ADR-013, dan ADR-017 saya setujui BERSYARAT. ADR-002 dan ADR-009 tetap menunggu hasil eksekusi terminal cPanel."*

---

## 4. Konsekuensi Pasca-Persetujuan

1. **Pembaruan Dokumen Baseline:**  
   Status DEC-012 pada [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md) dan [README.md](../lokataut-documentation-v1.2.2/README.md) akan dimutakhirkan menjadi `APPROVED` dengan mencatat tanggal dan bukti persetujuan.
2. **Kesiapan Fase Implementasi (Phase 1):**  
   Pengesahan tata kelola DEC-012, persetujuan ADR terkait Phase 1, dan penyelesaian bukti pre-flight hosting (ADR-002 & ADR-009) merupakan gerbang prasyarat mutlak sebelum repository berstatus `READY FOR CODING`.

