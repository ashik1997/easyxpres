<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\MaleRecharge;
use App\Models\MaliOperator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MaldivesRechargePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_open_the_dedicated_maldives_recharge_page(): void
    {
        [$user] = $this->createApprovedUserWithAccount(1200);
        $this->createOperators();

        $this->actingAs($user)
            ->get(route('app.male.recharge.form'))
            ->assertOk()
            ->assertDontSee('Secure request')
            ->assertDontSee('Available balance')
            ->assertSee('Recharge a Maldives number')
            ->assertSee('Maldives Recharge History')
            ->assertSee('Dhiraagu')
            ->assertSee('Ooredoo')
            ->assertSee('href="'.route('app.male.recharge.history').'"', false)
            ->assertSee('action="'.route('app.male.recharge.submit').'"', false)
            ->assertSee('name="mobile"', false)
            ->assertSee('name="amount"', false);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('app.male.recharge.form').'"', false);
    }

    public function test_maldives_history_shows_operator_amount_commission_and_status(): void
    {
        [$user, $account] = $this->createApprovedUserWithAccount(1200);
        $this->createOperators();

        MaleRecharge::create([
            'account_id' => $account->id,
            'mobile' => '+9607771234',
            'amount' => 50,
            'commission' => 3.5,
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('app.male.recharge.history'))
            ->assertOk()
            ->assertSee('Maldives Recharge History')
            ->assertSeeInOrder(['Number', 'Operator', 'Amount', 'Commission', 'Status', 'Date'])
            ->assertSee('+9607771234')
            ->assertSee('Dhiraagu')
            ->assertSee('MVR 50.00')
            ->assertSee('MVR 3.50')
            ->assertSee('Success');
    }

    public function test_existing_app_submit_flow_still_creates_recharge_and_deducts_balance(): void
    {
        Mail::fake();
        [$user, $account] = $this->createApprovedUserWithAccount(100);

        $this->actingAs($user)
            ->post(route('app.male.recharge.submit'), [
                'mobile' => '7771234',
                'amount' => 25,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('male_recharges', [
            'account_id' => $account->id,
            'mobile' => '7771234',
            'amount' => 25,
            'commission' => 0,
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
            'account_number' => 'MVR-'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            'balance' => $balance,
        ]);

        return [$user, $account];
    }

    private function createOperators(): void
    {
        MaliOperator::create([
            'name' => 'Dhiraagu',
            'code' => '7',
            'commission' => 7,
            'status' => 'active',
        ]);
        MaliOperator::create([
            'name' => 'Ooredoo',
            'code' => '9',
            'commission' => 5,
            'status' => 'active',
        ]);
    }
}
