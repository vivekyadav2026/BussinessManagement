<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\LocationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_inventory_stock_is_location_scoped()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc1 = Location::create(['organization_id' => $org->id, 'name' => 'Branch 1']);
        $loc2 = Location::create(['organization_id' => $org->id, 'name' => 'Branch 2']);

        $category = Category::create(['organization_id' => $org->id, 'name' => 'Electronics']);
        $product = Product::create([
            'organization_id' => $org->id,
            'category_id' => $category->id,
            'name' => 'Test Item',
            'sku' => 'TEST-123',
            'selling_price' => 100
        ]);

        // Adjust stock for Loc 1
        InventoryService::adjustStock($product, $loc1->id, 50, 'in', 'Initial stock');
        
        // Adjust stock for Loc 2
        InventoryService::adjustStock($product, $loc2->id, 25, 'in', 'Initial stock');

        // Login and check loc 1
        $this->actingAs($user);
        
        LocationManager::setActiveLocationId($loc1->id);
        $this->assertEquals(50, $product->stock);

        LocationManager::setActiveLocationId($loc2->id);
        $this->assertEquals(25, $product->stock);
        
        $this->assertDatabaseHas('stock_movements', [
            'location_id' => $loc1->id,
            'quantity' => 50
        ]);
        
        $this->assertDatabaseHas('stock_movements', [
            'location_id' => $loc2->id,
            'quantity' => 25
        ]);
    }
}
