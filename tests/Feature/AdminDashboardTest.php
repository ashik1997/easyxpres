<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_database_metrics_and_existing_report_sections(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
        ]);

        $approvedUser = User::factory()->create(['status' => 'approved']);
        User::factory()->create(['status' => 'pending']);

        Account::create([
            'user_id' => $approvedUser->id,
            'account_number' => 'ACCT-DASH-001',
            'balance' => 1250.50,
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Reports')
            ->assertSee('1,250.50')
            ->assertSee('Pending workload')
            ->assertSee('Total BD Recharge')
            ->assertSee('Total Maldives Recharge')
            ->assertSee('Total Package Orders')
            ->assertSee('Total Mobile Banking')
            ->assertSee('Total iBanking')
            ->assertSee('Package performance')
            ->assertSee('Mobile banking performance')
            ->assertSee('iBanking performance');
    }

    public function test_attention_link_opens_the_service_that_actually_has_pending_data(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin-link@example.test',
            'password' => bcrypt('password'),
        ]);

        $providerId = DB::table('mobile_bankings')->insertGetId([
            'name' => 'Test Wallet',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('mobile_banking_orders')->insert([
            'mobile_banking_id' => $providerId,
            'number' => '9607000000',
            'amount' => 100,
            'money_status' => 'personal',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Review mobile banking orders')
            ->assertSee('href="'.route('admin.mobile.banking.orders.list').'"', false);
    }
}
