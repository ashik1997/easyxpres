<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->string('license_number', 100)->nullable()->after('icon');
            $table->dropColumn('license_image');
        });
    }

    public function down(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->dropColumn('license_number');
            $table->string('license_image')->nullable()->after('icon');
        });
    }
};
