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
        Schema::create('webhook_webhook_event', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id')->nullable();
            $table->unsignedBigInteger('webhook_event_id')->nullable();
            
            $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
            $table->foreign('webhook_event_id')->references('id')->on('webhook_events')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_webhook_event');
    }
};
