@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <a href="{{ route('organization.invoices.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Invoices</a>
        <h1 class="text-2xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('organization.invoices.print', $invoice) }}" target="_blank" class="btn border border-gray-300 text-gray-700 bg-white btn-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / PDF
        </a>
        @if($invoice->status !== 'Cancelled')
            <form action="{{ route('organization.invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this invoice? Stock will be reversed.');">
                @csrf
                <button type="submit" class="btn border border-red-200 text-red-600 hover:bg-red-50 bg-white btn-sm">Cancel Invoice</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div class="panel">
            <div class="flex justify-between items-start border-b pb-4 mb-4">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Billed To</div>
                    <div class="font-bold text-lg text-gray-900"><a href="{{ route('organization.clients.show', $invoice->client_id) }}" class="hover:text-indigo-600">{{ $invoice->client->name }}</a></div>
                    <div class="text-sm text-gray-600">{{ $invoice->client->phone ?? '' }}</div>
                    <div class="text-sm text-gray-600">{{ $invoice->client->email ?? '' }}</div>
                    @if($invoice->client->gst_number)
                        <div class="text-sm text-gray-600 mt-1 font-mono">GST: {{ $invoice->client->gst_number }}</div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-bold mb-1">Status</div>
                    <span class="px-3 py-1 rounded-full text-sm font-bold 
                        {{ $invoice->status == 'Paid' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $invoice->status == 'Draft' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $invoice->status == 'Due' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $invoice->status == 'Overdue' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $invoice->status == 'Partially Paid' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $invoice->status == 'Cancelled' ? 'bg-gray-200 text-gray-600' : '' }}
                    ">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>

            <table class="inv-table w-full mb-4">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Tax</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="font-bold text-gray-900">{{ $item->product_name_snapshot }}</div>
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->tax, 2) }}</td>
                        <td class="text-right font-bold">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($invoice->notes)
            <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-700 border border-gray-100 mt-4">
                <strong>Notes:</strong> {{ $invoice->notes }}
            </div>
            @endif
        </div>

        <!-- Transaction History -->
        <div class="panel">
            <h3 class="font-bold border-b pb-2 mb-4">Transaction History</h3>
            @if($invoice->transactions->count() > 0)
                <table class="inv-table w-full">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->transactions()->latest()->get() as $tx)
                        <tr>
                            <td class="text-sm">{{ $tx->payment_date->format('M d, Y') }}</td>
                            <td class="text-sm font-medium">{{ $tx->payment_method }}</td>
                            <td class="text-sm text-gray-500">{{ $tx->reference_number ?? '-' }}</td>
                            <td class="text-right font-bold text-green-600">₹{{ number_format($tx->amount, 2) }}</td>
                            <td class="text-right">
                                <a href="{{ route('organization.transactions.receipt', $tx) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">Receipt</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm text-center py-4">No payments recorded yet.</p>
            @endif
        </div>
    </div>

    <div class="md:col-span-1 space-y-6">
        <div class="panel bg-gray-50 border-2 border-indigo-50">
            <h3 class="font-bold border-b pb-2 mb-4 text-indigo-900">Payment Summary</h3>
            
            <div class="flex justify-between mb-2 text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-bold text-gray-900">₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            
            <div class="flex justify-between mb-2 text-sm text-gray-600">
                <span>Total Tax</span>
                <span class="font-bold text-gray-900">₹{{ number_format($invoice->tax, 2) }}</span>
            </div>

            @if($invoice->discount > 0)
            <div class="flex justify-between mb-4 text-sm text-green-600 border-b pb-4">
                <span>Discount</span>
                <span class="font-bold">-₹{{ number_format($invoice->discount, 2) }}</span>
            </div>
            @else
            <div class="border-b mb-4 pb-2"></div>
            @endif

            <div class="flex justify-between mb-4 text-xl font-black text-indigo-900">
                <span>Grand Total</span>
                <span>₹{{ number_format($invoice->grand_total, 2) }}</span>
            </div>

            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="flex justify-between mb-2 text-sm text-gray-600">
                    <span>Amount Paid</span>
                    <span class="font-bold text-green-600">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t mt-2">
                    <span>Balance Due</span>
                    <span class="{{ $invoice->amount_due > 0 ? 'text-red-600' : '' }}">₹{{ number_format($invoice->amount_due, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="panel">
            <h3 class="font-bold border-b pb-2 mb-4">Dates & Info</h3>
            <div class="mb-3">
                <div class="text-xs text-gray-500 uppercase">Invoice Date</div>
                <div class="font-medium text-gray-900">{{ $invoice->invoice_date->format('F d, Y') }}</div>
            </div>
            <div class="mb-3">
                <div class="text-xs text-gray-500 uppercase">Due Date</div>
                <div class="font-medium {{ $invoice->due_date && $invoice->due_date < now() && $invoice->amount_due > 0 ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $invoice->due_date ? $invoice->due_date->format('F d, Y') : 'N/A' }}
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500 uppercase">Generated By</div>
                <div class="font-medium text-gray-900">Location: {{ $invoice->location->name ?? 'Unknown' }}</div>
            </div>
        </div>

        @if($invoice->amount_due > 0 && $invoice->status !== 'Cancelled')
        <div class="panel border-2 border-green-500">
            <h3 class="font-bold border-b pb-2 mb-4 text-green-700">Record Payment</h3>
            <form action="{{ route('organization.invoices.payments.store', $invoice) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount to Pay (₹)</label>
                    <input type="number" name="amount" value="{{ $invoice->amount_due }}" min="0.01" max="{{ $invoice->amount_due }}" step="0.01" class="w-full border-gray-300 rounded-lg font-bold" required>
                    @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border-gray-300 rounded-lg" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Razorpay">Razorpay</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reference Number</label>
                    <input type="text" name="reference_number" placeholder="Transaction ID (Optional)" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="w-full border-gray-300 rounded-lg text-sm" required>
                </div>
                <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white w-full justify-center">Record Payment</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
