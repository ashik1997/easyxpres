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
        Schema::table('app_setups', function (Blueprint $table) {
                       $table->text('registered_balance_content')->nullable()->after('add_balance_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            Schema::table('app_setups', function (Blueprint $table) {
            $table->dropColumn('registered_balance_content');
        });
        });
    }
};
