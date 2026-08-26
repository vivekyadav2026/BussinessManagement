@extends('layouts.sme')

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
    .no-print { display: none !important; }
}
</style>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 no-print">
        <a href="{{ route('organization.payroll.index') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Payroll</a>
        <button onclick="window.print()" class="btn btn-ghost text-xs">Print / PDF</button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium no-print">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium no-print">
            {{ session('error') }}
        </div>
    @endif

    <div id="printableArea" class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-end border-b border-gray-100 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->organization->name }}</h1>
                <p class="text-sm text-gray-500 font-mono">Payslip for {{ $dateObj->format('F Y') }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-xl text-gray-900">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                <p class="text-xs text-gray-400 font-mono">Employee ID: #{{ str_pad($payroll->employee->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <div>
                <h3 class="font-bold text-gray-700 border-b border-gray-100 pb-1 mb-2">Attendance Summary</h3>
                <table class="w-full text-sm">
                    <tr><td class="py-1 text-gray-600">Total Days in Month</td><td class="text-right font-mono">{{ $payroll->days_in_month }}</td></tr>
                    <tr><td class="py-1 text-gray-600">Effective Working Days</td><td class="text-right font-mono">{{ $payroll->effective_working_days }}</td></tr>
                </table>
            </div>
            <div>
                <h3 class="font-bold text-gray-700 border-b border-gray-100 pb-1 mb-2">Payment Details</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-1 text-gray-600">Status</td>
                        <td class="text-right">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $payroll->status === 'Paid' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-yellow-50 text-yellow-700 border border-yellow-100' }}">
                                {{ $payroll->status }}
                            </span>
                        </td>
                    </tr>
                    @if($payroll->status === 'Paid')
                    <tr><td class="py-1 text-gray-600">Payment Date</td><td class="text-right font-mono">{{ $payroll->payment_date->format('M d, Y') }}</td></tr>
                    <tr><td class="py-1 text-gray-600">Method</td><td class="text-right">{{ $payroll->payment_method }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Earnings -->
            <div>
                <h3 class="font-bold text-gray-700 border-b border-gray-100 pb-1 mb-2">Earnings</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-800">Basic Salary (Snapshot)</td>
                        <td class="text-right font-mono">₹{{ number_format($payroll->basic_salary, 2) }}</td>
                    </tr>
                    @if($payroll->allowances)
                        @foreach($payroll->allowances as $allowance)
                        <tr>
                            <td class="py-2 text-gray-800">{{ $allowance['name'] }}</td>
                            <td class="text-right font-mono">₹{{ number_format($allowance['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    @endif
                    <tr class="font-bold border-t border-gray-100">
                        <td class="py-2">Prorated Earned Gross</td>
                        <td class="text-right font-mono">₹{{ number_format($payroll->earned_gross, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Deductions -->
            <div>
                <h3 class="font-bold text-gray-700 border-b border-gray-100 pb-1 mb-2">Deductions</h3>
                <table class="w-full text-sm">
                    @if($payroll->deductions)
                        @foreach($payroll->deductions as $deduction)
                        <tr>
                            <td class="py-2 text-gray-800">{{ $deduction['name'] }}</td>
                            <td class="text-right font-mono">-₹{{ number_format($deduction['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    @endif
                    @if($payroll->manual_adjustment != 0)
                        <tr>
                            <td class="py-2 text-gray-800">Adjustment ({{ $payroll->adjustment_reason }})</td>
                            <td class="text-right font-mono">{{ $payroll->manual_adjustment > 0 ? '+' : '' }}₹{{ number_format($payroll->manual_adjustment, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="font-bold border-t border-gray-100">
                        <td class="py-2">Total Deductions (Fixed)</td>
                        <td class="text-right font-mono">-₹{{ number_format($payroll->total_deductions, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-100 p-4 rounded-xl text-right">
            <span class="text-gray-600 text-lg mr-4 font-bold">Net Salary:</span>
            <span class="text-3xl font-black text-indigo-700 font-mono">₹{{ number_format($payroll->net_salary, 2) }}</span>
        </div>
    </div>

    @if($payroll->status === 'Pending')
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8 no-print">
        <!-- Adjustment Form -->
        <div class="panel p-6 shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-gray-900 border-b border-gray-100 pb-2">Manual Adjustment</h3>
            <form action="{{ route('organization.payroll.updateAdjustment', $payroll) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Amount (+ for Bonus, - for Penalty)</label>
                    <input type="number" step="0.01" name="manual_adjustment" value="{{ $payroll->manual_adjustment }}" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Reason</label>
                    <input type="text" name="adjustment_reason" value="{{ $payroll->adjustment_reason }}" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <button type="submit" class="btn btn-gold py-2.5 px-6 w-full justify-center">Save Adjustment</button>
            </form>
        </div>

        <!-- Mark Paid Form -->
        <div class="panel p-6 shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-gray-900 border-b border-gray-100 pb-2">Finalize Payment</h3>
            <form action="{{ route('organization.payroll.markPaid', $payroll) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded px-3 py-2 text-sm">
                        <option>Bank Transfer</option>
                        <option>Cash</option>
                        <option>Cheque</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>
                <button type="submit" class="btn btn-gold bg-green-600 hover:bg-green-700 text-white border-none py-2.5 px-6 w-full justify-center">Mark as Paid</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
