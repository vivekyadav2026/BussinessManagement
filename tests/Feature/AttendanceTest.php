<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use App\Models\Employee;
use App\Models\Attendance;
use App\Services\LocationManager;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_bulk_attendance_prevents_duplicates_by_updating()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        
        $emp = Employee::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'Active'
        ]);

        $this->actingAs($user);
        LocationManager::setActiveLocationId($loc->id);

        $date = now()->toDateString();

        // First Submission
        $this->withSession(['active_location_id' => $loc->id])->post(route('organization.attendance.storeBulk'), [
            'date' => $date,
            'attendance' => [
                $emp->id => ['status' => 'Present', 'check_in' => '09:00']
            ]
        ]);

        $this->assertEquals(1, Attendance::count());
        $this->assertEquals('Present', Attendance::first()->status);

        // Resubmission on same date
        $response = $this->withSession(['active_location_id' => $loc->id])->post(route('organization.attendance.storeBulk'), [
            'date' => $date,
            'attendance' => [
                $emp->id => ['status' => 'Half Day', 'check_in' => '13:00']
            ]
        ]);
        
        $response->assertSessionHasNoErrors();

        $this->assertEquals(1, Attendance::count()); // Should update, not duplicate
        $att = Attendance::first();
        $this->assertEquals('Half Day', $att->status);
        $this->assertStringContainsString('13:00', $att->check_in);
    }

    public function test_attendance_service_calculates_effective_working_days()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $emp = Employee::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $date = now();
        
        // 2 Present, 1 Half Day, 1 Leave, 1 Absent
        Attendance::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'employee_id' => $emp->id, 'date' => $date->copy()->subDays(1), 'status' => 'Present']);
        Attendance::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'employee_id' => $emp->id, 'date' => $date->copy()->subDays(2), 'status' => 'Present']);
        Attendance::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'employee_id' => $emp->id, 'date' => $date->copy()->subDays(3), 'status' => 'Half Day']);
        Attendance::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'employee_id' => $emp->id, 'date' => $date->copy()->subDays(4), 'status' => 'Leave']);
        Attendance::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'employee_id' => $emp->id, 'date' => $date->copy()->subDays(5), 'status' => 'Absent']);

        $summary = AttendanceService::getMonthlySummary($emp, $date->month, $date->year);

        $this->assertEquals(2, $summary['present']);
        $this->assertEquals(1, $summary['half_days']);
        $this->assertEquals(1, $summary['absent']);
        $this->assertEquals(1, $summary['leaves']);
        
        // 2 present + (1 half day * 0.5) = 2.5 effective working days
        $this->assertEquals(2.5, $summary['effective_working_days']);
        $this->assertEquals(5, $summary['total_recorded_days']);
    }
}
