<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    private Category $categoryA;
    private Category $categoryB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryA = Category::create([
            'name' => 'Keamanan Digital',
            'slug' => 'keamanan-digital',
            'description' => 'Topik keamanan tautan dan berkas',
        ]);

        $this->categoryB = Category::create([
            'name' => 'Optimalisasi Web',
            'slug' => 'optimalisasi-web',
            'description' => 'Topik kinerja dan arsitektur web',
        ]);
    }

    public function test_blog_index_is_accessible_and_renders_published_articles(): void
    {
        Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Cara Mencegah Serangan Phishing Lewat Link',
            'slug' => 'cara-mencegah-phishing-link',
            'excerpt' => 'Ringkasan panduan phishing.',
            'content' => '## Kenali Ciri Phishing\n\nIsi panduan lengkap.',
            'status' => 'published',
            'published_at' => Carbon::now()->subHour(),
            'reading_time' => 4,
            'author_name' => 'Dimas Aditya',
        ]);

        Article::create([
            'category_id' => $this->categoryB->id,
            'title' => 'Teknik Caching Efisien untuk Shared Hosting',
            'slug' => 'teknik-caching-shared-hosting',
            'excerpt' => 'Ringkasan teknik caching.',
            'content' => '## Caching di Laravel\n\nOptimasi memory dan file cache.',
            'status' => 'published',
            'published_at' => Carbon::now()->subHours(2),
            'reading_time' => 5,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('Cara Mencegah Serangan Phishing Lewat Link');
        $response->assertSee('Teknik Caching Efisien untuk Shared Hosting');
        $response->assertSee('Keamanan Digital');
        $response->assertSee('Optimalisasi Web');
    }

    public function test_blog_search_filters_matching_articles(): void
    {
        Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Mengenal Kriptografi Token Safelink',
            'slug' => 'mengenal-kriptografi-token-safelink',
            'excerpt' => 'Enkripsi dan HMAC token.',
            'content' => 'Penjelasan kriptografi.',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinutes(10),
            'reading_time' => 3,
            'author_name' => 'Dimas Aditya',
        ]);

        Article::create([
            'category_id' => $this->categoryB->id,
            'title' => 'Panduan Konfigurasi Nginx Reverse Proxy',
            'slug' => 'panduan-konfigurasi-nginx',
            'excerpt' => 'Konfigurasi web server Nginx.',
            'content' => 'Panduan setup Nginx.',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinutes(20),
            'reading_time' => 6,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog?q=Kriptografi');

        $response->assertStatus(200);
        $response->assertSee('Mengenal Kriptografi Token Safelink');
        $response->assertDontSee('Panduan Konfigurasi Nginx Reverse Proxy');
    }

    public function test_blog_category_archive_renders_category_articles(): void
    {
        Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Artikel Khusus Keamanan Siber',
            'slug' => 'artikel-khusus-keamanan-siber',
            'excerpt' => 'Fokus keamanan.',
            'content' => 'Konten keamanan.',
            'status' => 'published',
            'published_at' => Carbon::now()->subDay(),
            'reading_time' => 4,
            'author_name' => 'Dimas Aditya',
        ]);

        Article::create([
            'category_id' => $this->categoryB->id,
            'title' => 'Artikel Khusus Web Performance',
            'slug' => 'artikel-khusus-web-performance',
            'excerpt' => 'Fokus performa.',
            'content' => 'Konten performa.',
            'status' => 'published',
            'published_at' => Carbon::now()->subDay(),
            'reading_time' => 5,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog/category/' . $this->categoryA->slug);

        $response->assertStatus(200);
        $response->assertSee('Artikel Khusus Keamanan Siber');
        $response->assertDontSee('Artikel Khusus Web Performance');
    }

    public function test_published_article_detail_page_is_accessible(): void
    {
        $article = Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Deep Dive: Mengapa URL Asli Tidak Boleh Bocor ke Client',
            'slug' => 'deep-dive-url-asli-tidak-boleh-bocor',
            'excerpt' => 'Analisis keamanan safelink modern.',
            'content' => "## Ancaman Scraping Bot\n\nBot dapat membaca DOM peramban jika link ditaruh di data-attribute.",
            'status' => 'published',
            'published_at' => Carbon::now()->subHours(3),
            'reading_time' => 5,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog/' . $article->slug);

        $response->assertStatus(200);
        $response->assertSee('Deep Dive: Mengapa URL Asli Tidak Boleh Bocor ke Client');
        $response->assertSee('Dimas Aditya');
        $response->assertSee('Ancaman Scraping Bot');
        $response->assertSee('5 menit membaca');
    }

    public function test_draft_article_returns_404_for_public_visitors(): void
    {
        $draft = Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Artikel Masih Konsep Rahasia',
            'slug' => 'artikel-masih-konsep-rahasia',
            'excerpt' => 'Hanya admin yang boleh tahu.',
            'content' => 'Draft rahasia.',
            'status' => 'draft',
            'published_at' => null,
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog/' . $draft->slug);

        $response->assertStatus(404);
    }

    public function test_scheduled_future_article_returns_404_for_public_visitors(): void
    {
        $scheduled = Article::create([
            'category_id' => $this->categoryA->id,
            'title' => 'Artikel Rilis Minggu Depan',
            'slug' => 'artikel-rilis-minggu-depan',
            'excerpt' => 'Belum terbit.',
            'content' => 'Konten terjadwal.',
            'status' => 'scheduled',
            'published_at' => Carbon::now()->addDays(7),
            'reading_time' => 3,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/blog/' . $scheduled->slug);

        $response->assertStatus(404);
    }

    public function test_non_existent_article_returns_404(): void
    {
        $response = $this->get('/blog/artikel-yang-tidak-pernah-ada-12345');

        $response->assertStatus(404);
    }
}
