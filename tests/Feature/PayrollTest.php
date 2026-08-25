<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Organization;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_calculation_with_attendance()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $loc = \App\Models\Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);
        
        $emp = Employee::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $structure = SalaryStructure::create([
            'organization_id' => $org->id,
            'employee_id' => $emp->id,
            'basic_salary' => 50000, // Monthly base
            'allowances' => [['name' => 'HRA', 'amount' => 10000]], // Gross = 60000
            'deductions' => [['name' => 'PF', 'amount' => 2000]], // Deduction = 2000
        ]);

        // Assuming August 2026 has 31 days
        $year = 2026;
        $month = 8;
        $daysInMonth = 31;
        
        // Add 15 days of attendance (Present)
        for ($i = 1; $i <= 15; $i++) {
            Attendance::create([
                'organization_id' => $org->id,
                'location_id' => $loc->id,
                'employee_id' => $emp->id,
                'date' => Carbon::create($year, $month, $i)->toDateString(),
                'status' => 'Present',
            ]);
        }
        
        $payroll = PayrollService::calculateAndDraft($emp, $month, $year);

        // Assertions
        $this->assertNotNull($payroll);
        $this->assertEquals(60000, $payroll->basic_salary + collect($payroll->allowances)->sum('amount'));
        $this->assertEquals(15, $payroll->effective_working_days);
        
        // Math check
        $dailyWage = 60000 / 31;
        $expectedGross = round($dailyWage * 15, 2);
        $this->assertEquals($expectedGross, $payroll->earned_gross);
        
        $expectedNet = max(0, $expectedGross - 2000);
        $this->assertEquals('Pending', $payroll->fresh()->status);
    }

    public function test_cannot_modify_paid_payroll()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $loc = \App\Models\Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);
        
        $emp = Employee::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        SalaryStructure::create([
            'organization_id' => $org->id,
            'employee_id' => $emp->id,
            'basic_salary' => 50000,
        ]);

        // Active subscription logic
        $plan = \App\Models\Plan::create(['name' => 'Pro']);
        $plan->features()->create(['feature_code' => 'module_payroll', 'feature_value' => 'true']);
        \App\Models\OrganizationSubscription::create(['organization_id' => $org->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'ends_at' => now()->addYear()]);

        $payroll = Payroll::create([
            'organization_id' => $org->id,
            'employee_id' => $emp->id,
            'month' => 8,
            'year' => 2026,
            'basic_salary' => 50000,
            'days_in_month' => 31,
            'effective_working_days' => 31,
            'earned_gross' => 50000,
            'net_salary' => 50000,
            'status' => 'Paid' // Already Paid
        ]);

        $this->actingAs($user);
        
        $role = \App\Models\Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $user->roles()->attach($role);

        $response = $this->put(route('organization.payroll.updateAdjustment', $payroll), [
            'manual_adjustment' => 5000,
            'adjustment_reason' => 'Bonus'
        ]);
        
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertEquals(50000, $payroll->fresh()->net_salary); // Unchanged
    }
}
