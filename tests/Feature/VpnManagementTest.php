<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Models\Vpn;
use App\Models\VpnName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VpnManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_displays_available_active_unexpired_vpns(): void
    {
        $user = $this->approvedUser();
        $activeName = VpnName::create(['name' => 'Nord VPN', 'status' => 'active']);
        $inactiveName = VpnName::create(['name' => 'Disabled VPN', 'status' => 'inactive']);

        $available = $this->vpn($activeName, ['user_name' => 'available-user']);
        $this->vpn($activeName, ['user_name' => 'sold-user', 'buy_now_status' => true]);
        $this->vpn($activeName, ['user_name' => 'inactive-user', 'status' => 'inactive']);
        $this->vpn($activeName, ['user_name' => 'expired-user', 'expire_date' => today()->subDay()]);
        $this->vpn($inactiveName, ['user_name' => 'disabled-name-user']);

        $response = $this->actingAs($user)->get(route('app.vpn'));

        $response->assertOk()
            ->assertSee($available->vpnName->name)
            ->assertDontSee('sold-user')
            ->assertDontSee('Disabled VPN');
        $this->assertCount(1, $response->viewData('vpns'));
    }

    public function test_user_can_purchase_vpn_and_balance_is_deducted(): void
    {
        $user = $this->approvedUser();
        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'EX-1001',
            'balance' => 100,
        ]);
        $vpn = $this->vpn(VpnName::create(['name' => 'Express VPN', 'status' => 'active']), [
            'payment' => 35.50,
        ]);

        $response = $this->actingAs($user)->post(route('vpn.buy'), ['vpn_id' => $vpn->id]);

        $response->assertRedirect(route('my.vpn'))->assertSessionHas('success');
        $this->assertEquals('64.50', $account->fresh()->balance);
        $this->assertTrue($vpn->fresh()->buy_now_status);
        $this->assertSame($user->id, $vpn->fresh()->user_id);
        $this->assertNotNull($vpn->fresh()->purchased_at);
    }

    public function test_insufficient_balance_does_not_sell_vpn(): void
    {
        $user = $this->approvedUser();
        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'EX-1002',
            'balance' => 10,
        ]);
        $vpn = $this->vpn(VpnName::create(['name' => 'Surf VPN', 'status' => 'active']), [
            'payment' => 50,
        ]);

        $response = $this->actingAs($user)->from(route('app.vpn'))->post(route('vpn.buy'), ['vpn_id' => $vpn->id]);

        $response->assertRedirect(route('app.vpn'))->assertSessionHas('error', 'Insufficient balance.');
        $this->assertEquals('10.00', $account->fresh()->balance);
        $this->assertFalse($vpn->fresh()->buy_now_status);
        $this->assertNull($vpn->fresh()->user_id);
    }

    private function approvedUser(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['status' => 'approved'])->save();

        return $user;
    }

    private function vpn(VpnName $vpnName, array $attributes = []): Vpn
    {
        return Vpn::create(array_merge([
            'vpn_name_id' => $vpnName->id,
            'expire_date' => today()->addMonth(),
            'user_name' => 'vpn-user',
            'password' => 'vpn-password',
            'link' => 'https://example.com/vpn',
            'payment' => 25,
            'status' => 'active',
            'buy_now_status' => false,
        ], $attributes));
    }
}
