<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopAutoLoginTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'base64:c2hvcC1hdXRvLWxvZ2luLXRlc3Qta2V5LTEyMzQ1Njc=';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'shop_auto_login.key' => self::KEY,
            'shop_auto_login.target_url' => 'http://127.0.0.1:8002/auth/shop/auto-login',
            'shop_auto_login.ttl_seconds' => 60,
        ]);
    }

    public function test_approved_user_is_redirected_with_an_encrypted_shop_handoff_token(): void
    {
        $user = User::factory()->create(['phone' => '9607712345', 'status' => 'approved']);

        $response = $this->actingAs($user)->get(route('shop.auto-login'));
        $location = $response->headers->get('Location');

        $response
            ->assertRedirectContains('http://127.0.0.1:8002/auth/shop/auto-login?token=')
            ->assertHeader('Referrer-Policy', 'no-referrer');

        $this->assertIsString($location);
        $this->assertStringNotContainsString('9607712345', $location);
        $this->assertStringNotContainsString($user->name, $location);
        $this->assertStringNotContainsString($user->email, $location);
    }

    public function test_shop_handoff_rejects_a_missing_phone_number(): void
    {
        $user = User::factory()->create(['phone' => null, 'status' => 'approved']);

        $this->actingAs($user)
            ->get(route('shop.auto-login'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'A valid phone number is required before opening Shop.');
    }

    public function test_shop_handoff_requires_authentication(): void
    {
        $this->get('/shop/auto-login')->assertRedirect(route('login'));
    }
}
