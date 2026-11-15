<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->string('mib_bank_name')->nullable()->after('add_balance_content');
            $table->string('mib_bank_logo')->nullable()->after('mib_bank_name');
            $table->longText('mib_balance_content')->nullable()->after('add_balance_content');
            $table->string('bml_bank_name')->nullable()->after('mib_balance_content');
            $table->string('bml_bank_logo')->nullable()->after('bml_bank_name');
            $table->longText('bml_balance_content')->nullable()->after('mib_balance_content');
        });

        DB::table('app_setups')->update([
            'mib_bank_name' => 'Maldives Islamic Bank',
            'mib_balance_content' => DB::raw('add_balance_content'),
            'bml_bank_name' => 'Bank of Maldives',
        ]);
    }

    public function down(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->dropColumn([
                'mib_bank_name', 'mib_bank_logo', 'mib_balance_content',
                'bml_bank_name', 'bml_bank_logo', 'bml_balance_content',
            ]);
        });
    }
};
