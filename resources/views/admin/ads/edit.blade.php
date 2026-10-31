@extends('layouts.admin')

@section('title', 'Edit Slot Iklan — ' . $slot->name)
@section('page_title', 'Edit Penempatan Slot Iklan')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
  <form action="{{ route('admin.ads.update', $slot) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="name" class="form-label">Nama Slot</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $slot->name) }}" required>
      </div>

      <div class="form-group">
        <label for="position" class="form-label">Posisi Halaman</label>
        <select name="position" id="position" class="form-control" required>
          <option value="header" {{ old('position', $slot->position) === 'header' ? 'selected' : '' }}>Header (Atas Halaman)</option>
          <option value="above_timer" {{ old('position', $slot->position) === 'above_timer' ? 'selected' : '' }}>Above Timer (Tepat Di Atas Hitung Mundur)</option>
          <option value="below_timer" {{ old('position', $slot->position) === 'below_timer' ? 'selected' : '' }}>Below Timer (Di Bawah Hitung Mundur / Jarak Aman 30px)</option>
          <option value="footer" {{ old('position', $slot->position) === 'footer' ? 'selected' : '' }}>Footer (Bawah Halaman)</option>
        </select>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="device_target" class="form-label">Target Perangkat</label>
        <select name="device_target" id="device_target" class="form-control" required>
          <option value="all" {{ old('device_target', $slot->device_target) === 'all' ? 'selected' : '' }}>Semua Perangkat (Desktop & Mobile)</option>
          <option value="desktop" {{ old('device_target', $slot->device_target) === 'desktop' ? 'selected' : '' }}>Hanya Desktop</option>
          <option value="mobile" {{ old('device_target', $slot->device_target) === 'mobile' ? 'selected' : '' }}>Hanya Smartphone (Mobile)</option>
        </select>
      </div>

      <div class="form-group">
        <label for="sort_order" class="form-label">Urutan Tampil (Sort Order)</label>
        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $slot->sort_order) }}" min="0" max="255" required>
      </div>
    </div>

    <div class="form-group">
      <label for="is_active" class="form-label">Status Penayangan</label>
      <select name="is_active" id="is_active" class="form-control">
        <option value="1" {{ old('is_active', $slot->is_active) ? 'selected' : '' }}>Aktif (Tayangkan Iklan)</option>
        <option value="0" {{ !old('is_active', $slot->is_active) ? 'selected' : '' }}>Nonaktif (Sembunyikan Iklan)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="ad_code" class="form-label">Tag Kode Script Iklan (HTML / JavaScript)</label>
      <textarea name="ad_code" id="ad_code" class="form-control" rows="8" style="font-family: monospace; font-size: 13px;" placeholder="<script type='text/javascript'>...</script>">{{ old('ad_code', $slot->ad_code) }}</textarea>
      <div class="form-help">Setiap perubahan kode iklan dicatat ke dalam security_logs audit trail secara otomatis.</div>
    </div>

    <div class="form-group">
      <label for="notes" class="form-label">Catatan Admin</label>
      <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes', $slot->notes) }}" placeholder="Catatan internal pengenal slot...">
    </div>

    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
      <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan Kode Iklan</button>
    </div>
  </form>
</div>
@endsection

