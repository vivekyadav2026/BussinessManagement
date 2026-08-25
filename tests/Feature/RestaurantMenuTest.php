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
use App\Models\MenuCategory;
use App\Models\MenuItem;

class RestaurantMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_manage_restaurant_menu()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        
        $p1 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.view', 'label' => 'View Restaurant']);
        $p2 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.manage', 'label' => 'Manage Restaurant']);
        $role->permissions()->attach([$p1->id, $p2->id]);
        $user->roles()->attach($role);

        $location = Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);
        
        // Mock active subscription for the middleware
        $plan = \App\Models\Plan::create(['name' => 'Pro']);
        $plan->features()->create(['feature_code' => 'module_restaurant', 'feature_value' => 'true']);
        \App\Models\OrganizationSubscription::create(['organization_id' => $org->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'ends_at' => now()->addYear()]);

        $this->actingAs($user)->withSession(['active_location_id' => $location->id]);

        $response = $this->post(route('organization.menu.categories.store'), [
            'name' => 'Beverages'
        ]);
        
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menu_categories', ['name' => 'Beverages', 'location_id' => $location->id]);
        
        $category = MenuCategory::first();

        $response = $this->post(route('organization.menu.items.store'), [
            'menu_category_id' => $category->id,
            'name' => 'Coke',
            'price' => 2.50
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('menu_items', ['name' => 'Coke', 'price' => 2.50]);

        // Public menu test
        $response = $this->get(route('public.menu', [$org->id, $location->id]));
        $response->assertStatus(200);
        $response->assertSee('Beverages');
        $response->assertSee('Coke');
    }
}
