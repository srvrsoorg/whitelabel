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
        Schema::create('user_billing_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->string('email');
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('region_code')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('tax_numbers')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_billing_details');
    }
};
