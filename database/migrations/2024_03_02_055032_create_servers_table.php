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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sa_org_id')->nullable();
            $table->bigInteger('sa_server_id')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('cloud_provider_id')->unsigned()->nullable();
            $table->bigInteger('plan_id')->unsigned()->nullable();
            $table->bigInteger('server_instance_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('ip');
            $table->string('hostname')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('version')->nullable();
            $table->string('arch')->default('x86_64');
            $table->string('web_server');
            $table->string('ssh_status')->default('inspecting');
            $table->string('agent_status')->default('inspecting');
            $table->string('agent_version')->nullable();
            $table->bigInteger('ssh_port')->default(22);
            $table->double('php_cli_version', 8, 1)->default('8.2');
            $table->string('key')->nullable();
            $table->string('database_type');
            $table->string('database_username')->nullable();
            $table->string('database_password')->nullable();
            $table->string('redis_password')->nullable();
            $table->string('timezone')->nullable();
            $table->string('country_code')->nullable();
            $table->boolean('nodejs')->default(false);
            $table->boolean('yarn')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cloud_provider_id')->references('id')->on('cloud_providers')->onDelete('set null');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
            $table->foreign('server_instance_id')->references('id')->on('server_instances')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
