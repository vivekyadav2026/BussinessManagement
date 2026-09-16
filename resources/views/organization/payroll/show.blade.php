@extends('layouts.sme')

@section('content')
<style>
@media print {
    body * { visibility: hidden; }
    #printableArea, #printableArea * { visibility: visible; }
    #printableArea { position: absolute; left: 0; top: 0; width: 100%; padding: 24px; }
    .no-print { display: none !important; }
}
</style>

<div class="max-w-5xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Header Controls (no-print) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5 no-print">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Finance &amp; HR</span>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" class="hover:text-slate-900 transition-colors">Payroll</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Payslip: {{ $payroll->employee->full_name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2 flex-wrap">
                        <span>Payslip: {{ $payroll->employee->full_name }}</span>
                        @if($payroll->status === 'Paid')
                        <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-300">
                            ✓ Paid
                        </span>
                        @else
                        <span class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-300">
                            Draft / Pending Payment
                        </span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        Salary statement and disbursement voucher for <strong class="text-slate-950">{{ $dateObj->format('F Y') }}</strong>.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('organization.payroll.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>&larr; Back to Directory</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-950 hover:bg-slate-900 text-amber-400 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Print / Download PDF</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs no-print">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs no-print">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Formal Printable Payslip Card -->
    <div id="printableArea" class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 sm:p-10 space-y-8">
        
        @php
            $orgName = $payroll->organization->name ?? $organization->name ?? optional(auth()->user()->organization)->name ?? 'Business Enterprise';
            $orgAddress = $payroll->organization->address ?? $organization->address ?? optional(auth()->user()->organization)->address ?? 'Business Headquarters';
        @endphp

        <!-- Document Header: Company Info & Payslip Ref -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-slate-200 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-400/30 flex items-center justify-center font-black text-slate-950 text-sm">
                        🏢
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-950 tracking-tight">
                        {{ $orgName }}
                    </h2>
                </div>
                <p class="text-xs text-slate-600 font-medium">
                    {{ $orgAddress }}
                </p>
                <div class="text-[11px] text-slate-500 font-medium">
                    Official Employee Salary Disbursement Slip
                </div>
            </div>

            <div class="sm:text-right space-y-1">
                <div class="inline-block bg-slate-100 text-slate-900 border border-slate-300 px-3 py-1 rounded-lg text-xs font-black tracking-wider uppercase">
                    Payslip • {{ $dateObj->format('F Y') }}
                </div>
                <div class="text-xs text-slate-600 font-mono">
                    Ref: #PAY-{{ $payroll->year }}-{{ str_pad($payroll->month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($payroll->id, 4, '0', STR_PAD_LEFT) }}
                </div>
                <div class="text-[11px] text-slate-500">
                    Generated: {{ $payroll->created_at ? $payroll->created_at->format('M d, Y') : date('M d, Y') }}
                </div>
            </div>
        </div>

        <!-- Employee & Attendance Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-slate-50/80 rounded-xl border border-slate-200/80">
            <div>
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Employee Name</span>
                <span class="text-xs font-extrabold text-slate-950 mt-0.5 block">
                    {{ $payroll->employee->full_name }}
                </span>
            </div>

            <div>
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Employee Code &amp; Role</span>
                <span class="text-xs font-bold text-slate-900 mt-0.5 block">
                    #{{ $payroll->employee->employee_code ?? $payroll->employee->id }} • {{ $payroll->employee->designation ?? 'Staff Member' }}
                </span>
            </div>

            <div>
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Branch / Outlet</span>
                <span class="text-xs font-bold text-slate-900 mt-0.5 block">
                    {{ $payroll->employee->location ? $payroll->employee->location->name : 'Head Office' }}
                </span>
            </div>

            <div>
                <span class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Attendance Days</span>
                <span class="text-xs font-black text-slate-950 font-mono mt-0.5 block">
                    {{ $payroll->effective_working_days }} / {{ $payroll->days_in_month }} Days
                </span>
            </div>
        </div>

        <!-- Financial Breakdown Tables (2 columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Column 1: Earnings -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Earnings &amp; Allowances</h3>
                    <span class="text-[11px] font-extrabold text-emerald-800">Gross Credits</span>
                </div>

                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2 font-medium text-slate-800">Basic Monthly Salary (Base)</td>
                            <td class="py-2 text-right font-mono font-bold text-slate-950">
                                ₹{{ number_format($payroll->basic_salary, 2) }}
                            </td>
                        </tr>
                        @if($payroll->allowances && count($payroll->allowances) > 0)
                            @foreach($payroll->allowances as $allowance)
                            <tr>
                                <td class="py-2 font-medium text-slate-800">{{ $allowance['name'] }}</td>
                                <td class="py-2 text-right font-mono font-bold text-slate-950">
                                    ₹{{ number_format($allowance['amount'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 font-extrabold text-slate-950 bg-slate-50/60">
                            <td class="py-2.5 px-2">Earned Gross (Prorated)</td>
                            <td class="py-2.5 px-2 text-right font-mono text-emerald-850 text-sm">
                                ₹{{ number_format($payroll->earned_gross, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Column 2: Deductions & Adjustments -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Deductions &amp; Adjustments</h3>
                    <span class="text-[11px] font-extrabold text-rose-800">Debits &amp; Adjustments</span>
                </div>

                <table class="w-full text-xs">
                    <tbody class="divide-y divide-slate-100">
                        @if($payroll->deductions && count($payroll->deductions) > 0)
                            @foreach($payroll->deductions as $deduction)
                            <tr>
                                <td class="py-2 font-medium text-slate-800">{{ $deduction['name'] }}</td>
                                <td class="py-2 text-right font-mono font-bold text-rose-850">
                                    -₹{{ number_format($deduction['amount'], 2) }}
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="py-2 text-slate-500 font-medium">Standard Deductions</td>
                                <td class="py-2 text-right font-mono text-slate-500">₹0.00</td>
                            </tr>
                        @endif

                        @if($payroll->manual_adjustment != 0)
                        <tr class="bg-amber-50/40">
                            <td class="py-2 font-bold text-amber-950">
                                Adjustment: {{ $payroll->adjustment_reason ?: 'Manual Correction' }}
                            </td>
                            <td class="py-2 text-right font-mono font-bold {{ $payroll->manual_adjustment > 0 ? 'text-emerald-800' : 'text-rose-850' }}">
                                {{ $payroll->manual_adjustment > 0 ? '+' : '' }}₹{{ number_format($payroll->manual_adjustment, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 font-extrabold text-slate-950 bg-slate-50/60">
                            <td class="py-2.5 px-2">Total Deductions</td>
                            <td class="py-2.5 px-2 text-right font-mono text-rose-850 text-sm">
                                -₹{{ number_format($payroll->total_deductions, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>

        <!-- Net Salary Grand Total Banner -->
        <div class="p-5 bg-slate-900 text-white rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Net Take-Home Salary</span>
                <span class="text-[11px] text-slate-300">Total payable after attendance proration, deductions &amp; adjustments</span>
            </div>
            <div class="sm:text-right">
                <div class="text-2xl sm:text-3xl font-black text-amber-400 font-mono">
                    ₹{{ number_format($payroll->net_salary, 2) }}
                </div>
                @if($payroll->status === 'Paid')
                <div class="text-[11px] text-emerald-400 font-bold mt-0.5">
                    ✓ Disbursed on {{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : 'Recorded Date' }} ({{ $payroll->payment_method }})
                </div>
                @else
                <div class="text-[11px] text-amber-400 font-medium mt-0.5">
                    ⏳ Settlement Pending
                </div>
                @endif
            </div>
        </div>

        <!-- Printable Footer Signatures -->
        <div class="grid grid-cols-2 gap-8 pt-10 border-t border-slate-200 text-xs">
            <div>
                <div class="border-t border-slate-400 w-48 pt-1 text-slate-700 font-bold">
                    Employee Signature
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Date of Receipt</div>
            </div>
            <div class="text-right">
                <div class="border-t border-slate-400 w-48 ml-auto pt-1 text-slate-700 font-bold">
                    Authorized Signatory
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">{{ $orgName }}</div>
            </div>
        </div>

    </div>

    <!-- 3. Operational Action Panels (no-print) when status is not Paid -->
    @if($payroll->status !== 'Paid')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 no-print">
        
        <!-- Panel 1: Manual Adjustment -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Salary Adjustment</h3>
                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                    Apply discretionary bonuses or special one-time deductions before settlement.
                </p>
            </div>

            <form action="{{ route('organization.payroll.updateAdjustment', $payroll) }}" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label for="manual_adjustment" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">
                        Adjustment Amount (₹) <span class="text-slate-500 font-normal">(+ for Bonus, - for Penalty)</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           id="manual_adjustment" 
                           name="manual_adjustment" 
                           value="{{ old('manual_adjustment', $payroll->manual_adjustment) }}" 
                           required 
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold font-mono text-slate-950 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs transition">
                </div>

                <div>
                    <label for="adjustment_reason" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">
                        Reason for Adjustment
                    </label>
                    <input type="text" 
                           id="adjustment_reason" 
                           name="adjustment_reason" 
                           value="{{ old('adjustment_reason', $payroll->adjustment_reason) }}" 
                           placeholder="e.g. Sales performance incentive, Overtime reward" 
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs font-semibold text-slate-950 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs transition">
                </div>

                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                    Save Adjustment
                </button>
            </form>
        </div>

        <!-- Panel 2: Finalize Payment -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Finalize &amp; Settle Payment</h3>
                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                    Confirm disbursement to lock the payslip and update payment audit logs.
                </p>
            </div>

            <form action="{{ route('organization.payroll.markPaid', $payroll) }}" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label for="payment_method" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">
                        Payment Method
                    </label>
                    <select name="payment_method" id="payment_method" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-950 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs bg-white cursor-pointer">
                        <option value="Bank Transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                        <option value="UPI">UPI / Digital Payout</option>
                        <option value="Cash">Cash Voucher</option>
                        <option value="Cheque">Company Cheque</option>
                    </select>
                </div>

                <div>
                    <label for="payment_date" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">
                        Disbursement Date
                    </label>
                    <input type="date" 
                           id="payment_date" 
                           name="payment_date" 
                           value="{{ date('Y-m-d') }}" 
                           required 
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-xs font-bold text-slate-950 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs transition cursor-pointer">
                </div>

                <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Mark as Paid &amp; Disbursed</span>
                </button>
            </form>
        </div>

    </div>
    @endif

</div>
@endsection
