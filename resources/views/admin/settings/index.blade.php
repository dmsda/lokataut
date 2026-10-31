@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Konfigurasi Sistem Global')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
  <form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="site_name" class="form-label">Nama Situs / Platform</label>
      <input type="text" name="site_name" id="site_name" class="form-control" value="{{ old('site_name', $settings['site_name']->value ?? 'LokaTaut') }}" required>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="default_countdown_seconds" class="form-label">Default Durasi Countdown (Detik)</label>
        <input type="number" name="default_countdown_seconds" id="default_countdown_seconds" class="form-control" value="{{ old('default_countdown_seconds', $settings['default_countdown_seconds']->value ?? 10) }}" min="5" max="60" required>
        <div class="form-help">Berlaku untuk tautan baru yang dibuat.</div>
      </div>

      <div class="form-group">
        <label for="session_ttl_minutes" class="form-label">Batas Waktu Sesi Token (Menit)</label>
        <input type="number" name="session_ttl_minutes" id="session_ttl_minutes" class="form-control" value="{{ old('session_ttl_minutes', $settings['session_ttl_minutes']->value ?? 30) }}" min="5" max="120" required>
        <div class="form-help">Sesi kedaluwarsa jika timer tidak diselesaikan dalam jangka waktu ini.</div>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="turnstile_mode" class="form-label">Mode Proteksi Cloudflare Turnstile</label>
        <select name="turnstile_mode" id="turnstile_mode" class="form-control" required>
          @php $currMode = $settings['turnstile_mode']->value ?? 'enabled'; @endphp
          <option value="enabled" {{ old('turnstile_mode', $currMode) === 'enabled' ? 'selected' : '' }}>Aktif Penuh (Verifikasi Bot Normal)</option>
          <option value="test" {{ old('turnstile_mode', $currMode) === 'test' ? 'selected' : '' }}>Mode Testing (Kunci Dummy Cloudflare)</option>
          <option value="bypass" {{ old('turnstile_mode', $currMode) === 'bypass' ? 'selected' : '' }}>Emergency Bypass (Bypass Verifikasi Turnstile)</option>
        </select>
        <div class="form-help">Pilih Emergency Bypass jika API Cloudflare sedang mengalami gangguan massal.</div>
      </div>

      <div class="form-group">
        <label for="adsterra_enabled" class="form-label">Status Tayangan Jaringan Iklan</label>
        @php $adActive = (bool) ($settings['adsterra_enabled']->value ?? true); @endphp
        <select name="adsterra_enabled" id="adsterra_enabled" class="form-control" required>
          <option value="1" {{ old('adsterra_enabled', $adActive ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif (Tayangkan Slot Iklan)</option>
          <option value="0" {{ old('adsterra_enabled', $adActive ? '1' : '0') === '0' ? 'selected' : '' }}>Nonaktif (Matikan Semua Iklan)</option>
        </select>
        <div class="form-help">Dapat dinonaktifkan sewaktu-waktu tanpa menghapus kode tag.</div>
      </div>
    </div>

    <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
      <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
    </div>
  </form>
</div>
@endsection

