@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payroll Directory</h1>
        <p class="text-gray-500 mt-1">Manage monthly salary slips, structures, and payouts for {{ $dateObj->format('F Y') }}</p>
    </div>
    <form action="{{ route('organization.payroll.generate') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <button type="submit" class="btn btn-gold py-2.5 px-6 shadow-sm">Generate Payroll</button>
    </form>
</div>

<!-- Filter -->
<div class="panel mb-6 p-6 shadow-sm">
    <form action="{{ route('organization.payroll.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end w-full">
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Month</label>
            <select name="month" class="w-full border-gray-300 rounded-lg text-sm">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Year</label>
            <select name="year" class="w-full border-gray-300 rounded-lg text-sm">
                @for($i=date('Y')-2; $i<=date('Y')+1; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-ghost py-2.5 px-6 flex-1 md:flex-none justify-center w-full md:w-auto">View</button>
    </form>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<!-- Table -->
<div class="panel p-6 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Employee</th>
                    <th class="text-left">Structure</th>
                    <th class="text-left">Status</th>
                    <th class="text-right">Net Salary</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    @php 
                        $payroll = $emp->payrolls->first(); 
                    @endphp
                    <tr>
                        <td class="font-bold text-gray-900">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                        <td>
                            @if($emp->salaryStructure)
                                <span class="bg-green-50 text-green-700 border border-green-100 px-2 py-0.5 rounded text-xs font-bold">Defined</span>
                            @else
                                <span class="bg-red-50 text-red-700 border border-red-100 px-2 py-0.5 rounded text-xs font-bold">Not Defined</span>
                            @endif
                            <a href="{{ route('organization.employees.salary-structure.show', $emp) }}" class="text-xs text-indigo-600 hover:underline ml-2 font-medium">Edit Structure</a>
                        </td>
                        <td>
                            @if($payroll)
                                @if($payroll->status === 'Paid')
                                    <span class="bg-green-50 text-green-700 border border-green-100 px-2.5 py-1 rounded text-xs font-bold">Paid</span>
                                @else
                                    <span class="bg-yellow-50 text-yellow-700 border border-yellow-100 px-2.5 py-1 rounded text-xs font-bold">Draft</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-right font-bold text-gray-900">
                            @if($payroll)
                                ₹{{ number_format($payroll->net_salary, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($payroll)
                                <a href="{{ route('organization.payroll.show', $payroll) }}" class="btn btn-ghost py-1 px-3 text-xs">View Payslip</a>
                            @endif
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
