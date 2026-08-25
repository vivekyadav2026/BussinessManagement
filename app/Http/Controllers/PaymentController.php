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
}
