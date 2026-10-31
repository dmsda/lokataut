<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  {{-- Anti-FOUT Theme Initializer --}}
  <x-theme-script />

  <title>Masuk Administrator — LokaTaut</title>
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
      position: relative;
    }
    .login-theme-bar {
      position: absolute;
      top: 20px;
      right: 20px;
    }
    .login-card {
      width: 100%;
      max-width: 420px;
      background: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 36px 32px;
      box-shadow: var(--shadow-lg);
    }
    .login-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }
    .brand-icon-lg {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 850;
      font-size: 20px;
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }
    .back-home-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: var(--text-dim);
      margin-top: 24px;
      justify-content: center;
      width: 100%;
    }
    .back-home-link:hover {
      color: var(--primary);
    }
  </style>
</head>
<body>
  <div class="login-theme-bar">
    <x-theme-toggle :compact="true" />
  </div>

  <div class="login-card">
    <div class="login-brand">
      <div class="brand-icon-lg" aria-hidden="true">L</div>
      <div>
        <div class="brand-text" style="font-size: 22px;">LokaTaut</div>
        <div style="font-size: 12px; color: var(--text-dim);">Single-Owner Link Management</div>
      </div>
    </div>

    @include('components.admin.alert')

    <form action="{{ route('login.attempt') }}" method="POST">
      @csrf
      
      <div class="form-group">
        <label for="email" class="form-label">Alamat Email Administrator</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', 'dimas@lokataut.com') }}" required autofocus autocomplete="username">
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Kata Sandi</label>
        <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
      </div>

      <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="cursor: pointer;">
        <label for="remember" style="font-size: 13px; color: var(--text-muted); cursor: pointer; user-select: none;">Ingat sesi saya</label>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14px;">Masuk ke Panel Admin</button>
    </form>

    <a href="{{ route('home') }}" class="back-home-link">
      &larr; Kembali ke Situs Publik
    </a>
  </div>

  <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
