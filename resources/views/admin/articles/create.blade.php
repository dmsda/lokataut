@extends('layouts.admin')

@section('title', 'Tulis Artikel Baru')
@section('page_title', 'Tulis Artikel Baru')

@section('content')
<div class="card" style="max-width: 960px; margin: 0 auto;">
  <form action="{{ route('admin.articles.store') }}" method="POST">
    @csrf

    <div class="form-group">
      <label for="title" class="form-label">Judul Artikel <span style="color: var(--danger);">*</span></label>
      <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Contoh: 7 Cara Mengamankan Tautan Unduhan dari Phishing dan Malware" required autofocus>
      @error('title')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="slug" class="form-label">Kustom Slug (URL Ramah SEO)</label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="Biarkan kosong untuk auto-generate dari judul">
        <div class="form-help">Hanya huruf kecil, angka, dan tanda hubung (-). Contoh: amankan-tautan-unduhan-phishing</div>
        @error('slug')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="category_id" class="form-label">Kategori <span style="color: var(--danger);">*</span></label>
        <select name="category_id" id="category_id" class="form-control" required>
          <option value="">-- Pilih Kategori --</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
          <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Langsung Terbit)</option>
          <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
          <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled (Terjadwal)</option>
        </select>
        @error('status')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="published_at" class="form-label">Waktu Publikasi (Opsional)</label>
        <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at') }}">
        <div class="form-help">Jika kosong saat memilih Published, waktu saat ini akan digunakan.</div>
        @error('published_at')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="grid-cols-2" style="margin-bottom: 0;">
      <div class="form-group">
        <label for="featured_image" class="form-label">URL Gambar Sampul (Opsional)</label>
        <input type="url" name="featured_image" id="featured_image" class="form-control" value="{{ old('featured_image') }}" placeholder="https://images.unsplash.com/photo-...">
        @error('featured_image')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="image_alt" class="form-label">Teks Alt Gambar</label>
        <input type="text" name="image_alt" id="image_alt" class="form-control" value="{{ old('image_alt') }}" placeholder="Deskripsi gambar untuk aksesibilitas dan SEO">
        @error('image_alt')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="form-group">
      <label for="excerpt" class="form-label">Ringkasan / Excerpt</label>
      <textarea name="excerpt" id="excerpt" rows="2" class="form-control" placeholder="Ringkasan singkat artikel untuk kartu blog dan cuplikan media sosial...">{{ old('excerpt') }}</textarea>
      <div class="form-help">Maksimal 300 karakter. Jika kosong, akan diambil otomatis dari paragraf awal konten.</div>
      @error('excerpt')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
        <label for="content" class="form-label" style="margin-bottom: 0;">Isi Konten Artikel (Markdown Format) <span style="color: var(--danger);">*</span></label>
        <span style="font-size: 12px; color: var(--text-dim);">Dukungan Markdown murni: ## Subjudul, **tebal**, *miring*, - list, `kode`</span>
      </div>
      <textarea name="content" id="content" rows="16" class="form-control" style="font-family: ui-monospace, Menlo, Monaco, Consolas, monospace; font-size: 13px; line-height: 1.6;" placeholder="# Judul Bagian&#10;&#10;Tuliskan paragraf pembuka di sini...&#10;&#10;## Subjudul Penting&#10;&#10;- Poin pertama&#10;- Poin kedua&#10;&#10;```php&#10;// cuplikan kode jika diperlukan&#10;```" required>{{ old('content') }}</textarea>
      @error('content')
        <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
      @enderror
    </div>

    <div style="border-top: 1px solid var(--border); padding-top: 20px; margin-top: 20px;">
      <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Pengaturan SEO & Penulis</h3>
      
      <div class="grid-cols-2" style="margin-bottom: 0;">
        <div class="form-group">
          <label for="meta_title" class="form-label">Meta Title (SEO)</label>
          <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title') }}" placeholder="Biarkan kosong untuk menggunakan judul artikel">
          @error('meta_title')
            <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="author_name" class="form-label">Nama Penulis</label>
          <input type="text" name="author_name" id="author_name" class="form-control" value="{{ old('author_name', 'Dimas Aditya') }}" placeholder="Dimas Aditya">
          @error('author_name')
            <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label for="meta_description" class="form-label">Meta Description (SEO)</label>
        <textarea name="meta_description" id="meta_description" rows="2" class="form-control" placeholder="Deskripsi meta untuk hasil penelusuran Google (150-160 karakter direkomendasikan)...">{{ old('meta_description') }}</textarea>
        @error('meta_description')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border);">
      <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan Artikel</button>
    </div>
  </form>
</div>
@endsection

