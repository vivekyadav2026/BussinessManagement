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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            // nullable product_id just in case a product is hard-deleted, but we'll use set null or keep it. Let's cascade or restrict?
            // Safer to let it nullify if product deleted, but for strictness let's cascade on delete. No, we shouldn't cascade delete invoices if a product is deleted! 
            // So nullable product_id, set null.
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            $table->string('product_name_snapshot'); // snapshot of name at time of purchase
            
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
