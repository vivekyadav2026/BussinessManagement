<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\RestaurantOrder;
use App\Services\RazorpayPaymentService;

class PaymentController extends Controller
{
    public function checkoutInvoice(Invoice $invoice)
    {
        // Simple security check or public token validation usually goes here
        if ($invoice->status === 'Paid' || $invoice->status === 'Cancelled') {
            return back()->with('error', 'Invoice cannot be paid.');
        }

        $balance = $invoice->grand_total - $invoice->amount_paid;

        $gatewayPayment = RazorpayPaymentService::createOrder($invoice, $balance);

        return view('payments.razorpay', [
            'payment' => $gatewayPayment,
            'amount' => $balance,
            'key' => config('services.razorpay.key'),
            'name' => $invoice->organization->name,
            'description' => 'Payment for Invoice ' . $invoice->invoice_number,
        ]);
    }

    public function checkoutRestaurantOrder(RestaurantOrder $order)
    {
        if ($order->payment_status === 'Paid' || $order->status === 'Cancelled') {
            return back()->with('error', 'Order cannot be paid or is already paid.');
        }

        $gatewayPayment = RazorpayPaymentService::createOrder($order, $order->total);

        return view('payments.razorpay', [
            'payment' => $gatewayPayment,
            'amount' => $order->total,
            'key' => config('services.razorpay.key'),
            'name' => $order->organization->name,
            'description' => 'Payment for Order ' . $order->order_number,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        try {
            $gatewayPayment = RazorpayPaymentService::verifyAndProcessPayment(
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment verified & recorded successfully!',
                'status' => $gatewayPayment->status,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
