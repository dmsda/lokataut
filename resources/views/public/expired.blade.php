@extends('layouts.public')

@section('title', 'Tautan Tidak Tersedia')

@section('content')
<div class="safelink-card" style="padding: 48px 32px;">
  <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--danger-subtle); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; margin: 0 auto 20px auto; border: 1px solid var(--danger);">
    !
  </div>

  <h1 class="link-title" style="color: var(--danger);">Tautan Tidak Tersedia</h1>

  <p class="link-subtitle" style="margin-bottom: 28px;">
    @if ($isExpired)
      Tautan yang Anda tuju telah kedaluwarsa sesuai batas waktu yang ditentukan oleh pemilik tautan.
    @else
      Tautan ini sedang dinonaktifkan atau telah dipindahkan ke tong sampah oleh pemilik platform.
    @endif
  </p>

  <div>
    <a href="{{ route('home') }}" class="btn-proceed" style="background: var(--text-muted); text-decoration: none; display: inline-flex; width: auto; min-width: 200px;">
      &larr; Kembali ke Beranda
    </a>
  </div>
</div>
@endsection
