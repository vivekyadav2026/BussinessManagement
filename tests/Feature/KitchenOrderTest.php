<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Organization;
use App\Models\Location;
use App\Models\RestaurantOrder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class KitchenOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_kitchen_staff_can_view_and_update_orders()
    {
        $org = Organization::create(['name' => 'Burger Joint']);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'Downtown', 'is_active' => true]);
        
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        
        $p1 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.view', 'label' => 'View']);
        $p2 = Permission::create(['module' => 'Restaurant', 'name' => 'restaurant.manage', 'label' => 'Manage']);
        $role->permissions()->attach([$p1->id, $p2->id]);
        $user->roles()->attach($role);
        $user->load('roles.permissions');

        // Active subscription logic
        $plan = \App\Models\Plan::create(['name' => 'Pro']);
        $plan->features()->create(['feature_code' => 'module_restaurant', 'feature_value' => 'true']);
        \App\Models\OrganizationSubscription::create(['organization_id' => $org->id, 'plan_id' => $plan->id, 'starts_at' => now(), 'ends_at' => now()->addYear()]);

        $order = RestaurantOrder::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'order_number' => 'ORD-TEST',
            'order_type' => 'Takeaway',
            'status' => 'Received',
            'special_notes' => 'No onions',
            'subtotal' => 10.00,
            'tax' => 0.50,
            'total' => 10.50,
        ]);

        \App\Models\RestaurantOrderItem::create([
            'restaurant_order_id' => $order->id,
            'name_snapshot' => 'Cheeseburger',
            'price_snapshot' => 10.00,
            'quantity' => 1,
            'total' => 10.00
        ]);

        $this->actingAs($user)->withSession(['active_location_id' => $loc->id]);

        $response = $this->get(route('organization.menu.kitchen.index'));

        $response->assertStatus(200);

        $response = $this->getJson(route('organization.menu.kitchen.orders.fetch'));
        $response->assertStatus(200);
        $response->assertJsonFragment(['order_number' => 'ORD-TEST']);
        $response->assertJsonFragment(['special_notes' => 'No onions']);

        $response = $this->postJson(route('organization.menu.kitchen.orders.status', $order->id), [
            'status' => 'Preparing'
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals('Preparing', $order->fresh()->status);
        $this->assertNull($order->fresh()->invoice_id);

        // Mark as Served to trigger invoice creation
        $response = $this->postJson(route('organization.menu.kitchen.orders.status', $order->id), [
            'status' => 'Served'
        ]);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('Served', $order->status);
        $this->assertNotNull($order->invoice_id);

        $invoice = \App\Models\Invoice::find($order->invoice_id);
        $this->assertNotNull($invoice);
        $this->assertEquals($order->total, $invoice->amount_paid);
        $this->assertEquals('Paid', $invoice->status);
        $this->assertEquals('Generated from Restaurant Order: ' . $order->order_number, $invoice->notes);
        
        // Assert idempotency (calling it again doesn't create another invoice)
        $initialInvoiceId = $order->invoice_id;
        $response = $this->postJson(route('organization.menu.kitchen.orders.status', $order->id), [
            'status' => 'Served'
        ]);
        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals($initialInvoiceId, $order->invoice_id);
        $this->assertEquals(1, \App\Models\Invoice::count());
    }
}
