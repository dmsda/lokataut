<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  {{-- Anti-FOUT Theme Initializer --}}
  <x-theme-script />

  <title>@yield('meta_title', 'LokaTaut — Pengelolaan Tautan Digital & Safelink Terpercaya')</title>
  <meta name="description" content="@yield('meta_description', 'LokaTaut adalah platform manajemen tautan dan pengalihan aman. Kelola shortlink, atur URL unduhan terproteksi, dan pantau analitik trafik.')">
  <link rel="canonical" href="@yield('canonical_url', url()->current())">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="@yield('og_type', 'website')">
  <meta property="og:url" content="@yield('canonical_url', url()->current())">
  <meta property="og:title" content="@yield('meta_title', 'LokaTaut — Pengelolaan Tautan Digital & Safelink Terpercaya')">
  <meta property="og:description" content="@yield('meta_description', 'LokaTaut adalah platform manajemen tautan dan pengalihan aman.')">
  <meta property="og:site_name" content="LokaTaut">
  <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))">

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('meta_title', 'LokaTaut — Pengelolaan Tautan Digital & Safelink Terpercaya')">
  <meta name="twitter:description" content="@yield('meta_description', 'LokaTaut adalah platform manajemen tautan dan pengalihan aman.')">
  <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.png'))">

  <!-- JSON-LD Structured Data -->
  <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
    "@type": "WebSite",
    "name": "LokaTaut",
    "url": "{{ url('/') }}",
    "description": "Platform manajemen tautan digital dan pengalihan safelink terpercaya.",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "{{ url('/blog') }}?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>
  @stack('schema')

  <!-- Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/site.css') }}">
  @stack('styles')
</head>
<body>
  <!-- Global Header -->
  <header class="site-header">
    <div class="container">
      <div class="nav-wrapper">
        <a href="{{ route('home') }}" class="brand-link" aria-label="LokaTaut Beranda">
          <div class="brand-logo-icon" aria-hidden="true">L</div>
          <span class="brand-name">LokaTaut</span>
        </a>

        <nav id="main-nav" class="main-nav" aria-label="Navigasi Utama">
          <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
          <a href="{{ route('home') }}#features" class="nav-link">Fitur</a>
          <a href="{{ route('home') }}#how-it-works" class="nav-link">Cara Kerja</a>
          <a href="{{ route('help') }}" class="nav-link {{ request()->routeIs('help') ? 'active' : '' }}">Panduan</a>
          <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Artikel</a>
          <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Tentang Kami</a>
        </nav>

        <div class="nav-actions">
          <!-- Theme Switcher Desktop -->
          <x-theme-toggle :compact="true" />

          <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk Admin</a>
          <a href="{{ route('help') }}" class="btn btn-primary btn-sm">Mulai Pelajari</a>
          
          <button id="mobile-menu-btn" class="mobile-menu-btn" aria-label="Buka Menu Navigasi" aria-expanded="false" aria-controls="mobile-nav-drawer">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Backdrop & Menu -->
  <div id="mobile-nav-backdrop" class="mobile-nav-backdrop" aria-hidden="true"></div>
  <aside id="mobile-nav-drawer" class="mobile-nav-drawer" aria-label="Menu Navigasi Mobile">
    <div class="mobile-drawer-header">
      <div class="brand-link">
        <div class="brand-logo-icon">L</div>
        <span class="brand-name">LokaTaut</span>
      </div>
      <button id="mobile-drawer-close" class="mobile-drawer-close" aria-label="Tutup Menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <nav class="mobile-nav-links">
      <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
      <a href="{{ route('home') }}#features" class="mobile-nav-link">Fitur Unggulan</a>
      <a href="{{ route('home') }}#how-it-works" class="mobile-nav-link">Alur Kerja</a>
      <a href="{{ route('help') }}" class="mobile-nav-link {{ request()->routeIs('help') ? 'active' : '' }}">Pusat Bantuan</a>
      <a href="{{ route('blog.index') }}" class="mobile-nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Artikel Edukasi</a>
      <a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">Tentang Kami</a>
      <a href="{{ route('contact') }}" class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak Pengelola</a>
    </nav>

    <div class="mobile-drawer-footer">
      <div>
        <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Pilihan Tema</div>
        <x-theme-toggle />
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
        <a href="{{ route('login') }}" class="btn btn-secondary" style="width: 100%;">Masuk Admin</a>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main style="flex: 1;">
    @yield('content')
  </main>

  <!-- Global Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-brand">
            <div class="brand-logo-icon" style="width: 28px; height: 28px; font-size: 14px;">L</div>
            <span style="font-size: 18px; font-weight: 800; color: var(--text-primary);">LokaTaut</span>
          </div>
          <p class="footer-desc">
            Platform utilitas tautan terorganisir dan pengalihan safelink. Membantu pemilik berkas mendistribusikan link secara terpusat, aman dari bot, dan terpantau dengan analitik akurat.
          </p>
        </div>

        <div>
          <h4 class="footer-heading">Navigasi Produk</h4>
          <ul class="footer-links">
            <li><a href="{{ route('home') }}" class="footer-link">Beranda</a></li>
            <li><a href="{{ route('home') }}#features" class="footer-link">Fitur Unggulan</a></li>
            <li><a href="{{ route('home') }}#how-it-works" class="footer-link">Alur Kerja</a></li>
            <li><a href="{{ route('help') }}" class="footer-link">Pusat Bantuan & FAQ</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-heading">Edukasi & Artikel</h4>
          <ul class="footer-links">
            <li><a href="{{ route('blog.index') }}" class="footer-link">Semua Artikel</a></li>
            <li><a href="{{ route('blog.category', 'panduan-shortlink') }}" class="footer-link">Panduan Shortlink</a></li>
            <li><a href="{{ route('blog.category', 'safelink-download') }}" class="footer-link">Safelink & Download</a></li>
            <li><a href="{{ route('blog.category', 'keamanan-digital') }}" class="footer-link">Keamanan Digital</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-heading">Informasi & Hukum</h4>
          <ul class="footer-links">
            <li><a href="{{ route('about') }}" class="footer-link">Tentang LokaTaut</a></li>
            <li><a href="{{ route('contact') }}" class="footer-link">Kontak Kami</a></li>
            <li><a href="{{ route('privacy-policy') }}" class="footer-link">Kebijakan Privasi</a></li>
            <li><a href="{{ route('terms') }}" class="footer-link">Syarat & Ketentuan</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div>&copy; {{ date('Y') }} LokaTaut. Hak cipta dilindungi undang-undang. Platform utilitas tautan privat.</div>
        <div style="display: flex; gap: 16px; align-items: center;">
          <a href="{{ route('sitemap') }}" class="footer-link" style="font-size: 12px;">Sitemap XML</a>
          <a href="{{ route('robots') }}" class="footer-link" style="font-size: 12px;">Robots.txt</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/theme.js') }}"></script>
  <script src="{{ asset('js/site.js') }}"></script>
  @stack('scripts')
</body>
</html>
