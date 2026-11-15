<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Admin;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardLicenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_a_licence_number_that_appears_on_the_dashboard(): void
    {
        $this->createAppSetup();

        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.setup.license.update'), [
            'license_number' => 'LIC-2026-001234',
        ]);

        $response->assertRedirect(route('admin.setup.content'));
        $response->assertSessionHasNoErrors();

        $this->assertSame(
            'LIC-2026-001234',
            DB::table('app_setups')->where('id', 1)->value('license_number')
        );

        $user = User::factory()->create([
            'phone' => '9607000001',
            'status' => 'approved',
        ]);
        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACCT00000001',
            'balance' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Licence Information')
            ->assertSee('Licence number')
            ->assertSee('LIC-2026-001234')
            ->assertDontSee('View the EasyXpres licence image');
    }

    public function test_admin_can_clear_the_dashboard_licence_number(): void
    {
        $this->createAppSetup('LIC-OLD-001');

        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.setup.license.update'), ['license_number' => ''])
            ->assertRedirect(route('admin.setup.content'))
            ->assertSessionHasNoErrors();

        $this->assertNull(DB::table('app_setups')->where('id', 1)->value('license_number'));
    }

    public function test_licence_number_cannot_exceed_the_database_limit(): void
    {
        $this->createAppSetup();

        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.setup.license.update'), [
                'license_number' => str_repeat('A', 101),
            ])
            ->assertSessionHasErrors('license_number');
    }

    public function test_dashboard_quick_actions_do_not_show_history_actions(): void
    {
        $this->createAppSetup();

        $user = User::factory()->create([
            'phone' => '9607000002',
            'status' => 'approved',
        ]);
        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACCT00000002',
            'balance' => 0,
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertMatchesRegularExpression(
            '/<div class="grid grid-cols-3.*?<\/div>\s*<\/section>/s',
            $response->getContent()
        );

        preg_match(
            '/<div class="grid grid-cols-3.*?<\/div>\s*<\/section>/s',
            $response->getContent(),
            $quickActions
        );

        $this->assertStringNotContainsString('History', $quickActions[0]);
        $this->assertStringContainsString('My Card', $quickActions[0]);
        $this->assertStringContainsString('Shop', $quickActions[0]);
        $this->assertStringContainsString(route('my.calling.card'), $quickActions[0]);
    }

    public function test_each_service_entry_point_shows_its_history_action(): void
    {
        $this->createAppSetup();

        $user = User::factory()->create([
            'phone' => '9607000003',
            'status' => 'approved',
        ]);
        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACCT00000003',
            'balance' => 0,
        ]);
        $package = Package::create([
            'name' => 'Test Package',
            'status' => 'active',
        ]);

        $servicePages = [
            [route('balance.add.form'), 'Balance History', route('app.balance.history')],
            [route('app.package.history'), 'Package History', route('app.recharge.packages.history')],
            [route('app.package.show', $package), 'History', route('app.recharge.packages.history')],
            [route('app.recharge.form'), 'BD Recharge History', route('app.recharge.history')],
            [route('app.male.recharge.form'), 'Maldives Recharge History', route('app.male.recharge.history')],
            [route('app.mobile.banking.view'), 'View History', route('app.mobile.banking.history')],
            [route('app.calling.card'), 'Card History', route('my.calling.card')],
            [route('app.ibanking.form'), 'Transfer History', route('app.ibanking.history')],
        ];

        foreach ($servicePages as [$pageUrl, $label, $historyUrl]) {
            $this->actingAs($user)
                ->get($pageUrl)
                ->assertOk()
                ->assertSee($label)
                ->assertSee('href="'.$historyUrl.'"', false);
        }
    }

    private function createAppSetup(?string $licenseNumber = null): void
    {
        DB::table('app_setups')->insert([
            'id' => 1,
            'add_balance_content' => '',
            'license_number' => $licenseNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
