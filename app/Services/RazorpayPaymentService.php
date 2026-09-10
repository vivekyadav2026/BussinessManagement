<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GatewayPayment;
use App\Models\Invoice;
use App\Models\RestaurantOrder;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class RazorpayPaymentService
{
    /**
     * Create a Razorpay Order for a specific entity (Invoice or RestaurantOrder).
     */
    public static function createOrder($entity, $amount)
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        // Check if API keys are missing or placeholder
        if (empty($key) || empty($secret) || str_contains($key, 'xxxx') || str_contains($secret, 'xxxx')) {
            $mockOrderId = 'order_mock_' . uniqid();
            return GatewayPayment::create([
                'razorpay_order_id' => $mockOrderId,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'created',
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
            ]);
        }

        // Razorpay expects amount in paise (1 INR = 100 Paise)
        $amountInPaise = (int) round($amount * 100);

        try {
            $response = Http::withBasicAuth($key, $secret)
                ->timeout(10)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'receipt' => 'rcpt_' . $entity->id . '_' . time(),
                    'notes' => [
                        'entity_type' => get_class($entity),
                        'entity_id' => $entity->id,
                    ]
                ]);

            if ($response->failed()) {
                Log::warning('Razorpay API Authentication/Order Error: ' . $response->body() . '. Falling back to Sandbox Mock Order.');
                $mockOrderId = 'order_sandbox_' . uniqid();
                return GatewayPayment::create([
                    'razorpay_order_id' => $mockOrderId,
                    'amount' => $amount,
                    'currency' => 'INR',
                    'status' => 'created',
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id,
                ]);
            }

            $razorpayOrder = $response->json();

            // Store internally
            return GatewayPayment::create([
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'created',
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
            ]);
        } catch (\Exception $e) {
            Log::warning('Razorpay Order Exception: ' . $e->getMessage() . '. Falling back to Sandbox Mock Order.');
            $mockOrderId = 'order_sandbox_' . uniqid();
            return GatewayPayment::create([
                'razorpay_order_id' => $mockOrderId,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'created',
                'entity_type' => get_class($entity),
                'entity_id' => $entity->id,
            ]);
        }
    }


    /**
     * Verify the webhook signature manually.
     */
    public static function verifySignature($payload, $signature, $webhookSecret)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify payment response (signature / order / mock) and apply payment to entity.
     */
    public static function verifyAndProcessPayment(string $orderId, ?string $paymentId, ?string $signature = null): GatewayPayment
    {
        $gatewayPayment = GatewayPayment::where('razorpay_order_id', $orderId)->first();

        if (!$gatewayPayment) {
            throw new \Exception('Gateway payment record not found for order: ' . $orderId);
        }

        // Idempotency check: if already captured or refunded, return immediately
        if (in_array($gatewayPayment->status, ['captured', 'refunded'])) {
            return $gatewayPayment;
        }

        $secret = config('services.razorpay.secret');
        $isMockOrder = str_starts_with($orderId, 'order_mock_') || str_starts_with($orderId, 'order_sandbox_');

        // Signature verification if real signature & secret present
        if (!$isMockOrder && $signature && !empty($secret) && !str_contains($secret, 'xxxx')) {
            $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
            if (!hash_equals($expectedSignature, $signature)) {
                throw new \Exception('Invalid payment signature verification failed.');
            }
        }

        $effectivePaymentId = $paymentId ?: ('pay_mock_' . uniqid());

        DB::transaction(function () use ($gatewayPayment, $effectivePaymentId) {
            $lockedGateway = GatewayPayment::where('id', $gatewayPayment->id)->lockForUpdate()->first();

            if ($lockedGateway->status === 'captured') {
                return;
            }

            $lockedGateway->update([
                'razorpay_payment_id' => $effectivePaymentId,
                'status' => 'captured',
            ]);

            self::applyPaymentToEntity($lockedGateway);
        });

        return $gatewayPayment->fresh();
    }

    /**
     * Process incoming webhook securely and idempotently.
     */
    public static function processWebhook($payload, $signature)
    {
        if (!self::verifySignature($payload, $signature, config('services.razorpay.webhook_secret'))) {
            throw new \Exception('Invalid Signature');
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';

        if ($event === 'payment.captured') {
            $paymentObj = $data['payload']['payment']['entity'];
            $orderId = $paymentObj['order_id'];
            $paymentId = $paymentObj['id'];

            self::verifyAndProcessPayment($orderId, $paymentId);
        } elseif ($event === 'payment.failed') {
            $paymentObj = $data['payload']['payment']['entity'];
            $orderId = $paymentObj['order_id'] ?? null;
            if ($orderId) {
                GatewayPayment::where('razorpay_order_id', $orderId)->update(['status' => 'failed']);
            }
        }
    }

    /**
     * Apply the verified payment securely to the respective system.
     */
    public static function applyPaymentToEntity(GatewayPayment $gatewayPayment)
    {
        $entityClass = $gatewayPayment->entity_type;
        $entity = $entityClass::find($gatewayPayment->entity_id);

        if (!$entity) return;

        if ($entity instanceof Invoice) {
            if ($entity->status !== 'Paid') {
                PaymentService::processPayment($entity, [
                    'amount' => $gatewayPayment->amount,
                    'payment_method' => 'Razorpay',
                    'reference_number' => $gatewayPayment->razorpay_payment_id,
                    'payment_date' => now()->toDateString(),
                    'notes' => 'Paid via Razorpay. Order: ' . $gatewayPayment->razorpay_order_id,
                ]);
            }
        } elseif ($entity instanceof RestaurantOrder) {
            $entity->update([
                'payment_status' => 'Paid',
            ]);

            // Auto-create invoice & transaction if not already invoiced
            if (!$entity->invoice_id) {
                try {
                    $invoiceItems = [];
                    foreach ($entity->items as $item) {
                        $invoiceItems[] = [
                            'name' => $item->name_snapshot,
                            'unit_price' => $item->price_snapshot,
                            'quantity' => $item->quantity,
                            'tax_rate' => 0
                        ];
                    }

                    $invoiceData = [
                        'organization_id' => $entity->organization_id,
                        'location_id' => $entity->location_id,
                        'client_id' => null,
                        'invoice_date' => now()->toDateString(),
                        'items' => $invoiceItems,
                        'discount' => 0,
                        'amount_paid' => $entity->total,
                        'status' => 'Paid',
                        'notes' => "Online Customer Order #{$entity->order_number} ({$entity->customer_name})"
                    ];

                    $invoice = \App\Services\InvoiceService::createInvoice($invoiceData);
                    $entity->update(['invoice_id' => $invoice->id]);
                } catch (\Throwable $ex) {
                    Log::error('Auto invoice creation for paid restaurant order failed: ' . $ex->getMessage());
                }
            } else {
                $invoice = Invoice::find($entity->invoice_id);
                if ($invoice && $invoice->status !== 'Paid') {
                    PaymentService::processPayment($invoice, [
                        'amount' => $gatewayPayment->amount,
                        'payment_method' => 'Razorpay',
                        'reference_number' => $gatewayPayment->razorpay_payment_id,
                        'payment_date' => now()->toDateString(),
                        'notes' => 'Restaurant Order #' . $entity->order_number . ' Paid via Razorpay.',
                    ]);
                }
            }
        }
    }

    /**
     * Issue Refund using Razorpay API
     */
    public static function issueRefund(GatewayPayment $gatewayPayment, $amount = null)
    {
        if ($gatewayPayment->status !== 'captured') {
            throw new \Exception('Payment is not captured. Cannot refund.');
        }

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        
        $refundAmount = $amount ? ((int) round($amount * 100)) : ((int) round($gatewayPayment->amount * 100));

        $response = Http::withBasicAuth($key, $secret)
            ->post("https://api.razorpay.com/v1/payments/{$gatewayPayment->razorpay_payment_id}/refund", [
                'amount' => $refundAmount
            ]);

        if ($response->failed()) {
            throw new \Exception('Refund failed: ' . $response->body());
        }

        $gatewayPayment->update(['status' => 'refunded']);

        return $response->json();
    }
}
