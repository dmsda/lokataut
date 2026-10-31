@extends('layouts.public')

@section('title', $link->title ?? 'Menyiapkan Tautan Unduhan Aman')

@section('content')
<div class="safelink-card">
  <!-- Ad Slot: Header -->
  <x-public.ad-slot :slots="$adSlots" position="header" />

  <h1 class="link-title">{{ $link->title ?? 'Tautan Unduhan Terproteksi' }}</h1>
  <p class="link-subtitle">Layanan pengalihan aman. Selesaikan verifikasi keamanan di bawah ini untuk mengakses tautan tujuan asli.</p>

  <!-- Ad Slot: Above Timer -->
  <x-public.ad-slot :slots="$adSlots" position="above_timer" />

  <!-- Error Alert Container -->
  <div id="alert-error" class="alert-box alert-box-danger"></div>

  <!-- Turnstile Bot Verification Box -->
  @if ($turnstileMode !== 'bypass')
    <div id="verification-box" class="verification-box">
      <div class="verification-heading">Verifikasi Pengunjung</div>
      <div class="turnstile-wrapper">
        <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-callback="onTurnstileSuccess" data-theme="auto"></div>
      </div>
      <div style="font-size: 12px; color: var(--text-light);">Mohon centang kotak verifikasi di atas untuk memulai hitung mundur.</div>
    </div>
  @endif

  <!-- Timer Countdown Box -->
  <div id="timer-box" class="timer-box">
    <div class="timer-circle">
      <span id="timer-count">{{ $countdownSeconds }}</span>
    </div>
    <div class="timer-status-text">Harap tunggu, tautan Anda sedang disiapkan di server...</div>
  </div>

  <!-- Ad Slot: Below Timer -->
  <x-public.ad-slot :slots="$adSlots" position="below_timer" />

  <!-- Action Box: Proceed Navigation (Strict 30px Anti-IVT Clearance) -->
  <div id="action-box" class="action-box">
    <button type="button" id="proceed-btn" class="btn-proceed">
      <span>Buka Tautan Tujuan &rarr;</span>
    </button>

    <!-- Fallback Direct POST for No-JS Browsers -->
    <noscript>
      <form action="{{ route('safelink.direct_proceed', $link->slug) }}" method="POST" style="margin-top: 16px;">
        @csrf
        <input type="hidden" name="token" value="{{ $rawToken }}">
        <button type="submit" class="btn-proceed">
          <span>Lanjutkan ke Tautan (No-JS)</span>
        </button>
      </form>
    </noscript>
  </div>

  <!-- Ad Slot: Footer -->
  <x-public.ad-slot :slots="$adSlots" position="footer" />
</div>
@endsection

@push('scripts')
<script>
  window.__LOKATAUT_CONFIG__ = {
    token: "{{ $rawToken }}",
    countdownSeconds: {{ (int) $countdownSeconds }},
    verifyUrl: "{{ route('safelink.verify', $link->slug) }}",
    proceedUrl: "{{ route('safelink.proceed', $link->slug) }}",
    csrfToken: "{{ csrf_token() }}",
    turnstileMode: "{{ $turnstileMode }}"
  };
</script>
@endpush

