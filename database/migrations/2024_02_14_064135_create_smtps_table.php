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
        Schema::create('smtps', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->bigInteger('port');
            $table->string('username');
            $table->string('password');
            $table->string('from_email');
            $table->string('from_name');
            $table->string('encryption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtps');
    }
};
