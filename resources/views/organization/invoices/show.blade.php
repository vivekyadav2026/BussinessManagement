@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 gap-4">
    <div>
        <a href="{{ route('organization.invoices.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900 transition-colors flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Invoices
        </a>
        <h1 class="text-2xl font-black text-gray-900">Invoice: {{ $invoice->invoice_number }}</h1>
    </div>
    <div class="flex items-center gap-2 w-full sm:w-auto">
        <a href="{{ route('organization.invoices.print', $invoice) }}" target="_blank" class="btn border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 btn-sm flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / PDF
        </a>
        @if($invoice->status !== 'Cancelled')
            <form action="{{ route('organization.invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this invoice? Stock will be reversed.');" class="m-0">
                @csrf
                <button type="submit" class="btn border border-red-200 text-red-600 hover:bg-red-50 bg-white btn-sm shadow-sm">Cancel Invoice</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Invoice Paper Sheet -->
    <div class="lg:col-span-2 space-y-6">
        <div class="panel bg-white p-6 sm:p-8 shadow-sm border border-gray-100 rounded-xl relative overflow-hidden">
            <!-- Decorative Stripe at the top -->
            <div class="absolute top-0 left-0 right-0 h-1.5 
                {{ $invoice->status == 'Paid' ? 'bg-green-500' : '' }}
                {{ $invoice->status == 'Draft' ? 'bg-gray-400' : '' }}
                {{ $invoice->status == 'Due' ? 'bg-blue-500' : '' }}
                {{ $invoice->status == 'Overdue' ? 'bg-rose-500 animate-pulse' : '' }}
                {{ $invoice->status == 'Partially Paid' ? 'bg-orange-500' : '' }}
                {{ $invoice->status == 'Cancelled' ? 'bg-gray-300' : '' }}
            "></div>

            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-gray-100 pb-6 mb-6">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Billed To</span>
                    @if($invoice->client)
                        <div class="font-bold text-xl text-gray-900 mt-1">
                            <a href="{{ route('organization.clients.show', $invoice->client_id) }}" class="hover:text-indigo-600 transition-colors">{{ $invoice->client->name }}</a>
                        </div>
                        <div class="text-sm text-gray-500 mt-1 flex flex-col gap-0.5">
                            @if($invoice->client->phone) <span>Phone: {{ $invoice->client->phone }}</span> @endif
                            @if($invoice->client->email) <span>Email: {{ $invoice->client->email }}</span> @endif
                            @if($invoice->client->gst_number) <span class="font-mono text-xs text-gray-600 mt-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 w-max">GST: {{ $invoice->client->gst_number }}</span> @endif
                        </div>
                    @else
                        <div class="font-bold text-lg text-gray-400 italic mt-1">Walk-in Client / General Customer</div>
                    @endif
                </div>
                
                <div class="text-left sm:text-right">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status & Details</span>
                    <div class="mt-2">
                        <span class="px-3 py-1 rounded-full text-xs font-black inline-block
                            {{ $invoice->status == 'Paid' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                            {{ $invoice->status == 'Draft' ? 'bg-gray-50 text-gray-600 border border-gray-200' : '' }}
                            {{ $invoice->status == 'Due' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                            {{ $invoice->status == 'Overdue' ? 'bg-rose-50 text-rose-700 border border-rose-200 animate-pulse' : '' }}
                            {{ $invoice->status == 'Partially Paid' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                            {{ $invoice->status == 'Cancelled' ? 'bg-gray-100 text-gray-500 border border-gray-300' : '' }}
                        ">
                            {{ $invoice->status }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-400 font-mono mt-3">
                        Generated by location:<br>
                        <span class="font-bold text-gray-700">{{ $invoice->location->name ?? 'Unknown Location' }}</span>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="inv-table w-full mb-6">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b border-gray-100">
                            <th class="py-3 px-4 text-left font-bold text-xs uppercase tracking-wider">Item Description</th>
                            <th class="py-3 px-4 text-right font-bold text-xs uppercase tracking-wider">Qty</th>
                            <th class="py-3 px-4 text-right font-bold text-xs uppercase tracking-wider">Unit Price</th>
                            <th class="py-3 px-4 text-right font-bold text-xs uppercase tracking-wider">Tax</th>
                            <th class="py-3 px-4 text-right font-bold text-xs uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-900 text-sm">{{ $item->product_name_snapshot }}</div>
                                @if($item->product && $item->product->sku)
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right text-sm text-gray-700 font-medium">{{ $item->quantity }}</td>
                            <td class="py-4 px-4 text-right text-sm text-gray-600 font-medium">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-4 px-4 text-right text-sm text-gray-500">₹{{ number_format($item->tax, 2) }}</td>
                            <td class="py-4 px-4 text-right text-sm font-bold text-gray-900">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invoice->notes)
            <div class="bg-slate-50 p-4 rounded-xl text-xs text-gray-600 border border-slate-100/50 mt-6 leading-relaxed">
                <span class="font-bold text-gray-700 block mb-1">Invoice Notes:</span>
                {{ $invoice->notes }}
            </div>
            @endif
        </div>

        <!-- Transaction History -->
        <div class="panel bg-white p-6 shadow-sm border border-gray-100 rounded-xl">
            <h3 class="font-black text-gray-900 text-base border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
                <span>Transaction History</span>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">{{ $invoice->transactions->count() }} payments</span>
            </h3>
            
            @if($invoice->transactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="inv-table w-full">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 border-b border-gray-100">
                                <th class="py-2.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Date</th>
                                <th class="py-2.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Method</th>
                                <th class="py-2.5 px-4 text-left font-bold text-xs uppercase tracking-wider">Reference</th>
                                <th class="py-2.5 px-4 text-right font-bold text-xs uppercase tracking-wider">Amount</th>
                                <th class="py-2.5 px-4 text-right font-bold text-xs uppercase tracking-wider">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($invoice->transactions()->latest()->get() as $tx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-4 text-gray-700">{{ $tx->payment_date->format('M d, Y') }}</td>
                                <td class="py-3.5 px-4"><span class="font-semibold text-gray-900">{{ $tx->payment_method }}</span></td>
                                <td class="py-3.5 px-4 text-gray-500 font-mono text-xs">{{ $tx->reference_number ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-right font-bold text-green-600">₹{{ number_format($tx->amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-right">
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
                <div class="text-center py-8 text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    No payments recorded yet for this invoice.
                </div>
            @endif
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Summary Cards -->
        <div class="panel bg-gradient-to-br from-slate-900 to-indigo-950 p-6 shadow-sm border border-slate-800 rounded-xl text-white">
            <h3 class="font-bold border-b border-white/10 pb-3 mb-4 text-white/90 text-sm uppercase tracking-wider">Payment Summary</h3>
            
            <div class="space-y-2 text-sm text-white/70">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-semibold text-white">₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Tax</span>
                    <span class="font-semibold text-white">₹{{ number_format($invoice->tax, 2) }}</span>
                </div>
                @if($invoice->discount > 0)
                <div class="flex justify-between text-green-400 font-medium">
                    <span>Discount</span>
                    <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                </div>
                @endif
            </div>

            <div class="border-t border-white/10 mt-4 pt-4 flex justify-between items-baseline mb-6">
                <span class="text-sm text-white/80">Grand Total</span>
                <span class="text-2xl font-black text-white">₹{{ number_format($invoice->grand_total, 2) }}</span>
            </div>

            <div class="bg-white/10 p-4 rounded-lg border border-white/10 space-y-2">
                <div class="flex justify-between text-xs text-white/75">
                    <span>Amount Paid</span>
                    <span class="font-bold text-green-400">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-white/10">
                    <span>Balance Due</span>
                    <span class="{{ $invoice->amount_due > 0 ? 'text-rose-400' : 'text-white' }}">₹{{ number_format($invoice->amount_due, 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Metadata -->
        <div class="panel bg-white p-5 shadow-sm border border-gray-100 rounded-xl">
            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-2 mb-4 text-sm uppercase tracking-wider">Dates & Metadata</h3>
            <div class="space-y-3.5">
                <div>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Invoice Date</span>
                    <span class="font-semibold text-sm text-gray-800">{{ $invoice->invoice_date->format('F d, Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wider block font-bold">Due Date</span>
                    <span class="font-semibold text-sm {{ $invoice->due_date && $invoice->due_date < now() && $invoice->amount_due > 0 ? 'text-rose-600' : 'text-gray-800' }}">
                        {{ $invoice->due_date ? $invoice->due_date->format('F d, Y') : 'N/A' }}
                    </span>
                </div>
                <div class="pt-2.5 border-t border-gray-50 flex justify-between items-center text-xs text-gray-500">
                    <span>Created:</span>
                    <span class="font-medium">{{ $invoice->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Record Payment Form -->
        @if($invoice->amount_due > 0 && $invoice->status !== 'Cancelled')
        <div class="panel border-2 border-green-500 bg-white p-5 rounded-xl shadow-md">
            <h3 class="font-bold text-green-700 border-b border-green-100 pb-2 mb-4 text-sm uppercase tracking-wider">Record Payment</h3>
            <form action="{{ route('organization.invoices.payments.store', $invoice) }}" method="POST" class="m-0 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount to Pay (₹)</label>
                    <input type="number" name="amount" value="{{ $invoice->amount_due }}" min="0.01" max="{{ $invoice->amount_due }}" step="0.01" class="w-full font-bold text-gray-800 border-gray-300 rounded-lg text-sm" required>
                    @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border-gray-300 rounded-lg text-sm font-medium" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Razorpay">Razorpay</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reference Number</label>
                    <input type="text" name="reference_number" placeholder="UPI Txn ID or Card slip info" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="w-full border-gray-300 rounded-lg text-sm" required>
                </div>
                <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white w-full justify-center py-2.5 font-bold shadow-sm rounded-lg transition-colors">Record Payment</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
