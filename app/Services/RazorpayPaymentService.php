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

        // Note: Razorpay expects amount in paise (1 INR = 100 Paise)
        $amountInPaise = (int) round($amount * 100);

        $response = Http::withBasicAuth($key, $secret)
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
            throw new \Exception('Failed to create Razorpay Order: ' . $response->body());
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
     * Process incoming webhook securely and idempotently.
     */
    public static function processWebhook($payload, $signature)
    {
        if (!self::verifySignature($payload, $signature, config('services.razorpay.webhook_secret'))) {
            throw new \Exception('Invalid Signature');
        }

        $data = json_decode($payload, true);
        $event = $data['event'];

        if ($event === 'payment.captured') {
            $paymentObj = $data['payload']['payment']['entity'];
            $orderId = $paymentObj['order_id'];
            $paymentId = $paymentObj['id'];

            // Find our local gateway payment
            $gatewayPayment = GatewayPayment::where('razorpay_order_id', $orderId)->first();

            if (!$gatewayPayment) {
                Log::warning('Razorpay webhook received for unknown order_id: ' . $orderId);
                return;
            }

            // IDEMPOTENCY CHECK
            if (in_array($gatewayPayment->status, ['captured', 'refunded'])) {
                Log::info('Razorpay webhook already processed for payment_id: ' . $paymentId);
                return;
            }

            DB::transaction(function () use ($gatewayPayment, $paymentId, $event) {
                // Lock row to prevent concurrent webhook processing
                $lockedGateway = GatewayPayment::where('id', $gatewayPayment->id)->lockForUpdate()->first();

                if ($lockedGateway->status === 'captured') {
                    return; // Another thread processed it
                }

                $lockedGateway->update([
                    'razorpay_payment_id' => $paymentId,
                    'status' => 'captured',
                    'webhook_event' => $event,
                ]);

                self::applyPaymentToEntity($lockedGateway);
            });
        } elseif ($event === 'payment.failed') {
            // Handle failure similarly...
        }
    }

    /**
     * Apply the verified payment securely to the respective system.
     */
    private static function applyPaymentToEntity(GatewayPayment $gatewayPayment)
    {
        $entityClass = $gatewayPayment->entity_type;
        $entity = $entityClass::find($gatewayPayment->entity_id);

        if (!$entity) return;

        if ($entity instanceof Invoice) {
            PaymentService::processPayment($entity, [
                'amount' => $gatewayPayment->amount,
                'payment_method' => 'Razorpay',
                'reference_number' => $gatewayPayment->razorpay_payment_id,
                'payment_date' => now()->toDateString(),
                'notes' => 'Paid via Razorpay Webhook. Order: ' . $gatewayPayment->razorpay_order_id,
            ]);
        } elseif ($entity instanceof RestaurantOrder) {
            // Update Restaurant Order directly if it hasn't been invoiced yet
            // Though theoretically, it might have an invoice_id if served.
            $entity->update([
                'payment_status' => 'Paid',
            ]);

            // If it has a related invoice, pay that too (or it was paid through the invoice branch)
            if ($entity->invoice_id) {
                $invoice = Invoice::find($entity->invoice_id);
                if ($invoice) {
                    PaymentService::processPayment($invoice, [
                        'amount' => $gatewayPayment->amount,
                        'payment_method' => 'Razorpay',
                        'reference_number' => $gatewayPayment->razorpay_payment_id,
                        'payment_date' => now()->toDateString(),
                        'notes' => 'Restaurant KOT Paid via Razorpay Webhook.',
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
