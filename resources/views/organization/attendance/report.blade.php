@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2 no-print" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">HR &amp; Staff</span>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.attendance.index') }}" class="hover:text-slate-900 transition-colors">Attendance</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Monthly Report</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">
                Monthly Attendance Report: {{ $dateObj->format('F Y') }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
                Consolidated staff attendance ledger and effective working day totals for payroll calculation.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end no-print">
            <a href="{{ route('organization.attendance.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Daily Register</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-950 hover:bg-slate-900 text-amber-400 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Print / Save PDF</span>
            </button>
        </div>
    </div>

    <!-- 2. Month & Year Filter Bar -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 sm:p-5 no-print">
        <form method="GET" action="{{ route('organization.attendance.report') }}" class="flex flex-col md:flex-row items-end gap-3.5">
            <div class="w-full md:w-48">
                <label for="month" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Select Month
                </label>
                <select name="month" id="month" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 py-2.5 px-3 bg-white outline-none cursor-pointer">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="w-full md:w-36">
                <label for="year" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                    Select Year
                </label>
                <select name="year" id="year" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 py-2.5 px-3 bg-white outline-none cursor-pointer">
                    @for($i = max(2030, now()->year); $i >= 2024; $i--)
                        <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Generate Report</span>
            </button>
        </form>
    </div>

    <!-- 3. KPI Metrics Summary Strip -->
    @php
        $totalPresent = 0;
        $totalHalfDays = 0;
        $totalLeaves = 0;
        $totalAbsent = 0;
        $totalEffectiveDays = 0;

        foreach($reportData as $row) {
            $s = $row['summary'];
            $totalPresent += $s['present'];
            $totalHalfDays += $s['half_days'];
            $totalLeaves += $s['leaves'];
            $totalAbsent += $s['absent'];
            $totalEffectiveDays += $s['effective_working_days'];
        }
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Active Staff</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ count($reportData) }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Staff included in report</div>
        </div>

        <!-- Metric 2: Total Present Logged -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Present Days</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-850">{{ $totalPresent }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Cumulative full-day attendance</div>
        </div>

        <!-- Metric 3: Total Leaves & Half-Days -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Leaves &amp; Half-Days</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">🌴</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalLeaves + $totalHalfDays }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $totalLeaves }} approved leaves • {{ $totalHalfDays }} half-days
            </div>
        </div>

        <!-- Metric 4: Effective Payroll Days -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Payroll Days</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalEffectiveDays }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Total payable employee days</div>
        </div>
    </div>

    <!-- 4. Report Data Table -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">
                    Staff Attendance Breakdown ({{ $dateObj->format('F Y') }})
                </h3>
                <p class="text-[11px] text-slate-600 font-medium mt-0.5">
                    Click any staff member to inspect their day-by-day attendance calendar.
                </p>
            </div>
            <span class="text-xs font-extrabold px-2.5 py-1 rounded-full bg-slate-200/80 text-slate-800">
                {{ count($reportData) }} Staff
            </span>
        </div>

        <div class="overflow-x-auto print:overflow-visible">
            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                <thead class="bg-slate-50/80 text-[11px] uppercase font-extrabold text-slate-900 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 pl-5">Staff Member</th>
                        <th class="py-3.5 px-4">Employee ID</th>
                        <th class="py-3.5 px-4 text-center">Recorded Days</th>
                        <th class="py-3.5 px-4 text-center text-emerald-800">Present</th>
                        <th class="py-3.5 px-4 text-center text-amber-800">Half-Days</th>
                        <th class="py-3.5 px-4 text-center text-indigo-800">Leaves</th>
                        <th class="py-3.5 px-4 text-center text-rose-800">Absent</th>
                        <th class="py-3.5 px-4 text-right pr-5 bg-amber-500/10 text-slate-950 font-black border-l border-amber-400/20">
                            Effective Working Days
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportData as $row)
                    @php 
                        $emp = $row['employee'];
                        $summary = $row['summary'];
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Employee Column -->
                        <td class="py-3.5 px-4 pl-5 align-middle">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($emp->first_name ?? 'E', 0, 1) . substr($emp->last_name ?? 'M', 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('organization.attendance.show', ['employee' => $emp, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="font-extrabold text-slate-950 hover:text-amber-700 transition-colors block">
                                        {{ $emp->full_name }}
                                    </a>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        {{ $emp->designation ?? 'Staff' }}
                                        @if($emp->location)
                                        • {{ $emp->location->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Code -->
                        <td class="py-3.5 px-4 align-middle text-slate-700 font-mono font-semibold">
                            #{{ $emp->employee_code ?? $emp->id }}
                        </td>

                        <!-- Total Days -->
                        <td class="py-3.5 px-4 align-middle text-center text-slate-800 font-bold">
                            {{ $summary['total_recorded_days'] }}
                        </td>

                        <!-- Present -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-200">
                                {{ $summary['present'] }}
                            </span>
                        </td>

                        <!-- Half Days -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            @if($summary['half_days'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-amber-50 text-amber-900 border border-amber-200">
                                {{ $summary['half_days'] }}
                            </span>
                            @else
                            <span class="text-slate-400 font-medium">-</span>
                            @endif
                        </td>

                        <!-- Leaves -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            @if($summary['leaves'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-900 border border-indigo-200">
                                {{ $summary['leaves'] }}
                            </span>
                            @else
                            <span class="text-slate-400 font-medium">-</span>
                            @endif
                        </td>

                        <!-- Absent -->
                        <td class="py-3.5 px-4 align-middle text-center">
                            @if($summary['absent'] > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-rose-50 text-rose-900 border border-rose-200">
                                {{ $summary['absent'] }}
                            </span>
                            @else
                            <span class="text-slate-400 font-medium">-</span>
                            @endif
                        </td>

                        <!-- Effective Working Days -->
                        <td class="py-3.5 px-4 pr-5 align-middle text-right bg-amber-500/10 font-black text-slate-950 border-l border-amber-400/20 text-sm">
                            {{ $summary['effective_working_days'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 px-4 text-center">
                            <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl mb-3">
                                📊
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">No attendance data found</h3>
                            <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">
                                No active employees found for this location in {{ $dateObj->format('F Y') }}.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; padding: 0 !important; }
}
</style>
@endsection
