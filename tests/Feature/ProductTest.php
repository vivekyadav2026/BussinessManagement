<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Location;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_crud_product()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);
        
        $category = Category::create([
            'organization_id' => $org->id,
            'name' => 'Electronics',
            'type' => 'product'
        ]);

        $this->actingAs($user);
        
        // Create
        $response = $this->post(route('organization.products.store'), [
            'name' => 'Laptop',
            'category_id' => $category->id,
            'barcode' => '123456789',
            'selling_price' => 1000,
            'purchase_price' => 800,
            'type' => 'Goods',
        ]);
        
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'name' => 'Laptop',
            'barcode' => '123456789'
        ]);
        
        $product = Product::first();
        
        // Read
        $response = $this->get(route('organization.products.index'));
        $response->assertSee('Laptop');
        
        // Update
        $response = $this->put(route('organization.products.update', $product), [
            'name' => 'Gaming Laptop',
            'category_id' => $category->id,
            'selling_price' => 1200,
            'purchase_price' => 900,
            'type' => 'Goods',
        ]);
        $this->assertDatabaseHas('products', ['name' => 'Gaming Laptop']);
    }
}
