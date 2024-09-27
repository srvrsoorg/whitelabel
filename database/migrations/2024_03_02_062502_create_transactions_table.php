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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('promo_code_id')->unsigned()->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('service')->nullable();
            $table->decimal('base_amount', 10, 3)->default(0.00);
            $table->decimal('discount_amount', 10, 3)->default(0.00);
            $table->decimal('tax_amount', 10, 3)->default(0.00);
            $table->decimal('final_amount', 10, 3)->default(0.00);
            $table->integer('status')->default(2);
            $table->string('key');
            $table->string('transaction_id')->nullable();
            $table->text('payment_link')->nullable();
            $table->text('company_address')->nullable();
            $table->text('company_tax_numbers')->nullable();
            $table->text('tax_details')->nullable();
            $table->text('address')->nullable();
            $table->text('tax_numbers')->nullable();
            $table->string('refund_id')->nullable();
            $table->string('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
