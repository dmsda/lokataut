<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="robots" content="noindex, nofollow, noarchive">
  
  {{-- Anti-FOUT Theme Initializer --}}
  <x-theme-script />

  <title>@yield('title', 'Tautan Aman') — LokaTaut</title>
  <link rel="stylesheet" href="{{ asset('css/safelink.css') }}">
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  @stack('styles')
</head>
<body>
  <header class="safelink-header">
    <a href="{{ route('home') }}" class="safelink-brand" aria-label="LokaTaut Beranda">
      <div class="safelink-logo-icon" aria-hidden="true">L</div>
      <span class="safelink-brand-name">{{ config('app.name', 'LokaTaut') }}</span>
    </a>

    <!-- Theme Switcher -->
    <x-theme-toggle :compact="true" />
  </header>

  <div class="safelink-container">
    @yield('content')
  </div>

  <footer class="safelink-footer">
    <p>&copy; {{ date('Y') }} {{ config('app.name', 'LokaTaut') }} &bull; Layanan Pengalihan Tautan Aman &amp; Privat.</p>
  </footer>

  <script src="{{ asset('js/theme.js') }}"></script>
  <script src="{{ asset('js/safelink.js') }}"></script>
  @stack('scripts')
</body>
</html>
