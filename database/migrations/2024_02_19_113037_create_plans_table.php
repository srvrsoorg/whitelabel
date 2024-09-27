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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('provider_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('region');
            $table->string('region_code')->nullable();
            $table->string('type')->default('standard');
            $table->bigInteger('cores')->default(0);
            $table->string('ram');
            $table->string('disk');
            $table->string('bandwidth')->nullable();
            $table->float('server_price', 8, 2)->default(0.0);
            $table->float('price_per_month', 8, 2)->default(0.0);
            $table->string('size_slug');
            $table->boolean('visible')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Define foreign key constraint
            $table->foreign('provider_id')->references('id')->on('cloud_providers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
