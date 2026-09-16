@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Finance &amp; HR</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Payroll</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $dateObj->format('F Y') }} Run</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">
                Payroll Management: {{ $dateObj->format('F Y') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
                Manage compensation plans, draft attendance-prorated salaries, and finalize staff payouts.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <form action="{{ route('organization.payroll.generate') }}" method="POST" class="inline m-0">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition" title="Compute prorated gross based on attendance records and draft payslips">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Generate / Recalculate Payroll</span>
                </button>
            </form>
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
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Compact Period Filter Toolbar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-4">
        <form action="{{ route('organization.payroll.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5">
                <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Payroll Period:</span>
                
                <select name="month" id="month" class="border border-slate-300 rounded-lg text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 py-2 px-3 bg-white outline-none cursor-pointer">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>

                <select name="year" id="year" class="border border-slate-300 rounded-lg text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 py-2 px-3 bg-white outline-none cursor-pointer">
                    @for($i = max(2030, (int)date('Y')); $i >= 2024; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>

                <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-2xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>View Payroll Run</span>
                </button>
            </div>

            <div class="text-xs text-slate-600 font-semibold hidden md:block">
                Working Days in Month: <strong class="text-slate-950">{{ $dateObj->daysInMonth }} days</strong>
            </div>
        </form>
    </div>

    <!-- 3. KPI Metrics Summary Strip -->
    @php
        $totalStaff = $employees->count();
        $configuredStructures = $employees->whereNotNull('salaryStructure')->count();
        $draftedPayrolls = 0;
        $paidPayrolls = 0;
        $totalNetPayout = 0;
        $totalGrossPayout = 0;

        foreach($employees as $emp) {
            $p = $emp->payrolls->first();
            if ($p) {
                $draftedPayrolls++;
                if ($p->status === 'Paid') $paidPayrolls++;
                $totalNetPayout += $p->net_salary;
                $totalGrossPayout += $p->earned_gross;
            }
        }
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Net Payout -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Net Salary Liability</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950 font-mono">
                ₹{{ number_format($totalNetPayout, 2) }}
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                Gross: ₹{{ number_format($totalGrossPayout, 2) }}
            </div>
        </div>

        <!-- Metric 2: Run Progress -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Slips Drafted</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">📋</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ $draftedPayrolls }} <span class="text-sm text-slate-500 font-normal">/ {{ $totalStaff }} staff</span>
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $totalStaff > 0 ? round(($draftedPayrolls / $totalStaff) * 100) : 0 }}% of workforce processed
            </div>
        </div>

        <!-- Metric 3: Disbursement Status -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Disbursement</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-850">
                {{ $paidPayrolls }} Paid
            </div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ max(0, $draftedPayrolls - $paidPayrolls) }} pending settlement
            </div>
        </div>

        <!-- Metric 4: Structure Compliance -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Salary Plans</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">⚙️</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">
                {{ $configuredStructures }} <span class="text-sm text-slate-500 font-normal">/ {{ $totalStaff }}</span>
            </div>
            <div class="text-[11px] font-medium mt-1 {{ $configuredStructures < $totalStaff ? 'text-amber-800 font-bold' : 'text-slate-600' }}">
                {{ $totalStaff - $configuredStructures > 0 ? ($totalStaff - $configuredStructures) . ' missing structures' : 'All structures configured' }}
            </div>
        </div>
    </div>

    <!-- 4. Search Filter -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="payroll-search" placeholder="Search employee name, code, status..." onkeyup="filterPayroll()" class="w-full pl-9 pr-3 py-2 text-xs font-medium text-slate-900 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-amber-500 focus:bg-white transition">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end text-xs text-slate-600 font-semibold">
                <span>Total Employees: <strong class="text-slate-950">{{ $employees->count() }}</strong></span>
            </div>
        </div>
    </div>

    <!-- 5. Payroll Master Registry Table -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse" id="payroll-table">
                <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 pl-5 whitespace-nowrap">Staff Member</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Compensation Plan</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Attendance Work Days</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Disbursement Status</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Net Payable</th>
                        <th class="py-3.5 px-4 text-right pr-5 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                    @php 
                        $payroll = $emp->payrolls->first(); 
                        $hasStructure = $emp->salaryStructure !== null;
                    @endphp
                    <tr class="payroll-row hover:bg-slate-50/80 transition-colors">
                        <!-- Employee Info -->
                        <td class="py-3.5 px-4 pl-5 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($emp->first_name ?? 'E', 0, 1) . substr($emp->last_name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-950 text-xs truncate">
                                        {{ $emp->first_name }} {{ $emp->last_name }}
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-medium mt-0.5 whitespace-nowrap">
                                        <span>#{{ $emp->employee_code ?? $emp->id }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $emp->designation ?? 'Staff Member' }}</span>
                                        @if($emp->location)
                                        <span class="mx-1">•</span>
                                        <span class="text-slate-500">{{ $emp->location->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Salary Structure -->
                        <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                            @if($hasStructure)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>₹{{ number_format($emp->salaryStructure->basic_salary, 2) }}/mo</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-50 text-rose-900 border border-rose-300 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>No Salary Plan</span>
                            </span>
                            @endif
                        </td>

                        <!-- Attendance Work Days -->
                        <td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
                            @if($payroll)
                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-900 text-xs font-extrabold font-mono border border-slate-200 shadow-2xs">
                                <span>{{ $payroll->effective_working_days }}</span>
                                <span class="text-slate-400 font-normal">/</span>
                                <span class="text-slate-600">{{ $payroll->days_in_month }} days</span>
                            </div>
                            @else
                            <span class="text-slate-400 text-xs font-medium">--</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
                            @if($payroll)
                                @if($payroll->status === 'Paid')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    <span>Paid</span>
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-900 border border-amber-300 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>Draft</span>
                                </span>
                                @endif
                            @elseif(!$hasStructure)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                                    <span>Plan Required</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                                    <span>Ready to Run</span>
                                </span>
                            @endif
                        </td>

                        <!-- Net Salary -->
                        <td class="py-3.5 px-4 align-middle text-right font-mono whitespace-nowrap">
                            @if($payroll)
                                <span class="text-sm font-black text-slate-950">₹{{ number_format($payroll->net_salary, 2) }}</span>
                            @else
                                <span class="text-slate-400 font-medium text-xs">--</span>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-3.5 px-4 pr-5 align-middle text-right whitespace-nowrap">
                            @if($payroll)
                                @if($payroll->status === 'Paid')
                                <a href="{{ route('organization.payroll.show', $payroll) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-900 font-bold text-xs rounded-lg border border-slate-300 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View Payslip</span>
                                </a>
                                @else
                                <a href="{{ route('organization.payroll.show', $payroll) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                    <span>Settle &amp; Pay</span>
                                    <span>&rarr;</span>
                                </a>
                                @endif
                            @elseif($hasStructure)
                                <a href="{{ route('organization.employees.salary-structure.show', $emp) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit Plan</span>
                                </a>
                            @else
                                <a href="{{ route('organization.employees.salary-structure.show', $emp) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Set Salary Plan</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl mb-3">
                                💰
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No employee records found</h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">
                                Add staff members in the Employee Management module to begin managing payroll.
                            </p>
                            <a href="{{ route('organization.employees.create') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-extrabold rounded-lg shadow-xs transition">
                                + Add New Employee
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function filterPayroll() {
    const input = document.getElementById('payroll-search');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('.payroll-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
@endsection
