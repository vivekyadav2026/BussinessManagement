<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Organization;
use App\Models\Invoice;
use App\Models\GatewayPayment;
use App\Services\RazorpayPaymentService;
use Illuminate\Support\Facades\Http;

class RazorpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_razorpay_webhook_captures_payment_and_updates_invoice()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $client = \App\Models\Client::create(['organization_id' => $org->id, 'name' => 'John Doe']);
        $loc = \App\Models\Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);

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

        $gatewayPayment = GatewayPayment::create([
            'razorpay_order_id' => 'order_test123',
            'amount' => 1000,
            'currency' => 'INR',
            'status' => 'created',
            'entity_type' => Invoice::class,
            'entity_id' => $invoice->id,
        ]);

        $payload = json_encode([
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_test456',
                        'order_id' => 'order_test123'
                    ]
                ]
            ]
        ]);

        config(['services.razorpay.webhook_secret' => 'secret']);
        $signature = hash_hmac('sha256', $payload, 'secret');

        // We need to properly mock the payload content for $request->getContent()
        $response = $this->call('POST', 'webhook/razorpay', [], [], [], [
            'HTTP_X_Razorpay_Signature' => $signature,
            'CONTENT_TYPE' => 'application/json'
        ], $payload);

        $response->assertStatus(200);

        $this->assertEquals('captured', $gatewayPayment->fresh()->status);
        $this->assertEquals('Paid', $invoice->fresh()->status);
        $this->assertEquals(0, $invoice->fresh()->balance_due);
    }

    public function test_webhook_signature_verification_rejects_invalid_signature()
    {
        $payload = json_encode(['event' => 'payment.captured']);
        $invalidSignature = 'fake_signature';

        $response = $this->postJson('/webhook/razorpay', json_decode($payload, true), [
            'X-Razorpay-Signature' => $invalidSignature
        ]);

        $response->assertStatus(400);
        $response->assertSee('Invalid Signature');
    }

    public function test_duplicate_webhook_is_handled_idempotently()
    {
        $org = Organization::create(['name' => 'Test Org']);
        $client = \App\Models\Client::create(['organization_id' => $org->id, 'name' => 'John Doe']);
        $loc = \App\Models\Location::create(['organization_id' => $org->id, 'name' => 'HQ', 'is_active' => true]);

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

        $gatewayPayment = GatewayPayment::create([
            'entity_id' => $invoice->id,
            'entity_type' => Invoice::class,
            'amount' => 1000,
            'currency' => 'INR',
            'razorpay_order_id' => 'order_xyz987',
            'status' => 'created'
        ]);

        $payload = json_encode([
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_def456',
                        'order_id' => 'order_xyz987'
                    ]
                ]
            ]
        ]);

        config(['services.razorpay.webhook_secret' => 'dummy_secret']);
        $signature = hash_hmac('sha256', $payload, 'dummy_secret');

        // First Webhook (Success)
        $this->call('POST', 'webhook/razorpay', [], [], [], [
            'HTTP_X_Razorpay_Signature' => $signature,
            'CONTENT_TYPE' => 'application/json'
        ], $payload)->assertStatus(200);

        // Assert payment captured
        $this->assertEquals('captured', $gatewayPayment->fresh()->status);
        $this->assertEquals(1000, $invoice->fresh()->amount_paid);

        // Duplicate Webhook
        $this->call('POST', 'webhook/razorpay', [], [], [], [
            'HTTP_X_Razorpay_Signature' => $signature,
            'CONTENT_TYPE' => 'application/json'
        ], $payload)->assertStatus(200);

        // Balance should NOT increase again
        $this->assertEquals(1000, $invoice->fresh()->amount_paid);
    }
}
