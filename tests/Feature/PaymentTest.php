<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_partial_payment_updates_status_and_balance()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        $invoice = Invoice::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now(),
            'grand_total' => 1000,
            'amount_paid' => 0,
            'status' => 'Due'
        ]);

        $this->actingAs($user);
        
        $response = $this->post(route('organization.invoices.payments.store', $invoice), [
            'amount' => 400,
            'payment_method' => 'Cash',
            'payment_date' => now()->toDateString()
        ]);

        $response->assertSessionHas('success');
        
        $invoice->refresh();
        $this->assertEquals(400, $invoice->amount_paid);
        $this->assertEquals(600, $invoice->amount_due);
        $this->assertEquals('Partially Paid', $invoice->status);
        $this->assertEquals(1, $invoice->transactions()->count());
    }

    public function test_full_payment_updates_status_to_paid()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        $invoice = Invoice::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now(),
            'grand_total' => 1000,
            'amount_paid' => 400, // partially paid earlier
            'status' => 'Partially Paid'
        ]);

        $this->actingAs($user);
        
        $response = $this->post(route('organization.invoices.payments.store', $invoice), [
            'amount' => 600, // exact remainder
            'payment_method' => 'UPI',
            'payment_date' => now()->toDateString()
        ]);

        $response->assertSessionHas('success');
        
        $invoice->refresh();
        $this->assertEquals(1000, $invoice->amount_paid);
        $this->assertEquals(0, $invoice->amount_due);
        $this->assertEquals('Paid', $invoice->status);
    }

    public function test_overpayment_is_prevented()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        $invoice = Invoice::create([
            'organization_id' => $org->id,
            'location_id' => $loc->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'invoice_date' => now(),
            'grand_total' => 1000,
            'amount_paid' => 800,
            'status' => 'Partially Paid'
        ]);

        $this->actingAs($user);
        
        $response = $this->post(route('organization.invoices.payments.store', $invoice), [
            'amount' => 300, // remaining is only 200
            'payment_method' => 'Cash',
            'payment_date' => now()->toDateString()
        ]);

        $response->assertSessionHas('error'); // Should throw error safely
        
        $invoice->refresh();
        $this->assertEquals(800, $invoice->amount_paid); // Should not have changed
        $this->assertEquals('Partially Paid', $invoice->status);
    }
}
