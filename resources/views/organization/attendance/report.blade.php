@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <a href="{{ route('organization.attendance.index') }}" class="text-xs text-slate-500 hover:text-indigo-600 mb-1 inline-block">&larr; Back to Daily Attendance</a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Monthly Attendance Report</h1>
        <p class="text-xs text-slate-500 mt-0.5">Comprehensive staff attendance summary for Payroll preparation.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print / Save PDF</span>
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-xs mb-6 no-print">
    <form method="GET" action="{{ route('organization.attendance.report') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-44">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Select Month</label>
            <select name="month" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="w-full md:w-36">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Select Year</label>
            <select name="year" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 py-2.5">
                @for($i = max(2030, now()->year); $i >= 2024; $i--)
                    <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>

        </div>
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex-1 md:flex-none justify-center">Generate Report</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100">
        <h3 class="font-bold text-slate-900 text-base">{{ $dateObj->format('F Y') }} Staff Summary</h3>
        <span class="text-xs text-slate-400 font-medium">Total Staff: {{ count($reportData) }}</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Employee Name</th>
                    <th class="py-3.5 px-4">Emp Code</th>
                    <th class="py-3.5 px-4 text-center">Recorded Days</th>
                    <th class="py-3.5 px-4 text-center text-emerald-700">Present</th>
                    <th class="py-3.5 px-4 text-center text-amber-700">Half Days</th>
                    <th class="py-3.5 px-4 text-center text-indigo-700">Leaves</th>
                    <th class="py-3.5 px-4 text-center text-rose-700">Absent</th>
                    <th class="py-3.5 px-4 text-right bg-indigo-50 text-indigo-900 font-bold border-l border-indigo-100">Effective Working Days</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-medium">
                @forelse($reportData as $row)
                @php 
                    $emp = $row['employee'];
                    $summary = $row['summary'];
                @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-3.5 px-4 font-bold text-slate-900">
                        <a href="{{ route('organization.attendance.show', ['employee' => $emp, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">{{ $emp->full_name }}</a>
                    </td>
                    <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $emp->employee_code ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-center text-slate-700 font-semibold">{{ $summary['total_recorded_days'] }}</td>
                    <td class="py-3.5 px-4 text-center text-emerald-700 font-bold text-sm">{{ $summary['present'] }}</td>
                    <td class="py-3.5 px-4 text-center text-amber-700 font-bold text-sm">{{ $summary['half_days'] }}</td>
                    <td class="py-3.5 px-4 text-center text-indigo-700 font-bold text-sm">{{ $summary['leaves'] }}</td>
                    <td class="py-3.5 px-4 text-center text-rose-700 font-bold text-sm">{{ $summary['absent'] }}</td>
                    <td class="py-3.5 px-4 text-right bg-indigo-50/50 font-black text-indigo-700 border-l border-indigo-100 text-base">
                        {{ $summary['effective_working_days'] }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-10 text-center text-slate-400 font-medium">No active employees found in this branch location.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; padding: 0 !important; }
    }
</style>
@endsection
