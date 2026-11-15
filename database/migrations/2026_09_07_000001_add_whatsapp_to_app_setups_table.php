<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('app_setups', 'whatsapp')) {
            Schema::table('app_setups', function (Blueprint $table) {
                $table->string('whatsapp', 32)->nullable()->after('telegram');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('app_setups', 'whatsapp')) {
            Schema::table('app_setups', function (Blueprint $table) {
                $table->dropColumn('whatsapp');
            });
        }
    }
};
