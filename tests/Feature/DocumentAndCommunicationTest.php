<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Organization;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Location;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\User;
use App\Services\DocumentService;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Notification;

class DocumentAndCommunicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_invoice_pdf()
    {
        $org = Organization::create(['name' => 'Org', 'tax_id' => 'GST123']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client']);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);

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

        $pdf = DocumentService::generateInvoicePdf($invoice);
        $this->assertNotNull($pdf);
        $this->assertTrue(strlen($pdf->output()) > 0);
    }

    public function test_can_generate_payslip_pdf()
    {
        $org = Organization::create(['name' => 'Org']);
        $emp = Employee::create(['organization_id' => $org->id, 'first_name' => 'Jane', 'last_name' => 'Doe', 'basic_salary' => 50000]);
        $payroll = Payroll::create([
            'organization_id' => $org->id,
            'employee_id' => $emp->id,
            'month' => '8',
            'year' => 2026,
            'basic_salary' => 50000,
            'net_salary' => 50000,
            'status' => 'Paid'
        ]);

        $pdf = DocumentService::generatePayslipPdf($payroll);
        $this->assertNotNull($pdf);
        $this->assertTrue(strlen($pdf->output()) > 0);
    }

    public function test_can_send_invoice_notifications()
    {
        Notification::fake();

        $org = Organization::create(['name' => 'Org']);
        $client = Client::create(['organization_id' => $org->id, 'name' => 'Test Client', 'phone' => '1234567890']);
        $loc = Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);

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

        CommunicationService::sendInvoice($invoice, ['mail', 'whatsapp']);

        Notification::assertSentTo(
            $client,
            \App\Notifications\InvoiceNotification::class
        );
    }
}
