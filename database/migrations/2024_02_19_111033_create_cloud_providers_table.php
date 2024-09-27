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
        Schema::create('cloud_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->text('access_key');
            $table->text('access_secret')->nullable();
            $table->boolean('visible')->default(true);
            $table->string('key');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_providers');
    }
};
