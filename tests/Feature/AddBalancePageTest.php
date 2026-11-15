<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddBalancePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_open_the_dedicated_add_balance_page(): void
    {
        $user = User::factory()->create([
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        Account::create([
            'user_id' => $user->id,
            'account_number' => 'UI-'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            'balance' => 1200,
        ]);

        $response = $this->actingAs($user)
            ->get(route('balance.add.form'));

        $response
            ->assertOk()
            ->assertDontSee('EasyXpres wallet')
            ->assertDontSee('Available balance')
            ->assertSee('Manual Request')
            ->assertSee('Pay with Digital Banking')
            ->assertSee('Balance History')
            ->assertSee('href="'.route('app.balance.history').'"', false)
            ->assertSee('action="'.route('app.balance.add').'"', false)
            ->assertSee('name="amount"', false)
            ->assertSee('name="file_upload"', false);

        $this->assertMatchesRegularExpression(
            '/id="manual-method-tab".*?class="balance-method-card is-selected".*?aria-selected="true"/s',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/id="manual-method-panel"\s+class="balance-method-panel p-5 sm:p-7"/s',
            $response->getContent()
        );
    }
}
