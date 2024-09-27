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
        Schema::create('cloud_provider_ssh_keys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('provider_id')->unsigned();
            $table->string('name');
            $table->string('ssh_key_id')->nullable();
            $table->string('region')->nullable();
            $table->string("key")->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('provider_id')->references('id')->on('cloud_providers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_provider_ssh_keys');
    }
};
