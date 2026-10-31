@extends('layouts.admin')

@section('title', 'Profil & Keamanan')
@section('page_title', 'Pengaturan Profil & Ganti Kata Sandi')

@section('content')
<div class="grid-cols-2" style="max-width: 900px; margin: 0 auto;">
  <!-- Profile Info Card -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Informasi Administrator Tunggal</div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
      <div>
        <div class="stat-label">Nama Pemilik</div>
        <div style="font-size: 16px; font-weight: 600;">{{ $admin->name }}</div>
      </div>

      <div>
        <div class="stat-label">Alamat Email Login</div>
        <div style="font-size: 16px; font-weight: 600;">{{ $admin->email }}</div>
      </div>

      <div>
        <div class="stat-label">Login Terakhir</div>
        <div style="font-size: 14px; color: var(--text-muted);">
          {{ $admin->last_login_at ? $admin->last_login_at->translatedFormat('d F Y, H:i:s') : 'Belum pernah login' }}
        </div>
      </div>

      <div>
        <div class="stat-label">Alamat IP Terakhir</div>
        <div style="font-size: 14px; color: var(--text-muted); font-family: monospace;">
          {{ $admin->last_login_ip ?? '127.0.0.1' }}
        </div>
      </div>
    </div>
  </div>

  <!-- Change Password Form (FR-AUTH-004) -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ganti Kata Sandi (FR-AUTH-004)</div>
    </div>

    <form action="{{ route('admin.profile.password') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
        <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password">
      </div>

      <div class="form-group">
        <label for="new_password" class="form-label">Kata Sandi Baru</label>
        <input type="password" name="new_password" id="new_password" class="form-control" required autocomplete="new-password">
        <div class="form-help">Minimal 12 karakter, memuat kombinasi huruf besar, huruf kecil, angka, dan simbol khusus (!@#$%^&*).</div>
      </div>

      <div class="form-group">
        <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required autocomplete="new-password">
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">Perbarui Kata Sandi</button>
    </form>
  </div>
</div>
@endsection

