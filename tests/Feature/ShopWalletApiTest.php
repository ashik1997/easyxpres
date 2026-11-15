<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopWalletApiTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'shop-wallet-test-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'shop_wallet.key' => self::KEY,
            'shop_wallet.clock_skew_seconds' => 300,
        ]);

        DB::table('app_setups')->insert([
            'id' => 1,
            'add_balance_content' => '',
            'mvr_to_bdt' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_debit_is_atomic_and_idempotent(): void
    {
        $account = $this->wallet(100);
        $reference = (string) Str::uuid();
        $payload = [
            'phone' => '9607712345',
            'amount_bdt' => 500,
            'reference' => $reference,
            'order_code' => 'ORD-TEST-1',
        ];

        $this->signedPost('/api/shop/wallet/debit', $payload)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('amount_mvr', 25)
            ->assertJsonPath('balance_after_mvr', 75)
            ->assertJsonPath('already_processed', false);

        DB::table('app_setups')->where('id', 1)->update(['mvr_to_bdt' => 25]);

        $this->signedPost('/api/shop/wallet/debit', $payload)
            ->assertOk()
            ->assertJsonPath('already_processed', true)
            ->assertJsonPath('exchange_rate_bdt_per_mvr', 20)
            ->assertJsonPath('balance_after_mvr', 75);

        $this->assertEquals(75.0, (float) $account->fresh()->balance);
        $this->assertSame(1, Transaction::where('transaction_id', 'SHOP-'.$reference)->count());
    }

    public function test_insufficient_balance_does_not_create_a_transaction(): void
    {
        $account = $this->wallet(10);
        $reference = (string) Str::uuid();

        $this->signedPost('/api/shop/wallet/debit', [
            'phone' => '9607712345',
            'amount_bdt' => 500,
            'reference' => $reference,
            'order_code' => 'ORD-TEST-2',
        ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'insufficient_balance')
            ->assertJsonPath('message', 'Insufficient wallet balance. Please choose another payment method.');

        $this->assertEquals(10.0, (float) $account->fresh()->balance);
        $this->assertDatabaseMissing('transactions', ['transaction_id' => 'SHOP-'.$reference]);
    }

    public function test_reversal_restores_the_balance_only_once(): void
    {
        $account = $this->wallet(100);
        $reference = (string) Str::uuid();
        $debit = [
            'phone' => '9607712345',
            'amount_bdt' => 500,
            'reference' => $reference,
            'order_code' => 'ORD-TEST-3',
        ];

        $this->signedPost('/api/shop/wallet/debit', $debit)->assertOk();

        $reverse = [
            'phone' => '9607712345',
            'reference' => $reference,
            'reason' => 'Order rollback test',
        ];
        $this->signedPost('/api/shop/wallet/reverse', $reverse)
            ->assertOk()
            ->assertJsonPath('already_processed', false)
            ->assertJsonPath('balance_after_mvr', 100);
        $this->signedPost('/api/shop/wallet/reverse', $reverse)
            ->assertOk()
            ->assertJsonPath('already_processed', true)
            ->assertJsonPath('balance_after_mvr', 100);

        $this->assertEquals(100.0, (float) $account->fresh()->balance);
        $this->assertSame(1, Transaction::where('transaction_id', 'SHOP-REV-'.$reference)->count());
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $this->postJson('/api/shop/wallet/balance', [
            'phone' => '9607712345',
            'amount_bdt' => 500,
        ], [
            'X-Shop-Timestamp' => (string) now()->timestamp,
            'X-Shop-Signature' => str_repeat('0', 64),
        ])->assertUnauthorized();
    }

    private function wallet(float $balance): Account
    {
        $user = User::factory()->create([
            'phone' => '9607712345',
            'status' => 'approved',
        ]);

        return Account::create([
            'user_id' => $user->id,
            'account_number' => 'TEST-'.$user->id,
            'balance' => $balance,
        ]);
    }

    private function signedPost(string $path, array $payload)
    {
        $timestamp = (string) now()->timestamp;
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $timestamp."\nPOST\n".$path."\n".$body, self::KEY);

        return $this->call('POST', $path, [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_SHOP_TIMESTAMP' => $timestamp,
            'HTTP_X_SHOP_SIGNATURE' => $signature,
        ], $body);
    }
}
