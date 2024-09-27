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
        Schema::create('server_instances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cloud_provider_id')->unsigned()->nullable();
            $table->foreign('cloud_provider_id')->references('id')->on('cloud_providers')->onDelete('set null');
            $table->string('name');
            $table->string('ip')->nullable();
            $table->string('version')->nullable();
            $table->string('key');
            $table->string('instance_id')->nullable();
            $table->float('price')->default(0);
            $table->bigInteger('cpu')->default(0);
            $table->bigInteger('disk_size')->default(0);
            $table->bigInteger('memory')->default(0);
            $table->bigInteger('transfer')->default(0);
            $table->string('region');
            $table->string('region_zone')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_instances');
    }
};
