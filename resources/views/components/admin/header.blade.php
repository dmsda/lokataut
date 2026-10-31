<header class="app-header">
  <div style="display: flex; align-items: center; gap: 12px;">
    <!-- Mobile Sidebar Toggle -->
    <button id="admin-sidebar-toggle" class="sidebar-toggle-btn" aria-label="Buka Menu Sidebar" aria-expanded="false">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <div class="header-title">
      @yield('page_title', 'Admin Dashboard')
    </div>
  </div>

  <div class="header-actions">
    <!-- Theme Switcher -->
    <x-theme-toggle :compact="true" />

    <a href="{{ route('admin.profile') }}" class="admin-pill" title="Profil Administrator">
      <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
      <span class="admin-email-text">{{ auth()->user()->email ?? 'admin' }}</span>
    </a>

    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
      @csrf
      <button type="submit" class="btn btn-secondary btn-sm" title="Keluar dari sesi admin">Keluar</button>
    </form>
  </div>
</header>
