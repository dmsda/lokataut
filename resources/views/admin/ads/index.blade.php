@extends('layouts.admin')

@section('title', 'Manajemen Slot Iklan')
@section('page_title', 'Konfigurasi Slot Iklan & Monetisasi')

@section('content')
<div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--primary);">
  <div style="font-weight: 600; font-size: 15px; margin-bottom: 6px;">Kebijakan Monetisasi & Kepatuhan Jaringan Iklan (Rule 4)</div>
  <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">
    Platform LokaTaut menerapkan pemisahan ketat antara tombol navigasi dan materi iklan. Google AdSense dilarang pada halaman hitung mundur (Thin content / Forced waiting). Gunakan tag iklan banner dari Adsterra atau Monetag. Tombol pengalihan memiliki jarak aman minimum 30px (Anti-IVT).
  </p>
</div>

<div class="card">
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Nama Slot</th>
          <th>Posisi Halaman</th>
          <th>Target Perangkat</th>
          <th>Status</th>
          <th>Kode Iklan Terpasang</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($slots as $slot)
          <tr>
            <td>
              <div style="font-weight: 600;">{{ $slot->name }}</div>
              <div style="font-size: 12px; color: var(--text-dim);">{{ $slot->notes ?? '-' }}</div>
            </td>
            <td>
              <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">{{ $slot->position }}</span>
            </td>
            <td>{{ ucfirst($slot->device_target) }}</td>
            <td>
              @if ($slot->is_active)
                <span class="badge badge-active">Aktif</span>
              @else
                <span class="badge badge-inactive">Nonaktif</span>
              @endif
            </td>
            <td>
              @if (!empty($slot->ad_code))
                <span style="color: #34d399; font-size: 13px;">✓ Terpasang ({{ strlen($slot->ad_code) }} bytes)</span>
              @else
                <span style="color: var(--text-dim); font-size: 13px;">Belum dipasang</span>
              @endif
            </td>
            <td style="text-align: right;">
              <a href="{{ route('admin.ads.edit', $slot) }}" class="btn btn-secondary btn-sm">Edit Tag Iklan</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align: center; color: var(--text-dim); padding: 32px;">
              Belum ada slot iklan yang dikonfigurasi.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

