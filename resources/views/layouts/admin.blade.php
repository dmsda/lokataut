<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  {{-- Anti-FOUT Theme Initializer --}}
  <x-theme-script />

  <title>@yield('title', 'Admin Panel') — LokaTaut</title>
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  @stack('styles')
</head>
<body>
  <div class="app-layout">
    <!-- Mobile Sidebar Backdrop -->
    <div id="admin-sidebar-backdrop" class="sidebar-backdrop" aria-hidden="true"></div>

    @include('components.admin.sidebar')
    
    <div class="app-main">
      @include('components.admin.header')
      
      <main class="content-wrapper">
        @include('components.admin.alert')
        @yield('content')
      </main>
    </div>
  </div>

  <script src="{{ asset('js/theme.js') }}"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  @stack('scripts')
</body>
</html>
