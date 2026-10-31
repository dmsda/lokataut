@extends('layouts.admin')

@section('title', 'Manajemen Tautan')
@section('page_title', 'Daftar Tautan Unduhan')

@section('content')
<div class="card" style="margin-bottom: 24px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <!-- Filter Tabs -->
    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.links.index', ['status' => 'all']) }}" class="btn {{ $statusFilter === 'all' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Semua ({{ $counts['all'] }})
      </a>
      <a href="{{ route('admin.links.index', ['status' => 'active']) }}" class="btn {{ $statusFilter === 'active' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Aktif ({{ $counts['active'] }})
      </a>
      <a href="{{ route('admin.links.index', ['status' => 'inactive']) }}" class="btn {{ $statusFilter === 'inactive' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Nonaktif ({{ $counts['inactive'] }})
      </a>
      <a href="{{ route('admin.links.index', ['status' => 'trash']) }}" class="btn {{ $statusFilter === 'trash' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        Tong Sampah ({{ $counts['trash'] }})
      </a>
    </div>

    <!-- Search Form -->
    <div style="display: flex; gap: 12px; align-items: center;">
      <form action="{{ route('admin.links.index') }}" method="GET" style="display: flex; gap: 8px;">
        <input type="hidden" name="status" value="{{ $statusFilter }}">
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul / slug..." class="form-control" style="width: 220px; padding: 6px 12px; font-size: 13px;">
        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
      </form>

      <a href="{{ route('admin.links.create') }}" class="btn btn-primary btn-sm">+ Buat Tautan</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Tautan Safelink</th>
          <th>Tujuan Asli (Dekripsi Aman)</th>
          <th>Web Sumber</th>
          <th>Timer</th>
          <th>Kunjungan</th>
          <th>Pengalihan</th>
          <th>Status</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($links as $link)
          <tr>
            <td>
              <div style="font-weight: 600;">/s/{{ $link->slug }}</div>
              <div style="font-size: 12px; color: var(--text-dim);">{{ $link->title ?? 'Tanpa Judul' }}</div>
            </td>
            <td>
              <div style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: monospace; font-size: 12px; color: var(--text-muted);" title="{{ $link->destination_url }}">
                {{ $link->destination_url }}
              </div>
            </td>
            <td><span class="badge" style="background: rgba(255,255,255,0.05);">{{ $link->source_website ?? '-' }}</span></td>
            <td>{{ $link->countdown_seconds }}s</td>
            <td>{{ format_number($link->total_visits) }}</td>
            <td><span style="color: #34d399; font-weight: 600;">{{ format_number($link->total_completions) }}</span></td>
            <td>
              @if ($link->trashed())
                <span class="badge badge-danger">Dihapus (Trash)</span>
              @elseif ($link->isExpired())
                <span class="badge badge-warning">Kedaluwarsa</span>
              @elseif ($link->status === 'active')
                <span class="badge badge-active">Aktif</span>
              @else
                <span class="badge badge-inactive">Nonaktif</span>
              @endif
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 6px;">
                @if ($link->trashed())
                  <form action="{{ route('admin.links.restore', $link->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm" style="color: #34d399;">Pulihkan</button>
                  </form>
                  <form action="{{ route('admin.links.force_delete', $link->id) }}" method="POST" style="margin: 0;" data-confirm="HAPUS PERMANEN? Tindakan ini tidak dapat dibatalkan. Riwayat analitik snapshot tetap aman.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Purge</button>
                  </form>
                @else
                  <button type="button" class="btn btn-secondary btn-sm" data-copy-url="{{ url('/s/' . $link->slug) }}">Salin</button>
                  <a href="{{ route('admin.links.edit', $link) }}" class="btn btn-secondary btn-sm">Edit</a>
                  <form action="{{ route('admin.links.destroy', $link) }}" method="POST" style="margin: 0;" data-confirm="Pindahkan tautan ke tong sampah?">
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
            <td colspan="8" style="text-align: center; color: var(--text-dim); padding: 40px;">
              Tidak ada tautan yang sesuai dengan filter.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pagination-wrapper">
    <div>Menampilkan {{ $links->count() }} dari {{ $links->total() }} tautan</div>
    <div>{{ $links->links() }}</div>
  </div>
</div>
@endsection

