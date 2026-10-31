@extends('layouts.admin')

@section('title', 'Buat Tautan Baru')
@section('page_title', 'Buat Tautan Safelink Baru')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
  <form action="{{ route('admin.links.store') }}" method="POST">
    @csrf

    <div class="form-group">
      <label for="destination_url" class="form-label">URL Tujuan Asli (Wajib)</label>
      <input type="url" name="destination_url" id="destination_url" class="form-control" value="{{ old('destination_url') }}" placeholder="https://example.com/files/download.zip" required autofocus>
      <div class="form-help">URL ini dienkripsi dengan standar AES-256 dan TIDAK PERNAH dikirimkan ke peramban pengunjung sebelum timer selesai.</div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="title" class="form-label">Judul / Label Tautan (Opsional)</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: Adobe Photoshop 2026 Installer">
      </div>

      <div class="form-group">
        <label for="slug" class="form-label">Kustom Slug (Opsional)</label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="Biarkan kosong untuk kode acak 6 karakter">
        <div class="form-help">Hanya huruf, angka, dan tanda hubung (-). Contoh: photoshop-v26</div>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="source_website" class="form-label">Website Sumber (Tagging)</label>
        <input type="text" name="source_website" id="source_website" class="form-control" value="{{ old('source_website') }}" placeholder="Contoh: softwareku.id">
        <div class="form-help">Berguna untuk memfilter performa konversi per situs pengirim.</div>
      </div>

      <div class="form-group">
        <label for="countdown_seconds" class="form-label">Durasi Hitung Mundur (Detik)</label>
        <input type="number" name="countdown_seconds" id="countdown_seconds" class="form-control" value="{{ old('countdown_seconds', 10) }}" min="5" max="60" required>
        <div class="form-help">Minimal 5 detik, maksimal 60 detik (Rekomendasi: 10 detik).</div>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="expires_at" class="form-label">Masa Kedaluwarsa Tautan (Opsional)</label>
        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at') }}">
        <div class="form-help">Biarkan kosong jika tautan berlaku selamanya.</div>
      </div>

      <div class="form-group">
        <label for="status" class="form-label">Status Awal</label>
        <select name="status" id="status" class="form-control">
          <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif (Dapat Diakses)</option>
          <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif (Draft / Dimatikan)</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="meta_description" class="form-label">Deskripsi Meta / Catatan Internal</label>
      <textarea name="meta_description" id="meta_description" class="form-control" rows="2" placeholder="Catatan internal pengingat tautan...">{{ old('meta_description') }}</textarea>
    </div>

    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
      <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan & Terbitkan Tautan</button>
    </div>
  </form>
</div>
@endsection

