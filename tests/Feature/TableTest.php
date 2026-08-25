<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Location;
use App\Models\RestaurantTable;

class TableTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_manage_tables_and_resolve_public_menu()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        
        $p1 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.view', 'label' => 'View']);
        $p2 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.manage', 'label' => 'Manage']);
        $role->permissions()->attach([$p1->id, $p2->id]);
        $user->roles()->attach($role);

        $location = Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);
        
        // Mock active subscription for the middleware
        $plan = \App\Models\Plan::create(['name' => 'Pro']);
        $plan->features()->create(['feature_code' => 'module_restaurant', 'feature_value' => 'true']);
        \App\Models\OrganizationSubscription::create(['organization_id' => $org->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'ends_at' => now()->addYear()]);

        $this->actingAs($user)->withSession(['active_location_id' => $location->id]);

        $response = $this->post(route('organization.menu.tables.store'), [
            'name' => 'Table 1'
        ]);
        
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('restaurant_tables', ['name' => 'Table 1', 'location_id' => $location->id]);
        
        $table = RestaurantTable::first();
        $this->assertNotNull($table->public_token);

        // Test public token resolution
        $response = $this->get(route('public.menu.table', $table->public_token));
        $response->assertStatus(200);
        $response->assertSessionHas('restaurant_table_id', $table->id);
    }
}
