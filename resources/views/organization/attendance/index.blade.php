@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daily Attendance</h1>
        <p class="text-gray-500 mt-1">Mark attendance for {{ $dateObj->format('l, F j, Y') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('organization.attendance.report') }}" class="btn btn-ghost text-xs">Monthly Report</a>
    </div>
</div>

<div class="panel mb-6 p-6 shadow-sm">
    <form method="GET" action="{{ route('organization.attendance.index') }}" class="flex gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Date</label>
            <input type="date" name="date" value="{{ $date }}" class="border-gray-300 rounded-lg text-sm" onchange="this.form.submit()">
        </div>
        @if($date !== now()->toDateString())
        <a href="{{ route('organization.attendance.index') }}" class="text-indigo-600 hover:underline text-sm pb-2.5">Jump to Today</a>
        @endif
    </form>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('organization.attendance.storeBulk') }}" method="POST">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    
    <div class="panel p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="inv-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Employee</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Check In</th>
                        <th class="text-left">Check Out</th>
                        <th class="text-right">History</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    @php 
                        $att = $emp->attendances->first(); 
                        $status = $att->status ?? 'Present'; // Default to present for quick entry
                    @endphp
                    <tr>
                        <td>
                            <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $emp->employee_code ?? 'No Code' }} • {{ $emp->designation ?? 'Staff' }}</div>
                        </td>
                        <td>
                            <select name="attendance[{{ $emp->id }}][status]" class="border-gray-300 rounded text-sm w-32 focus:ring-indigo-500 focus:border-indigo-500 {{ 
                                $status == 'Present' ? 'text-green-700 font-bold bg-green-50' : 
                                ($status == 'Absent' ? 'text-red-700 font-bold bg-red-50' : 
                                ($status == 'Half Day' ? 'text-orange-700 font-bold bg-orange-50' : 'text-blue-700 font-bold bg-blue-50'))
                            }}" onchange="this.className = this.options[this.selectedIndex].className + ' border-gray-300 rounded text-sm w-32'">
                                <option value="Present" class="text-green-700 font-bold bg-green-50" {{ $status == 'Present' ? 'selected' : '' }}>Present</option>
                                <option value="Absent" class="text-red-700 font-bold bg-red-50" {{ $status == 'Absent' ? 'selected' : '' }}>Absent</option>
                                <option value="Half Day" class="text-orange-700 font-bold bg-orange-50" {{ $status == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                <option value="Leave" class="text-blue-700 font-bold bg-blue-50" {{ $status == 'Leave' ? 'selected' : '' }}>Leave</option>
                            </select>
                        </td>
                        <td>
                            <input type="time" name="attendance[{{ $emp->id }}][check_in]" value="{{ $att->check_in ?? '' }}" class="border-gray-300 rounded text-sm text-gray-600">
                        </td>
                        <td>
                            <input type="time" name="attendance[{{ $emp->id }}][check_out]" value="{{ $att->check_out ?? '' }}" class="border-gray-300 rounded text-sm text-gray-600">
                        </td>
                        <td class="text-right">
                            <a href="{{ route('organization.attendance.show', $emp) }}" class="btn btn-ghost py-1 px-3 text-xs">Calendar</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">No active employees found in this location.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->count() > 0)
        <div class="mt-6 flex justify-end">
            <button type="submit" class="btn btn-gold py-2.5 px-8 shadow-sm">Save Attendance</button>
        </div>
        @endif
    </div>
</form>
@endsection
