<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vpn_name_id')->constrained('vpn_names')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('expire_date');
            $table->string('user_name');
            $table->text('password');
            $table->text('link');
            $table->decimal('payment', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('buy_now_status')->default(false);
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'buy_now_status', 'expire_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpns');
    }
};
