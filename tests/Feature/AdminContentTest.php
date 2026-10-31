<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Dimas Aditya',
            'email' => 'dimas@lokataut.com',
            'password' => Hash::make('SecretAdmin123!@#'),
        ]);

        $this->category = Category::create([
            'name' => 'Keamanan Digital',
            'slug' => 'keamanan-digital',
            'description' => 'Kategori artikel keamanan',
        ]);
    }

    public function test_guest_cannot_access_admin_content_routes(): void
    {
        $this->get('/admin/articles')->assertRedirect('/login');
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->get('/admin/articles/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_articles_index(): void
    {
        Article::create([
            'category_id' => $this->category->id,
            'title' => 'Panduan Keamanan Safelink Admin',
            'slug' => 'panduan-keamanan-safelink-admin',
            'excerpt' => 'Ringkasan panduan.',
            'content' => 'Konten panduan admin.',
            'status' => 'published',
            'published_at' => now(),
            'reading_time' => 4,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/articles');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Artikel');
        $response->assertSee('Panduan Keamanan Safelink Admin');
    }

    public function test_admin_can_view_create_article_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/articles/create');

        $response->assertStatus(200);
        $response->assertSee('Tulis Artikel Baru');
        $response->assertSee($this->category->name);
    }

    public function test_admin_can_create_new_article(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/articles', [
            'category_id' => $this->category->id,
            'title' => 'Mengapa Keamanan Token Safelink Krusial',
            'slug' => 'mengapa-keamanan-token-safelink-krusial',
            'excerpt' => 'Artikel tentang pentingnya HMAC.',
            'content' => "## Mengapa Token Hash Dibutuhkan\n\nPenjelasan mendalam mengenai token hash.",
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
            'meta_title' => 'Keamanan Token Safelink',
            'meta_description' => 'Penjelasan teknis tentang HMAC dan session token safelink.',
            'author_name' => 'Dimas Aditya',
        ]);

        $response->assertRedirect('/admin/articles');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('articles', [
            'slug' => 'mengapa-keamanan-token-safelink-krusial',
            'status' => 'published',
            'author_name' => 'Dimas Aditya',
        ]);
    }

    public function test_admin_can_update_article(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Judul Sebelum Diperbarui',
            'slug' => 'judul-sebelum-diperbarui',
            'excerpt' => 'Ringkasan lama.',
            'content' => 'Konten lama.',
            'status' => 'draft',
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->actingAs($this->admin)->put('/admin/articles/' . $article->id, [
            'category_id' => $this->category->id,
            'title' => 'Judul Setelah Diperbarui Berhasil',
            'slug' => 'judul-setelah-diperbarui-berhasil',
            'excerpt' => 'Ringkasan baru yang telah direvisi.',
            'content' => 'Konten baru yang lebih komprehensif.',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d\TH:i'),
            'author_name' => 'Dimas Aditya',
        ]);

        $response->assertRedirect('/admin/articles');
        $response->assertSessionHas('success');

        $article->refresh();
        $this->assertEquals('Judul Setelah Diperbarui Berhasil', $article->title);
        $this->assertEquals('published', $article->status);
    }

    public function test_admin_can_soft_delete_article(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Artikel yang Akan Dihapus',
            'slug' => 'artikel-akan-dihapus',
            'excerpt' => 'Ringkasan.',
            'content' => 'Konten.',
            'status' => 'draft',
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->actingAs($this->admin)->delete('/admin/articles/' . $article->id);

        $response->assertRedirect('/admin/articles');
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_admin_can_restore_soft_deleted_article(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Artikel Terhapus',
            'slug' => 'artikel-terhapus',
            'excerpt' => 'Ringkasan.',
            'content' => 'Konten.',
            'status' => 'draft',
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);
        $article->delete();

        $this->assertSoftDeleted('articles', ['id' => $article->id]);

        $response = $this->actingAs($this->admin)->post('/admin/articles/' . $article->id . '/restore');

        $response->assertRedirect();
        $this->assertNotSoftDeleted('articles', ['id' => $article->id]);
    }

    public function test_admin_can_force_delete_article(): void
    {
        $article = Article::create([
            'category_id' => $this->category->id,
            'title' => 'Artikel Purge Permanen',
            'slug' => 'artikel-purge-permanen',
            'excerpt' => 'Ringkasan.',
            'content' => 'Konten.',
            'status' => 'draft',
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);
        $article->delete();

        $response = $this->actingAs($this->admin)->delete('/admin/articles/' . $article->id . '/force');

        $response->assertRedirect();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_admin_can_manage_categories(): void
    {
        // 1. Create category
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Kategori Baru Pengujian',
            'slug' => 'kategori-baru-pengujian',
            'description' => 'Deskripsi kategori pengujian.',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['slug' => 'kategori-baru-pengujian']);

        $category = Category::where('slug', 'kategori-baru-pengujian')->firstOrFail();

        // 2. Update category
        $response = $this->actingAs($this->admin)->put('/admin/categories/' . $category->id, [
            'name' => 'Kategori Diubah Namanya',
            'slug' => 'kategori-diubah-namanya',
            'description' => 'Deskripsi diperbarui.',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['slug' => 'kategori-diubah-namanya']);

        // 3. Delete category
        $response = $this->actingAs($this->admin)->delete('/admin/categories/' . $category->id);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_with_articles_cannot_be_deleted(): void
    {
        Article::create([
            'category_id' => $this->category->id,
            'title' => 'Artikel Pengikat Kategori',
            'slug' => 'artikel-pengikat-kategori',
            'excerpt' => 'Ringkasan.',
            'content' => 'Konten.',
            'status' => 'published',
            'published_at' => now(),
            'reading_time' => 2,
            'author_name' => 'Dimas Aditya',
        ]);

        $response = $this->actingAs($this->admin)->delete('/admin/categories/' . $this->category->id);

        $response->assertRedirect('/admin/categories');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }
}

