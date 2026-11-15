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
        Schema::create('calling_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Optional user relation');
            $table->string('account_no')->nullable();
            $table->string('card_no');
            $table->string('user_name');
            $table->string('pin');
            $table->integer('point')->default(0);
            $table->string('minute_sec')->nullable()->comment('Format mm:ss or total seconds');
            $table->decimal('payment', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->tinyInteger('buy_now_status')->default(0)->comment('0 = not bought, 1 = bought');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calling_cards');
    }
};
