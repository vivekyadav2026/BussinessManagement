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
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->index(['organization_id', 'location_id', 'status'], 'idx_org_loc_status');
        });

        Schema::table('restaurant_order_items', function (Blueprint $table) {
            $table->index('restaurant_order_id', 'idx_kds_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropIndex('idx_org_loc_status');
        });

        Schema::table('restaurant_order_items', function (Blueprint $table) {
            $table->dropIndex('idx_kds_order_id');
        });
    }
};
