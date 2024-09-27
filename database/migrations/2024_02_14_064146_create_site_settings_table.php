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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sa_org_id')->nullable();
            $table->string('app_name');
            $table->text('tag_line')->nullable();
            $table->text('logo')->nullable();
            $table->text('icon')->nullable();
            $table->text('favicon')->nullable();
            $table->string('color_code')->nullable();
            $table->text('color_palette')->nullable();
            $table->text('header')->nullable();
            $table->text('footer')->nullable();
            $table->string('analytics')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
