<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_accessible_and_contains_core_elements(): void
    {
        $category = Category::create([
            'name' => 'Keamanan Digital',
            'slug' => 'keamanan-digital',
            'description' => 'Edukasi keamanan tautan',
        ]);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Panduan Lengkap Keamanan Tautan',
            'slug' => 'panduan-lengkap-keamanan-tautan',
            'excerpt' => 'Ringkasan panduan keamanan tautan digital.',
            'content' => "## Pengantar Keamanan\n\nIsi konten lengkap edukasi.",
            'status' => 'published',
            'published_at' => now(),
            'reading_time' => 3,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('LokaTaut');
        $response->assertSee('Fitur Lengkap Pengelolaan Tautan Digital');
        $response->assertSee('Panduan Lengkap Keamanan Tautan');
        $response->assertSee('Pertanyaan yang Sering Diajukan (FAQ)');
    }

    public function test_about_page_is_accessible(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Mengenal LokaTaut');
        $response->assertSee('Single-Owner Governance');
    }

    public function test_help_faq_page_is_accessible(): void
    {
        $response = $this->get('/help');
        $response->assertStatus(200);
        $response->assertSee('Pusat Bantuan & Panduan');
        $response->assertSee('Cloudflare Turnstile');
    }

    public function test_privacy_policy_page_is_accessible_and_mentions_pseudonymization(): void
    {
        $response = $this->get('/privacy-policy');
        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('HMAC-SHA256');
    }

    public function test_terms_of_service_page_is_accessible(): void
    {
        $response = $this->get('/terms');
        $response->assertStatus(200);
        $response->assertSee('Syarat & Ketentuan Layanan', false);
        $response->assertSee('Kebijakan Konten & Larangan Penggunaan', false);
    }

    public function test_contact_page_is_accessible(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Hubungi Kami');
        $response->assertSee('dimas@lokataut.com');
    }

    public function test_contact_form_submits_successfully_with_valid_data(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'subject' => 'Pertanyaan Kemitraan',
            'message' => 'Halo pengelola LokaTaut, saya ingin bertanya mengenai integrasi tautan edukasi.',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');
    }

    public function test_contact_form_validation_rejects_invalid_email(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Budi Santoso',
            'email' => 'not-an-email',
            'subject' => 'Pertanyaan',
            'message' => 'Pesan pengujian',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_sitemap_xml_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('xml', $response->headers->get('Content-Type') ?? '');
        $response->assertSee('<urlset', false);
        $response->assertSee('<loc>', false);
    }

    public function test_robots_txt_returns_text_with_sitemap(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
        $response->assertSee('User-agent: *');
        $response->assertSee('Sitemap:');
        $response->assertSee('Disallow: /admin/');
    }
}
