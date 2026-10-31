@extends('layouts.public')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<div class="safelink-card" style="padding: 48px 32px;">
  <div style="font-size: 64px; font-weight: 850; color: var(--text-dim); line-height: 1; margin-bottom: 16px; font-family: var(--font-mono);">404</div>
  <h1 class="link-title">Halaman atau Tautan Tidak Ditemukan</h1>
  <p class="link-subtitle" style="margin-bottom: 28px;">Alamat atau slug tautan yang Anda tuju tidak terdaftar atau telah dihapus secara permanen dari sistem.</p>
  <div>
    <a href="{{ route('home') }}" class="btn-proceed" style="text-decoration: none; display: inline-flex; width: auto; min-width: 200px;">
      &larr; Kembali ke Beranda
    </a>
  </div>
</div>
@endsection
