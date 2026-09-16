<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Organization;
use App\Models\Location;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\RestaurantOrder;

class PublicOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_order_from_public_menu()
    {
        $org = Organization::create([
            'name' => 'Burger Joint',
            'cgst_percent' => 2.5,
            'sgst_percent' => 2.5,
        ]);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'Downtown', 'is_active' => true]);
        
        $cat = MenuCategory::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'name' => 'Burgers']);
        $item = MenuItem::create([
            'menu_category_id' => $cat->id,
            'name' => 'Cheeseburger',
            'price' => 10.00,
            'is_available' => true
        ]);

        $table = RestaurantTable::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'name' => 'Table 5'
        ]);

        // 1. Visit QR code
        $response = $this->get(route('public.menu.table', $table->public_token));
        $response->assertStatus(200);
        $response->assertSessionHas('restaurant_table_id', $table->id);

        // 2. Add to cart
        $response = $this->post(route('public.order.add', [$org->id, $loc->id]), [
            'menu_item_id' => $item->id
        ]);
        $response->assertRedirect();
        
        $this->assertEquals(1, session("cart_{$loc->id}")[$item->id]['quantity']);

        // 3. Checkout (Table session is active, so order_type is forced to Dine-in)
        $response = $this->post(route('public.order.place', [$org->id, $loc->id]), [
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890'
        ]);
        
        $response->assertRedirect();

        $this->assertDatabaseHas('restaurant_orders', [
            'organization_id' => $org->id,
            'customer_name' => 'John Doe',
            'restaurant_table_id' => $table->id,
            'order_type' => 'Dine-in',
            'total' => 10.50 // 10 + 5% tax
        ]);

        $order = RestaurantOrder::first();
        $this->assertDatabaseHas('restaurant_order_items', [
            'restaurant_order_id' => $order->id,
            'menu_item_id' => $item->id,
            'quantity' => 1,
            'total' => 10.00
        ]);

        // 4. Tracking page
        $response = $this->get(route('public.order.track', [$org->id, $loc->id, $order->order_number]));
        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_order_with_zero_gst_does_not_add_tax()
    {
        $org = Organization::create([
            'name' => 'Zero Tax Cafe',
            'cgst_percent' => 0.00,
            'sgst_percent' => 0.00,
        ]);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'Cafe Outlet', 'is_active' => true]);
        
        $cat = MenuCategory::create(['organization_id' => $org->id, 'location_id' => $loc->id, 'name' => 'Beverages']);
        $item = MenuItem::create([
            'menu_category_id' => $cat->id,
            'name' => 'Coffee',
            'price' => 222.00,
            'is_available' => true
        ]);

        $this->post(route('public.order.add', [$org->id, $loc->id]), [
            'menu_item_id' => $item->id
        ]);

        // Cart should show exactly 222.00 without any tax added
        $cartResponse = $this->get(route('public.order.cart', [$org->id, $loc->id]));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('222.00');
        $cartResponse->assertDontSee('Central GST (2.5%)');

        // Place order
        $response = $this->post(route('public.order.place', [$org->id, $loc->id]), [
            'customer_name' => 'Zero Tax Customer',
            'customer_phone' => '9999999999',
            'order_type' => 'Takeaway'
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('restaurant_orders', [
            'organization_id' => $org->id,
            'customer_name' => 'Zero Tax Customer',
            'subtotal' => 222.00,
            'tax' => 0.00,
            'total' => 222.00,
        ]);
    }
}
