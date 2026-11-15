<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('app_setups', 'mvr_to_bdt')) {
            Schema::table('app_setups', function (Blueprint $table) {
                $table->decimal('mvr_to_bdt', 15, 6)->nullable();
            });
        }

        if (! Schema::hasColumn('balance_payment_transactions', 'exchange_rate')) {
            Schema::table('balance_payment_transactions', function (Blueprint $table) {
                $table->decimal('exchange_rate', 15, 6)->nullable()->after('balance_currency');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('balance_payment_transactions', 'exchange_rate')) {
            Schema::table('balance_payment_transactions', function (Blueprint $table) {
                $table->dropColumn('exchange_rate');
            });
        }

        // mvr_to_bdt is also used by existing non-EPS features, so keep it.
    }
};
