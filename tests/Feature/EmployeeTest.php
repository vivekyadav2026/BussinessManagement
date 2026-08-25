<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_org_admin_can_view_employees()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $admin = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $admin->roles()->attach($role);

        $response = $this->actingAs($admin)->get('/organization/employees');
        $response->assertStatus(200);
    }

    public function test_can_create_employee_without_account()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $admin = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $admin->roles()->attach($role);

        $response = $this->actingAs($admin)->post('/organization/employees', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'create_account' => false,
        ]);

        $response->assertRedirect('/organization/employees');
        
        $this->assertDatabaseHas('employees', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'organization_id' => $org->id,
            'user_id' => null,
        ]);
    }

    public function test_can_create_employee_with_account_and_role()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $admin = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $admin->roles()->attach($role);
        
        $cashierRole = Role::create(['name' => 'Cashier', 'organization_id' => $org->id]);

        $response = $this->actingAs($admin)->post('/organization/employees', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'create_account' => 1,
            'password' => 'password123',
            'role' => 'Cashier',
        ]);

        $response->assertRedirect('/organization/employees');
        
        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'organization_id' => $org->id,
        ]);
        
        $newUser = User::where('email', 'jane@example.com')->first();
        $this->assertTrue($newUser->hasRole('Cashier'));
        
        // Assert employee was created and linked
        $this->assertDatabaseHas('employees', [
            'first_name' => 'Jane',
            'user_id' => $newUser->id,
        ]);
    }
}
