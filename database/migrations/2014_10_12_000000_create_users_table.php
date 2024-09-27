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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('role')->default('customer');
            $table->string('status')->default('active');
            $table->text('avatar')->nullable();
            $table->decimal('credits', 10, 3)->default(0);
            $table->string('country_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('region_name')->nullable();
            $table->string('region_code')->nullable();
            $table->string('api_token')->nullable();
            $table->boolean('google2fa_enable')->default(0);
            $table->string('google2fa_secret')->nullable();
            $table->string('timezone')->default('Asia/Kolkata');
            $table->boolean('two_fa_enable')->default(0);
            $table->string('stripe_id', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
