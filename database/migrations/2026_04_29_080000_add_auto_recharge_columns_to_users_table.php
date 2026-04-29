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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('auto_recharge_enabled')->default(false)->after('reminder_minimum_credit');
            $table->double('auto_recharge_minimum_credit', 20, 3)->nullable()->after('auto_recharge_enabled');
            $table->double('auto_recharge_amount', 20, 3)->nullable()->after('auto_recharge_minimum_credit');
            $table->string('auto_recharge_payment_gateway')->nullable()->after('auto_recharge_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'auto_recharge_enabled',
                'auto_recharge_minimum_credit',
                'auto_recharge_amount',
                'auto_recharge_payment_gateway',
            ]);
        });
    }
};
