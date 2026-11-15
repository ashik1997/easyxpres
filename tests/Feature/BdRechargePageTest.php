<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Recharge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BdRechargePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_open_the_dedicated_bd_recharge_page(): void
    {
        [$user] = $this->createApprovedUserWithAccount(1200);

        $this->actingAs($user)
            ->get(route('app.recharge.form'))
            ->assertOk()
            ->assertDontSee('Secure request')
            ->assertDontSee('Available balance')
            ->assertSee('Recharge a BD number')
            ->assertSee('BD Recharge History')
            ->assertSee('href="'.route('app.recharge.history').'"', false)
            ->assertSee('action="'.route('app.recharge.submit').'"', false)
            ->assertSee('name="mobile"', false)
            ->assertSee('name="amount"', false);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('app.recharge.form').'"', false);
    }

    public function test_recharge_history_shows_saved_data_and_prefix_based_operator(): void
    {
        [$user, $account] = $this->createApprovedUserWithAccount(1200);

        Recharge::create([
            'account_id' => $account->id,
            'mobile' => '+8801712345678',
            'amount' => 25,
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('app.recharge.history'))
            ->assertOk()
            ->assertSee('BD Recharge History')
            ->assertSeeInOrder(['Number', 'Operator', 'Amount', 'Status', 'Date'])
            ->assertSee('+8801712345678')
            ->assertSee('Grameenphone')
            ->assertSee('MVR 25.00')
            ->assertSee('Success');
    }

    public function test_existing_app_submit_flow_still_creates_recharge_and_deducts_balance(): void
    {
        Mail::fake();
        [$user, $account] = $this->createApprovedUserWithAccount(100);

        $this->actingAs($user)
            ->post(route('app.recharge.submit'), [
                'mobile' => '01812345678',
                'amount' => 25,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('recharges', [
            'account_id' => $account->id,
            'mobile' => '01812345678',
            'amount' => 25,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 75,
        ]);
    }

    private function createApprovedUserWithAccount(float $balance): array
    {
        $user = User::factory()->create([
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'BDR-'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            'balance' => $balance,
        ]);

        return [$user, $account];
    }
}
