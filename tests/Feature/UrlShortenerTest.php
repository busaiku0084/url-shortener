<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_short_url(): void
    {
        $response = $this->postJson('/api/urls', [
            'url' => 'https://example.com/very/long/path',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'short_url',
                'short_code',
                'original_url',
                'expires_at',
            ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com/very/long/path',
        ]);
    }

    public function test_can_create_short_url_with_custom_code(): void
    {
        $response = $this->postJson('/api/urls', [
            'url' => 'https://example.com',
            'custom_code' => 'mycode',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['short_code' => 'mycode']);
    }

    public function test_can_create_short_url_with_expiration(): void
    {
        $response = $this->postJson('/api/urls', [
            'url' => 'https://example.com',
            'expires_at' => now()->addDays(7)->toIso8601String(),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['expires_at']);
    }

    public function test_rejects_invalid_url(): void
    {
        $response = $this->postJson('/api/urls', [
            'url' => 'not-a-url',
        ]);

        $response->assertStatus(422);
    }

    public function test_rejects_duplicate_custom_code(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'taken',
        ]);

        $response = $this->postJson('/api/urls', [
            'url' => 'https://example.com/other',
            'custom_code' => 'taken',
        ]);

        $response->assertStatus(422);
    }

    public function test_redirects_to_original_url(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'abc123',
        ]);

        $response = $this->get('/abc123');

        $response->assertRedirect('https://example.com');
    }

    public function test_increments_click_count(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'abc123',
        ]);

        $this->get('/abc123');
        $this->get('/abc123');

        $this->assertEquals(2, $url->fresh()->clicks);
    }

    public function test_expired_url_returns_404(): void
    {
        Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'expired',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get('/expired');

        $response->assertStatus(404);
    }

    public function test_can_get_url_stats(): void
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'stats1',
        ]);
        Url::where('id', $url->id)->update(['clicks' => 42]);

        $response = $this->getJson('/api/urls/stats1');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'short_code' => 'stats1',
                'original_url' => 'https://example.com',
                'clicks' => 42,
            ]);
    }

    public function test_nonexistent_code_returns_404(): void
    {
        $response = $this->get('/nonexist');

        $response->assertStatus(404);
    }
}
