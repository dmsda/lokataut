@extends('layouts.admin')

@section('title', 'Kategori Artikel')
@section('page_title', 'Kelola Kategori Artikel & Topik')

@section('content')
<div class="grid-cols-2" style="align-items: start; gap: 24px;">
  <!-- Form Tambah Kategori -->
  <div class="card">
    <div class="card-header" style="margin-bottom: 16px;">
      <div class="card-title">Tambah Kategori Baru</div>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="name" class="form-label">Nama Kategori <span style="color: var(--danger);">*</span></label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Keamanan Siber" required>
        @error('name')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="slug" class="form-label">Slug URL (Opsional)</label>
        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}" placeholder="Biarkan kosong untuk auto-generate">
        <div class="form-help">Hanya huruf kecil, angka, dan tanda hubung (-). Contoh: keamanan-siber</div>
        @error('slug')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="description" class="form-label">Deskripsi Topik (Opsional)</label>
        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Penjelasan singkat fokus topik pada kategori ini...">{{ old('description') }}</textarea>
        @error('description')
          <div style="color: var(--danger); font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">+ Tambah Kategori</button>
    </form>
  </div>

  <!-- Daftar Kategori -->
  <div class="card">
    <div class="card-header" style="margin-bottom: 16px;">
      <div class="card-title">Daftar Kategori Tersedia ({{ $categories->count() }})</div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nama & Deskripsi</th>
            <th>Slug</th>
            <th style="text-align: center;">Jumlah Artikel</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($categories as $cat)
            <tr>
              <td>
                <div style="font-weight: 600; color: var(--text-primary);">{{ $cat->name }}</div>
                @if ($cat->description)
                  <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($cat->description, 60) }}</div>
                @endif
              </td>
              <td>
                <code style="font-size: 12px; color: var(--accent);">{{ $cat->slug }}</code>
              </td>
              <td style="text-align: center;">
                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; font-weight: 600;">
                  {{ $cat->articles_count }}
                </span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->slug) }}', '{{ addslashes($cat->description ?? '') }}')">Edit</button>
                  @if ($cat->articles_count === 0)
                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="margin: 0;" data-confirm="Hapus kategori '{{ $cat->name }}'?">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align: center; color: var(--text-dim); padding: 32px;">Belum ada kategori yang dibuat.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
  <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); width: 100%; max-width: 480px; padding: 24px; box-shadow: var(--shadow-lg);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--text-main);">Edit Kategori</h3>
      <button type="button" onclick="closeEditModal()" style="background: none; border: none; color: var(--text-dim); font-size: 20px; cursor: pointer;" aria-label="Tutup Dialog">&times;</button>
    </div>

    <form id="editCategoryForm" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="edit_name" class="form-label">Nama Kategori <span style="color: var(--danger);">*</span></label>
        <input type="text" name="name" id="edit_name" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="edit_slug" class="form-label">Slug URL <span style="color: var(--danger);">*</span></label>
        <input type="text" name="slug" id="edit_slug" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="edit_description" class="form-label">Deskripsi Topik</label>
        <textarea name="description" id="edit_description" rows="3" class="form-control"></textarea>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px;">
        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function editCategory(id, name, slug, description) {
  const modal = document.getElementById('editCategoryModal');
  const form = document.getElementById('editCategoryForm');
  form.action = `/admin/categories/${id}`;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_slug').value = slug;
  document.getElementById('edit_description').value = description;
  modal.style.display = 'flex';
}

function closeEditModal() {
  const modal = document.getElementById('editCategoryModal');
  if (modal) modal.style.display = 'none';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeEditModal();
});

document.addEventListener('click', function(e) {
  const modal = document.getElementById('editCategoryModal');
  if (modal && e.target === modal) closeEditModal();
});
</script>
@endsection

