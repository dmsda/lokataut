@extends('layouts.public')

@section('title', '403 — Akses Ditolak')

@section('content')
<div class="safelink-card" style="padding: 48px 32px;">
  <div style="font-size: 64px; font-weight: 850; color: var(--danger); line-height: 1; margin-bottom: 16px; font-family: var(--font-mono);">403</div>
  <h1 class="link-title">Akses Tidak Diizinkan</h1>
  <p class="link-subtitle" style="margin-bottom: 28px;">Anda tidak memiliki kredensial otoritas yang cukup untuk mengakses halaman atau fungsi yang diminta.</p>
  <div>
    <a href="{{ route('login') }}" class="btn-proceed" style="text-decoration: none; display: inline-flex; width: auto; min-width: 200px; background-color: var(--primary);">
      Masuk Administrator &rarr;
    </a>
  </div>
</div>
@endsection
