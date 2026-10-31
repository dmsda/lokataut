@extends('layouts.admin')

@section('title', 'Audit Keamanan')
@section('page_title', 'Audit Log Insiden & Keamanan')

@section('content')
<div class="card" style="margin-bottom: 24px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
      <div style="font-weight: 600; font-size: 16px;">Filter Jenis Event Audit</div>
      <div style="font-size: 13px; color: var(--text-muted);">Merekam login, perubahan kata sandi, deteksi bypass countdown, dan modifikasi kode iklan.</div>
    </div>

    <form action="{{ route('admin.security.logs') }}" method="GET" style="display: flex; gap: 8px;">
      <select name="event_type" class="form-control" style="width: 240px; padding: 6px 12px; font-size: 13px;">
        <option value="">Semua Event</option>
        @foreach ($eventTypes as $type)
          <option value="{{ $type }}" {{ $eventType === $type ? 'selected' : '' }}>{{ $type }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Waktu Kejadian</th>
          <th>Tipe Event</th>
          <th>Alamat IP Asal</th>
          <th>Admin / Pemicu</th>
          <th>Detail Insiden (JSON Payload)</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($logs as $log)
          <tr>
            <td style="font-size: 12px; color: var(--text-dim); white-space: nowrap;">
              {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
            </td>
            <td>
              @if (str_contains($log->event_type, 'bypass') || str_contains($log->event_type, 'replay') || str_contains($log->event_type, 'failed'))
                <span class="badge badge-danger">{{ $log->event_type }}</span>
              @elseif (str_contains($log->event_type, 'success') || str_contains($log->event_type, 'created'))
                <span class="badge badge-active">{{ $log->event_type }}</span>
              @else
                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">{{ $log->event_type }}</span>
              @endif
            </td>
            <td style="font-family: monospace; font-size: 12px;">{{ $log->ip_address }}</td>
            <td>{{ $log->admin ? $log->admin->name : 'Pengunjung Publik' }}</td>
            <td>
              @if (!empty($log->details))
                <pre style="font-size: 11px; background: var(--bg-app); padding: 6px 10px; border-radius: 4px; overflow-x: auto; max-width: 400px; color: var(--text-muted);">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
              @else
                <span style="color: var(--text-dim);">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; color: var(--text-dim); padding: 32px;">
              Belum ada log keamanan yang tercatat.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pagination-wrapper">
    <div>Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} catatan log</div>
    <div>{{ $logs->links() }}</div>
  </div>
</div>
@endsection

