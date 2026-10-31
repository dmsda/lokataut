<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Link;
use App\Models\VisitorSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafelinkEngineTest extends TestCase
{
    use RefreshDatabase;

    private Link $link;

    protected function setUp(): void
    {
        parent::setUp();

        $this->link = Link::create([
            'slug' => 'secret-download',
            'destination_url' => 'https://super-confidential-storage.com/payload.iso',
            'title' => 'ISO Payload Download',
            'countdown_seconds' => 5,
            'status' => 'active',
        ]);
    }

    public function test_safelink_page_never_exposes_destination_url_in_html(): void
    {
        $response = $this->get('/s/secret-download');

        $response->assertStatus(200);
        $response->assertSee('ISO Payload Download');

        // Critical Security Rule 3: The destination URL MUST NEVER be sent to client
        $response->assertDontSee('https://super-confidential-storage.com/payload.iso');
        $response->assertDontSee('payload.iso');

        // Visitor session must be created
        $this->assertDatabaseHas('visitor_sessions', [
            'link_id' => $this->link->id,
            'completed' => false,
        ]);
    }

    public function test_turnstile_verification_sets_countdown_started_at(): void
    {
        // 1. Visit page to generate session
        $this->get('/s/secret-download');

        $session = VisitorSession::where('link_id', $this->link->id)->first();
        $this->assertNotNull($session);
        $this->assertFalse($session->turnstile_verified);
        $this->assertNull($session->countdown_started_at);

        // We need the raw token: let's create a known raw token
        $rawToken = bin2hex(random_bytes(32));
        $session->update([
            'token_hash' => hash('sha256', $rawToken),
        ]);

        // 2. Call verify API with dummy test token
        $response = $this->postJson('/s/secret-download/verify', [
            'token' => $rawToken,
            'turnstile_token' => 'test_dummy_pass',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $session->refresh();
        $this->assertTrue($session->turnstile_verified);
        $this->assertNotNull($session->countdown_started_at);
    }

    public function test_server_rejects_premature_proceed_attempt_before_timer_expires(): void
    {
        $rawToken = bin2hex(random_bytes(32));

        // Create verified session where countdown started only 1 second ago (needs 5s)
        $session = VisitorSession::create([
            'link_id' => $this->link->id,
            'token_hash' => hash('sha256', $rawToken),
            'turnstile_verified' => true,
            'turnstile_verified_at' => Carbon::now(),
            'countdown_started_at' => Carbon::now()->subSeconds(1), // Only 1 second elapsed!
            'countdown_seconds' => 5,
            'completed' => false,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $response = $this->postJson('/s/secret-download/proceed', [
            'token' => $rawToken,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        // Session must still be incomplete
        $session->refresh();
        $this->assertFalse($session->completed);
    }

    public function test_proceed_succeeds_after_countdown_and_returns_decrypted_url(): void
    {
        $rawToken = bin2hex(random_bytes(32));

        // Create verified session where countdown started 6 seconds ago (needed 5s)
        $session = VisitorSession::create([
            'link_id' => $this->link->id,
            'token_hash' => hash('sha256', $rawToken),
            'turnstile_verified' => true,
            'turnstile_verified_at' => Carbon::now()->subSeconds(6),
            'countdown_started_at' => Carbon::now()->subSeconds(6), // 6 seconds elapsed
            'countdown_seconds' => 5,
            'completed' => false,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $response = $this->postJson('/s/secret-download/proceed', [
            'token' => $rawToken,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect_url' => 'https://super-confidential-storage.com/payload.iso',
        ]);

        // Atomic update completed
        $session->refresh();
        $this->assertTrue($session->completed);
        $this->assertNotNull($session->completed_at);

        // Completion visit recorded
        $this->assertDatabaseHas('visits', [
            'link_id' => $this->link->id,
            'event_type' => 'completed',
        ]);
    }

    public function test_replay_attack_with_same_token_is_rejected(): void
    {
        $rawToken = bin2hex(random_bytes(32));

        // Already completed session
        VisitorSession::create([
            'link_id' => $this->link->id,
            'token_hash' => hash('sha256', $rawToken),
            'turnstile_verified' => true,
            'turnstile_verified_at' => Carbon::now()->subSeconds(10),
            'countdown_started_at' => Carbon::now()->subSeconds(10),
            'countdown_seconds' => 5,
            'completed' => true, // Already completed!
            'completed_at' => Carbon::now()->subSeconds(5),
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $response = $this->postJson('/s/secret-download/proceed', [
            'token' => $rawToken,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_expired_link_renders_expired_view(): void
    {
        $expiredLink = Link::create([
            'slug' => 'expired-link',
            'destination_url' => 'https://example.com/expired',
            'title' => 'Expired Link',
            'expires_at' => Carbon::now()->subDay(),
            'status' => 'active',
        ]);

        $response = $this->get('/s/expired-link');
        $response->assertStatus(200);
        $response->assertSee('Tautan Tidak Tersedia');
    }
}
