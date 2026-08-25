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
        Schema::create('gateway_payments', function (Blueprint $table) {
            $table->id();
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('INR');
            $table->string('status')->default('created'); // created, authorized, captured, failed, refunded
            $table->string('entity_type'); // morphs
            $table->unsignedBigInteger('entity_id');
            $table->string('webhook_event')->nullable();
            $table->timestamps();
            
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_payments');
    }
};
