<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('category')->default('business')->after('type');
        });
        
        // Update existing Restaurant POS plan
        \DB::table('plans')->where('name', 'like', '%Restaurant%')->update(['category' => 'restaurant']);
        // Addons can apply to all
        \DB::table('plans')->where('type', 'addon')->update(['category' => 'all']);
    }
    public function down(): void {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
