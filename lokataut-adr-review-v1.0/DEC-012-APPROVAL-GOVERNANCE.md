# Tata Kelola & Prosedur Pengesahan ADR: DEC-012 — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | DEC-012-GOV-01 |
| **Versi Dokumen** | 1.0 |
| **Status Tata Kelola** | **UNDER REVIEW (Menunggu Keputusan Product Owner atas DEC-012)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [OPEN-DECISIONS.md](../lokataut-documentation-v1.2.2/OPEN-DECISIONS.md), [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md), & [AGENTS.md](../AGENTS.md)) |
| **Tanggal Terbit** | 31 Oktober 2026 |
| **Penyusun** | Lead Software Architect & Technical Documentation Auditor |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Prinsip Fundamental Tata Kelola ADR (Governance Principles)

Architecture Decision Record (ADR) adalah dokumen kontrak teknis yang mengikat arah pengembangan sistem LokaTaut. Untuk menjaga integritas sistem dan mencegah penyimpangan arsitektur oleh AI Coding Agent, aturan tata kelola berikut berlaku secara mutlak:

1. **Otoritas Tunggal Product Owner (Sole Approval Authority):**  
   Hanya Product Owner (Dimas Aditya) yang memiliki hak dan wewenang sah untuk menyetujui (*Approve*), menolak (*Reject*), atau menangguhkan (*Condition/Revise*) setiap ADR.
2. **Larangan Mutlak Persetujuan Otomatis oleh AI (Zero AI Authority):**  
   AI Coding Agent, Documentation Agent, maupun Review Agent **DILARANG KERAS** menganggap, menyimpulkan, atau menandai sebuah ADR sebagai `APPROVED` atas inisiatif sendiri. Segala bentuk status persetujuan yang disematkan tanpa pernyataan eksplisit dari Product Owner dianggap **batal demi hukum arsitektur (*void*)**.
3. **Persetujuan Granular per ADR (ADR-by-ADR Evaluation):**  
   Setiap keputusan arsitektur dievaluasi secara independen berdasarkan konsekuensi, trade-off, dan risikonya. Persetujuan diberikan secara spesifik per ID ADR, bukan persetujuan borongan tanpa pemeriksaan (*no blind rubber-stamping*).
4. **Kewajiban Bukti Persetujuan (Approval Evidence Trail):**  
   Setiap ADR yang beralih status wajib mencantumkan:
   - Tanggal persetujuan (YYYY-MM-DD).
   - Otoritas pemberi persetujuan (Dimas Aditya).
   - Bukti persetujuan (Kutipan instruksi percakapan atau tanda tangan lembar register).
5. **Kekekalan Rekam Keputusan (Immutability of Approved ADRs):**  
   ADR yang telah berstatus `APPROVED` tidak boleh diubah secara diam-diam. Jika di masa depan terjadi perubahan kebutuhan teknis, perubahan wajib dicatat melalui ADR baru yang berstatus `SUPERSEDES [ADR-XXX]`.

---

## 2. Lembar Kerja Pengesahan ADR (ADR Sign-Off Sheet)

> **PETUNJUK BAGI PRODUCT OWNER:**  
> Kolom **Keputusan Product Owner (PO Decision)** dan **Catatan Khusus PO** sengaja dikosongkan. Kolom ini disediakan khusus bagi Dimas Aditya untuk menentukan persetujuan formal terhadap masing-masing dari 17 ADR.

| ADR ID | Judul Keputusan Arsitektur | Rekomendasi Peninjau | Risiko Arsitektur Utama | Keputusan PO *(Isi: Setuju / Tolak / Bersyarat)* | Catatan Khusus PO | Status Formal Saat Ini |
|:---:|---|:---:|---|:---:|:---:|:---:|
| **ADR-001** | Modular Monolith Laravel 13.x | `SOUND WITH CONDITIONS` | Inkonsistensi versi PHP CLI cPanel | | | `PROPOSED / UNDER REVIEW` |
| **ADR-002** | MySQL 8.0 sebagai RDBMS Tunggal | `BLOCKED BY MISSING EVIDENCE` | Varian MariaDB di cPanel belum diverifikasi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-003** | Blade + Native CSS + Vanilla JS | `TECHNICALLY SOUND` | Disiplin penulisan CSS tanpa utility classes | | | `PROPOSED / UNDER REVIEW` |
| **ADR-004** | Penolakan AdSense & Adsterra Candidate | `SOUND WITH CONDITIONS` | Akun/domain Adsterra belum terverifikasi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-005** | Tabel `visitor_sessions` & UA Hash | `TECHNICALLY SOUND` | Pertumbuhan baris tabel sesi aktif | | | `PROPOSED / UNDER REVIEW` |
| **ADR-006** | Cloudflare Turnstile Bot Mitigation | `SOUND WITH CONDITIONS` | Ketergantungan jaringan eksternal (timeout) | | | `PROPOSED / UNDER REVIEW` |
| **ADR-007** | Countdown Server & Atomic Token | `TECHNICALLY SOUND` | Ketergantungan sinkronisasi NTP server | | | `PROPOSED / UNDER REVIEW` |
| **ADR-008** | Enkripsi AES-256 Destination URL | `TECHNICALLY SOUND` | Kehilangan `APP_KEY` mematikan dekripsi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-009** | Target WarnaHost Starter Unlimited | `BLOCKED BY MISSING EVIDENCE` | 7 perintah pre-flight belum dieksekusi | | | `PROPOSED / UNDER REVIEW` |
| **ADR-010** | Retensi 90 Hari & Keyed HMAC IP Hash | `TECHNICALLY SOUND` | Kehilangan log forensik mentah > 90 hari | | | `PROPOSED / UNDER REVIEW` |
| **ADR-011** | Hashed Session Token (SHA-256) | `TECHNICALLY SOUND` | Format heksadesimal harus konsisten | | | `PROPOSED / UNDER REVIEW` |
| **ADR-012** | Antarmuka Ganti Password Admin | `TECHNICALLY SOUND` | Potensi brute force jika rate limit jebol | | | `PROPOSED / UNDER REVIEW` |
| **ADR-013** | Pre-Built Vendor ZIP Deployment | `SOUND WITH CONDITIONS` | Perbedaan ekstensi mesin build lokal & host | | | `PROPOSED / UNDER REVIEW` |
| **ADR-014** | Suspend Popunder & Tombol Steril | `TECHNICALLY SOUND` | Pendapatan awal lebih konservatif | | | `PROPOSED / UNDER REVIEW` |
| **ADR-015** | Kebijakan Hapus & Retensi Analitik | `TECHNICALLY SOUND` | Penanganan relasi bernilai NULL di Eloquent | | | `PROPOSED / UNDER REVIEW` |
| **ADR-016** | Mandatori ANALYTICS_SALT Mandiri | `TECHNICALLY SOUND` | Crash fail-fast jika lupa pasang di `.env` | | | `PROPOSED / UNDER REVIEW` |
| **ADR-017** | Aggregate Key NOT NULL & Snapshot | `SOUND WITH CONDITIONS` | Penambahan ukuran baris (+75 byte/baris) | | | `PROPOSED / UNDER REVIEW` |

---

## 3. Prosedur Pengesahan bagi Product Owner

Untuk meresmikan keputusan atas DEC-012 dan rangkaian 17 ADR:

### Opsi A — Pengesahan Melalui Pesan Percakapan (Paling Cepat):
Product Owner cukup memberikan konfirmasi tertulis pada percakapan proyek. Contoh instruksi yang sah:
> *"Saya menyetujui DEC-012 mengenai tata kelola ADR. Untuk ke-17 ADR, saya menyetujui seluruh rekomendasi tim peninjau: ADR-003, ADR-005, ADR-007, ADR-008, ADR-010, ADR-011, ADR-012, ADR-014, ADR-015, dan ADR-016 resmi APPROVED. ADR-001, ADR-004, ADR-006, ADR-013, dan ADR-017 APPROVED WITH CONDITIONS. ADR-002 dan ADR-009 menunggu hasil eksekusi terminal."*

### Opsi B — Penandatanganan Dokumen Terpusat:
Product Owner dapat mengisi dan menandatangani formulir keputusan eksekutif pada [PRODUCT-OWNER-DECISION-REGISTER.md](./PRODUCT-OWNER-DECISION-REGISTER.md).

---

## 4. Konsekuensi Pasca-Persetujuan

1. **Pembaruan Dokumen Baseline:**  
   Setelah instruksi eksplisit diterima dari Product Owner, status ADR pada [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md) dan [README.md](../lokataut-documentation-v1.2.2/README.md) akan dimutakhirkan menjadi `APPROVED` dengan mencatat tanggal dan referensi bukti persetujuan.
2. **Kesiapan Fase Implementasi (Phase 1):**  
   Pengesahan DEC-012 dan penyelesaian bukti pre-flight (ADR-002 & ADR-009) merupakan gerbang prasyarat terakhir sebelum repository dinyatakan berstatus `READY FOR CODING`.

