@extends('layouts.sme')

@section('title', 'Invoice Details - ' . $invoice->invoice_number)

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Action Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.invoices.index') }}" class="hover:text-slate-900 transition-colors">Operations</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.invoices.index') }}" class="hover:text-slate-900 transition-colors">Invoices</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $invoice->invoice_number }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    🧾
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Invoice: {{ $invoice->invoice_number }}</h1>
                        @if($invoice->status === 'Paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                Paid
                            </span>
                        @elseif($invoice->status === 'Partially Paid')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-900 border border-amber-300">
                                Partially Paid
                            </span>
                        @elseif($invoice->status === 'Due')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-50 text-blue-800 border border-blue-300">
                                Due
                            </span>
                        @elseif($invoice->status === 'Overdue')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-800 border border-rose-300 animate-pulse">
                                Overdue
                            </span>
                        @elseif($invoice->status === 'Draft')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                Draft
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                {{ $invoice->status }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Issued on {{ $invoice->invoice_date->format('d M, Y') }} &bull; Location: <span class="font-bold text-slate-900">{{ $invoice->location->name ?? 'Active Branch' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Header Action CTAs -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.invoices.print', $invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print A4 Invoice</span>
            </a>
            <a href="{{ route('organization.invoices.receipt', $invoice) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 font-extrabold text-xs rounded-lg shadow-2xs transition">
                <span>🧾 POS Receipt</span>
            </a>
            @if($invoice->status !== 'Cancelled')
                <form action="{{ route('organization.invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this invoice? Stock quantities will be automatically restored.');" class="m-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-rose-50 border border-rose-300 text-rose-700 font-bold text-xs rounded-lg shadow-2xs transition">
                        Cancel Invoice
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Invoice Content (Col 1 & 2) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Client & Invoice Info Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6 relative overflow-hidden">
                <!-- Top Status Stripe -->
                <div class="absolute top-0 left-0 right-0 h-1.5 
                    {{ $invoice->status == 'Paid' ? 'bg-emerald-500' : '' }}
                    {{ $invoice->status == 'Draft' ? 'bg-slate-400' : '' }}
                    {{ $invoice->status == 'Due' ? 'bg-blue-500' : '' }}
                    {{ $invoice->status == 'Overdue' ? 'bg-rose-500' : '' }}
                    {{ $invoice->status == 'Partially Paid' ? 'bg-amber-500' : '' }}
                    {{ $invoice->status == 'Cancelled' ? 'bg-slate-300' : '' }}
                "></div>

                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-slate-200 pb-5 mb-5">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Billed Customer</span>
                        @if($invoice->client)
                            <div class="font-extrabold text-lg text-slate-950">
                                <a href="{{ route('organization.clients.show', $invoice->client_id) }}" class="text-indigo-600 hover:underline">
                                    {{ $invoice->client->name }}
                                </a>
                            </div>
                            <div class="text-xs text-slate-600 mt-1 flex flex-col gap-0.5">
                                @if($invoice->client->phone) <span><strong>Phone:</strong> {{ $invoice->client->phone }}</span> @endif
                                @if($invoice->client->email) <span><strong>Email:</strong> {{ $invoice->client->email }}</span> @endif
                                @if($invoice->client->gst_number) 
                                    <span class="font-mono text-xs text-slate-800 mt-1 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 w-max">
                                        GSTIN: {{ $invoice->client->gst_number }}
                                    </span> 
                                @endif
                            </div>
                        @else
                            <div class="font-bold text-base text-slate-600 italic">Walk-in Client / General Customer</div>
                        @endif
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Billing Details</span>
                        <div class="text-xs text-slate-700 space-y-1">
                            <div><span class="text-slate-500 font-medium">Issue Date:</span> <strong class="text-slate-950">{{ $invoice->invoice_date->format('d M, Y') }}</strong></div>
                            <div><span class="text-slate-500 font-medium">Due Date:</span> <strong class="{{ $invoice->due_date && $invoice->due_date < now() && $invoice->amount_due > 0 ? 'text-rose-600' : 'text-slate-950' }}">{{ $invoice->due_date ? $invoice->due_date->format('d M, Y') : 'Immediate' }}</strong></div>
                            <div><span class="text-slate-500 font-medium">Branch:</span> <strong class="text-slate-950">{{ $invoice->location->name ?? 'Default Branch' }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Itemized Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-[11px] font-extrabold uppercase tracking-wider">
                                <th class="py-3 px-4">Item Description</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Tax (GST)</th>
                                <th class="py-3 px-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @foreach($invoice->items as $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-slate-950">{{ $item->product_name_snapshot }}</div>
                                    @if($item->product && $item->product->sku)
                                        <div class="text-[11px] text-slate-500 font-mono mt-0.5">SKU: {{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-700">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">₹{{ number_format($item->tax, 2) }}</td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-slate-950">₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($invoice->notes)
                <div class="mt-5 p-3.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 leading-relaxed">
                    <span class="font-extrabold text-slate-900 block mb-0.5">Invoice Notes:</span>
                    {{ $invoice->notes }}
                </div>
                @endif
            </div>

            <!-- Transaction Ledger History -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <h3 class="font-extrabold text-slate-950 text-sm uppercase tracking-wider flex items-center gap-2">
                        <span>Payment Transactions</span>
                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full font-bold border border-slate-200">
                            {{ $invoice->transactions->count() }}
                        </span>
                    </h3>
                </div>

                @if($invoice->transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4">Method</th>
                                <th class="py-2.5 px-4">Reference #</th>
                                <th class="py-2.5 px-4 text-right">Amount</th>
                                <th class="py-2.5 px-4 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @foreach($invoice->transactions()->latest()->get() as $tx)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ $tx->payment_date->format('d M, Y') }}</td>
                                <td class="py-3 px-4"><span class="font-bold text-slate-950 bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200">{{ $tx->payment_method }}</span></td>
                                <td class="py-3 px-4 text-slate-600 font-mono text-xs">{{ $tx->reference_number ?? '-' }}</td>
                                <td class="py-3 px-4 text-right font-black text-emerald-700">₹{{ number_format($tx->amount, 2) }}</td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('organization.transactions.receipt', $tx) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 font-bold text-xs hover:underline">
                                        View
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-slate-400 text-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 text-base mx-auto mb-2">
                        💳
                    </div>
                    No payments have been recorded yet for this invoice.
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Summary & Payment Recorder (Col 3) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Financial Breakdown Panel -->
            <div class="bg-slate-950 rounded-xl border border-slate-900 p-6 shadow-sm text-white">
                <h3 class="font-extrabold border-b border-slate-800 pb-3 mb-4 text-slate-200 text-xs uppercase tracking-wider">
                    Financial Breakdown
                </h3>

                <div class="space-y-2.5 text-xs text-slate-300">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-white">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>

                    @php
                        $cgstVal = $invoice->effective_cgst;
                        $sgstVal = $invoice->effective_sgst;
                        $cgstRate = (float)($invoice->organization->cgst_percent ?? 0);
                        $sgstRate = (float)($invoice->organization->sgst_percent ?? 0);
                        if ($cgstRate <= 0 && $invoice->subtotal > 0 && $cgstVal > 0) {
                            $cgstRate = round(($cgstVal / $invoice->subtotal) * 100, 2);
                        }
                        if ($sgstRate <= 0 && $invoice->subtotal > 0 && $sgstVal > 0) {
                            $sgstRate = round(($sgstVal / $invoice->subtotal) * 100, 2);
                        }
                    @endphp

                    @if($cgstVal > 0)
                    <div class="flex justify-between">
                        <span>CGST ({{ $cgstRate }}%)</span>
                        <span class="font-semibold text-white">₹{{ number_format($cgstVal, 2) }}</span>
                    </div>
                    @endif

                    @if($sgstVal > 0)
                    <div class="flex justify-between">
                        <span>SGST ({{ $sgstRate }}%)</span>
                        <span class="font-semibold text-white">₹{{ number_format($sgstVal, 2) }}</span>
                    </div>
                    @endif

                    @if($invoice->discount > 0)
                    <div class="flex justify-between text-emerald-400 font-bold">
                        <span>Discount</span>
                        <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-slate-800 mt-4 pt-4 flex justify-between items-baseline mb-5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Grand Total</span>
                    <span class="text-2xl font-black text-amber-400">₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>

                @php
                    // For draft invoices without any actual payment transactions, amount_paid should be treated as 0
                    $actualAmountPaid = $invoice->status === 'Draft' && $invoice->transactions->count() === 0 ? 0 : (float)$invoice->amount_paid;
                    $actualBalanceDue = max(0, (float)$invoice->grand_total - $actualAmountPaid);
                @endphp

                <div class="bg-slate-900/90 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="flex justify-between text-xs text-slate-300">
                        <span>Settled Amount</span>
                        <span class="font-extrabold text-emerald-400">₹{{ number_format($actualAmountPaid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-black pt-2 border-t border-slate-800 text-white">
                        <span>Balance Due</span>
                        <span class="{{ $actualBalanceDue > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                            ₹{{ number_format($actualBalanceDue, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Draft Notice & Finalize Action Card -->
            @if($invoice->status === 'Draft')
            <div class="bg-amber-50 rounded-xl border-2 border-amber-400 shadow-sm p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📝</span>
                    <h4 class="font-black text-amber-950 text-xs uppercase tracking-wider">Draft Invoice (Quotation/Estimate)</h4>
                </div>
                <p class="text-xs text-amber-900 leading-relaxed font-medium">
                    This invoice is currently in <strong>Draft</strong> mode. Warehouse stock has <strong>NOT</strong> been deducted yet.
                </p>
                <div class="pt-2 border-t border-amber-200/80 flex flex-col gap-2">
                    <form action="{{ route('organization.invoices.finalize', $invoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_status" value="Due">
                        <button type="submit" onclick="return confirm('Convert this draft to an official bill? Warehouse inventory stock will be deducted.');" class="w-full py-2 px-3 bg-slate-950 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg transition shadow-xs flex items-center justify-center gap-1.5">
                            <span>Finalize Bill & Deduct Stock &rarr;</span>
                        </button>
                    </form>
                    <form action="{{ route('organization.invoices.finalize', $invoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="target_status" value="Paid">
                        <button type="submit" onclick="return confirm('Mark as fully paid and deduct warehouse stock?');" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-lg transition shadow-xs flex items-center justify-center gap-1.5">
                            <span>✓ Finalize & Mark Fully Paid</span>
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Record Payment Form Card -->
            @if(($actualBalanceDue > 0 || $invoice->status === 'Draft') && $invoice->status !== 'Cancelled' && $invoice->status !== 'Paid')
            <div class="bg-white rounded-xl border-2 border-emerald-500 shadow-sm p-6">
                <div class="flex items-center gap-2 border-b border-emerald-100 pb-3 mb-4">
                    <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">
                        ₹
                    </span>
                    <h3 class="font-extrabold text-emerald-950 text-xs uppercase tracking-wider">
                        Record Payment Settlement
                    </h3>
                </div>

                <form action="{{ route('organization.invoices.payments.store', $invoice) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Amount to Pay (₹) *</label>
                        <input type="number" name="amount" value="{{ $actualBalanceDue }}" min="0.01" max="{{ $actualBalanceDue }}" step="0.01" class="w-full font-black text-slate-950 border border-slate-300 rounded-lg text-sm px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                        @error('amount') <span class="text-xs text-rose-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Payment Method *</label>
                        <select name="payment_method" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-900 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI / QR Code</option>
                            <option value="Card">Debit / Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer / NEFT</option>
                            <option value="Razorpay">Razorpay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Reference / UTR / Txn ID</label>
                        <input type="text" name="reference_number" placeholder="Optional UPI UTR or receipt number" class="w-full border border-slate-300 rounded-lg text-xs px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-950">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="w-full border border-slate-300 rounded-lg text-xs font-medium px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-slate-950" required>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-2">
                        <span>Confirm & Record Payment</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
