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
        Schema::create('billing_details', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('region_code')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('tax_numbers')->nullable();
            $table->string('billing_mode');
            $table->float('new_registration_free_credits', 20, 2)->default(0.00);
            $table->float('minimum_recharge_amount', 20, 2)->default(0.00);
            $table->bigInteger('due_days')->nullable();
            $table->bigInteger('retention_period')->nullable();
            $table->string('currency')->default('USD');
            $table->string('currency_symbol')->default('$');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_details');
    }
};
