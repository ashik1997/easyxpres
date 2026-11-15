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
        Schema::create('ibanking_orders', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('bank_name_id');

            // Banking Info
            $table->string('branch')->nullable();
            $table->string('account_no');
            $table->string('upload_slip')->nullable();

            // Amount
            $table->decimal('amount', 15, 2);
            $table->decimal('bdt_amount', 15, 2)->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibanking_orders');
    }
};
