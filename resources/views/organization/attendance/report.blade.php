@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <a href="{{ route('organization.attendance.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Daily Attendance</a>
        <h1 class="text-2xl font-bold text-gray-900">Monthly Attendance Report</h1>
        <p class="text-gray-500 mt-1">Export-ready view for Payroll preparation.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 btn-sm">Print / PDF</button>
    </div>
</div>

<div class="panel mb-6 bg-gray-50 no-print">
    <form method="GET" action="{{ route('organization.attendance.report') }}" class="flex gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Month</label>
            <select name="month" class="border-gray-300 rounded-lg text-sm w-40">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Year</label>
            <select name="year" class="border-gray-300 rounded-lg text-sm w-32">
                @for($i = now()->year; $i >= now()->year - 5; $i--)
                    <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-gold py-2">Generate Report</button>
    </form>
</div>

<div class="panel">
    <h3 class="font-bold text-lg mb-4">{{ $dateObj->format('F Y') }} Summary</h3>
    
    <div class="overflow-x-auto">
        <table class="inv-table w-full whitespace-nowrap">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3">Employee Name</th>
                    <th class="p-3">Emp Code</th>
                    <th class="p-3 text-center">Recorded Days</th>
                    <th class="p-3 text-center text-green-700">Present</th>
                    <th class="p-3 text-center text-orange-700">Half Days</th>
                    <th class="p-3 text-center text-blue-700">Leaves</th>
                    <th class="p-3 text-center text-red-700">Absent</th>
                    <th class="p-3 text-right bg-indigo-50 text-indigo-900 font-bold border-l border-indigo-100">Effective Working Days</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData as $row)
                @php 
                    $emp = $row['employee'];
                    $summary = $row['summary'];
                @endphp
                <tr class="hover:bg-gray-50 border-b border-gray-100">
                    <td class="p-3 font-bold text-gray-900"><a href="{{ route('organization.attendance.show', ['employee' => $emp, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="hover:text-indigo-600">{{ $emp->full_name }}</a></td>
                    <td class="p-3 text-gray-500">{{ $emp->employee_code ?? '-' }}</td>
                    <td class="p-3 text-center">{{ $summary['total_recorded_days'] }}</td>
                    <td class="p-3 text-center font-medium">{{ $summary['present'] }}</td>
                    <td class="p-3 text-center font-medium">{{ $summary['half_days'] }}</td>
                    <td class="p-3 text-center font-medium">{{ $summary['leaves'] }}</td>
                    <td class="p-3 text-center font-medium">{{ $summary['absent'] }}</td>
                    <td class="p-3 text-right bg-indigo-50/50 font-black text-indigo-700 border-l border-indigo-100 text-lg">
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
