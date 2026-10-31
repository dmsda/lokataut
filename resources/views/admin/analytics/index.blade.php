@extends('layouts.admin')

@section('title', 'Laporan Analitik Trafik')
@section('page_title', 'Analitik Trafik & Kinerja Safelink')

@section('content')
<div class="card" style="margin-bottom: 24px;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
      <div style="font-weight: 600; font-size: 16px;">Rentang Waktu Laporan</div>
      <div style="font-size: 13px; color: var(--text-muted);">Data mentah disimpan selama 90 hari sesuai kebijakan retensi DEC-006.</div>
    </div>

    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.analytics.index', ['days' => 7]) }}" class="btn {{ $days === 7 ? 'btn-primary' : 'btn-secondary' }} btn-sm">7 Hari</a>
      <a href="{{ route('admin.analytics.index', ['days' => 30]) }}" class="btn {{ $days === 30 ? 'btn-primary' : 'btn-secondary' }} btn-sm">30 Hari</a>
      <a href="{{ route('admin.analytics.index', ['days' => 90]) }}" class="btn {{ $days === 90 ? 'btn-primary' : 'btn-secondary' }} btn-sm">90 Hari</a>
    </div>
  </div>
</div>

<div class="grid-cols-2">
  <!-- Trend Chart -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Tren Kunjungan vs Pengalihan ({{ $days }} Hari)</div>
    </div>

    @php
      $maxVal = 1;
      foreach ($trend as $t) {
        if ($t['impressions'] > $maxVal) $maxVal = $t['impressions'];
        if ($t['completions'] > $maxVal) $maxVal = $t['completions'];
      }
    @endphp

    <div class="chart-container" style="overflow-x: auto;">
      @foreach ($trend as $day)
        @php
          $hImp = max(6, round(($day['impressions'] / $maxVal) * 160));
          $hComp = max(6, round(($day['completions'] / $maxVal) * 160));
        @endphp
        <div class="chart-bar-group" style="min-width: 24px;">
          <div class="chart-bars">
            <div class="chart-bar bar-impressions" style="height: {{ $hImp }}px;" title="{{ $day['impressions'] }} Kunjungan pada {{ $day['date'] }}"></div>
            <div class="chart-bar bar-completions" style="height: {{ $hComp }}px;" title="{{ $day['completions'] }} Pengalihan pada {{ $day['date'] }}"></div>
          </div>
          <div class="chart-label" style="font-size: 10px;">{{ $day['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Top Performing Links -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ringkasan Slug Terpopuler</div>
    </div>

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Slug Tautan</th>
            <th>Tayangan</th>
            <th>Pengalihan</th>
            <th>Konversi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($topDailyStats as $stat)
            @php
              $cr = $stat->total_imp > 0 ? round(($stat->total_comp / $stat->total_imp) * 100, 1) : 0;
            @endphp
            <tr>
              <td><strong>/s/{{ $stat->link_slug }}</strong></td>
              <td>{{ format_number($stat->total_imp) }}</td>
              <td><span style="color: #34d399; font-weight: 600;">{{ format_number($stat->total_comp) }}</span></td>
              <td>{{ $cr }}%</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align: center; color: var(--text-dim); padding: 24px;">
                Belum ada data agregasi harian tercatat.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Recent Real-time Visits Log -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Log Event Kunjungan Terbaru (20 Record Terakhir)</div>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Tautan</th>
          <th>Tipe Event</th>
          <th>Perangkat / Browser</th>
          <th>Sistem Operasi</th>
          <th>Negara</th>
          <th>IP Hash (HMAC)</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($recentVisits as $visit)
          <tr>
            <td style="font-size: 12px; color: var(--text-dim);">
              {{ $visit->created_at ? $visit->created_at->format('H:i:s - d M') : '-' }}
            </td>
            <td>
              <strong>/s/{{ $visit->link_slug_snapshot }}</strong>
            </td>
            <td>
              @if ($visit->event_type === 'impression')
                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">Impression</span>
              @elseif ($visit->event_type === 'verified')
                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">Verified</span>
              @elseif ($visit->event_type === 'completed')
                <span class="badge badge-active">Completed</span>
              @else
                <span class="badge badge-danger">Error</span>
              @endif
            </td>
            <td>
              {{ ucfirst($visit->device_type) }} &bull; {{ $visit->browser ?? 'Unknown' }}
            </td>
            <td>{{ $visit->os ?? 'Unknown' }}</td>
            <td>
              <span class="badge" style="background: rgba(255, 255, 255, 0.05);">
                {{ $visit->country_code ?? 'XX' }}
              </span>
            </td>
            <td>
              <span style="font-family: monospace; font-size: 11px; color: var(--text-dim);" title="{{ $visit->ip_hash }}">
                {{ substr($visit->ip_hash, 0, 16) }}...
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 32px;">
              Belum ada log kunjungan yang tercatat di sistem.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

