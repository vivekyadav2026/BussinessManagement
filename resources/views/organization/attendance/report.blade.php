@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <a href="{{ route('organization.attendance.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Daily Attendance</a>
        <h1 class="text-2xl font-bold text-gray-900">Monthly Attendance Report</h1>
        <p class="text-gray-500 mt-1">Export-ready view for Payroll preparation.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn btn-ghost text-xs">Print / PDF</button>
    </div>
</div>

<div class="panel mb-6 p-6 shadow-sm no-print">
    <form method="GET" action="{{ route('organization.attendance.report') }}" class="flex flex-col md:flex-row gap-4 items-end">
        <div class="w-full md:w-40">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Month</label>
            <select name="month" class="w-full border-gray-300 rounded-lg text-sm">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="w-full md:w-32">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year</label>
            <select name="year" class="w-full border-gray-300 rounded-lg text-sm">
                @for($i = now()->year; $i >= now()->year - 5; $i--)
                    <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-gold py-2.5 px-6 flex-1 md:flex-none justify-center w-full md:w-auto">Generate Report</button>
    </form>
</div>

<div class="panel p-6 shadow-sm">
    <h3 class="font-bold text-lg mb-4">{{ $dateObj->format('F Y') }} Summary</h3>
    
    <div class="overflow-x-auto">
        <table class="inv-table w-full whitespace-nowrap">
            <thead>
                <tr>
                    <th class="text-left">Employee Name</th>
                    <th class="text-left">Emp Code</th>
                    <th class="text-center">Recorded Days</th>
                    <th class="text-center text-green-700">Present</th>
                    <th class="text-center text-orange-700">Half Days</th>
                    <th class="text-center text-blue-700">Leaves</th>
                    <th class="text-center text-red-700">Absent</th>
                    <th class="text-right bg-indigo-50 text-indigo-900 font-bold border-l border-indigo-100">Effective Working Days</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                @php 
                    $emp = $row['employee'];
                    $summary = $row['summary'];
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="font-bold text-gray-900"><a href="{{ route('organization.attendance.show', ['employee' => $emp, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="hover:text-indigo-600">{{ $emp->full_name }}</a></td>
                    <td class="text-gray-500 font-mono">{{ $emp->employee_code ?? '-' }}</td>
                    <td class="text-center">{{ $summary['total_recorded_days'] }}</td>
                    <td class="text-center font-medium">{{ $summary['present'] }}</td>
                    <td class="text-center font-medium">{{ $summary['half_days'] }}</td>
                    <td class="text-center font-medium">{{ $summary['leaves'] }}</td>
                    <td class="text-center font-medium">{{ $summary['absent'] }}</td>
                    <td class="text-right bg-indigo-50/50 font-black text-indigo-700 border-l border-indigo-100 text-lg">
                        {{ $summary['effective_working_days'] }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-8 text-gray-500">No employees found in this location.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .panel { box-shadow: none; border: 1px solid #e5e7eb; }
        body { background: white; padding: 0; }
        .dash-head { margin-bottom: 20px; }
    }
</style>
@endsection
