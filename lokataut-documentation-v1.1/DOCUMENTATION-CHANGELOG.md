# Documentation Changelog — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | CHANGELOG |
| **Versi** | 1.1 |
| **Terakhir Diperbarui** | 30 Oktober 2026 |

---

## Versi 1.1 (30 Oktober 2026) — Audit & Consistency Revision

### Perubahan Arsitektur & Framework
- **DOC-01, DOC-02, DOC-03, DOC-05, DOC-07, DOC-12, DOC-13, DOC-14, DOC-15, DOC-16:**
  - *Temuan Awal:* Menetapkan Laravel 11 sebagai baseline padahal sudah EOL sejak 12 Maret 2026.
  - *Perubahan:* Memperbarui baseline teknologi ke **Laravel 13.x** dengan runtime **PHP 8.3+** (didukung hingga 17 Maret 2028).
  - *Dampak:* Menjamin dukungan patch keamanan selama siklus hidup proyek 2 tahun ke depan.

### Perubahan Tata Kelola & Approval Governance
- **DOC-15 (ADR), AGENTS.md, README.md:**
  - *Temuan Awal:* Status ADR-001 s/d ADR-010 tercatat `APPROVED` dan AGENTS.md mengklaim persetujuan sepihak sebelum PO meninjau.
  - *Perubahan:* Seluruh status ADR dikembalikan ke **`PROPOSED`** / **`UNDER REVIEW`**. Dibuat tabel resmi *Approval Registry* di README dokumentasi.

### Perbaikan Integritas Referensi & Tautan
- **DOC-03, DOC-04, DOC-07:**
  - *Temuan Awal:* Tautan relatif mengarah ke dokumen fiktif (`01-project-brief.md`, `04-tech-stack.md`) dan folder non-eksisten (`02-technical/`, `03-design/`).
  - *Perubahan:* Memperbaiki 100% path relatif Markdown ke folder kanonikal (`01-product/`, `02-analysis/`, `03-architecture/`, `04-design/`, `05-security-monetization/`, `06-quality/`, `07-operations/`, `08-ai-guidelines/`, `09-decisions/`, `10-roadmap/`).

### Perubahan Keamanan & Privasi
- **DOC-02, DOC-05, DOC-07, DOC-09:**
  - *Temuan Awal:* Overclaiming bahwa URL tujuan tidak pernah diketahui pengunjung.
  - *Perubahan:* Meluruskan klausul: URL tujuan dirahasiakan selama alur safelink, namun dikirimkan melalui HTTP 302 Location header saat proses unduhan selesai.
- **DOC-06, DOC-09:**
  - *Temuan Awal:* IP hashing sederhana menggunakan `APP_KEY` dan pengikatan sesi IP kaku yang bermasalah bagi pengguna mobile.
  - *Perubahan:* Memformalkan penggunaan `hash_hmac('sha256', $ip, config('app.analytics_salt'))`. Pengikatan sesi diubah menjadi Session Token + User-Agent Hash.
- **DOC-07, DOC-09:**
  - *Temuan Awal:* Potensi race condition pada eksekusi one-time redirect token.
  - *Perubahan:* Menetapkan atomic SQL update: `UPDATE visitor_sessions SET completed = 1 WHERE token = ? AND completed = 0;`.
- **DOC-06, DOC-07, DOC-10:**
  - *Temuan Awal:* Potensi Stored XSS pada render kode iklan `{!! $slot->ad_code !!}`.
  - *Perubahan:* Menambahkan audit log perubahan ad code, validasi admin ketat, dan rekomendasi Content Security Policy (CSP).

### Perubahan Monetisasi
- **DOC-10, DOC-15:**
  - *Temuan Awal:* Menetapkan Adsterra sebagai keputusan final berdasarkan asumsi kebijakan.
  - *Perubahan:* Mengubah status Adsterra menjadi **`CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT VERIFICATION)`** dan mendokumentasikan batasan ToS Adsterra mengenai larangan *incentivized traffic*.

### Perubahan UI/UX & Basis Data
- **DOC-08:**
  - *Perubahan:* Menambahkan label visual wajib `"Advertisement / Iklan Sponsor"` di atas setiap slot iklan untuk mencegah dark pattern tombol download palsu.
- **DOC-06:**
  - *Perubahan:* Menegaskan definisi formal metrik analitik (`impressions`, `verified`, `completions`, `unique_visitors`) dan menambahkan composite unique index `(link_id, date)` pada `daily_stats`.

---

## Versi 1.0 (30 Oktober 2026) — Rilis Draf Awal
- Pembuatan draf awal 16 dokumen spesifikasi teknis dan operasional.

