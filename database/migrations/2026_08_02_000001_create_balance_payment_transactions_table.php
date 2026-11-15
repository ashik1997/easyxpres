<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ledger_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->string('transaction_id', 64)->unique();
            $table->string('gateway', 30)->default('eps');
            $table->string('gateway_transaction_id', 100)->nullable();
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('MVR');
            $table->decimal('balance_amount', 15, 2);
            $table->char('balance_currency', 3)->default('MVR');
            $table->string('status', 20)->default('pending');
            $table->char('merchant_id_hash', 64);
            $table->char('store_id_hash', 64);
            $table->char('callback_token_hash', 64);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('status_log')->nullable();
            $table->unsignedInteger('callback_count')->default(0);
            $table->timestamp('last_callback_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->timestamps();

            $table->unique('gateway_transaction_id', 'bpt_gateway_txn_unique');
            $table->index(['user_id', 'created_at'], 'bpt_user_created_index');
            $table->index(['gateway', 'status'], 'bpt_gateway_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_payment_transactions');
    }
};
