@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Kinerja Platform')

@section('content')
<div class="grid-cols-4">
  <div class="stat-box">
    <div class="stat-label">Total Tautan</div>
    <div class="stat-value">{{ format_number($summary['total_links']) }}</div>
    <div class="stat-sub">{{ $summary['active_links'] }} tautan aktif saat ini</div>
  </div>

  <div class="stat-box">
    <div class="stat-label">Kunjungan Hari Ini</div>
    <div class="stat-value">{{ format_number($summary['today_visits']) }}</div>
    <div class="stat-sub">Tayangan halaman safelink</div>
  </div>

  <div class="stat-box">
    <div class="stat-label">Pengalihan Hari Ini</div>
    <div class="stat-value">{{ format_number($summary['today_completions']) }}</div>
    <div class="stat-sub">Sukses mencapai tujuan</div>
  </div>

  <div class="stat-box">
    <div class="stat-label">Artikel Terbit</div>
    <div class="stat-value">{{ format_number($articleStats['published'] ?? 0) }}</div>
    <div class="stat-sub">{{ $articleStats['draft'] ?? 0 }} draf tersimpan</div>
  </div>
</div>

<div class="grid-cols-2">
  <!-- 7-Day Trend Chart -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Tren Trafik 7 Hari Terakhir</div>
      <div style="font-size: 12px; display: flex; gap: 12px; align-items: center;">
        <span style="display: flex; align-items: center; gap: 4px;">
          <span style="width: 10px; height: 10px; background: var(--primary); border-radius: 2px;"></span> Kunjungan
        </span>
        <span style="display: flex; align-items: center; gap: 4px;">
          <span style="width: 10px; height: 10px; background: var(--success); border-radius: 2px;"></span> Pengalihan
        </span>
      </div>
    </div>

    @php
      $maxVal = 1;
      foreach ($dailyTrend as $t) {
        if ($t['impressions'] > $maxVal) $maxVal = $t['impressions'];
        if ($t['completions'] > $maxVal) $maxVal = $t['completions'];
      }
    @endphp

    <div class="chart-container">
      @foreach ($dailyTrend as $day)
        @php
          $hImp = max(6, round(($day['impressions'] / $maxVal) * 160));
          $hComp = max(6, round(($day['completions'] / $maxVal) * 160));
        @endphp
        <div class="chart-bar-group">
          <div class="chart-bars">
            <div class="chart-bar bar-impressions" style="height: {{ $hImp }}px;" title="{{ $day['impressions'] }} Kunjungan"></div>
            <div class="chart-bar bar-completions" style="height: {{ $hComp }}px;" title="{{ $day['completions'] }} Pengalihan"></div>
          </div>
          <div class="chart-label">{{ $day['label'] }}</div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Device Breakdown -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Distribusi Perangkat Pengunjung</div>
      <a href="{{ route('admin.analytics.index') }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
    </div>

    @php
      $totalDev = array_sum($deviceBreakdown);
    @endphp

    <div style="display: flex; flex-direction: column; gap: 16px; padding: 10px 0;">
      @foreach (['desktop' => 'Desktop / PC', 'mobile' => 'Smartphone (Mobile)', 'tablet' => 'Tablet', 'bot' => 'Bot / Crawler'] as $key => $name)
        @php
          $cnt = $deviceBreakdown[$key] ?? 0;
          $pct = $totalDev > 0 ? round(($cnt / $totalDev) * 100, 1) : 0;
        @endphp
        <div>
          <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
            <span>{{ $name }}</span>
            <span style="font-weight: 600;">{{ format_number($cnt) }} ({{ $pct }}%)</span>
          </div>
          <div style="width: 100%; height: 8px; background: var(--bg-app); border-radius: 4px; overflow: hidden;">
            <div style="width: {{ $pct }}%; height: 100%; background: var(--primary); border-radius: 4px;"></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Top Links Table -->
<div class="card" style="margin-bottom: 24px;">
  <div class="card-header">
    <div class="card-title">Tautan Trafik Teratas</div>
    <a href="{{ route('admin.links.create') }}" class="btn btn-primary btn-sm">+ Buat Tautan Baru</a>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Slug & URL Publik</th>
          <th>Judul / Label</th>
          <th>Website Sumber</th>
          <th>Total Kunjungan</th>
          <th>Pengalihan Sukses</th>
          <th>Status</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($topLinks as $link)
          <tr>
            <td>
              <div style="font-weight: 600;">/s/{{ $link->slug }}</div>
              <a href="{{ url('/s/' . $link->slug) }}" target="_blank" style="font-size: 12px; color: var(--text-dim);">Buka Halaman ↗</a>
            </td>
            <td>{{ $link->title ?? 'Tanpa Judul' }}</td>
            <td><span class="badge" style="background: rgba(255,255,255,0.05);">{{ $link->source_website ?? '-' }}</span></td>
            <td><strong>{{ format_number($link->total_visits) }}</strong></td>
            <td><span style="color: #34d399; font-weight: 600;">{{ format_number($link->total_completions) }}</span></td>
            <td>
              @if ($link->status === 'active')
                <span class="badge badge-active">Aktif</span>
              @else
                <span class="badge badge-inactive">Nonaktif</span>
              @endif
            </td>
            <td style="text-align: right;">
              <button type="button" class="btn btn-secondary btn-sm" data-copy-url="{{ url('/s/' . $link->slug) }}">Salin</button>
              <a href="{{ route('admin.links.edit', $link) }}" class="btn btn-secondary btn-sm">Edit</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 32px;">
              Belum ada tautan yang dibuat. Silakan klik tombol <strong>+ Buat Tautan Baru</strong> di atas.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Latest Articles Table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">Artikel Edukasi & Publikasi Terbaru</div>
    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary btn-sm">Semua Artikel</a>
      <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">+ Tulis Artikel</a>
    </div>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Judul Artikel</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($latestArticles as $art)
          <tr>
            <td>
              <div style="font-weight: 600;">{{ $art->title }}</div>
              <div style="font-size: 12px; color: var(--text-dim);">/blog/{{ $art->slug }}</div>
            </td>
            <td>
              <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                {{ $art->category->name ?? 'Umum' }}
              </span>
            </td>
            <td>
              @if ($art->status === 'published')
                <span class="badge badge-active">Published</span>
              @else
                <span class="badge badge-inactive">{{ ucfirst($art->status) }}</span>
              @endif
            </td>
            <td style="font-size: 13px; color: var(--text-muted);">
              {{ $art->published_at ? $art->published_at->format('d/m/Y') : '-' }}
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 6px;">
                @if ($art->status === 'published')
                  <a href="{{ route('blog.show', $art->slug) }}" target="_blank" class="btn btn-secondary btn-sm">Lihat ↗</a>
                @endif
                <a href="{{ route('admin.articles.edit', $art) }}" class="btn btn-secondary btn-sm">Edit</a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; color: var(--text-dim); padding: 24px;">
              Belum ada artikel yang dibuat.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
