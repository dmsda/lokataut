<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $articles = [
            [
                'category_id' => $categories['panduan-shortlink']->id ?? null,
                'title' => 'Apa Itu Shortlink? Pengertian, Fungsi, dan Cara Kerjanya',
                'slug' => 'apa-itu-shortlink-fungsi-cara-kerja',
                'excerpt' => 'Pelajari konsep dasar pemendekan URL (shortlink), bagaimana server menangani kode status HTTP 301 dan 302, serta manfaatnya dalam komunikasi digital.',
                'meta_title' => 'Apa Itu Shortlink? Pengertian, Fungsi, dan Cara Kerjanya — LokaTaut',
                'meta_description' => 'Mengenal pengertian shortlink, cara kerja redirect di balik layar server, dan alasan mengapa tautan pendek sangat penting untuk berbagi informasi.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 4,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'content' => <<<'MARKDOWN'
## Mengenal Konsep Pemendekan URL (Shortlink)

Di era internet modern, alamat web (URL) sering kali memiliki struktur yang sangat panjang dan rumit. Parameter analitik seperti UTM tracking, folder bertingkat, hingga hash query string dapat membuat sebuah URL mencapai ratusan karakter. Kondisi ini menyulitkan pembaca saat melihat tautan di media sosial, aplikasi pesan singkat, maupun media cetak.

**Shortlink** adalah representasi ringkas dari sebuah alamat web yang panjang. Ketika seseorang mengklik shortlink, server pemendek tautan akan menerima permintaan tersebut, mencocokkan kode unik (slug) dengan URL asli di basis data, lalu memberikan respon pengalihan ke tujuan yang sebenarnya.

### Bagaimana Server Menangani Pengalihan URL?

Secara teknis, proses pengalihan shortlink terjadi dalam hitungan milidetik melalui protokol HTTP. Dua kode status respon yang paling umum digunakan adalah:

1. **HTTP 301 Moved Permanently:** Pengalihan permanen yang memberi tahu peramban dan mesin pencari bahwa halaman telah berpindah alamat untuk seterusnya. Respon ini biasanya disimpan dalam memori cache browser.
2. **HTTP 302 Found (Temporary Redirect):** Pengalihan sementara yang mengarahkan peramban ke tujuan tanpa menyimpan status permanen. Model ini sangat cocok untuk pengelolaan link dinamis dan pelacakan metrik analitik secara *real-time*.

### Fungsi dan Manfaat Utama Shortlink

- **Meningkatkan Kerapian Tampilan:** Tautan pendek terlihat lebih profesional, mudah dibaca, dan tidak merusak estetika tata letak pesan atau dokumen.
- **Mempermudah Pengingatan:** Dengan menggunakan slug kustom yang deskriptif, pengguna dapat mengingat alamat tautan dengan lebih cepat.
- **Pelacakan Statistik Terpusat:** Pemilik tautan dapat memantau volume tayangan, waktu akses, serta persebaran geografis pengunjung secara akurat.

Menggunakan layanan manajemen tautan yang andal memungkinkan Anda mengontrol distribusi konten secara terorganisir tanpa khawatir tautan panjang terpotong saat dikirimkan.
MARKDOWN
            ],
            [
                'category_id' => $categories['safelink-download']->id ?? null,
                'title' => 'Apa Itu Safelink? Cara Kerja dan Manfaatnya untuk Berbagi File',
                'slug' => 'apa-itu-safelink-cara-kerja-manfaat',
                'excerpt' => 'Memahami peran halaman perantara safelink dalam memfilter bot otomatis, memverifikasi keaslian pengunjung, dan menyalurkan trafik unduhan secara aman.',
                'meta_title' => 'Apa Itu Safelink? Cara Kerja dan Manfaat Berbagi File — LokaTaut',
                'meta_description' => 'Pelajari arsitektur halaman safelink, peran bot detection, dan bagaimana perlindungan ini membantu pemilik file mengelola distribusi berkas digital.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(8),
                'content' => <<<'MARKDOWN'
## Apa Itu Halaman Safelink?

Bagi Anda yang sering mengunduh berkas perangkat lunak, aset grafis, atau dokumen digital, Anda mungkin pernah diarahkan ke sebuah halaman perantara sebelum berkas mulai terunduh. Halaman perantara inilah yang dikenal dengan istilah **safelink**.

Berbeda dengan shortlink standar yang langsung mengalihkan browser seketika, safelink bertindak sebagai gerbang penyaring (*gateway*). Pengunjung diminta menyelesaikan tahapan tertentu—seperti verifikasi interaksi manusia atau menunggu hitung mundur—sebelum tombol menuju tautan unduhan asli aktif.

### Mengapa Pemilik Konten Menggunakan Safelink?

Distribusi berkas di internet publik menghadapi sejumlah tantangan nyata:

1. **Serangan Bot Otomatis (Web Scraping):** Skrip perayap (*crawler*) sering kali mengunduh file berukuran besar secara otomatis, yang dapat menghabiskan kuota *bandwidth* server penyimpanan secara cuma-cuma.
2. **Pencegahan Hotlinking Ilegal:** Menempatkan link unduhan langsung di forum terbuka membuat pihak ketiga dapat menyematkan tautan tersebut di situs mereka tanpa izin.
3. **Monetisasi Pendukung Operasional:** Mengelola infrastruktur hosting dan penyimpanan membutuhkan biaya. Safelink menyediakan ruang bagi pemilik platform untuk menampilkan penempatan iklan sponsor yang relevan.

### Alur Keamanan di Balik Safelink Modern

Pada sistem safelink yang dirancang dengan prinsip rekayasa yang aman:
- **URL tujuan asli tidak pernah dipublikasikan di awal.** Tautan unduhan disimpan secara terenkripsi di basis data server dan hanya didekripsi saat sesi pengunjung tervalidasi.
- **Sesi berbasis token unik.** Setiap kunjungan menerima token berbatas waktu (*Time-to-Live*) yang diikat dengan sidik peramban pengunjung.
- **Verifikasi ketat sebelum pengalihan.** Setelah hitung mundur tervalidasi di sisi server (*server-side verification*), pengunjung diarahkan dengan aman menuju berkas tujuan.
MARKDOWN
            ],
            [
                'category_id' => $categories['tips-produktivitas']->id ?? null,
                'title' => 'Cara Membagikan Link Download dengan Lebih Rapi',
                'slug' => 'cara-membagikan-link-download-rapi',
                'excerpt' => 'Strategi praktis merapikan tautan berkas di media sosial, blog, dan kanal komunitas agar tampak profesional dan terhindar dari pemblokiran filter spam.',
                'meta_title' => 'Cara Membagikan Link Download dengan Lebih Rapi — LokaTaut',
                'meta_description' => 'Tips membagikan link download file dengan slug terstruktur, penamaan deskriptif, dan format yang rapi untuk meningkatkan kenyamanan audiens.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 4,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(6),
                'content' => <<<'MARKDOWN'
## Kerapian Tautan Mencerminkan Kredibilitas Pengirim

Ketika Anda membagikan tautan unduhan dokumen kerja, materi pelatihan, atau berkas installer kepada audiens atau rekan kerja, format tampilan tautan memegang peranan besar. Tautan yang berantakan dengan deretan angka acak sering kali memicu keraguan apakah berkas tersebut aman dibuka atau mengandung malware.

Berikut beberapa langkah sederhana yang dapat Anda terapkan untuk merapikan tautan unduhan:

### 1. Gunakan Kustom Slug yang Relevan

Alih-alih menggunakan tautan acak seperti `lokataut.com/s/7x9Qz2`, pilihlah slug yang mencerminkan isi berkas secara gamblang, misalnya:
`lokataut.com/s/panduan-akuntansi-2026`

Slug yang deskriptif memberikan konteks langsung kepada penerima mengenai berkas yang akan mereka unduh sebelum mereka mengkliknya.

### 2. Sertakan Deskripsi Singkat Berkas

Jangan hanya menempel tautan telanjang (*naked link*) pada kolom pesan. Sertakan selalu rincian informasi penting seperti:
- **Nama file:** Contoh: Form Registrasi Beasiswa 2026
- **Format berkas:** PDF / ZIP / XLSX
- **Ukuran perkiraan:** Sekitar 2.4 MB

Informasi ini membantu penerima mempersiapkan koneksi data dan perangkat yang sesuai.

### 3. Pisahkan Antara Tautan Utama dan Panduan Pendukung

Jika Anda membagikan beberapa berkas sekaligus, buat daftar bertingkat menggunakan nomor atau bullet point. Tata letak yang terstruktur mengurangi kemungkinan audiens mengunduh berkas yang salah.
MARKDOWN
            ],
            [
                'category_id' => $categories['safelink-download']->id ?? null,
                'title' => 'Mengapa Beberapa Link Download Menggunakan Countdown?',
                'slug' => 'mengapa-link-download-menggunakan-countdown',
                'excerpt' => 'Ulasan teknis mengapa hitung mundur waktu diterapkan pada halaman pengalihan tautan, hubungannya dengan server rate limiting, dan verifikasi bot.',
                'meta_title' => 'Mengapa Link Download Menggunakan Countdown? — LokaTaut',
                'meta_description' => 'Pahami alasan teknis di balik penggunaan timer countdown pada halaman pengalihan unduhan berkas dan bagaimana sistem membedakan manusia dari bot.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 4,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'content' => <<<'MARKDOWN'
## Fungsi di Balik Timer Hitung Mundur

Sebagian orang mungkin bertanya-tanya mengapa mereka harus menunggu selama 5 hingga 10 detik sebelum tombol unduhan dapat diklik. Apakah hitung mundur (*countdown*) tersebut sekadar formalitas, atau ada tujuan teknis di baliknya?

Jawabannya adalah: **hitungan mundur memiliki peran penting dalam arsitektur keamanan dan keandalan sistem distribusi tautan.**

### 1. Memperlambat Otomatisasi Skrip (Rate Limiting Alami)

Skrip bot otomatis dirancang untuk bergerak sangat cepat. Bot dapat memindai dan mengirimkan ribuan permintaan dalam satu detik untuk menguras bandwidth server. 

Dengan mewajibkan jeda waktu sebelum tautan dibuka, beban server penyimpanan berkas dapat dikendalikan. Jeda waktu ini memaksa laju permintaan berjalan pada ritme alami perilaku manusia.

### 2. Memberikan Waktu bagi Verifikasi Keamanan

Saat countdown berjalan di browser, sistem di sisi server melakukan sejumlah pemeriksaan penting:
- Memastikan sesi token belum kedaluwarsa.
- Menilai apakah interaksi yang terjadi wajar dan tidak meniru manipulasi JavaScript.
- Memverifikasi sinyal pemeriksaan bot pihak ketiga seperti Cloudflare Turnstile.

### 3. Perlindungan Server-Side Terhadap Bypass

Pada platform yang dirancang dengan benar, timer tidak sekadar berjalan secara visual di JavaScript peramban. Waktu mulai dicatat di basis data server. Jika ada pihak yang mencoba memicu tombol navigasi secara instan sebelum durasi yang ditentukan terlewati, server akan menolak permintaan tersebut secara tegas (*anti-bypass protection*).
MARKDOWN
            ],
            [
                'category_id' => $categories['keamanan-digital']->id ?? null,
                'title' => 'Tips Mengenali Link Download yang Aman Sebelum Dibuka',
                'slug' => 'tips-mengenali-link-download-aman',
                'excerpt' => 'Panduan praktis melindungi perangkat dari tautan phishing, malware, dan unduhan berbahaya dengan memeriksa protokol, domain, dan sertifikat SSL.',
                'meta_title' => 'Tips Mengenali Link Download yang Aman Sebelum Dibuka — LokaTaut',
                'meta_description' => 'Kenali ciri-ciri tautan unduhan yang aman dan cara memeriksa domain mencurigakan sebelum mengunduh berkas ke komputer atau ponsel Anda.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(4),
                'content' => <<<'MARKDOWN'
## Keamanan Siber Dimulai dari Kehati-hatian Mengklik Tautan

Internet menyediakan akses tak terbatas ke berbagai macam informasi dan aplikasi pendukung pekerjaan. Namun di balik kemudahan tersebut, tautan berbahaya (*malicious link*) tetap menjadi salah satu vektor utama penyebaran malware, ransomware, dan penipuan digital (*phishing*).

Sebelum Anda mengklik tombol unduh pada halaman tautan apa pun, luangkan waktu sejenak untuk mempraktikkan langkah-langkah verifikasi berikut:

### 1. Periksa Protokol HTTPS dan Nama Domain

Pastikan tautan yang Anda buka diawali dengan protokol aman `https://` yang ditandai dengan ikon gembok di bilah peramban. Selain itu, perhatikan ejaan nama domain dengan teliti. Penyerang siber kerap menggunakan teknik *typosquatting* (misalnya mengganti huruf 'o' dengan angka '0') untuk mengelabui pengunjung.

### 2. Arahkan Kursor (Hover) untuk Memeriksa URL Sebenarnya

Di perangkat komputer, arahkan kursor ke atas tautan tanpa mengkliknya. Perhatikan pojok kiri bawah jendela browser Anda. Peramban akan menampilkan alamat URL asli yang menjadi tujuan klik. Jika teks yang tertulis di halaman berbeda jauh dengan alamat yang muncul di bilah status peramban, Anda patut waspada.

### 3. Hindari Mengunduh Ekstensi Berkas yang Berisiko Tinggi

Waspadai berkas yang berformat `.exe`, `.scr`, `.bat`, atau `.vbs` jika Anda merasa sedang mengunduh dokumen teks atau materi presentasi. Berkas dokumen kerja yang sah umumnya berekstensi `.pdf`, `.docx`, `.xlsx`, atau terkompresi dalam format standar `.zip` dan `.tar.gz`.

Dengan menerapkan kebiasaan memeriksa tautan secara cermat, Anda dapat menjaga integritas data pribadi dan keamanan perangkat kerja Anda.
MARKDOWN
            ],
            [
                'category_id' => $categories['tips-produktivitas']->id ?? null,
                'title' => 'Cara Mengelola Banyak Link Download agar Tidak Berantakan',
                'slug' => 'cara-mengelola-banyak-link-download',
                'excerpt' => 'Metode sistematis mengarsipkan tautan materi digital, memanfaatkan tagging sumber web, dan memperbarui URL tujuan tanpa merusak link lama.',
                'meta_title' => 'Cara Mengelola Banyak Link Download agar Tidak Berantakan — LokaTaut',
                'meta_description' => 'Pelajari cara mengelola puluhan hingga ratusan link unduhan menggunakan sistem sentralisasi, pelabelan situs sumber, dan pemantauan tautan aktif.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'content' => <<<'MARKDOWN'
## Tantangan Mengelola Koleksi Tautan Skala Besar

Bagi pembuat konten, pengelola repositori perangkat lunak, maupun tenaga pendidik, jumlah tautan yang harus dibagikan dapat bertambah dengan cepat. Jika Anda mengelola puluhan atau ratusan link unduhan hanya melalui catatan spreadsheet biasa, Anda akan segera menghadapi masalah klasik:

- Tautan tujuan di server penyimpanan asli kadaluwarsa atau berganti alamat.
- Sulit mengetahui tautan mana yang masih aktif dan mana yang sudah rusak (*broken link*).
- Tidak memiliki visibilitas terhadap seberapa banyak file tersebut diakses oleh audiens.

### 1. Terapkan Sentralisasi Tautan (Decoupled Links)

Kunci utama dalam manajemen tautan profesional adalah **pemisahan antara tautan publik dan URL penyimpanan fisik**.

Ketika Anda membagikan tautan perantara, Anda bebas mengubah alamat server unduhan di panel kontrol kapan pun diperlukan. Anda tidak perlu menyunting ulang artikel lama atau memperbarui postingan di media sosial hanya karena file Anda dipindahkan ke server baru.

### 2. Manfaatkan Tagging Sumber Web (Source Tagging)

Kelompokkan tautan berdasarkan kategori atau situs web tempat tautan tersebut dipasang. Dengan memberi label sumber (misalnya `website-utama`, `newsletter-mingguan`, atau `kanal-telegram`), Anda dapat membandingkan efektivitas setiap kanal distribusi secara obyektif.

### 3. Evaluasi dan Bersihkan Tautan Usang Secara Berkala

Buat jadwal berkala untuk meninjau status tautan. Tautan untuk acara atau promosi yang telah selesai dapat dinonaktifkan atau disetel masa kadaluwarsanya (*expiration date*) agar tidak menimbulkan kebingungan bagi pengunjung di masa mendatang.
MARKDOWN
            ],
            [
                'category_id' => $categories['teknologi-internet']->id ?? null,
                'title' => 'Apa Perbedaan Shortlink, URL Redirect, dan Safelink?',
                'slug' => 'perbedaan-shortlink-url-redirect-safelink',
                'excerpt' => 'Membongkar perbedaan arsitektur, tujuan bisnis, mekanisme keamanan, dan alur peramban antara shortlink konvensional, HTTP redirect, dan safelink.',
                'meta_title' => 'Perbedaan Shortlink, URL Redirect, dan Safelink — LokaTaut',
                'meta_description' => 'Pahami perbedaan mendasar antara URL redirect standar, pemendek tautan (shortlink), dan halaman proteksi safelink dari segi teknis dan fungsi.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 4,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
                'content' => <<<'MARKDOWN'
## Membedakan Tiga Mekanisme Navigasi Web

Istilah *shortlink*, *redirect*, dan *safelink* sering kali tertukar dalam percakapan sehari-hari. Meskipun ketiganya sama-sama bertujuan menghantarkan pengunjung ke sebuah alamat web tertentu, cara kerja dan peruntukannya sangat berbeda.

Mari kita telaah perbedaannya secara terperinci:

### 1. URL Redirect (Pengalihan HTTP Murni)

URL redirect adalah mekanisme standar pada server web untuk mengalihkan peramban dari satu alamat ke alamat lain. Pengalihan ini bekerja di tingkat protokol jaringan (HTTP header) dan tidak memerlukan rendering halaman HTML. 

Contoh penggunaannya meliputi pengalihan dari versi non-SSL (`http://`) ke SSL (`https://`), atau pengalihan subdomain ke domain utama.

### 2. Shortlink (Pemendek Tautan Standar)

Shortlink menggabungkan basis data pemetaan dengan URL redirect. Pengguna mengakses URL pendek, server membaca basis data untuk menemukan alamat panjang pasangannya, lalu segera mengirimkan respon `301` atau `302`. 

Tujuan utamanya adalah **efisiensi karakter dan pelacakan klik**. Pengunjung dialihkan secara instan tanpa jeda waktu.

### 3. Safelink (Halaman Perantara Terproteksi)

Berbeda dengan shortlink instan, safelink menampilkan **antarmuka visual perantara**. Pada arsitektur safelink:
- Server tidak langsung mengarahkan peramban.
- Terjadi proses validasi seperti mitigasi bot dan penundaan waktu hitung mundur.
- Tautan tujuan baru diberikan setelah token sesi diverifikasi valid di sisi server.

Safelink dirancang untuk skenario di mana perlindungan tautan, pencegahan bot, dan penempatan sponsor menjadi prioritas operasional.
MARKDOWN
            ],
            [
                'category_id' => $categories['keamanan-digital']->id ?? null,
                'title' => 'Hal yang Perlu Diperhatikan Sebelum Membagikan File di Internet',
                'slug' => 'hal-penting-sebelum-membagikan-file-internet',
                'excerpt' => 'Checklist komprehensif sebelum mengunggah file publik: perlindungan hak cipta, penghapusan metadata sensitif, enkripsi arsip, dan kontrol akses.',
                'meta_title' => 'Hal yang Perlu Diperhatikan Sebelum Membagikan File di Internet — LokaTaut',
                'meta_description' => 'Ketahui panduan penting sebelum membagikan file di internet: sanitasi metadata, perlindungan privasi data, dan kehati-hatian lisensi berkas.',
                'author_name' => 'Dimas Aditya',
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => Carbon::now()->subDay(),
                'content' => <<<'MARKDOWN'
## Tanggung Jawab Keamanan Saat Mendistribusikan Berkas Digital

Sekali sebuah berkas diunggah dan dibagikan secara publik di internet, berkas tersebut dapat disalin dan didistribusikan kembali oleh siapa pun tanpa batasan. Oleh karena itu, penting untuk memastikan bahwa berkas yang Anda bagikan bersih dari data sensitif dan mematuhi etika hukum digital.

Sebelum Anda membagikan tautan berkas, pastikan Anda telah memeriksa beberapa poin krusial berikut:

### 1. Bersihkan Metadata Dokumen (EXIF & Author Info)

Banyak format file—seperti PDF, gambar JPEG, dan dokumen Word—secara otomatis merekam metadata tersembunyi. Metadata ini bisa mencakup nama lengkap pembuat dokumen, software yang digunakan, model perangkat, hingga koordinat GPS lokasi pemotretan. 

Sebelum membagikan berkas ke publik, gunakan fitur *Inspect Document* atau aplikasi pembersih metadata untuk menghapus informasi pribadi tersebut.

### 2. Terapkan Kata Sandi pada Berkas Sensitif

Jika berkas yang dibagikan ditujukan untuk kelompok terbatas, pertimbangkan untuk mengompresi berkas dalam format arsip terenkripsi (seperti ZIP dengan enkripsi AES-256). Jangan pernah membagikan kata sandi arsip di tempat yang sama dengan tautan unduhan publik.

### 3. Pastikan Legalitas dan Hak Distribusi

Pastikan Anda memiliki hak cipta atau izin lisensi resmi atas berkas yang Anda distribusikan. Menghindari materi bajakan tidak hanya melindungi Anda dari sanksi hukum, namun juga menjaga reputasi domain dan tautan Anda dari risiko pemblokiran oleh penyedia layanan internet (ISP) atau penyedia hosting.
MARKDOWN
            ],
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(
                ['slug' => $art['slug']],
                $art
            );
        }
    }
}

