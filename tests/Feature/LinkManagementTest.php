<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Link;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LinkManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Dimas Aditya',
            'email' => 'dimas@lokataut.com',
            'password' => Hash::make('SecretAdmin123!@#'),
        ]);
    }

    public function test_admin_can_view_links_index(): void
    {
        $this->actingAs($this->admin);

        Link::create([
            'slug' => 'photoshop-2026',
            'destination_url' => 'https://downloads.example.com/ps2026.zip',
            'title' => 'Adobe Photoshop 2026',
            'status' => 'active',
            'countdown_seconds' => 10,
        ]);

        $response = $this->get('/admin/links');
        $response->assertStatus(200);
        $response->assertSee('photoshop-2026');
        $response->assertSee('Adobe Photoshop 2026');
    }

    public function test_admin_can_create_link_with_custom_slug(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/links', [
            'destination_url' => 'https://example.com/my-secure-file.zip',
            'title' => 'My Secure File',
            'slug' => 'custom-file',
            'countdown_seconds' => 15,
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/links');

        $this->assertDatabaseHas('links', [
            'slug' => 'custom-file',
            'title' => 'My Secure File',
            'countdown_seconds' => 15,
        ]);
    }

    public function test_admin_can_create_link_with_auto_generated_slug(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/links', [
            'destination_url' => 'https://example.com/another-file.zip',
            'title' => 'Auto Slug Test',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/links');

        $link = Link::where('title', 'Auto Slug Test')->first();
        $this->assertNotNull($link);
        $this->assertEquals(6, strlen($link->slug));
    }

    public function test_admin_can_edit_link(): void
    {
        $this->actingAs($this->admin);

        $link = Link::create([
            'slug' => 'old-slug',
            'destination_url' => 'https://example.com/old.zip',
            'title' => 'Old Title',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        $response = $this->put("/admin/links/{$link->id}", [
            'destination_url' => 'https://example.com/new.zip',
            'title' => 'Updated Title',
            'slug' => 'new-slug',
            'countdown_seconds' => 20,
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/links');

        $link->refresh();
        $this->assertEquals('new-slug', $link->slug);
        $this->assertEquals('Updated Title', $link->title);
        $this->assertEquals(20, $link->countdown_seconds);
    }

    public function test_admin_can_soft_delete_and_restore_link(): void
    {
        $this->actingAs($this->admin);

        $link = Link::create([
            'slug' => 'delete-me',
            'destination_url' => 'https://example.com/file.zip',
            'title' => 'Delete Me',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        // Soft delete
        $response = $this->delete("/admin/links/{$link->id}");
        $response->assertRedirect('/admin/links');

        $this->assertSoftDeleted('links', ['id' => $link->id]);

        // Restore
        $restoreResponse = $this->post("/admin/links/{$link->id}/restore");
        $restoreResponse->assertRedirect('/admin/links?status=trash');

        $this->assertNotSoftDeleted('links', ['id' => $link->id]);
    }

    public function test_admin_can_force_delete_link_while_preserving_visit_snapshots(): void
    {
        $this->actingAs($this->admin);

        $link = Link::create([
            'slug' => 'purge-target',
            'destination_url' => 'https://example.com/purge.zip',
            'title' => 'Purge Target',
            'countdown_seconds' => 10,
            'status' => 'active',
        ]);

        // Record a visit with snapshot
        $visit = Visit::create([
            'link_id' => $link->id,
            'link_id_snapshot' => $link->id,
            'link_slug_snapshot' => 'purge-target',
            'event_type' => 'impression',
            'ip_hash' => 'dummy_ip_hash',
            'device_type' => 'desktop',
            'created_at' => now(),
        ]);

        // Soft delete first
        $link->delete();

        // Hard purge
        $response = $this->delete("/admin/links/{$link->id}/force");
        $response->assertRedirect('/admin/links?status=trash');

        // Link row in links table is gone
        $this->assertDatabaseMissing('links', ['id' => $link->id]);

        // Visit row still exists, link_id is set to NULL, snapshots remain intact!
        $visit->refresh();
        $this->assertNull($visit->link_id);
        $this->assertEquals($link->id, $visit->link_id_snapshot);
        $this->assertEquals('purge-target', $visit->link_slug_snapshot);
    }

    public function test_admin_cannot_use_reserved_slugs(): void
    {
        $this->actingAs($this->admin);

        foreach (['admin', 'blog', 'login', 'api', 'dashboard'] as $reservedSlug) {
            $response = $this->post('/admin/links', [
                'destination_url' => 'https://example.com/file.zip',
                'title' => 'Reserved Slug Attempt',
                'slug' => $reservedSlug,
                'countdown_seconds' => 10,
                'status' => 'active',
            ]);

            $response->assertSessionHasErrors(['slug']);
        }
    }
}

