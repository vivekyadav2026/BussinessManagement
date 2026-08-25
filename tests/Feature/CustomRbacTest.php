<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CustomRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
        
        // Dummy protected route
        Route::get('/test-products', function () {
            return 'Products OK';
        })->middleware(['auth', 'permission:products.view']);
    }

    public function test_organization_admin_bypasses_permissions()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $admin = User::factory()->create(['organization_id' => $org->id]);
        
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $admin->roles()->attach($role);

        $response = $this->actingAs($admin)->get('/test-products');
        
        $response->assertStatus(200);
        $response->assertSee('Products OK');
    }

    public function test_employee_without_permission_is_blocked()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $employee = User::factory()->create(['organization_id' => $org->id]);
        
        $role = Role::create(['name' => 'Cashier', 'organization_id' => $org->id]);
        $employee->roles()->attach($role);
        // Cashier has NO permissions yet

        $response = $this->actingAs($employee)->get('/test-products');
        
        $response->assertStatus(403);
    }

    public function test_employee_with_permission_can_access()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $employee = User::factory()->create(['organization_id' => $org->id]);
        
        $role = Role::create(['name' => 'Inventory Manager', 'organization_id' => $org->id]);
        $permission = Permission::where('name', 'products.view')->first();
        $role->permissions()->attach($permission);
        
        $employee->roles()->attach($role);

        $response = $this->actingAs($employee)->get('/test-products');
        
        $response->assertStatus(200);
        $response->assertSee('Products OK');
    }
}
