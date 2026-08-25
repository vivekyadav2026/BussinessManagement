<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\AnalyticsService;
use App\Services\BusinessHealthService;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_service_calculates_aggregates_correctly()
    {
        $org = Organization::create(['name' => 'Test Org']);
        
        $product = Product::create([
            'organization_id' => $org->id,
            'name' => 'Test Product',
            'purchase_price' => 50,
            'selling_price' => 100,
        ]);

        $loc = \App\Models\Location::create(['organization_id' => $org->id, 'name' => 'Store', 'is_active' => true]);
        $client = \App\Models\Client::create(['organization_id' => $org->id, 'name' => 'John Doe']);

        $invoice = Invoice::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => Carbon::now(),
            'due_date' => Carbon::now()->subDay(), // overdue
            'subtotal' => 100,
            'grand_total' => 100,
            'amount_due' => 100,
            'status' => 'Overdue'
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'quantity' => 2,
            'unit_price' => 100,
            'total' => 200
        ]);

        // Mock a second invoice for last month
        $invoiceOld = Invoice::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-002',
            'invoice_date' => Carbon::now()->subMonth()->startOfMonth(),
            'due_date' => Carbon::now()->subMonth()->startOfMonth(),
            'subtotal' => 50,
            'grand_total' => 50,
            'amount_paid' => 50,
            'status' => 'Paid'
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoiceOld->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'quantity' => 1,
            'unit_price' => 100,
            'total' => 100
        ]);

        $sales = AnalyticsService::getSalesAndProfitMetrics($org->id);
        
        // This Month: Grand Total = 100
        $this->assertEquals(100, $sales['sales_month']);
        // Last Month: Grand Total = 50
        $this->assertEquals(50, $sales['sales_last_month']);
        
        // Profit this month: 2 qty * (100 price - 50 cost) = 100
        $this->assertEquals(100, $sales['profit_month']);
        // Profit last month: 1 qty * (100 price - 50 cost) = 50
        $this->assertEquals(50, $sales['profit_last_month']);
        
        // Growth: (100 - 50) / 50 * 100 = 100%
        $this->assertEquals(100, $sales['sales_growth']);
        $this->assertEquals(100, $sales['profit_growth']);

        $receivables = AnalyticsService::getReceivablesMetrics($org->id);
        $this->assertEquals(100, $receivables['outstanding']);
        $this->assertEquals(100, $receivables['overdue']);
        
        $health = BusinessHealthService::calculateScore($org->id);
        // Assert we got a valid score between 0 and 100
        $this->assertGreaterThanOrEqual(0, $health['score']);
        $this->assertLessThanOrEqual(100, $health['score']);
    }

    public function test_dashboard_can_be_rendered_for_org_admin()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Organization Admin']);
        $user->roles()->attach($role);

        $this->actingAs($user);
        $response = $this->get(route('organization.dashboard'));
        $response->assertStatus(200);
    }
}
