@extends('layouts.site')

@section('meta_title', 'Kontak Kami — LokaTaut')
@section('meta_description', 'Hubungi tim pengelola LokaTaut untuk pertanyaan, laporan tautan bermasalah, atau pertanyaan teknis terkait platform.')

@section('content')
<div class="container section">
  <div style="max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 48px;">
      <span class="section-badge">Bantuan & Komunikasi</span>
      <h1 class="section-title">Hubungi Kami</h1>
      <p class="section-sub">Kirimkan pertanyaan, masukan teknis, atau laporkan tautan yang bermasalah.</p>
    </div>

    @if (session('success'))
      <div class="alert-box alert-success">
        <strong>Sukses:</strong> {{ session('success') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="alert-box alert-danger">
        <strong style="display: block; margin-bottom: 4px;">Harap perbaiki kesalahan berikut:</strong>
        <ul style="padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="grid-2" style="align-items: flex-start;">
      <!-- Contact Info Card -->
      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Informasi Kontak Pengelola</h3>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
          LokaTaut dikelola secara mandiri oleh pemilik platform. Kami berusaha menanggapi setiap korespondensi yang masuk dalam kurun waktu 1-2 hari kerja.
        </p>

        <div style="display: flex; flex-direction: column; gap: 16px; font-size: 14px;">
          <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Email Resmi:</div>
            <a href="mailto:dimas@lokataut.com" style="font-weight: 600; font-size: 15px;">dimas@lokataut.com</a>
          </div>

          <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Waktu Respon:</div>
            <div style="color: var(--text-secondary);">Senin – Jumat (09:00 – 17:00 WIB)</div>
          </div>

          <div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Pusat Bantuan & FAQ:</div>
            <div style="color: var(--text-secondary);">Silakan periksa halaman <a href="{{ route('help') }}">Panduan</a> untuk jawaban cepat atas pertanyaan umum.</div>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary);">Kirim Pesan Langsung</h3>

        <form action="{{ route('contact.submit') }}" method="POST">
          @csrf

          <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control" placeholder="Nama Anda">
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Alamat Email Anda</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-control" placeholder="nama@domain.com">
          </div>

          <div class="form-group">
            <label for="subject" class="form-label">Subjek Pesan</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" placeholder="Contoh: Laporan Tautan Rusak / Pertanyaan" required class="form-control">
          </div>

          <div class="form-group">
            <label for="message" class="form-label">Isi Pesan</label>
            <textarea name="message" id="message" rows="5" required class="form-control" placeholder="Tuliskan pesan lengkap Anda di sini...">{{ old('message') }}</textarea>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Kirim Pesan Sekarang</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
