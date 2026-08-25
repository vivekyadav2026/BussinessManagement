<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_super_admin_can_access_dashboard()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin)->get('/super-admin');

        $response->assertStatus(200);
        $response->assertSee('Platform Overview');
    }

    public function test_organization_admin_cannot_access_super_admin_dashboard()
    {
        $orgAdmin = User::factory()->create();
        $orgAdmin->assignRole('Organization Admin');

        $response = $this->actingAs($orgAdmin)->get('/super-admin');

        $response->assertStatus(403);
    }
    
    public function test_super_admin_can_view_organizations_list()
    {
        Organization::create(['name' => 'Test Org']);
        
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin)->get('/super-admin/organizations');

        $response->assertStatus(200);
        $response->assertSee('Test Org');
    }
}
