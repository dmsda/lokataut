@extends('layouts.site')

@section('meta_title', 'Pusat Bantuan & Panduan — LokaTaut')
@section('meta_description', 'Panduan penggunaan LokaTaut: cara menyelesaikan verifikasi safelink, mengatasi kendala tombol unduhan tidak muncul, dan FAQ.')

@section('content')
<div class="container section">
  <div class="reading-container">
    <div style="text-align: center; margin-bottom: 48px;">
      <span class="section-badge">Pusat Bantuan</span>
      <h1 class="section-title" style="font-size: 38px;">Panduan & Pusat Bantuan</h1>
      <p class="section-sub">Panduan langkah demi langkah dan solusi kendala penggunaan tautan LokaTaut.</p>
    </div>

    <div class="prose">
      <h2>Panduan Mengakses Tautan Unduhan Safelink</h2>
      <p>
        Jika Anda membuka tautan beralamat <code>lokataut.com/s/...</code>, ikuti 3 langkah mudah berikut untuk mencapai berkas unduhan Anda:
      </p>

      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px; margin: 24px 0;">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--primary);">Langkah 1: Selesaikan Verifikasi Pengunjung</h4>
        <p style="font-size: 14px; margin-bottom: 0;">
          Pada kotak verifikasi di tengah layar, centang kotak Cloudflare Turnstile. Sistem akan memverifikasi peramban Anda secara otomatis dalam 1-2 detik tanpa perlu tebak gambar rumit.
        </p>
      </div>

      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px; margin: 24px 0;">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--primary);">Langkah 2: Tunggu Hitung Mundur Server Selesai</h4>
        <p style="font-size: 14px; margin-bottom: 0;">
          Setelah verifikasi lolos, lingkaran hitung mundur (biasanya 5 hingga 10 detik) akan berjalan. Harap tetap berada di tab halaman hingga angka timer mencapai 0.
        </p>
      </div>

      <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 24px; margin: 24px 0;">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; color: var(--primary);">Langkah 3: Klik Tombol "Buka Tautan Tujuan"</h4>
        <p style="font-size: 14px; margin-bottom: 0;">
          Tombol biru besar akan muncul di bawah timer dengan jarak aman yang jelas. Klik tombol tersebut untuk langsung dialihkan ke server berkas unduhan asli Anda.
        </p>
      </div>

      <h2>Troubleshooting: Solusi Kendala Umum</h2>

      <h3>1. Tombol Verifikasi Turnstile Tidak Muncul atau Gagal Dimuat</h3>
      <p>
        <strong>Penyebab:</strong> Ekstensi peramban seperti pemblokir skrip agresif (NoScript / uBlock origin mode ketat) atau koneksi VPN tertentu dapat memblokir skrip resmi Cloudflare.
      </p>
      <p>
        <strong>Solusi:</strong> Muat ulang halaman (F5), nonaktifkan sementara pemblokir skrip pada domain lokataut.com, atau gunakan mode penyamaran (*Incognito Window*).
      </p>

      <h3>2. Muncul Pesan "Hitung mundur belum selesai di server"</h3>
      <p>
        <strong>Penyebab:</strong> Terjadi ketidakcocokan waktu antara jam komputer lokal dan server, atau percobaan mempercepat timer visual JavaScript.
      </p>
      <p>
        <strong>Solusi:</strong> Pastikan jam di perangkat Anda sinkron dengan waktu internet otomatis (*Set Time Automatically*), lalu muat ulang halaman tautan.
      </p>

      <h3>3. Muncul Halaman "Tautan Tidak Tersedia"</h3>
      <p>
        <strong>Penyebab:</strong> Tautan yang Anda buka telah kedaluwarsa sesuai batas waktu yang ditentukan pemilik, atau dinonaktifkan oleh administrator.
      </p>
      <p>
        <strong>Solusi:</strong> Hubungi penyedia berkas atau sumber artikel tempat Anda menemukan tautan tersebut untuk meminta tautan unduhan terbaru.
      </p>

      <h2>Butuh Bantuan Tambahan?</h2>
      <p>
        Kirimkan pesan Anda melalui formulir di halaman <a href="{{ route('contact') }}">Kontak Kami</a> atau baca panduan mendalam di rubrik <a href="{{ route('blog.index') }}">Artikel LokaTaut</a>.
      </p>
    </div>
  </div>
</div>
@endsection

