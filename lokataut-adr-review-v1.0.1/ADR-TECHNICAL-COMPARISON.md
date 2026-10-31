# Architectural Trade-off & Technical Comparison — LokaTaut

| Atribut | Nilai |
|---|---|
| **Document ID** | ADR-COMP-01 |
| **Versi Dokumen** | 1.0.1 (Remediasi Konsistensi & Idempotensi) |
| **Status Dokumen** | **UNDER REVIEW (Menunggu Keputusan Formal Product Owner atas DEC-012 & DEC-016)** |
| **Dokumentasi Baseline** | `lokataut-documentation-v1.2.2/` (khususnya [DOC-05](../lokataut-documentation-v1.2.2/03-architecture/05-system-architecture.md) & [DOC-15](../lokataut-documentation-v1.2.2/09-decisions/15-architecture-decisions.md)) |
| **Tanggal Audit** | 31 Oktober 2026 |
| **Penyusun** | Principal Software Architect & Senior System Analyst |
| **Otoritas Persetujuan** | Product Owner (Dimas Aditya) — *AI Agent tidak memiliki hak otorisasi* |

---

## 1. Pendahuluan & Dimensi Evaluasi

Dokumen ini menyajikan analisis komparatif antara keputusan arsitektur yang dirancang dalam LokaTaut v1.2.2 melawan berbagai alternatif industri. Evaluasi ditimbang berdasarkan 6 kriteria rekayasa perangkat lunak (*software engineering dimensions*):

1. **Shared Hosting Compatibility (SHC):** Kelayakan eksekusi pada lingkungan WarnaHost Starter (LiteSpeed, 1 vCPU, 2 GB RAM fisik CloudLinux, tanpa root, tanpa Docker, tanpa persistent background daemon).
2. **Security & Privacy Posture (SPP):** Kepatuhan standar keamanan internasional (OWASP ASVS v4.0, NIST SP 800-57 Part 1, Anti-Bypass, dan UU PDP / GDPR).
3. **Performance & Latency (PERF):** Kemampuan mencapai Core Web Vitals optimal (LCP < 1.2 detik, TTFB < 200 ms, transfer aset publik < 20 KB).
4. **Maintenance & Operational Simplicity (MOS):** Kemudahan pengelolaan jangka panjang oleh single owner tanpa beban tim DevOps terspesialisasi.
5. **Development Velocity (DEV):** Kecepatan implementasi, kelengkapan tooling ekosistem, kemudahan automated testing, dan kejelasan struktur kode.
6. **Total Cost of Ownership (TCO):** Efisiensi biaya infrastruktur dan lisensi (target < Rp 100.000 / bulan).

---

## 2. Matriks Perbandingan Arsitektural Utama

---

### Perbandingan 1: Arsitektur Sistem (Modular Monolith vs Microservices vs Serverless)

| Kriteria | Modular Monolith (PILIHAN LOKATAUT) | Microservices Architecture | Serverless / Cloud Functions |
|---|:---:|:---:|:---:|
| **Kompatibilitas Shared Hosting** | **Sangat Baik (100%)** — Berjalan di Apache/LiteSpeed tunggal | **Mustahil (0%)** — Memerlukan container Docker & Kubernetes | **Tidak Cocok** — Memerlukan platform cloud terkelola (AWS Lambda, Vercel) |
| **Security & Isolation** | **Tinggi** — Batas domain internal (`app/Modules/*`) terisolasi di kode | **Sangat Tinggi** — Isolasi fisik per service, namun surface network membesar | **Moderat** — Ketergantungan IAM cloud & cold start |
| **Performa & Latensi Internal** | **Nol Overhead Jaringan** — Panggilan fungsi in-memory PHP | **Tinggi Latensi** — Overhead serialisasi JSON/gRPC lintas network | **Bervariasi** — Masalah cold start memicu latensi awal 500ms - 2s |
| **Beban Operasional & DevOps** | **Rendah** — 1 basis data, 1 git repository, 1 pipeline deployment | **Sangat Berat** — Memerlukan service discovery, distributed tracing, mesh | **Tinggi** — Vendor lock-in, konfigurasi API gateway rumit |
| **Biaya Bulanan (TCO)** | **~Rp 79.000/bulan** (Shared Hosting WarnaHost) | **> Rp 500.000/bulan** (Multi VPS / Cluster) | **Tak Terprediksi** (Biaya pay-per-execution melonjak saat lonjakan trafik) |
| **Status Rekomendasi** | **TERPILIH (ADR-001 / DEC-001)** | **DITOLAK** | **DITOLAK** |

---

### Perbandingan 2: Framework Backend (Laravel 13.x vs Symfony 7 vs Slim / Microframework)

| Kriteria | Laravel 13.x (PILIHAN LOKATAUT) | Symfony 7.x | Slim Framework 4 / Microframework |
|---|:---:|:---:|:---:|
| **Siklus Hidup (Dukungan s/d)** | **17 Maret 2028 (Aktif)** | **November 2028 (LTS)** | Bergantung pustaka lepas |
| **Kelengkapan Fitur Out-of-the-Box** | **Sangat Lengkap** (Eloquent, Blade, Form Request, Scheduler, Throttling) | **Modular** (Memerlukan instalasi bundle terpisah) | **Minimalis** (Hanya routing & PSR-7 container) |
| **Ekosistem Shared Hosting di Indonesia** | **Standar Industri** — Didukung 100% oleh cPanel & LiteSpeed | **Baik** — Namun panduan cPanel lokal lebih jarang | **Baik** — Ringan, namun banyak kode utilitas harus ditulis manual |
| **Kecepatan Pengembangan (Velocity)** | **Sangat Cepat** — Scaffolding controller, migrasi, dan testing terintegrasi | **Moderat** — Konfigurasi dependency injection lebih verbose | **Lambat** — Harus mengintegrasikan ORM, validasi, dan template engine manual |
| **Kemudahan Pengujian (Testability)** | **Sangat Tinggi** (`TestCase`, `Http::fake()`, Database Transactions) | **Tinggi** (WebTestCase / PHPUnit) | **Moderat** (Harus membangun harness pengujian sendiri) |
| **Status Rekomendasi** | **TERPILIH (ADR-001 / DEC-001)** | **DITOLAK** | **DITOLAK** |

---

### Perbandingan 3: Basis Data Relasional (MySQL 8.0 vs SQLite 3 vs PostgreSQL 16)

| Kriteria | MySQL 8.0 / MariaDB 10.5+ (PILIHAN LOKATAUT) | SQLite 3 | PostgreSQL 16 |
|---|:---:|:---:|:---:|
| **Ketersediaan di Shared Hosting** | **100% Native** di seluruh cPanel WarnaHost | **Tersedia** (berbasis file) | **Jarang Tersedia** (hanya di paket VPS/Dedicated) |
| **Konkurensi Penulisan (Write Traffic)** | **Sangat Baik** (Row-level locking via InnoDB) | **Sangat Buruk** (Database-level locking, rentan disk lock error saat spike) | **Sangat Baik** (MVCC mutakhir) |
| **Integritas Constraint & Upsert** | **Sangat Baik** (`INSERT ... ON DUPLICATE KEY UPDATE` / Unique index) | **Moderat** | **Sangat Baik** (`ON CONFLICT DO UPDATE`) |
| **Manajemen Backup & Maintenance** | **Sangat Mudah** (phpMyAdmin & native `mysqldump`) | **Mudah** (Salin file .sqlite) | **Memerlukan pgAdmin / Terminal akses root** |
| **Konsumsi Memori RAM** | Dikelola terpusat oleh daemon MySQL server hosting | Membebani proses PHP | Memerlukan alokasi buffer memory mandiri |
| **Status Rekomendasi** | **TERPILIH (ADR-002)** | **DITOLAK** (Tidak layak untuk trafik safelink) | **DITOLAK** (Kendala ketersediaan hosting) |

---

### Perbandingan 4: Frontend & Antarmuka (Blade + Vanilla JS vs SPA Vue/React vs Livewire)

| Kriteria | Blade + Native CSS + Vanilla JS (PILIHAN LOKATAUT) | Single Page App (Vue 3 / React 19) | Laravel Livewire 3 / Inertia.js |
|---|:---:|:---:|:---:|
| **First Contentful Paint (FCP)** | **< 300 ms** (Server-side rendering murni) | **1.2 s – 2.5 s** (Menunggu unduh bundle JS) | **400 ms – 700 ms** |
| **Largest Contentful Paint (LCP)** | **< 1.0 s** | **> 2.0 s** (Rentan penalti skor Google) | **< 1.5 s** |
| **Ukuran Transfer Aset Publik** | **< 15 KB Total** (CSS < 8 KB, JS < 5 KB) | **180 KB – 400 KB** (Framework runtime) | **60 KB – 120 KB** |
| **Build Step di Server Produksi** | **Nol (Zero Build Step)** — Tidak butuh Node.js / NPM | **Wajib** Node.js build runner atau CI artifact | **Wajib** kompilasi frontend assets |
| **Toleransi Jaringan Seluler Lemah** | **Sangat Tinggi** — Countdown berjalan lokal tanpa network call | **Rendah** — Jika bundle gagal unduh, halaman blank | **Rentan** — Livewire butuh koneksi server aktif untuk state sync |
| **Status Rekomendasi** | **TERPILIH (ADR-003)** | **DITOLAK** | **DITOLAK** (Terlalu berat untuk publik safelink) |

---

### Perbandingan 5: Perlindungan Bot (Cloudflare Turnstile vs Google reCAPTCHA v2/v3 vs Math Captcha)

| Kriteria | Cloudflare Turnstile (PILIHAN LOKATAUT) | Google reCAPTCHA v2 / v3 | Custom Math / Image Captcha |
|---|:---:|:---:|:---:|
| **Pengalaman Pengguna (UX)** | **Tanpa Gesekan (Smart Challenge)** — Tanpa puzzle gambar | v2: Puzzle gambar lalu lintas di ponsel; v3: Sering memblokir IP seluler | Mengganggu pengunjung manusia |
| **Biaya & Ketentuan Layanan** | **Gratis pada Paket Standar** (Berdasarkan ToS Cloudflare saat ini) | Gratis hingga 10.000 asesmen/bulan (Google Cloud Billing wajib) | Gratis (dikelola server sendiri) |
| **Ketahanan terhadap AI/Bot Modern** | **Sangat Tinggi** (Analisis telemetri browser Cloudflare) | **Tinggi** | **Sangat Rendah** (Mudah di-bypass script OCR/LLM) |
| **Dampak Privasi Pengunjung** | **Tinggi** (Cloudflare tidak menjual data penjelajahan untuk iklan) | **Rendah** (Google melacak cookie penjelajahan lintas situs) | **Tinggi** (Lokal) |
| **Kemudahan Automated Testing** | **Tinggi** (Menyediakan testing key resmi & mudah di-mock) | **Moderat** | **Sulit** (Perlu OCR bypass di test) |
| **Status Rekomendasi** | **TERPILIH (ADR-006 / DEC-005, DEC-011)** | **DITOLAK** | **DITOLAK** |

---

### Perbandingan 6: Penyimpanan Token Sesi Safelink (SHA-256 Hash vs Plaintext vs Signed JWT)

| Kriteria | SHA-256 Hashed Token (PILIHAN LOKATAUT) | Plaintext Token di DB | Signed JWT (Client-Side) |
|---|:---:|:---:|:---:|
| **Ketahanan Kebocoran Database** | **Sangat Kuat** (Penyerang tidak bisa merekonstruksi token mentah) | **Rentan Fatal** (Token dapat langsung dipakai mem-bypass safelink) | Tidak relevan (Token di client) |
| **Pencegahan Race Condition / Replay** | **Sangat Mudah & Atomic** (`UPDATE ... completed = 1 WHERE ...`) | Mudah & Atomic | **Sangat Rumit** (JWT stateless sulit dibatalkan sekali pakai tanpa blacklist DB) |
| **Beban Kriptografi Server** | **Sangat Ringan** (1x SHA-256 hash < 0.05 ms) | Nol | Moderat (Verifikasi signature HMAC/RSA) |
| **Kepatuhan OWASP ASVS v4.0** | **Patuh 100%** (Section 3.2.4: Sensitive Data at Rest) | **Melanggar Standar** | Patuh bersyarat |
| **Status Rekomendasi** | **TERPILIH (ADR-011 / DEC-009)** | **DITOLAK** (Celah keamanan v1.0) | **DITOLAK** (Overhead pembatalan sekali pakai) |

---

### Perbandingan 7: Integritas Agregasi Harian (Idempotensi 3-Pilar vs Nullable Unique Key vs Materialized Views)

| Kriteria | Idempotensi 3-Pilar + aggregate_key (PILIHAN LOKATAUT) | `UNIQUE KEY (link_id, date)` Nullable | Database Materialized Views |
|---|:---:|:---:|:---:|
| **Perilaku SQL terhadap Nilai NULL** | **Kebal Anomali 100%** (`aggregate_key NOT NULL` via DEC-015) | **Rentan Duplikasi** (MySQL mengizinkan banyak baris `(NULL, date)`) | Tidak ada isu duplikasi |
| **Jaminan Idempotensi Eksekusi** | **Sangat Kuat** (DB Transaction + Row Lock + `withoutOverlapping`) | **Gagal** saat `link_id` menjadi NULL pasca hard-delete | Bergantung trigger refresh |
| **Kompatibilitas MySQL 8.0 & MariaDB** | **100% Native** di seluruh versi InnoDB | **Native** (namun cacat logika keunikan) | **Tidak Didukung Native** di MySQL/MariaDB |
| **Konservasi Histori Tautan Terhapus** | **Tersimpan Permanen** sebagai `link:{snapshot_id}` (DEC-016) | **Hilang** jika tautan di-hard delete sebelum cron malam | Tersimpan |
| **Overhead Penyimpanan Disk** | Terukur (Simulasi +67,5 MB/90 hari pada trafik normal) | Nol | Tinggi (Menyalin snapshot seluruh data) |
| **Status Rekomendasi** | **TERPILIH (ADR-017 / DEC-015, DEC-016)** | **DITOLAK** (Bug integritas v1.2.1) | **DITOLAK** (Keterbatasan fitur RDBMS) |

---

### Perbandingan 8: Strategi Deployment (Pre-Built Vendor ZIP vs Live Composer Install on Server)

| Kriteria | Pre-Built Vendor ZIP Artifact (PILIHAN LOKATAUT) | Live `composer install` di Server Hosting |
|---|:---:|:---:|
| **Risiko Out-of-Memory (OOM SIGKILL)** | **0% (Nol)** — Kompilasi dependensi dilakukan di luar server | **Tinggi (> 40%)** — Composer butuh memori besar (> 1.5 GB) |
| **Kecepatan Proses Deployment** | **Sangat Cepat (< 2 menit)** — Cukup uncompress dan jalankan migrasi | **Lambat (5–15 menit)** — Resolusi dependensi via jaringan server |
| **Ketergantungan Ekstensi Server** | Menuntut keselarasan versi PHP minor lokal dan target hosting | Bergantung pada ekstensi PHP CLI server hosting |
| **Toleransi Limit Shared Hosting** | **Sangat Baik** — Tidak memicu pemblokiran CloudLinux LVE | **Berisiko Terkena Suspend** akibat lonjakan CPU/RAM berlebih |
| **Status Rekomendasi** | **TERPILIH (ADR-013 / DEC-010)** | **OPSI SEKUNDER / TIDAK DISARANKAN** |

---

## 3. Kesimpulan Sintesis Arsitektur

Kombinasi arsitektur LokaTaut v1.2.2:
$$\text{Laravel 13} + \text{Modular Monolith} + \text{MySQL/InnoDB} + \text{Blade/Vanilla JS} + \text{Turnstile} + \text{AES-256} + \text{Pre-built Deploy}$$
merupakan **titik keseimbangan Pareto optimal (*Pareto-Optimal Frontier*)** yang memadukan keandalan teknis, efisiensi biaya infrastruktur, dan kepatuhan standar keamanan modern. Tidak ada over-engineering, namun tidak ada kompromi terhadap keamanan data dan integritas bisnis.

