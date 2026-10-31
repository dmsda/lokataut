# LokaTaut — AI Development Rules & Operational Directives

> **PERINGATAN PENTING UNTUK SEMUA AI CODING AGENT:**  
> File ini berisi instruksi operasional wajib yang mengatur tata cara kerja setiap AI Coding Agent di dalam repository LokaTaut. Anda **DILARANG** bertindak sebagai Product Manager, mengubah arsitektur, menambah fitur di luar spesifikasi yang telah disetujui (APPROVED), atau memulai fase koding sebelum dokumentasi berstatus APPROVED secara resmi oleh Product Owner.

---

## 1. Pemisahan Peran & Tanggung Jawab

| Peran | Entitas | Tanggung Jawab |
|---|---|---|
| **Product Owner** | User (Dimas Aditya) | Otoritas tunggal yang memberikan arahan, memvalidasi asumsi, dan menyetujui (APPROVE) setiap dokumen dan keputusan teknis. |
| **Documentation Agent** | AI Documentation Specialist | Menganalisis kebutuhan, menyusun blueprint teknis, PRD, arsitektur, ADR, dan menjaga konsistensi dokumentasi. |
| **Coding Agent** | AI Coding Agent (Anda saat fase implementasi) | Mengimplementasikan kode sesuai spesifikasi teknis yang disetujui tanpa improvisasi di luar scope. |
| **QA Agent** | AI QA / Test Runner Agent | Menguji implementasi kode terhadap acceptance criteria dan test cases yang tercantum di DOC-12. |

---

## 2. Checklist Wajib Sebelum Memulai Pekerjaan (Pre-Work Protocol)

Sebelum menulis baris kode apapun, setiap AI Coding Agent **WAJIB** menjalankan urutan berikut:

1. **Baca File Ini (`AGENTS.md`)** secara keseluruhan.
2. **Baca `README.md`** di root dan di `lokataut-documentation-v1.2.2/README.md`.
3. **Periksa Status Persetujuan Dokumentasi:** Pastikan dokumen yang mendasari tugas Anda berstatus `APPROVED` pada *Approval Registry* di `lokataut-documentation-v1.2.2/README.md`. **DILARANG KERAS** mengimplementasikan modul yang dokumen rujukannya masih berstatus `DRAFT` atau `UNDER REVIEW` tanpa konfirmasi eksplisit dari Product Owner.
4. **Periksa Implementasi yang Ada:** Jalankan `git status` dan telaah struktur file serta migrasi database yang sudah ada.
5. **Identifikasi Dependensi & Batasan:** Periksa constraint hosting (Shared Hosting WarnaHost Starter Unlimited: 1 Core, 2 GB RAM, PHP 8.3+, LiteSpeed, no root, no Redis, no Docker di production).

---

## 3. Golden Rules Pengembangan

### Rule 1: Ikuti PRD & Scope yang Disetujui
- Rujuk `lokataut-documentation-v1.2.2/01-product/02-prd.md` dan `lokataut-documentation-v1.2.2/01-product/03-scope-requirements.md`.
- **DILARANG KERAS** menambahkan fitur-fitur publik seperti: registrasi publisher/advertiser, user wallet, payment gateway, referral system, revenue sharing, public marketplace, atau SaaS multi-tenant.
- LokaTaut adalah platform **PRIVATE / SINGLE OWNER**. Hanya ada satu akun administrator.

### Rule 2: Patuhi Arsitektur & Tech Stack Baseline
- Rujuk `lokataut-documentation-v1.2.2/03-architecture/05-system-architecture.md` dan `lokataut-documentation-v1.2.2/03-architecture/07-technical-specification.md`.
- **Backend:** Laravel 13.x (PHP 8.3+), Blade Views & Components, MySQL 8.0+.
- **Frontend:** Semantic HTML5, Native CSS (no Bootstrap, no Tailwind di public safelink), Vanilla JavaScript ES6+ (no React/Vue/SPA).
- **DILARANG** menambahkan runtime baru (Node.js backend, Go, Python), daemon/worker persisten yang butuh supervisor root, atau in-memory store wajib seperti Redis.

### Rule 3: Integritas Keamanan & Integritas Basis Data Tanpa Kompromi
- Rujuk `lokataut-documentation-v1.2.2/05-security-monetization/09-security-privacy.md` dan `lokataut-documentation-v1.2.2/03-architecture/06-database-design.md`.
- **URL tujuan (destination URL) TIDAK PERNAH dikirim ke client/browser selama alur safelink berlangsung** (sebelum Turnstile lolos + countdown selesai + token server-side tervalidasi). URL baru diserahkan melalui HTTP 302 Location header saat proses selesai.
- Token sesi safelink harus acak secara kriptografis (`bin2hex(random_bytes(32))`), berbatas waktu (TTL), disimpan di database dalam bentuk **SHA-256 hash (`token_hash`)**, sedangkan raw token hanya dipegang browser.
- **Transisi status token wajib atomic di database** (`UPDATE visitor_sessions SET completed = 1 WHERE token_hash = ? AND completed = 0`) untuk mencegah race condition.
- Validasi countdown harus terjadi di **server-side** (timestamp check). Jangan pernah mempercayai manipulasi timer dari JavaScript client.
- Wajib menerapkan CSRF, sanitasi XSS, prepared statements (Eloquent/Query Builder), dan rate limiting pada semua public endpoint.
- Pseudonimisasi IP pengunjung wajib menggunakan `ANALYTICS_SALT` yang terisolasi dari `APP_KEY` tanpa fallback (NIST SP 800-57). `ANALYTICS_SALT` wajib dikonfigurasi di environment production.
- **Kebijakan Hapus Data & Integritas Agregasi:** Soft Delete (`$link->delete()`) sebagai default mempertahankan riwayat analitik. Hard Delete (`$link->forceDelete()`) meng-cascade `visitor_sessions` dan menyetel `visits.link_id` serta `daily_stats.link_id` ke `NULL` (`ON DELETE SET NULL`) dengan snapshot `link_id_snapshot` dan `link_slug_snapshot` di `visits` serta `aggregate_key` dan `link_slug` di `daily_stats` dipertahankan secara immutable. Agregasi harian wajib idempoten berbasis constraint unik NOT NULL `(aggregate_key, date)`.
- Penyimpanan kode iklan (`ad_code`) wajib dibatasi hanya untuk Super Admin dengan audit log di `security_logs` dan header CSP yang ketat untuk mencegah Stored XSS.
- Penggantian kata sandi admin (`FR-AUTH-004`) wajib memvalidasi kata sandi saat ini (`current_password`) dan merotasi sesi login.

### Rule 4: Kepatuhan Monetisasi & Kebijakan Jaringan Iklan
- Rujuk `lokataut-documentation-v1.2.2/05-security-monetization/10-monetization-specification.md`.
- **DILARANG MENGGUNAKAN GOOGLE ADSENSE** pada halaman safelink/countdown untuk mematuhi Google Publisher Policies (Thin content & forced waiting) demi mitigasi risiko sanksi akun.
- Gunakan integrasi slot iklan Adsterra / Monetag yang berstatus `CANDIDATE AD NETWORK (PROPOSED - PENDING ACCOUNT & DOMAIN VERIFICATION)`.
- **Tombol navigasi aksi safelink (`#proceed-btn`) wajib steril mutlak dari skrip atau pemicu iklan pihak ketiga** (Anti-IVT). Format popunder ditangguhkan pada MVP. Jarak aman 30px diterapkan sebagai internal engineering safeguard (WCAG 2.5.5).
- Jangan merekayasa HTTP referrer organik palsu atau memaksa klik iklan pengunjung (*no forced clicks, no accidental clicks layout*).

### Rule 5: Konvensi Koding & Struktur File
- Ikuti standar PSR-12, `declare(strict_types=1);`, dan konvensi standar Laravel 13.
- Controller harus tipis: validasi via Form Request, logika bisnis di Service Layer (`app/Services`), respon via Blade view / HTTP redirect.
- Gunakan migrasi database untuk setiap perubahan skema tabel.
- Jangan menyimpan secret atau API key di dalam kode sumber; selalu gunakan `.env` dan `config/services.php`.
- Perintah `php artisan key:generate` HANYA dijalankan satu kali saat instalasi awal pertama kali. Jangan jalankan pada deployment berikutnya.

### Rule 6: Disiplin Pengujian (Testing Requirements)
- Rujuk `lokataut-documentation-v1.2.2/06-quality/12-testing-qa.md`.
- Setiap fitur P0 dan P1 wajib memiliki Feature Test atau Unit Test (PHPUnit / Pest).
- Jalankan test suite (`php artisan test`) secara riil sebelum menyatakan sebuah task selesai.
- **DILARANG** menandai pekerjaan selesai (*Definition of Done*) jika pengujian belum dijalankan atau masih terdapat error/failure. Status pada fase pra-koding adalah `DOCUMENTED` dan `TEST NOT RUN`.

### Rule 7: Manajemen Perubahan & Dokumentasi
- Jika menemukan kendala teknis yang memaksa perubahan desain database atau arsitektur, **hentikan pekerjaan**, konsultasikan kepada Product Owner, dan dokumentasikan melalui Architecture Decision Record (ADR) di `lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md`.
- Perbarui dokumentasi terkait segera setelah keputusan disetujui.

---

## 4. Roadmap & Urutan Eksekusi

Setiap AI Coding Agent **HANYA** boleh mengerjakan task sesuai urutan fase pada:
`lokataut-documentation-v1.2.2/10-roadmap/16-implementation-roadmap.md`

- **Phase 0:** Persetujuan Dokumentasi & Pre-Flight Checks
- **Phase 1:** Fondasi Proyek & Autentikasi Admin Tunggal (Laravel 13)
- **Phase 2:** Manajemen Link (CRUD, Slug Generator, Expiration, Source Tagging)
- **Phase 3:** Safelink Engine (Token Session, Turnstile Verification, Server-side Countdown, Secure Redirect)
- **Phase 4:** Monetisasi & Manajemen Slot Iklan (Adsterra Integration)
- **Phase 5:** Analitik Kunjungan & Pelaporan Kinerja Link
- **Phase 6:** Pengerasan Keamanan, Rate Limiting, & Optimasi Shared Hosting
- **Phase 7:** Pengujian Menyeluruh (QA), UAT, & Panduan Deployment

Jangan melompat ke fase berikutnya jika fase prasyarat belum selesai dan belum lolos verifikasi automated testing.

---

## 5. Definition of Done (DoD)

Sebuah task/fitur dinyatakan **DONE** hanya jika:
1. Kode sesuai dengan PRD (`DOC-02`) dan Spesifikasi Teknis (`DOC-07`).
2. Tidak ada pelanggaran terhadap aturan keamanan (`DOC-09`).
3. Migrasi database dan model relationship sudah berjalan bersih.
4. Form Request validation sudah menangani input valid, invalid, dan edge cases.
5. Automated test (P0/P1) berhasil dieksekusi dengan status hijau (PASS).
6. Tampilan UI responsif di layar mobile dan desktop tanpa error console JS.
7. Tidak ada file `.env`, credential rahasia, atau log debugging (`dd()`, `dump()`) tertinggal.
8. Telah ditinjau dan disetujui oleh Product Owner.

---
*Dokumen ini merupakan aturan operasional baku repository LokaTaut. Versi 1.2.2 (Revisi Oktober 2026).*
