@extends('layouts.site')

@section('meta_title', 'LokaTaut — Pengelolaan Tautan Digital & Safelink Terpercaya')
@section('meta_description', 'Kelola tautan digital dengan mudah dan terorganisir. Buat tautan singkat, lindungi URL unduhan melalui safelink dari bot, dan pantau analitik kunjungan secara akurat.')

@section('content')
<!-- Section 1: Hero -->
<section class="hero-section">
  <div class="container">
    <div class="hero-grid">
      <div>
        <div class="hero-badge">
          <span class="hero-badge-dot"></span> Sistem Manajemen Tautan & Safelink Privat
        </div>
        
        <h1 class="hero-title">
          Kelola Tautan Digital dengan Lebih Mudah dan Terorganisir.
        </h1>
        
        <p class="hero-sub">
          LokaTaut membantu Anda merapikan URL panjang, mengamankan proses pengalihan unduhan dari perayapan bot otomatis, serta memantau statistik kunjungan secara terpusat.
        </p>

        <div class="hero-cta">
          <a href="#how-it-works" class="btn btn-primary btn-lg">Pelajari Cara Kerjanya &rarr;</a>
          <a href="{{ route('help') }}" class="btn btn-secondary btn-lg">Baca Panduan Penggunaan</a>
        </div>

        <div class="hero-trust">
          <div class="hero-trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span><strong>Server-Side</strong> Validation</span>
          </div>
          <div class="hero-trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span><strong>AES-256</strong> URL Encryption</span>
          </div>
          <div class="hero-trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span><strong>Turnstile</strong> Bot Shield</span>
          </div>
        </div>
      </div>

      <!-- Hero Interactive SVG/HTML UI Mockup -->
      <div>
        <div class="hero-mockup-wrapper">
          <div class="mockup-header">
            <div class="mockup-dots">
              <span class="mockup-dot dot-red"></span>
              <span class="mockup-dot dot-yellow"></span>
              <span class="mockup-dot dot-green"></span>
            </div>
            <div class="mockup-address">https://lokataut.com/s/installer-2026</div>
          </div>

          <div class="mockup-content">
            <div class="mockup-card" style="border-left: 4px solid var(--primary); padding: 16px;">
              <div style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 4px;">Tautan Aktif Terproteksi</div>
              <div style="font-size: 15px; font-weight: 700; color: var(--text-primary);">Adobe Photoshop 2026 Core Package</div>
              <div style="font-family: var(--font-mono); font-size: 12px; color: var(--primary); margin-top: 4px;">lokataut.com/s/photoshop-v26</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <div class="mockup-card" style="padding: 14px;">
                <div style="font-size: 11px; color: var(--text-muted);">Hitung Mundur</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--primary);">10 Detik</div>
                <div style="font-size: 11px; color: var(--success); margin-top: 2px;">✓ Server Verified</div>
              </div>

              <div class="mockup-card" style="padding: 14px;">
                <div style="font-size: 11px; color: var(--text-muted);">Status Bot Shield</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--success);">Turnstile</div>
                <div style="font-size: 11px; color: var(--text-dim); margin-top: 2px;">Smart Challenge</div>
              </div>
            </div>

            <div class="mockup-card" style="background: var(--bg-surface); text-align: center; padding: 16px;">
              <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px;">URL Asli Disimpan Terenkripsi (AES-256):</div>
              <div style="font-family: var(--font-mono); font-size: 12px; background: var(--bg-subtle); border: 1px solid var(--border-subtle); padding: 6px 12px; border-radius: var(--radius-sm); color: var(--text-secondary); word-break: break-all;">
                ••••••••••••••••••••••••••••••••••••••••
              </div>
              <div style="font-size: 11px; color: var(--text-dim); margin-top: 6px;">URL baru diserahkan ke browser setelah verifikasi server selesai.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section 2: Product Features -->
<section id="features" class="section section-alt">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Fitur Platform</span>
      <h2 class="section-title">Fitur Lengkap Pengelolaan Tautan Digital</h2>
      <p class="section-sub">LokaTaut dirancang khusus sebagai sistem pengelolaan terpusat untuk berkas, file download, dan tautan publik.</p>
    </div>

    <div class="grid-3">
      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
        </div>
        <h3 class="feature-title">Manajemen Shortlink Terpusat</h3>
        <p class="feature-desc">Perpendek URL panjang dan rumit menjadi tautan rapi dengan format canonical <code>/s/{slug}</code>. Perbarui URL tujuan kapan saja tanpa mengubah link yang telah disebar.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
        </div>
        <h3 class="feature-title">Kustomisasi Slug URL</h3>
        <p class="feature-desc">Tentukan akhiran tautan yang deskriptif dan mudah diingat oleh audiens Anda (misalnya <code>/s/panduan-2026</code>) atau gunakan generator acak otomatis yang unik.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
        </div>
        <h3 class="feature-title">Proteksi Safelink dari Bot</h3>
        <p class="feature-desc">URL tujuan asli tersimpan dalam bentuk terenkripsi AES-256 dan dilindungi dari perayap bot liar melalui pemeriksaan cerdas Cloudflare Turnstile.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <h3 class="feature-title">Hitung Mundur Sisi Server</h3>
        <p class="feature-desc">Validasi jeda hitung mundur dievaluasi langsung di sisi server menggunakan timestamp sesi nyata, sehingga upaya manipulasi timer di peramban klien dapat dicegah.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </div>
        <h3 class="feature-title">Statistik Kunjungan Terukur</h3>
        <p class="feature-desc">Pelaporan analitik terpusat mencakup tayangan, pengalihan berhasil, perangkat, dan negara asal pengunjung harian dengan pseudonimisasi IP berstandar privasi.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
        </div>
        <h3 class="feature-title">Penempatan Iklan yang Wajar</h3>
        <p class="feature-desc">Dukungan pengelolaan 4 slot iklan sponsor dengan batas aman jarak navigasi 30px (Anti-IVT) yang mematuhi standar kenyamanan pengunjung tanpa tombol jebakan.</p>
      </div>
    </div>
  </div>
</section>

<!-- Section 3: How It Works -->
<section id="how-it-works" class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Alur Operasional</span>
      <h2 class="section-title">Bagaimana LokaTaut Bekerja</h2>
      <p class="section-sub">Empat langkah mudah dalam siklus distribusi tautan mulai dari pemasukan URL hingga pengalihan aman.</p>
    </div>

    <div class="grid-2" style="align-items: center;">
      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="step-item">
          <div class="step-number">1</div>
          <div class="step-body">
            <h4>Pemasukan URL Tujuan & Konfigurasi</h4>
            <p>Admin memasukkan URL berkas unduhan asli, menentukan slug unik, website sumber, dan durasi hitung mundur (default: 10 detik).</p>
          </div>
        </div>

        <div class="step-item">
          <div class="step-number">2</div>
          <div class="step-body">
            <h4>Enkripsi Data & Pembuatan Slug</h4>
            <p>Sistem mengenkripsi URL tujuan menggunakan cipher AES-256 dan menerbitkan tautan publik beralamat <code>/s/{slug}</code>.</p>
          </div>
        </div>

        <div class="step-item">
          <div class="step-number">3</div>
          <div class="step-body">
            <h4>Distribusi Tautan ke Pengunjung</h4>
            <p>Tautan dibagikan ke artikel, media sosial, atau komunitas. Pengunjung yang membuka tautan akan diarahkan ke halaman safelink terproteksi.</p>
          </div>
        </div>

        <div class="step-item">
          <div class="step-number">4</div>
          <div class="step-body">
            <h4>Verifikasi Selesai & Pengalihan HTTP 302</h4>
            <p>Setelah lolos verifikasi bot dan timer server tuntas, server menerbitkan respon HTTP 302 yang mengantarkan browser ke alamat tujuan asli.</p>
          </div>
        </div>
      </div>

      <!-- Flowchart Visual -->
      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-md);">
        <div style="font-weight: 700; font-size: 16px; margin-bottom: 20px; text-align: center; color: var(--text-primary);">Arsitektur Alur Pengunjung Safelink</div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="background: var(--primary-subtle); border: 1px solid var(--primary-border); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; text-align: center; font-weight: 600; color: var(--primary);">
            1. Pengunjung Membuka /s/{slug}
          </div>
          <div style="text-align: center; color: var(--text-dim); font-size: 14px; line-height: 1;">↓</div>
          <div style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; text-align: center; color: var(--text-secondary);">
            2. Token Sesi Kriptografis 32-Byte Dibuat (SHA-256)
          </div>
          <div style="text-align: center; color: var(--text-dim); font-size: 14px; line-height: 1;">↓</div>
          <div style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; text-align: center; color: var(--text-secondary);">
            3. Verifikasi Cloudflare Turnstile & Timer Berjalan
          </div>
          <div style="text-align: center; color: var(--text-dim); font-size: 14px; line-height: 1;">↓</div>
          <div style="background: var(--success-subtle); border: 1px solid var(--success-border); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 14px; text-align: center; font-weight: 600; color: var(--success);">
            4. Atomic Token Update & Redirect HTTP 302 Aman
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section 4: Why LokaTaut -->
<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Nilai Tambah</span>
      <h2 class="section-title">Mengapa Menggunakan LokaTaut?</h2>
      <p class="section-sub">Solusi terukur yang memadukan kerapian tautan publik dengan kontrol distribusi berkas digital.</p>
    </div>

    <div class="grid-3">
      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Manajemen Terpusat</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Ganti alamat server file unduhan kapan saja tanpa perlu menyunting kembali artikel blog atau postingan lama yang telah beredar luas.</p>
      </div>

      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Efisiensi Tautan</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">URL pendek memudahkan pembacaan di layar ponsel dan meminimalkan risiko tautan terpotong saat disalin di aplikasi pesan.</p>
      </div>

      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Statistik Ringan Tanpa Bloat</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Data kunjungan dirangkum secara harian menggunakan agregasi idempoten yang hemat sumber daya server.</p>
      </div>

      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Kendali Hitung Mundur</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Sesuaikan durasi timer countdown per masing-masing tautan secara fleksibel mulai dari 5 detik hingga 60 detik.</p>
      </div>

      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Privasi Pengunjung Terjaga</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Alamat IP pengunjung disamarkan menggunakan fungsi HMAC bergaram rahasia yang terisolasi, tanpa menyimpan alamat IP mentah.</p>
      </div>

      <div class="feature-card">
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Cepat & Hemat Sumber Daya</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Dibangun di atas Laravel 13, Native CSS, dan Blade SSR tanpa script berat, menjamin waktu muat tetap instan di koneksi seluler.</p>
      </div>
    </div>
  </div>
</section>

<!-- Section 5: Use Cases -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Skenario Penggunaan</span>
      <h2 class="section-title">Dirancang untuk Berbagai Kebutuhan Distribusi</h2>
      <p class="section-sub">LokaTaut dapat diadaptasi untuk aneka skenario berbagi tautan berkas digital di internet.</p>
    </div>

    <div class="grid-3">
      <div class="use-case-card">
        <div class="use-case-icon">
          <svg viewBox="0 0 24 24"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        </div>
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Distribusi Software & Installer</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Lindungi server unduhan aplikasi dari scraper otomatis dan berikan link resmi yang rapi bagi komunitas pengguna software.</p>
      </div>

      <div class="use-case-card">
        <div class="use-case-icon">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Berbagi Dokumen & Ebook</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Sebarkan berkas PDF panduan, modul kuliah, atau materi riset dengan pelacakan volume kunjungan yang terorganisir per materi.</p>
      </div>

      <div class="use-case-card">
        <div class="use-case-icon">
          <svg viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
        </div>
        <h4 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Distribusi Template & Aset Grafis</h4>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">Kirimkan template dokumen, preset desain, dan materi kreatif kepada audiens dengan halaman perantara yang bersih dan profesional.</p>
      </div>
    </div>
  </div>
</section>

<!-- Section 6: Latest Articles -->
<section class="section section-alt">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; flex-wrap: wrap; gap: 16px;">
      <div>
        <span class="section-badge">Blog & Edukasi</span>
        <h2 class="section-title" style="margin-bottom: 0;">Artikel & Panduan Terbaru</h2>
      </div>
      <a href="{{ route('blog.index') }}" class="btn btn-secondary">Lihat Semua Artikel &rarr;</a>
    </div>

    <div class="grid-3">
      @forelse ($latestArticles as $article)
        <article class="article-card">
          <div class="article-card-body">
            <div class="article-meta">
              <span class="category-tag">{{ $article->category->name ?? 'Umum' }}</span>
              <span>&bull;</span>
              <span>{{ $article->reading_time }} mnt baca</span>
            </div>

            <h3 class="article-card-title">
              <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
            </h3>

            <p class="article-card-excerpt">{{ $article->excerpt }}</p>

            <div class="article-card-footer">
              <span>{{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : '-' }}</span>
              <a href="{{ route('blog.show', $article->slug) }}" style="font-weight: 600; font-size: 13px;">Baca Selengkapnya &rarr;</a>
            </div>
          </div>
        </article>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-muted);">
          Belum ada artikel yang dipublikasikan.
        </div>
      @endforelse
    </div>
  </div>
</section>

<!-- Section 7: FAQ -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Tanya Jawab</span>
      <h2 class="section-title">Pertanyaan yang Sering Diajukan (FAQ)</h2>
      <p class="section-sub">Informasi faktual dan transparan mengenai cara kerja teknis platform LokaTaut.</p>
    </div>

    <div class="faq-list">
      <div class="faq-item active">
        <button type="button" class="faq-question" aria-expanded="true" aria-controls="faq-ans-1" id="faq-q-1">
          <span>Apa itu LokaTaut?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-1" role="region" aria-labelledby="faq-q-1">
          LokaTaut adalah platform utilitas tautan terorganisir dan pengalihan safelink privat. Platform ini digunakan untuk memendekkan alamat web, melindungi berkas unduhan dari perayapan bot liar, serta melacak analitik performa kunjungan secara terpusat.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question" aria-expanded="false" aria-controls="faq-ans-2" id="faq-q-2">
          <span>Bagaimana cara kerja shortlink di LokaTaut?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-2" role="region" aria-labelledby="faq-q-2">
          Setiap tautan yang dibuat diberikan slug unik (misalnya <code>/s/installer-v2</code>). Ketika pengunjung membuka tautan tersebut, server mencocokkan slug dengan URL asli di database, lalu mengarahkan pengunjung ke tujuan yang tepat.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question" aria-expanded="false" aria-controls="faq-ans-3" id="faq-q-3">
          <span>Mengapa pengunjung harus melewati hitung mundur (countdown)?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-3" role="region" aria-labelledby="faq-q-3">
          Countdown bertindak sebagai rate limiting alami untuk memperlambat otomatisasi bot dan memastikan sesi kunjungan divalidasi dengan aman di sisi server sebelum berkas unduhan diserahkan.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question" aria-expanded="false" aria-controls="faq-ans-4" id="faq-q-4">
          <span>Apakah LokaTaut menyimpan file fisik pengguna?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-4" role="region" aria-labelledby="faq-q-4">
          Tidak. LokaTaut bukan layanan penyimpanan awan (cloud storage). LokaTaut hanya mengelola alamat URL pengalihan. File fisik Anda tetap tersimpan di server penyimpanan pihak ketiga Anda sendiri.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question" aria-expanded="false" aria-controls="faq-ans-5" id="faq-q-5">
          <span>Apakah URL tujuan asli dapat dilihat oleh peramban sebelum timer selesai?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-5" role="region" aria-labelledby="faq-q-5">
          Tidak. Pada halaman safelink LokaTaut, URL tujuan asli disimpan dalam bentuk terenkripsi AES-256 dan tidak pernah disertakan ke dalam kode HTML maupun skrip JavaScript peramban sebelum verifikasi timer tuntas di server.
        </div>
      </div>

      <div class="faq-item">
        <button type="button" class="faq-question" aria-expanded="false" aria-controls="faq-ans-6" id="faq-q-6">
          <span>Apakah pengunjung umum dapat membuat shortlink di situs ini?</span>
          <span class="faq-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </button>
        <div class="faq-answer" id="faq-ans-6" role="region" aria-labelledby="faq-q-6">
          LokaTaut beroperasi sebagai platform privat milik pemilik tunggal (<em>single owner</em>). Pembuatan tautan hanya dapat dilakukan melalui panel kontrol administrator terproteksi untuk menjaga keamanan domain dari penyalahgunaan spam publik.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Section 8: Final CTA -->
<section class="section">
  <div class="container">
    <div class="cta-banner">
      <div style="max-width: 640px; margin: 0 auto;">
        <h2>Mulai Pelajari Pengelolaan Tautan yang Efisien</h2>
        <p>
          Baca panduan lengkap mengenai struktur shortlink, tips membagikan file secara rapi, dan keamanan siber di pusat edukasi LokaTaut.
        </p>
        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
          <a href="{{ route('blog.index') }}" class="btn btn-primary btn-lg">Jelajahi Artikel Edukasi</a>
          <a href="{{ route('contact') }}" class="btn btn-secondary btn-lg" style="background: rgba(255,255,255,0.12); color: #ffffff !important; border-color: rgba(255,255,255,0.25);">Hubungi Kami</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
