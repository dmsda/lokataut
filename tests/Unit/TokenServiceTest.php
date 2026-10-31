<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TokenService;
use Tests\TestCase;
use RuntimeException;

class TokenServiceTest extends TestCase
{
    public function test_generate_raw_token_returns_64_hex_string(): void
    {
        $service = new TokenService();
        $token = $service->generateRawToken();

        $this->assertEquals(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    public function test_hash_token_produces_consistent_sha256(): void
    {
        $service = new TokenService();
        $raw = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
        $hash = $service->hashToken($raw);

        $this->assertEquals(hash('sha256', $raw), $hash);
        $this->assertEquals(64, strlen($hash));
    }

    public function test_hash_ip_throws_runtime_exception_when_salt_is_missing(): void
    {
        $service = new TokenService();

        // Temporarily clear config
        config(['services.analytics.salt' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('ANALYTICS_SALT is not configured');

        $service->hashIp('192.168.1.1');
    }

    public function test_hash_ip_returns_hmac_sha256_when_salt_is_configured(): void
    {
        $service = new TokenService();

        config(['services.analytics.salt' => 'super_secret_salt_key']);

        $ip = '203.0.113.195';
        $hash = $service->hashIp($ip);

        $expected = hash_hmac('sha256', $ip, 'super_secret_salt_key');
        $this->assertEquals($expected, $hash);
        $this->assertEquals(64, strlen($hash));
    }
}
