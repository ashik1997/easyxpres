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
        Schema::create('mobile_banking_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->integer('mobile_banking_id')->nullable();
            $table->string('number'); 
            $table->decimal('amount', 10, 2);
            $table->text('note_admin')->nullable(); 
            $table->enum('money_status', ['personal', 'agent']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_banking_orders');
    }
};
