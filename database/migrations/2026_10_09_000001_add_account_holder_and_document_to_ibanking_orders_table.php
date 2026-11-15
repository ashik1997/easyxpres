<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ibanking_orders', 'account_holder_name')) {
            Schema::table('ibanking_orders', function (Blueprint $table) {
                $table->string('account_holder_name')->nullable()->after('bank_name_id');
            });
        }

        if (! Schema::hasColumn('ibanking_orders', 'document_attachment')) {
            Schema::table('ibanking_orders', function (Blueprint $table) {
                $table->string('document_attachment')->nullable()->after('account_no');
            });
        }
    }

    public function down(): void
    {
        $columns = array_filter([
            Schema::hasColumn('ibanking_orders', 'account_holder_name') ? 'account_holder_name' : null,
            Schema::hasColumn('ibanking_orders', 'document_attachment') ? 'document_attachment' : null,
        ]);

        if ($columns) {
            Schema::table('ibanking_orders', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
