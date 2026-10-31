<aside id="app-sidebar" class="app-sidebar">
  <div class="sidebar-brand">
    <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-main);">
      <div class="brand-icon" aria-hidden="true">L</div>
      <span class="brand-text">LokaTaut</span>
      <span class="brand-badge">Admin</span>
    </a>

    <!-- Mobile Close Button -->
    <button id="admin-sidebar-close" class="sidebar-close-btn" aria-label="Tutup Menu Sidebar">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M18 6L6 18M6 6l12 12"/>
      </svg>
    </button>
  </div>

  <nav class="sidebar-nav" aria-label="Navigasi Admin">
    <span class="nav-label">Navigasi Utama</span>
    
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
      Dashboard
    </a>

    <a href="{{ route('admin.links.index') }}" class="nav-item {{ request()->routeIs('admin.links.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
      Manajemen Tautan
    </a>

    <a href="{{ route('admin.analytics.index') }}" class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      Analitik Trafik
    </a>

    <span class="nav-label">Konten & Publikasi</span>

    <a href="{{ route('admin.articles.index') }}" class="nav-item {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
      Artikel Blog
    </a>

    <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
      Kategori Topik
    </a>

    <span class="nav-label">Monetisasi & Konfigurasi</span>

    <a href="{{ route('admin.ads.index') }}" class="nav-item {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM8 15h2v-2h2v-2h-2V9H8v6zm6 0h2v-6h-2v6z"/></svg>
      Slot Iklan
    </a>

    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
      Pengaturan
    </a>

    <a href="{{ route('admin.security.logs') }}" class="nav-item {{ request()->routeIs('admin.security.*') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
      Audit Keamanan
    </a>

    <span class="nav-label">Situs Publik</span>

    <a href="{{ route('home') }}" target="_blank" class="nav-item">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
      Buka Situs Publik ↗
    </a>
  </nav>

  <div class="sidebar-footer">
    <div>LokaTaut v1.2.2</div>
    <div>Private Owner Platform</div>
  </div>
</aside>
