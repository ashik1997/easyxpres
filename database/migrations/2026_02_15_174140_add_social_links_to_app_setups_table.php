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
            $table->string('facebook')->nullable()->after('add_balance_content');
            $table->string('youtube')->nullable()->after('facebook');
            $table->string('telegram')->nullable()->after('youtube');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->dropColumn(['facebook', 'youtube', 'telegram']);
        });
    }
};
