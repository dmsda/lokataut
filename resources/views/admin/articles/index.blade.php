@extends('layouts.admin')

@section('title', 'Manajemen Artikel')
@section('page_title', 'Kelola Artikel & Publikasi Edukasi')

@section('content')
<div class="card" style="margin-bottom: 24px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <!-- Filter Tabs -->
    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.articles.index', ['status' => 'all']) }}" class="btn {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Semua ({{ $counts['all'] }})
      </a>
      <a href="{{ route('admin.articles.index', ['status' => 'published']) }}" class="btn {{ $statusFilter === 'published' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Diterbitkan ({{ $counts['published'] }})
      </a>
      <a href="{{ route('admin.articles.index', ['status' => 'draft']) }}" class="btn {{ $statusFilter === 'draft' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Draf ({{ $counts['draft'] }})
      </a>
      <a href="{{ route('admin.articles.index', ['status' => 'trash']) }}" class="btn {{ $statusFilter === 'trash' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Tong Sampah ({{ $counts['trash'] }})
      </a>
    </div>

    <!-- Search & Category Filters -->
    <div style="display: flex; gap: 10px; align-items: center;">
      <form action="{{ route('admin.articles.index') }}" method="GET" style="display: flex; gap: 8px;">
        <input type="hidden" name="status" value="{{ $statusFilter }}">

        <select name="category_id" class="form-control" style="width: 170px; padding: 6px 10px; font-size: 13px;" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>

        <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul..." class="form-control" style="width: 180px; padding: 6px 10px; font-size: 13px;">
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
      </form>

      <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">+ Tulis Artikel</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Judul Artikel</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Waktu Baca</th>
          <th>Tanggal Publikasi</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($articles as $article)
          <tr>
            <td>
              <div style="font-weight: 600; font-size: 15px;">{{ $article->title }}</div>
              <div style="font-size: 12px; color: var(--text-dim); font-family: monospace;">/blog/{{ $article->slug }}</div>
            </td>
            <td>
              <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                {{ $article->category->name ?? 'Tanpa Kategori' }}
              </span>
            </td>
            <td>
              @if ($article->trashed())
                <span class="badge badge-danger">Dihapus</span>
              @elseif ($article->status === 'published')
                <span class="badge badge-active">Published</span>
              @elseif ($article->status === 'scheduled')
                <span class="badge badge-warning">Terjadwal</span>
              @else
                <span class="badge badge-inactive">Draf</span>
              @endif
            </td>
            <td>{{ $article->reading_time }} menit</td>
            <td style="font-size: 13px; color: var(--text-muted);">
              {{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 6px;">
                @if ($article->trashed())
                  <form action="{{ route('admin.articles.restore', $article->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" style="color: #34d399;">Pulihkan</button>
                  </form>
                  <form action="{{ route('admin.articles.force_delete', $article->id) }}" method="POST" style="margin: 0;" data-confirm="Hapus artikel ini secara permanen?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Purge</button>
                  </form>
                @else
                  @if ($article->status === 'published')
                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat ↗</a>
                  @endif
                  <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-secondary btn-sm">Edit</a>
                  <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" style="margin: 0;" data-confirm="Pindahkan artikel ke tong sampah?">
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
            <td colspan="6" style="text-align: center; color: var(--text-dim); padding: 40px;">
              Tidak ada artikel yang sesuai dengan filter.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pagination-wrapper">
    <div>Menampilkan {{ $articles->count() }} dari {{ $articles->total() }} artikel</div>
    <div>{{ $articles->links() }}</div>
  </div>
</div>
@endsection

