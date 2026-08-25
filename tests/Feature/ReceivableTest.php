<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Location;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\Reminders\ReminderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ReceivableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_receivables_dashboard_loads_with_kpis()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        // Overdue Invoice
        Invoice::create([
            'organization_id' => $org->id, 'location_id' => $loc->id, 'client_id' => $client->id,
            'invoice_number' => 'INV-001', 'invoice_date' => now()->subDays(10), 'due_date' => now()->subDays(2),
            'grand_total' => 1000, 'amount_paid' => 200, 'status' => 'Partially Paid'
        ]);

        // Paid Invoice (Should not appear in overdue/outstanding)
        Invoice::create([
            'organization_id' => $org->id, 'location_id' => $loc->id, 'client_id' => $client->id,
            'invoice_number' => 'INV-002', 'invoice_date' => now(), 'due_date' => now(),
            'grand_total' => 500, 'amount_paid' => 500, 'status' => 'Paid'
        ]);

        $this->actingAs($user);
        
        $response = $this->get(route('organization.receivables.index'));
        $response->assertStatus(200);
        $response->assertSee('800.00'); // 1000 - 200 outstanding
        $response->assertDontSee('500.00'); // Paid should not be summed
    }

    public function test_reminder_manager_resolves_channels_and_prevents_fake_whatsapp()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client', 'phone' => '1234567890']);
        
        $invoice = Invoice::create([
            'organization_id' => $org->id, 'location_id' => $loc->id, 'client_id' => $client->id,
            'invoice_number' => 'INV-001', 'invoice_date' => now(), 'due_date' => now()->subDays(2),
            'grand_total' => 1000, 'amount_paid' => 0, 'status' => 'Due'
        ]);

        $this->actingAs($user);

        // Test WhatsApp Reminder
        $response = $this->post(route('organization.invoices.remind', $invoice), ['channel' => 'whatsapp']);
        $response->assertSessionHas('error'); // WhatsApp is just a placeholder and returns false success

        // Test Payment Link Generation
        $linkResponse = $this->get(route('organization.invoices.payment-link', $invoice));
        $linkResponse->assertStatus(200);
        $this->assertStringContainsString('signature=', $linkResponse->json('url'));
    }

    public function test_public_signed_invoice_url_works()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        
        $invoice = Invoice::create([
            'organization_id' => $org->id, 'location_id' => $loc->id, 'client_id' => $client->id,
            'invoice_number' => 'INV-001', 'invoice_date' => now(), 'due_date' => now(),
            'grand_total' => 1000, 'amount_paid' => 0, 'status' => 'Due'
        ]);

        $url = URL::temporarySignedRoute('public.invoice.pay', now()->addMinutes(5), ['invoice' => $invoice->id]);
        
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee('INV-001');
    }
}
