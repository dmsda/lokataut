@extends('layouts.site')

@section('meta_title', 'Kebijakan Privasi — LokaTaut')
@section('meta_description', 'Kebijakan privasi resmi LokaTaut: penjelasan pengumpulan data analitik, pseudonimisasi IP berstandar HMAC-SHA256, retensi 90 hari, dan cookies.')

@section('content')
<div class="container section">
  <div class="reading-container">
    <div style="text-align: center; margin-bottom: 48px;">
      <span class="section-badge">Transparansi Data</span>
      <h1 class="section-title" style="font-size: 38px;">Kebijakan Privasi</h1>
      <p class="section-sub">Terakhir diperbarui: 31 Oktober 2026</p>
    </div>

    <div class="prose">
      <p>
        Selamat datang di <strong>LokaTaut</strong>. Privasi pengunjung dan pengguna kami merupakan hal yang sangat fundamental. Dokumen Kebijakan Privasi ini menjelaskan bagaimana platform kami mengumpulkan, memproses, menyimpan, dan melindungi informasi ketika Anda mengakses situs web utama maupun halaman pengalihan safelink kami.
      </p>

      <h2>1. Prinsip Pengumpulan Data & Pseudonimisasi IP</h2>
      <p>
        LokaTaut mengadopsi prinsip minimisasi data (*data minimization*). Kami tidak mengharuskan pengunjung halaman safelink untuk mendaftar akun atau menyerahkan data pribadi seperti nama, nomor telepon, atau data kependudukan.
      </p>
      <p>
        Untuk keperluan statistik kunjungan dan deteksi lalu lintas tidak wajar (*abuse prevention*), server kami mencatat informasi teknis berikut:
      </p>
      <ul>
        <li>
          <strong>Alamat IP yang Disamarkan (HMAC-SHA256):</strong> Alamat IP pengunjung tidak pernah disimpan dalam format teks mentah (*plain IP*). Sistem kami segera mengubah alamat IP menjadi string acak 64-karakter menggunakan fungsi kriptografis <code>hash_hmac('sha256', $ip, $salt)</code> dengan garam rahasia independen (NIST SP 800-57). Data mentah IP asli langsung dibuang dari memori server.
        </li>
        <li><strong>Informasi Peramban (User-Agent):</strong> Digunakan untuk mengidentifikasi tipe perangkat (desktop, ponsel, tablet) dan jenis peramban (Chrome, Firefox, Safari) guna optimasi tampilan.</li>
        <li><strong>Informasi Perujuk (HTTP Referrer):</strong> Alamat web asal yang mengarahkan pengunjung ke tautan LokaTaut.</li>
        <li><strong>Kode Negara Kasar:</strong> Diperoleh dari header jaringan Cloudflare (misalnya ID untuk Indonesia) tanpa koordinat lokasi presisi.</li>
      </ul>

      <h2>2. Kebijakan Retensi dan Penghapusan Data</h2>
      <p>
        Kami menerapkan batasan masa simpan data (*retention period*) yang tegas untuk menghemat sumber daya dan menjaga privasi:
      </p>
      <ul>
        <li><strong>Sesi Pengunjung (visitor_sessions):</strong> Dihapus secara otomatis oleh cron pembersih dalam waktu maksimal <strong>24 jam</strong> setelah sesi berakhir.</li>
        <li><strong>Log Peristiwa Kunjungan (visits):</strong> Data mentah analitik disimpan selama maksimal <strong>90 hari</strong> (kebijakan DEC-006). Setelah 90 hari, baris mentah dihapus permanen setelah dipastikan telah terekap ke dalam statistik harian.</li>
        <li><strong>Statistik Agregat Harian (daily_stats):</strong> Berisi angka ringkasan total (misalnya: 500 tayangan, 420 penyelesaian) tanpa keterkaitan ke pengguna individual, disimpan permanen untuk pelaporan tren jangka panjang.</li>
      </ul>

      <h2>3. Penggunaan Cookie</h2>
      <p>
        LokaTaut hanya menggunakan cookie esensial yang diperlukan untuk fungsi teknis platform:
      </p>
      <ul>
        <li><strong>Cookie Sesi (lokataut-session & XSRF-TOKEN):</strong> Digunakan untuk keamanan formulir dan menjaga integritas sesi penelusuran. Cookie ini bersifat sementara dan otomatis kadaluwarsa saat sesi selesai.</li>
        <li>Kami tidak menggunakan cookie pelacak pihak ketiga (*third-party tracking cookies*) untuk profiling lintas situs pribadi.</li>
      </ul>

      <h2>4. Layanan Pihak Ketiga (Third-Party Services)</h2>
      <p>
        Dalam pengoperasian platform, LokaTaut bermitra dengan sejumlah penyedia infrastruktur terpercaya:
      </p>
      <ul>
        <li>
          <strong>Cloudflare Turnstile:</strong> Kami menggunakan Turnstile untuk memeriksa apakah pengunjung adalah manusia atau bot otomatis. Turnstile beroperasi tanpa menggunakan CAPTCHA interaktif yang rumit dan tidak melacak riwayat penelusuran pribadi Anda di situs lain.
        </li>
        <li>
          <strong>Jaringan Iklan Sponsor (Adsterra / Monetag):</strong> Halaman safelink dapat memuat materi banner iklan dari jaringan mitra untuk menopang biaya operasional server. Mitra iklan pihak ketiga dapat menggunakan teknologi periklanan mereka sendiri yang diatur dalam kebijakan privasi masing-masing mitra.
        </li>
      </ul>

      <h2>5. Hak-Hak Pengunjung</h2>
      <p>
        Pengunjung berhak untuk:
      </p>
      <ul>
        <li>Mengakses informasi secara transparan mengenai bagaimana data teknis diproses.</li>
        <li>Menonaktifkan cookie melalui pengaturan peramban masing-masing (meskipun beberapa fungsi proteksi formulir mungkin terpengaruh).</li>
        <li>Menghubungi pengelola platform untuk mengajukan pertanyaan seputar perlindungan privasi data.</li>
      </ul>

      <h2>6. Perubahan Kebijakan Privasi</h2>
      <p>
        Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu untuk menyesuaikan dengan pembaruan fitur teknis atau regulasi yang berlaku. Tanggal pembaruan terakhir akan selalu dicantumkan di bagian atas halaman ini.
      </p>

      <h2>7. Hubungi Kami</h2>
      <p>
        Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi kami melalui email: <a href="mailto:dimas@lokataut.com">dimas@lokataut.com</a>.
      </p>
    </div>
  </div>
</div>
@endsection

