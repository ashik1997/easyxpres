<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_payment_transactions', function (Blueprint $table) {
            $table->string('gateway_session_id', 100)
                ->nullable()
                ->after('gateway_transaction_id');
            $table->unique('gateway_session_id', 'bpt_gateway_session_unique');
        });

        // Older releases stored InitializeEPS.TransactionId (the checkout session UUID)
        // in gateway_transaction_id. V5 returns a separate EpsTransactionId after payment.
        DB::table('balance_payment_transactions')
            ->where('gateway', 'eps')
            ->whereNull('gateway_session_id')
            ->whereNotNull('gateway_transaction_id')
            ->update([
                'gateway_session_id' => DB::raw('gateway_transaction_id'),
                'gateway_transaction_id' => null,
            ]);
    }

    public function down(): void
    {
        Schema::table('balance_payment_transactions', function (Blueprint $table) {
            $table->dropUnique('bpt_gateway_session_unique');
            $table->dropColumn('gateway_session_id');
        });
    }
};
