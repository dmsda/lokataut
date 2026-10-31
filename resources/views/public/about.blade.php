@extends('layouts.site')

@section('meta_title', 'Tentang Kami — LokaTaut')
@section('meta_description', 'Mengenal LokaTaut: platform manajemen tautan dan pengalihan aman terorganisir yang dirancang dengan prinsip rekayasa privasi dan keandalan.')

@section('content')
<div class="container section">
  <div class="reading-container">
    <div style="text-align: center; margin-bottom: 48px;">
      <span class="section-badge">Profil Platform</span>
      <h1 class="section-title" style="font-size: 38px;">Mengenal LokaTaut</h1>
      <p class="section-sub">Solusi utilitas tautan terorganisir dan pengalihan berkas digital yang handal.</p>
    </div>

    <div class="prose">
      <h2>Visi & Tujuan Platform</h2>
      <p>
        <strong>LokaTaut</strong> lahir dari kebutuhan nyata untuk mengelola, merapikan, dan melindungi distribusi berkas digital di internet. Dalam operasional sehari-hari, pengembang perangkat lunak, kreator konten, dan pengelola arsip digital kerap menghadapi masalah saat membagikan tautan unduhan: tautan yang terlalu panjang, perayapan liar oleh bot tak bertanggung jawab, dan kesulitan memperbarui URL tujuan saat berkas dipindahkan ke server baru.
      </p>
      <p>
        LokaTaut menyediakan infrastruktur perantara yang menyederhanakan siklus hidup tautan tersebut. Dengan memisahkan alamat publik dari lokasi penyimpanan fisik, pemilik berkas memiliki kontrol penuh atas alur distribusi tautan mereka.
      </p>

      <h2>Prinsip Rekayasa Perangkat Lunak</h2>
      <p>
        Dalam pengembangannya, LokaTaut dibangun berlandaskan tiga pilar utama:
      </p>
      <ul>
        <li>
          <strong>Validasi di Sisi Server (Server-Side Integrity):</strong> Tidak mempercayai manipulasi di peramban klien. Seluruh logika hitung mundur, verifikasi token, dan pengalihan URL dieksekusi serta divalidasi langsung di sisi server.
        </li>
        <li>
          <strong>Privasi Terjaga (Privacy-First Analytics):</strong> Pelacakan trafik tidak mengumpulkan identitas pribadi sensitif. Alamat IP pengunjung disamarkan menggunakan fungsi hash kriptografis bergaram (HMAC-SHA256) sehingga data mentah tidak dapat direkonstruksi.
        </li>
        <li>
          <strong>Arsitektur Ringan & Bersahabat (Resource Efficiency):</strong> Menggunakan stack Laravel 13, Native CSS, dan Blade SSR tanpa dependensi pustaka frontend yang membengkak, memastikan waktu muat tetap instan di koneksi seluler sekalipun.
        </li>
      </ul>

      <h2>Tata Kelola Operasional (Single-Owner Governance)</h2>
      <p>
        Berbeda dengan platform publik umum, LokaTaut beroperasi sebagai platform privat milik pemilik tunggal (*private single-owner*). Platform ini dikembangkan dan dikelola secara mandiri oleh <strong>Dimas Aditya</strong>. Model tata kelola tertutup ini bertujuan untuk menjamin integritas setiap tautan yang diterbitkan, menjaga reputasi domain dari tautan spam berbahaya, serta memberikan kepastian keamanan bagi para pengunjung yang mengunduh berkas.
      </p>

      <h2>Batasan Layanan & Transparansi Penggunaan</h2>
      <p>
        Penting untuk dipahami bahwa LokaTaut <strong>bukan merupakan layanan penyimpanan awan (cloud storage)</strong>. Seluruh berkas digital fisik yang Anda unduh tetap tersimpan pada server penyimpanan resmi pihak ketiga yang telah ditentukan oleh pengelola. LokaTaut murni bertindak sebagai perantara cerdas untuk memverifikasi pengunjung, merapikan URL, dan menyajikan pengalihan yang aman.
      </p>

      <h2>Hubungi Pengelola</h2>
      <p>
        Jika Anda memiliki pertanyaan teknis, saran perbaikan, atau ingin melaporkan kendala terkait tautan tertentu, silakan kunjungi halaman <a href="{{ route('contact') }}">Kontak Kami</a>.
      </p>
    </div>
  </div>
</div>
@endsection

