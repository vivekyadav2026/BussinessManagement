<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Services\PaymentService;

class TransactionController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        abort_if($invoice->organization_id !== auth()->user()->organization_id, 403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,UPI,Card,Razorpay',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            PaymentService::processPayment($invoice, $request->all());
            return back()->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt(Transaction $transaction)
    {
        abort_if($transaction->organization_id !== auth()->user()->organization_id, 403);
        $transaction->load(['invoice.client', 'organization', 'location']);
        return view('organization.transactions.receipt', compact('transaction'));
    }
}
