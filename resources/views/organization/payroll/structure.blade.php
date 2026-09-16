@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Finance &amp; HR</span>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.payroll.index') }}" class="hover:text-slate-900 transition-colors">Payroll</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $employee->full_name }} Structure</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.payroll.index') }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2 flex-wrap">
                        <span>Salary Plan: {{ $employee->full_name }}</span>
                        @if($structure)
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-300">
                            Defined
                        </span>
                        @else
                        <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-rose-50 text-rose-900 border border-rose-300">
                            Unconfigured
                        </span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        <span>#{{ $employee->employee_code ?? $employee->id }}</span>
                        <span class="mx-1">•</span>
                        <span>{{ $employee->designation ?? 'Staff Member' }}</span>
                        @if($employee->location)
                        <span class="mx-1">•</span>
                        <span>{{ $employee->location->name }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('organization.payroll.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>Cancel</span>
            </a>
            <a href="{{ route('organization.employees.show', $employee) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>Employee Profile</span>
            </a>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-300 rounded-xl p-4 text-xs text-rose-950">
        <div class="font-extrabold flex items-center gap-1.5 mb-1 text-sm">
            <span>⚠️</span> Please fix the following validation issues:
        </div>
        <ul class="list-disc pl-5 space-y-0.5 font-medium">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. Main 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT FORM (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('organization.employees.salary-structure.store', $employee) }}" method="POST" id="salary-structure-form">
                @csrf

                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Base Compensation Setup</h2>
                            <p class="text-[11px] text-slate-600 font-medium mt-0.5">Define fixed salary components, recurring allowances, and deductions.</p>
                        </div>
                        <span class="text-xs font-extrabold text-rose-600">* Required</span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">

                        <!-- Basic Monthly Salary -->
                        <div class="p-4 bg-amber-50/40 border border-amber-300/60 rounded-xl space-y-2">
                            <label for="basic_salary" class="block text-xs font-extrabold text-slate-950 uppercase tracking-wider">
                                Basic Monthly Salary (₹) <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-700 font-extrabold text-sm">
                                    ₹
                                </span>
                                <input type="number" 
                                       step="0.01" 
                                       id="basic_salary" 
                                       name="basic_salary" 
                                       value="{{ old('basic_salary', $structure->basic_salary ?? 0) }}" 
                                       required 
                                       oninput="recalculateTotals()"
                                       class="w-full pl-9 pr-4 py-3 border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-lg font-black font-mono text-slate-950 outline-none transition bg-white shadow-2xs">
                            </div>
                            <p class="text-[11px] text-slate-600 font-medium">
                                Core monthly wage used for daily rate prorations based on recorded attendance.
                            </p>
                        </div>

                        <!-- Allowances & Deductions Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Section: Allowances -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-emerald-700 font-bold text-sm">+</span>
                                        <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Allowances</h3>
                                    </div>
                                    <button type="button" onclick="addRow('allowances-container', 'allowances')" class="text-xs font-extrabold text-amber-800 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 px-2 py-0.5 rounded-md transition shadow-2xs">
                                        + Add Item
                                    </button>
                                </div>

                                <div id="allowances-container" class="space-y-2">
                                    @php $allowances = old('allowances', $structure->allowances ?? []); @endphp
                                    @forelse($allowances as $index => $allowance)
                                    <div class="flex gap-2 items-center row-item bg-slate-50/70 p-2 rounded-lg border border-slate-200">
                                        <input type="text" 
                                               name="allowances[{{ $index }}][name]" 
                                               value="{{ $allowance['name'] }}" 
                                               placeholder="Name (e.g. HRA, Medical)" 
                                               class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-semibold text-slate-900 bg-white outline-none focus:border-amber-500">
                                        <input type="number" 
                                               step="0.01" 
                                               name="allowances[{{ $index }}][amount]" 
                                               value="{{ $allowance['amount'] }}" 
                                               placeholder="Amount (₹)" 
                                               oninput="recalculateTotals()"
                                               class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-bold font-mono text-slate-900 bg-white outline-none focus:border-amber-500 allowance-input">
                                        <button type="button" onclick="this.closest('.row-item').remove(); recalculateTotals();" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-600 rounded hover:bg-rose-50 transition" title="Remove">
                                            ✕
                                        </button>
                                    </div>
                                    @empty
                                    <div class="text-[11px] text-slate-400 italic py-2 text-center empty-notice">
                                        No recurring allowances added. Click "+ Add Item" to define HRA, Transport, etc.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Section: Deductions -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-rose-700 font-bold text-sm">-</span>
                                        <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Fixed Deductions</h3>
                                    </div>
                                    <button type="button" onclick="addRow('deductions-container', 'deductions')" class="text-xs font-extrabold text-slate-800 hover:text-slate-950 bg-slate-100 hover:bg-slate-200 border border-slate-300 px-2 py-0.5 rounded-md transition shadow-2xs">
                                        + Add Item
                                    </button>
                                </div>

                                <div id="deductions-container" class="space-y-2">
                                    @php $deductions = old('deductions', $structure->deductions ?? []); @endphp
                                    @forelse($deductions as $index => $deduction)
                                    <div class="flex gap-2 items-center row-item bg-slate-50/70 p-2 rounded-lg border border-slate-200">
                                        <input type="text" 
                                               name="deductions[{{ $index }}][name]" 
                                               value="{{ $deduction['name'] }}" 
                                               placeholder="Name (e.g. PF, TDS)" 
                                               class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-semibold text-slate-900 bg-white outline-none focus:border-amber-500">
                                        <input type="number" 
                                               step="0.01" 
                                               name="deductions[{{ $index }}][amount]" 
                                               value="{{ $deduction['amount'] }}" 
                                               placeholder="Amount (₹)" 
                                               oninput="recalculateTotals()"
                                               class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-bold font-mono text-rose-800 bg-white outline-none focus:border-amber-500 deduction-input">
                                        <button type="button" onclick="this.closest('.row-item').remove(); recalculateTotals();" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-600 rounded hover:bg-rose-50 transition" title="Remove">
                                            ✕
                                        </button>
                                    </div>
                                    @empty
                                    <div class="text-[11px] text-slate-400 italic py-2 text-center empty-notice">
                                        No recurring deductions added. Click "+ Add Item" to specify PF, ESI, TDS, etc.
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                        </div>

                        <!-- Live Calculation Preview Summary Card -->
                        <div class="p-4 bg-slate-900 text-white rounded-xl space-y-3">
                            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider block">
                                Projected Monthly Pay Summary (Full Month Basis)
                            </span>
                            <div class="grid grid-cols-3 gap-3 text-xs border-t border-slate-800 pt-3">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Gross Salary</span>
                                    <span class="font-black font-mono text-white text-base" id="summary-gross">₹0.00</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Total Deductions</span>
                                    <span class="font-black font-mono text-rose-400 text-base" id="summary-deductions">₹0.00</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-amber-400 block text-[11px] font-bold">Estimated Net Base</span>
                                    <span class="font-black font-mono text-amber-400 text-lg" id="summary-net">₹0.00</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Sticky / Footer Actions -->
                    <div class="bg-slate-50 border-t border-slate-200 p-4 flex items-center justify-between">
                        <a href="{{ route('organization.payroll.index') }}" class="px-4 py-2 border border-slate-300 text-slate-800 bg-white hover:bg-slate-50 rounded-lg font-bold text-xs shadow-2xs transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Salary Structure</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDEBAR (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Policy Guide Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Payroll Proration Rules</h3>
                <ul class="space-y-2.5 text-xs text-slate-700">
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Attendance Integration:</strong> Each month's payroll is prorated based on `Effective Working Days / Days in Month`.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Approved Leaves:</strong> Paid leave days count towards effective working days and are not deducted.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Half-Day Calculation:</strong> Recorded half-days count as 0.5 of a standard day's wage.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span><strong>Manual Overrides:</strong> Discretionary bonuses or overtime bonuses can be applied on the individual payslip before disbursement.</span>
                    </li>
                </ul>
            </div>

            <!-- Employee Meta Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-xs text-slate-700 space-y-2">
                <div class="font-bold text-slate-950 flex items-center gap-1.5">
                    <span>👤</span> Employee Record
                </div>
                <div class="space-y-1 text-[11px] leading-relaxed">
                    <div><strong>Joining Date:</strong> {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') : 'Not specified' }}</div>
                    <div><strong>Contact:</strong> {{ $employee->phone ?: $employee->email ?: 'No contact details' }}</div>
                    <div><strong>Employment Status:</strong> <span class="text-emerald-700 font-bold">{{ $employee->status }}</span></div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
let counter = 2000;

function addRow(containerId, fieldName) {
    const container = document.getElementById(containerId);
    // Hide empty notice if present
    const notice = container.querySelector('.empty-notice');
    if (notice) notice.style.display = 'none';

    counter++;
    const isDeduction = fieldName === 'deductions';
    const amountClass = isDeduction ? 'deduction-input text-rose-800' : 'allowance-input text-slate-900';
    const placeholderText = isDeduction ? 'Name (e.g. PF, TDS)' : 'Name (e.g. HRA, Medical)';

    const html = `
        <div class="flex gap-2 items-center row-item bg-slate-50/70 p-2 rounded-lg border border-slate-200">
            <input type="text" 
                   name="${fieldName}[${counter}][name]" 
                   placeholder="${placeholderText}" 
                   class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-semibold text-slate-900 bg-white outline-none focus:border-amber-500">
            <input type="number" 
                   step="0.01" 
                   name="${fieldName}[${counter}][amount]" 
                   placeholder="Amount (₹)" 
                   oninput="recalculateTotals()"
                   class="w-1/2 border border-slate-300 rounded-md px-2.5 py-1.5 text-xs font-bold font-mono bg-white outline-none focus:border-amber-500 ${amountClass}">
            <button type="button" onclick="this.closest('.row-item').remove(); recalculateTotals();" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-rose-600 rounded hover:bg-rose-50 transition" title="Remove">
                ✕
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    recalculateTotals();
}

function recalculateTotals() {
    const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
    
    let totalAllowances = 0;
    document.querySelectorAll('.allowance-input').forEach(input => {
        totalAllowances += parseFloat(input.value) || 0;
    });

    let totalDeductions = 0;
    document.querySelectorAll('.deduction-input').forEach(input => {
        totalDeductions += parseFloat(input.value) || 0;
    });

    const gross = basic + totalAllowances;
    const net = Math.max(0, gross - totalDeductions);

    document.getElementById('summary-gross').textContent = '₹' + gross.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-deductions').textContent = '₹' + totalDeductions.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-net').textContent = '₹' + net.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Initial calculation on page load
document.addEventListener('DOMContentLoaded', recalculateTotals);
</script>
@endsection
