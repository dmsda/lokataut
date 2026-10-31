@extends('layouts.admin')

@section('title', 'Edit Tautan /s/' . $link->slug)
@section('page_title', 'Perbarui Konfigurasi Tautan')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
  <form action="{{ route('admin.links.update', $link) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="destination_url" class="form-label">URL Tujuan Asli (Wajib)</label>
      <input type="url" name="destination_url" id="destination_url" class="form-control" value="{{ old('destination_url', $link->destination_url) }}" required autofocus>
      <div class="form-help">URL terenkripsi AES-256. Pengunjung hanya dialihkan ke URL ini setelah melewati validasi timer server.</div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="title" class="form-label">Judul / Label Tautan</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $link->title) }}">
      </div>

      <div class="form-group">
        <label for="slug" class="form-label">Slug Tautan (/s/{slug})</label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $link->slug) }}" required>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="source_website" class="form-label">Website Sumber</label>
        <input type="text" name="source_website" id="source_website" class="form-control" value="{{ old('source_website', $link->source_website) }}">
      </div>

      <div class="form-group">
        <label for="countdown_seconds" class="form-label">Durasi Hitung Mundur (Detik)</label>
        <input type="number" name="countdown_seconds" id="countdown_seconds" class="form-control" value="{{ old('countdown_seconds', $link->countdown_seconds) }}" min="5" max="60" required>
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="expires_at" class="form-label">Masa Kedaluwarsa Tautan</label>
        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control" value="{{ old('expires_at', $link->expires_at?->format('Y-m-d\TH:i')) }}">
      </div>

      <div class="form-group">
        <label for="status" class="form-label">Status Tautan</label>
        <select name="status" id="status" class="form-control">
          <option value="active" {{ old('status', $link->status) === 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="inactive" {{ old('status', $link->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="meta_description" class="form-label">Deskripsi Meta / Catatan Internal</label>
      <textarea name="meta_description" id="meta_description" class="form-control" rows="2">{{ old('meta_description', $link->meta_description) }}</textarea>
    </div>

    <div style="display: flex; gap: 12px; justify-content: space-between; margin-top: 24px;">
      <button type="button" class="btn btn-secondary" data-copy-url="{{ url('/s/' . $link->slug) }}">Salin Tautan Publik</button>

      <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </div>
  </form>
</div>
@endsection

