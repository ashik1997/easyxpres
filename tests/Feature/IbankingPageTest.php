<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IbankingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_open_the_dedicated_ibanking_page(): void
    {
        $user = $this->approvedUserWithAccount();

        DB::table('bank_names')->insert([
            [
                'bank_name' => 'Professional Bank Limited',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bank_name' => 'Inactive Bank Limited',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('ibanking_rates')->insert([
            'rate' => 19.15,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('app.ibanking.form'));

        $response
            ->assertOk()
            ->assertSee('Send money to Bangladesh')
            ->assertSee('Professional Bank Limited')
            ->assertDontSee('Inactive Bank Limited')
            ->assertSee('1 MVR = 19.15 BDT')
            ->assertSee('action="'.route('app.ibanking.add').'"', false)
            ->assertSee('href="'.route('app.ibanking.history').'"', false)
            ->assertSee('name="bank_name_id"', false)
            ->assertSee('name="account_no"', false)
            ->assertSee('name="amount"', false)
            ->assertSee('Minimum 30,000 BDT')
            ->assertDontSee('Minimum 50,000 BDT');
    }

    public function test_dashboard_ibanking_action_links_to_the_dedicated_page(): void
    {
        $user = $this->approvedUserWithAccount();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('href="'.route('app.ibanking.form').'"', false)
            ->assertDontSee('id="iBankingModal"', false);
    }

    public function test_inactive_bank_cannot_be_used_for_an_ibanking_transfer(): void
    {
        $user = $this->approvedUserWithAccount();

        $inactiveBankId = DB::table('bank_names')->insertGetId([
            'bank_name' => 'Inactive Bank Limited',
            'status' => 'inactive',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('app.ibanking.form'))
            ->post(route('app.ibanking.add'), [
                'bank_name_id' => $inactiveBankId,
                'account_no' => '1234567890',
                'amount' => 3000,
            ]);

        $response
            ->assertRedirect(route('app.ibanking.form'))
            ->assertSessionHasErrors('bank_name_id');

        $this->assertDatabaseCount('ibanking_orders', 0);
    }

    public function test_all_ibanking_endpoints_reject_an_amount_below_30000_bdt(): void
    {
        $user = $this->approvedUserWithAccount();
        $bankId = $this->activeBankId();
        $this->setActiveRate(20);

        foreach (['ibanking.add', 'app.ibanking.add'] as $routeName) {
            $response = $this->actingAs($user)
                ->from(route('app.ibanking.form'))
                ->post(route($routeName), [
                    'bank_name_id' => $bankId,
                    'account_holder_name' => 'Test Account Holder',
                    'branch' => 'Dhaka Main Branch',
                    'account_no' => '1234567890',
                    'amount' => 1499.99,
                ]);

            $response
                ->assertRedirect(route('app.ibanking.form'))
                ->assertSessionHasErrors([
                    'amount' => 'The converted iBanking amount must be at least 30,000 BDT.',
                ]);
        }

        $this->assertDatabaseCount('ibanking_orders', 0);
        $this->assertSame(6000.0, (float) $user->account->fresh()->balance);
    }

    public function test_ibanking_allows_an_amount_equal_to_30000_bdt(): void
    {
        Mail::fake();

        $user = $this->approvedUserWithAccount();
        $bankId = $this->activeBankId();
        $this->setActiveRate(20);

        $response = $this->actingAs($user)->post(route('app.ibanking.add'), [
            'bank_name_id' => $bankId,
            'account_holder_name' => 'Test Account Holder',
            'branch' => 'Dhaka Main Branch',
            'account_no' => '1234567890',
            'amount' => 1500,
        ]);

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('ibanking_orders', [
            'account_id' => $user->account->id,
            'amount' => 1500,
            'bdt_amount' => 30000,
            'status' => 'pending',
        ]);
        $this->assertSame(4500.0, (float) $user->account->fresh()->balance);
    }

    private function activeBankId(): int
    {
        return DB::table('bank_names')->insertGetId([
            'bank_name' => 'Professional Bank Limited',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function setActiveRate(float $rate): void
    {
        DB::table('ibanking_rates')->insert([
            'rate' => $rate,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function approvedUserWithAccount(): User
    {
        $user = User::factory()->create([
            'status' => 'approved',
            'email_verified_at' => now(),
        ]);

        Account::create([
            'user_id' => $user->id,
            'account_number' => 'UI-'.str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            'balance' => 6000,
        ]);

        return $user;
    }
}
