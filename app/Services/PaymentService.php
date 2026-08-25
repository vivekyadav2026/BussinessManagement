<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Process a payment securely using pessimistic locking.
     */
    public static function processPayment(Invoice $invoice, array $data)
    {
        return DB::transaction(function () use ($invoice, $data) {
            // Lock the invoice row to prevent race conditions (e.g. double-click submit)
            $lockedInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->first();

            if ($lockedInvoice->status === 'Cancelled') {
                throw new \Exception("Cannot record payment for a cancelled invoice.");
            }

            $amountToPay = (float) $data['amount'];
            
            if ($amountToPay <= 0) {
                throw new \Exception("Payment amount must be greater than zero.");
            }

            $amountDue = $lockedInvoice->amount_due;
            
            // Allow a small floating point margin if necessary, but generally strict
            if (round($amountToPay, 2) > round($amountDue, 2)) {
                throw new \Exception("Payment amount (₹{$amountToPay}) cannot exceed the balance due (₹{$amountDue}).");
            }

            // Create Transaction
            $transaction = Transaction::create([
                'organization_id' => $lockedInvoice->organization_id,
                'location_id' => $lockedInvoice->location_id,
                'invoice_id' => $lockedInvoice->id,
                'amount' => $amountToPay,
                'payment_method' => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Update Invoice Paid Amount
            $newAmountPaid = $lockedInvoice->amount_paid + $amountToPay;
            $lockedInvoice->amount_paid = $newAmountPaid;

            // Auto-resolve Status
            if (round($newAmountPaid, 2) >= round($lockedInvoice->grand_total, 2)) {
                $lockedInvoice->status = 'Paid';
            } else {
                $lockedInvoice->status = 'Partially Paid';
            }

            $lockedInvoice->save();

            return $transaction;
        });
    }
}
