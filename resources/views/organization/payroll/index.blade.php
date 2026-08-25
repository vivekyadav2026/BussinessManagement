@extends('layouts.sme')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Payroll - {{ $dateObj->format('F Y') }}</h1>
        <form action="{{ route('organization.payroll.generate') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Generate Payroll</button>
        </form>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded shadow mb-6 flex gap-4 items-end">
        <form action="{{ route('organization.payroll.index') }}" method="GET" class="flex gap-4 items-end w-full">
            <div>
                <label class="block text-sm text-gray-600">Month</label>
                <select name="month" class="border rounded px-3 py-2 w-32">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Year</label>
                <select name="year" class="border rounded px-3 py-2 w-32">
                    @for($i=date('Y')-2; $i<=date('Y')+1; $i++)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">View</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Employee</th>
                    <th class="p-3">Structure</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Net Salary</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                    @php 
                        $payroll = $emp->payrolls->first(); 
                    @endphp
                    <tr class="border-b">
                        <td class="p-3 font-medium">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                        <td class="p-3">
                            @if($emp->salaryStructure)
                                <span class="text-green-600 font-medium">Defined</span>
                            @else
                                <span class="text-red-600">Not Defined</span>
                            @endif
                            <a href="{{ route('organization.employees.salary-structure.show', $emp) }}" class="text-xs text-blue-600 ml-2">Edit</a>
                        </td>
                        <td class="p-3">
                            @if($payroll)
                                @if($payroll->status === 'Paid')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Paid</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">Draft</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">Not Generated</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            @if($payroll)
                                ${{ number_format($payroll->net_salary, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            @if($payroll)
                                <a href="{{ route('organization.payroll.show', $payroll) }}" class="text-blue-600 hover:underline">View Payslip</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                @if($employees->isEmpty())
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">No employees found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
