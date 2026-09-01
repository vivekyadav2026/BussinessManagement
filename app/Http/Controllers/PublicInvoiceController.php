<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\GatewayPayment;
use App\Services\RazorpayPaymentService;
use Illuminate\Support\Facades\Log;

class PublicInvoiceController extends Controller
{
    public function show(Request $request, Invoice $invoice)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'This payment link is invalid or has expired.');
        }
        
        $invoice->load(['client', 'organization', 'location', 'items']);

        $payment = null;
        $key = config('services.razorpay.key');

        if ($invoice->amount_due > 0 && $invoice->status !== 'Cancelled') {
            try {
                // Check for existing created payment order for this invoice
                $payment = GatewayPayment::where('entity_type', get_class($invoice))
                    ->where('entity_id', $invoice->id)
                    ->where('status', 'created')
                    ->latest()
                    ->first();

                if (!$payment && $key && $key !== 'rzp_test_xxxxxxxxx') {
                    $payment = RazorpayPaymentService::createOrder($invoice, $invoice->amount_due);
                }
            } catch (\Exception $e) {
                Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            }
        }
        
        return view('public.invoice.pay', compact('invoice', 'payment', 'key'));
    }
}

