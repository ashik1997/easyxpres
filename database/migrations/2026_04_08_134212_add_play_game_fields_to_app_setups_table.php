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
            $table->tinyInteger('play_game_status')->default(0)->after('telegram');
            $table->string('play_game_link')->nullable()->after('play_game_status');
            $table->longText('play_game_content')->nullable()->after('play_game_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_setups', function (Blueprint $table) {
            $table->dropColumn([
                'play_game_status',
                'play_game_link',
                'play_game_content'
            ]);
        });
    }
};
