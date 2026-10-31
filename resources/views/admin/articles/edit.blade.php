@extends('layouts.admin')

@section('title', 'Edit Artikel')
@section('page_title', 'Perbarui Artikel')

@section('content')
<div class="card" style="max-width: 960px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
    <div>
      <span style="font-size: 13px; color: var(--text-dim);">Sedang menyunting:</span>
      <h2 style="font-size: 18px; margin: 2px 0 0; color: var(--text-primary);">{{ $article->title }}</h2>
    </div>
    @if ($article->status === 'published')
      <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat di Web ↗</a>
    @endif
  </div>

  <form action="{{ route('admin.articles.update', $article->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="title" class="form-label">Judul Artikel <span style="color: var(--danger);">*</span></label>
      <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $article->title) }}" required autofocus>
      @error('title')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="slug" class="form-label">Slug URL <span style="color: var(--danger);">*</span></label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $article->slug) }}" required>
        <div class="form-help">Hanya huruf kecil, angka, dan tanda hubung (-). Mengubah slug dapat mempengaruhi bookmark lama.</div>
        @error('slug')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="category_id" class="form-label">Kategori <span style="color: var(--danger);">*</span></label>
        <select name="category_id" id="category_id" class="form-control" required>
          <option value="">-- Pilih Kategori --</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
        @error('category_id')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="status" class="form-label">Status Publikasi <span style="color: var(--danger);">*</span></label>
        <select name="status" id="status" class="form-control" required>
          <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published (Terbit)</option>
          <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
          <option value="scheduled" {{ old('status', $article->status) === 'scheduled' ? 'selected' : '' }}>Scheduled (Terjadwal)</option>
        </select>
        @error('status')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="published_at" class="form-label">Waktu Publikasi</label>
        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
        <div class="form-help">Waktu artikel dirilis ke publik.</div>
        @error('published_at')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="featured_image" class="form-label">URL Gambar Sampul (Opsional)</label>
        <input type="url" name="featured_image" id="featured_image" class="form-control" value="{{ old('featured_image', $article->featured_image) }}" placeholder="https://images.unsplash.com/photo-...">
        @error('featured_image')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="image_alt" class="form-label">Teks Alt Gambar</label>
        <input type="text" name="image_alt" id="image_alt" class="form-control" value="{{ old('image_alt', $article->image_alt) }}" placeholder="Deskripsi gambar untuk aksesibilitas dan SEO">
        @error('image_alt')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="form-group">
      <label for="excerpt" class="form-label">Ringkasan / Excerpt</label>
      <textarea name="excerpt" id="excerpt" rows="2" class="form-control" placeholder="Ringkasan singkat artikel...">{{ old('excerpt', $article->excerpt) }}</textarea>
      <div class="form-help">Maksimal 300 karakter. Ditampilkan pada listing blog dan open graph.</div>
      @error('excerpt')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
        <label for="content" class="form-label" style="margin-bottom: 0;">Isi Konten Artikel (Markdown) <span style="color: var(--danger);">*</span></label>
        <span style="font-size: 12px; color: var(--text-dim);">Estimasi membaca: ~{{ $article->reading_time }} menit</span>
      </div>
      <textarea name="content" id="content" rows="18" class="form-control" style="font-family: ui-monospace, Menlo, Monaco, Consolas, monospace; font-size: 13px; line-height: 1.6;" required>{{ old('content', $article->content) }}</textarea>
      @error('content')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div style="border-top: 1px solid var(--border); padding-top: 20px; margin-top: 20px;">
      <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Pengaturan SEO & Penulis</h3>
      
      <div class="grid-cols-2" style="margin-bottom: 0;">
        <div class="form-group">
          <label for="meta_title" class="form-label">Meta Title (SEO)</label>
          <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $article->meta_title) }}" placeholder="Biarkan kosong untuk judul default">
          @error('meta_title')
            <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="author_name" class="form-label">Nama Penulis</label>
          <input type="text" name="author_name" id="author_name" class="form-control" value="{{ old('author_name', $article->author_name) }}">
          @error('author_name')
            <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label for="meta_description" class="form-label">Meta Description (SEO)</label>
        <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $article->meta_description) }}</textarea>
        @error('meta_description')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
      <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>
@endsection

