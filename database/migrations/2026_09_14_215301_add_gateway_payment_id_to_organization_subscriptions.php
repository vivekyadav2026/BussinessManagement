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
        Schema::table('organization_subscriptions', function (Blueprint $table) {
            $table->foreignId('gateway_payment_id')->nullable()->after('plan_id')->constrained('gateway_payments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['gateway_payment_id']);
            $table->dropColumn('gateway_payment_id');
        });
    }
};
