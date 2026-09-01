@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daily Attendance Register</h1>
        <p class="text-xs text-slate-500 mt-1">Record and manage daily staff attendance for <b>{{ $dateObj->format('l, F j, Y') }}</b>.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('organization.attendance.report') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h55.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Monthly Report</span>
        </a>
    </div>
</div>

<!-- Date Navigation & Quick Action Controls -->
<div class="bg-white rounded-2xl border border-gray-100 p-3.5 shadow-xs mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3">
    <!-- Date Picker Form -->
    <form method="GET" action="{{ route('organization.attendance.index') }}" class="flex items-center gap-1.5 shrink-0">
        @php
            $prevDate = $dateObj->copy()->subDay()->toDateString();
            $nextDate = $dateObj->copy()->addDay()->toDateString();
        @endphp
        <a href="{{ route('organization.attendance.index', ['date' => $prevDate]) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl transition shadow-2xs shrink-0 flex items-center justify-center" title="Previous Day">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        
        <input type="date" name="date" value="{{ $date }}" class="border-gray-300 rounded-xl text-xs font-bold text-slate-800 py-1.5 px-2.5 focus:ring-indigo-500 focus:border-indigo-500 shadow-2xs shrink-0" onchange="this.form.submit()">
        
        <a href="{{ route('organization.attendance.index', ['date' => $nextDate]) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl transition shadow-2xs shrink-0 flex items-center justify-center" title="Next Day">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>

        @if($date !== now()->toDateString())
            <a href="{{ route('organization.attendance.index') }}" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition border border-indigo-200 shrink-0">Today</a>
        @endif
    </form>

    <!-- Quick Bulk Action Buttons -->
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="markAllStatus('Present')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl transition whitespace-nowrap">
            ⚡ Present All
        </button>
        <button type="button" onclick="markAllStatus('Absent')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl transition whitespace-nowrap">
            ⚡ Absent All
        </button>
        @php
            $defaultIn = $organization->default_check_in ?? '09:00';
            $defaultOut = $organization->default_check_out ?? '18:00';
            $formattedIn = \Carbon\Carbon::parse($defaultIn)->format('g:i A');
            $formattedOut = \Carbon\Carbon::parse($defaultOut)->format('g:i A');
        @endphp
        <button type="button" onclick="autoFillHours('{{ $defaultIn }}', '{{ $defaultOut }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl transition whitespace-nowrap" title="Fill organization default office hours">
            ⏰ Default Hours ({{ $formattedIn }} - {{ $formattedOut }})
        </button>
    </div>
</div>




@if(session('success'))
<div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 border border-emerald-200 text-sm font-semibold shadow-xs flex items-center gap-2">
    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Attendance Sheet Form -->
<form action="{{ route('organization.attendance.storeBulk') }}" method="POST">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 space-y-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="py-3.5 px-4">Employee Details</th>
                        <th class="py-3.5 px-4">Attendance Status</th>
                        <th class="py-3.5 px-4">Check In</th>
                        <th class="py-3.5 px-4">Check Out</th>
                        <th class="py-3.5 px-4 text-right">Calendar History</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs font-medium">
                    @forelse($employees as $emp)
                    @php 
                        $att = $emp->attendances->first(); 
                        $status = $att->status ?? 'Present';
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-900 text-sm">{{ $emp->full_name }}</div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $emp->employee_code ?? 'EMP' }} • {{ $emp->designation ?? 'Staff Member' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <select name="attendance[{{ $emp->id }}][status]" class="attendance-status-select border border-gray-300 rounded-xl text-xs font-bold py-2 px-3 outline-none transition w-36 {{ 
                                $status == 'Present' ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 
                                ($status == 'Absent' ? 'text-rose-700 bg-rose-50 border-rose-200' : 
                                ($status == 'Half Day' ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-indigo-700 bg-indigo-50 border-indigo-200'))
                            }}" onchange="updateSelectStyle(this)">
                                <option value="Present" class="text-emerald-700 font-bold bg-emerald-50" {{ $status == 'Present' ? 'selected' : '' }}>✓ Present</option>
                                <option value="Absent" class="text-rose-700 font-bold bg-rose-50" {{ $status == 'Absent' ? 'selected' : '' }}>✗ Absent</option>
                                <option value="Half Day" class="text-amber-700 font-bold bg-amber-50" {{ $status == 'Half Day' ? 'selected' : '' }}>🌗 Half Day</option>
                                <option value="Leave" class="text-indigo-700 font-bold bg-indigo-50" {{ $status == 'Leave' ? 'selected' : '' }}>🌴 Leave</option>
                            </select>
                        </td>
                        <td class="py-3.5 px-4">
                            <input type="time" name="attendance[{{ $emp->id }}][check_in]" value="{{ $att->check_in ?? '' }}" class="check-in-input border border-gray-300 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-semibold outline-none focus:border-indigo-500">
                        </td>
                        <td class="py-3.5 px-4">
                            <input type="time" name="attendance[{{ $emp->id }}][check_out]" value="{{ $att->check_out ?? '' }}" class="check-out-input border border-gray-300 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-semibold outline-none focus:border-indigo-500">
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('organization.attendance.show', $emp) }}" class="p-1.5 inline-flex items-center gap-1 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-xs font-bold" title="View Monthly Calendar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Calendar</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-400 font-medium">No active employees found in this branch location.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->count() > 0)
        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs rounded-xl shadow-md transition uppercase tracking-wider">
                Save Daily Attendance Register
            </button>
        </div>
        @endif
    </div>
</form>

<script>
function markAllStatus(status) {
    document.querySelectorAll('.attendance-status-select').forEach(select => {
        select.value = status;
        updateSelectStyle(select);
    });
}

function autoFillHours(defaultIn = '09:00', defaultOut = '18:00') {
    document.querySelectorAll('.check-in-input').forEach(input => {
        if (!input.value) input.value = defaultIn;
    });
    document.querySelectorAll('.check-out-input').forEach(input => {
        if (!input.value) input.value = defaultOut;
    });
}


function updateSelectStyle(select) {
    const val = select.value;
    select.className = 'attendance-status-select border rounded-xl text-xs font-bold py-2 px-3 outline-none transition w-36 ';
    if (val === 'Present') select.className += 'text-emerald-700 bg-emerald-50 border-emerald-200';
    else if (val === 'Absent') select.className += 'text-rose-700 bg-rose-50 border-rose-200';
    else if (val === 'Half Day') select.className += 'text-amber-700 bg-amber-50 border-amber-200';
    else select.className += 'text-indigo-700 bg-indigo-50 border-indigo-200';
}
</script>
@endsection
