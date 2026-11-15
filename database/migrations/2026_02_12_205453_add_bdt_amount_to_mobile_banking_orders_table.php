<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mobile_banking_orders', function (Blueprint $table) {
            $table->decimal('bdt_amount', 10, 2)->nullable()->after('amount')->comment('Calculated BDT amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_banking_orders', function (Blueprint $table) {
            $table->dropColumn('bdt_amount');
        });
    }
};
