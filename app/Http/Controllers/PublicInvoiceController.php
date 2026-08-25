<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class PublicInvoiceController extends Controller
{
    public function show(Request $request, Invoice $invoice)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'This payment link is invalid or has expired.');
        }
        
        $invoice->load(['client', 'organization', 'location', 'items']);
        
        return view('public.invoice.pay', compact('invoice'));
    }
}
