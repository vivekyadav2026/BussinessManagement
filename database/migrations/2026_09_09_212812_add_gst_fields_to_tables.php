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
        Schema::table('organizations', function (Blueprint $table) {
            $table->decimal('cgst_percent', 5, 2)->default(0)->after('gst_number');
            $table->decimal('sgst_percent', 5, 2)->default(0)->after('cgst_percent');
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->decimal('cgst', 10, 2)->default(0)->after('tax');
            $table->decimal('sgst', 10, 2)->default(0)->after('cgst');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('cgst', 10, 2)->default(0)->after('tax');
            $table->decimal('sgst', 10, 2)->default(0)->after('cgst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['cgst_percent', 'sgst_percent']);
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropColumn(['cgst', 'sgst']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cgst', 'sgst']);
        });
    }
};
