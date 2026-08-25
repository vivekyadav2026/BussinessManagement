<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\LocationManager;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_org_admin_can_manage_locations()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $admin = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $admin->roles()->attach($role);

        $response = $this->actingAs($admin)->post('/organization/locations', [
            'name' => 'Downtown Branch',
            'phone' => '1234567890'
        ]);

        $response->assertRedirect('/organization/locations');
        $this->assertDatabaseHas('locations', ['name' => 'Downtown Branch']);
    }

    public function test_user_can_switch_assigned_location()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        
        $loc1 = Location::create(['organization_id' => $org->id, 'name' => 'Branch 1', 'is_active' => true]);
        $loc2 = Location::create(['organization_id' => $org->id, 'name' => 'Branch 2', 'is_active' => true]);
        
        // Assign user to loc2
        $user->locations()->attach($loc2);

        $response = $this->actingAs($user)->post('/organization/set-location', [
            'location_id' => $loc2->id
        ]);
        
        $response->assertSessionHas('success');
        $this->assertEquals($loc2->id, LocationManager::getActiveLocationId());

        // Should fail accessing loc1
        $response = $this->actingAs($user)->post('/organization/set-location', [
            'location_id' => $loc1->id
        ]);
        $response->assertStatus(403);
    }
}
