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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            
            $table->integer('month');
            $table->integer('year');
            
            // Snapshots
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->json('allowances')->nullable();
            $table->json('deductions')->nullable();
            
            // Math
            $table->decimal('days_in_month', 8, 2)->default(0);
            $table->decimal('effective_working_days', 8, 2)->default(0);
            
            $table->decimal('earned_gross', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('manual_adjustment', 12, 2)->default(0);
            $table->string('adjustment_reason')->nullable();
            $table->decimal('net_salary', 12, 2)->default(0);
            
            // Status
            $table->enum('status', ['Pending', 'Paid'])->default('Pending');
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year'], 'emp_month_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
