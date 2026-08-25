<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Services\InventoryService;
use App\Services\LocationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_invoice_creation_deducts_stock_securely()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        $product = Product::create([
            'organization_id' => $org->id,
            'name' => 'Laptop',
            'selling_price' => 1000,
            'tax_rate' => 10
        ]);
        
        // Add 10 stock
        InventoryService::adjustStock($product, $loc->id, 10, 'in', 'Initial');

        // Create invoice
        $this->actingAs($user);
        LocationManager::setActiveLocationId($loc->id);

        $payload = [
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'Paid',
            'discount' => 100,
            'amount_paid' => 1000, // 1000 + 100 tax - 100 discount = 1000 grand total
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ];

        $response = $this->postJson(route('organization.invoices.store'), $payload);
        $response->assertStatus(200);

        // Check stock deducted
        $this->assertEquals(9, $product->fresh()->stock);

        // Check financials calculated correctly regardless of what frontend sent
        $invoice = Invoice::first();
        $this->assertEquals(1000, $invoice->subtotal);
        $this->assertEquals(100, $invoice->tax);
        $this->assertEquals(100, $invoice->discount);
        $this->assertEquals(1000, $invoice->grand_total);
        $this->assertEquals(1000, $invoice->amount_paid);
        $this->assertEquals(0, $invoice->amount_due);
    }

    public function test_cancelling_invoice_restores_stock()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        $product = Product::create([
            'organization_id' => $org->id,
            'name' => 'Phone',
            'selling_price' => 500,
        ]);
        InventoryService::adjustStock($product, $loc->id, 5, 'in', 'Initial');

        $this->actingAs($user);
        LocationManager::setActiveLocationId($loc->id);

        $payload = [
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'Due',
            'items' => [['product_id' => $product->id, 'quantity' => 2]]
        ];

        $this->postJson(route('organization.invoices.store'), $payload);
        $this->assertEquals(3, $product->fresh()->stock);

        $invoice = Invoice::first();
        
        // Cancel it
        $this->post(route('organization.invoices.cancel', $invoice));

        $this->assertEquals('Cancelled', $invoice->fresh()->status);
        $this->assertEquals(5, $product->fresh()->stock);
    }
}
