@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-24">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">HR &amp; Staff</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Attendance</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Daily Register</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Daily Attendance Register</h1>
            <p class="text-xs sm:text-sm text-slate-700 font-medium mt-1 leading-relaxed">
                Log attendance status, check-in, and check-out timestamps for <strong class="text-slate-950">{{ $dateObj->format('l, F j, Y') }}</strong>.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.attendance.report', ['month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Monthly Payroll Report</span>
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

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. Daily Summary KPI Metrics Strip -->
    @php
        $totalStaff = $employees->count();
        $presentCount = 0;
        $absentCount = 0;
        $halfDayCount = 0;
        $leaveCount = 0;
        $markedCount = 0;

        foreach($employees as $emp) {
            $att = $emp->attendances->first();
            if ($att) {
                $markedCount++;
                if ($att->status === 'Present') $presentCount++;
                elseif ($att->status === 'Absent') $absentCount++;
                elseif ($att->status === 'Half Day') $halfDayCount++;
                elseif ($att->status === 'Leave') $leaveCount++;
            }
        }
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Scheduled Staff -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Scheduled Staff</span>
                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $totalStaff }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $markedCount }}/{{ $totalStaff }} roll-calls recorded
            </div>
        </div>

        <!-- Metric 2: Present Today -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Present</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-850">{{ $presentCount }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $totalStaff > 0 ? round(($presentCount / $totalStaff) * 100) : 0 }}% attendance rate
            </div>
        </div>

        <!-- Metric 3: Absent -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Absent</span>
                <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center text-sm font-bold">✗</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-rose-850">{{ $absentCount }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">Unexcused staff absences</div>
        </div>

        <!-- Metric 4: Leaves & Half-Days -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Leaves / Half-Day</span>
                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">🌴</span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-slate-950">{{ $leaveCount + $halfDayCount }}</div>
            <div class="text-[11px] text-slate-600 font-medium mt-1">
                {{ $leaveCount }} on leave • {{ $halfDayCount }} half-day
            </div>
        </div>
    </div>

    <!-- 3. Date Navigator & Quick Operation Controls -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-3.5 sm:p-4">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Date Picker Form -->
            <form method="GET" action="{{ route('organization.attendance.index') }}" class="flex items-center gap-2 shrink-0">
                @php
                    $prevDate = $dateObj->copy()->subDay()->toDateString();
                    $nextDate = $dateObj->copy()->addDay()->toDateString();
                @endphp
                <a href="{{ route('organization.attendance.index', ['date' => $prevDate]) }}" class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg transition border border-slate-200 shadow-2xs shrink-0" title="Previous Day">
                    <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                
                <input type="date" 
                       name="date" 
                       value="{{ $date }}" 
                       class="border border-slate-300 rounded-lg text-xs font-extrabold text-slate-950 py-2 px-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs outline-none cursor-pointer" 
                       onchange="this.form.submit()">
                
                <a href="{{ route('organization.attendance.index', ['date' => $nextDate]) }}" class="w-9 h-9 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg transition border border-slate-200 shadow-2xs shrink-0" title="Next Day">
                    <svg class="w-4 h-4 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>

                @if($date !== now()->toDateString())
                    <a href="{{ route('organization.attendance.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-bold rounded-lg transition border border-slate-200 shadow-2xs shrink-0">
                        Today
                    </a>
                @endif
            </form>

            <!-- Quick Batch Fill Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="markAllStatus('Present')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-extrabold rounded-lg transition whitespace-nowrap shadow-2xs">
                    ⚡ Mark All Present
                </button>
                <button type="button" onclick="markAllStatus('Absent')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-300 text-xs font-extrabold rounded-lg transition whitespace-nowrap shadow-2xs">
                    ⚡ Mark All Absent
                </button>
                @php
                    $defaultIn = $organization->default_check_in ?? '09:00';
                    $defaultOut = $organization->default_check_out ?? '18:00';
                    $formattedIn = \Carbon\Carbon::parse($defaultIn)->format('g:i A');
                    $formattedOut = \Carbon\Carbon::parse($defaultOut)->format('g:i A');
                @endphp
                <button type="button" onclick="autoFillHours('{{ $defaultIn }}', '{{ $defaultOut }}')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 text-xs font-bold rounded-lg transition whitespace-nowrap shadow-2xs" title="Auto-fill default scheduled office hours">
                    ⏰ Fill Hours ({{ $formattedIn }} - {{ $formattedOut }})
                </button>
            </div>

        </div>
    </div>

    <!-- 4. Attendance Sheet Form -->
    <form action="{{ route('organization.attendance.storeBulk') }}" method="POST" id="attendance-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-[11px] uppercase font-extrabold text-slate-900 border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 pl-5">Staff Member</th>
                            <th class="py-3.5 px-4">Attendance Status</th>
                            <th class="py-3.5 px-4">Check-In</th>
                            <th class="py-3.5 px-4">Check-Out</th>
                            <th class="py-3.5 px-4 text-right pr-5">Monthly History</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($employees as $emp)
                        @php 
                            $att = $emp->attendances->first(); 
                            $status = $att->status ?? 'Present';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Employee Column -->
                            <td class="py-3.5 px-4 pl-5 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-900 text-amber-400 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($emp->first_name ?? 'E', 0, 1) . substr($emp->last_name ?? 'M', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-950 text-xs truncate">
                                            {{ $emp->full_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-600 font-medium mt-0.5">
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

                            <!-- Attendance Status Selector -->
                            <td class="py-3.5 px-4 align-middle">
                                <select name="attendance[{{ $emp->id }}][status]" 
                                        class="attendance-status-select border rounded-lg text-xs font-extrabold py-2 px-3 outline-none transition w-40 cursor-pointer shadow-2xs {{ 
                                            $status == 'Present' ? 'text-emerald-900 bg-emerald-50 border-emerald-300' : 
                                            ($status == 'Absent' ? 'text-rose-900 bg-rose-50 border-rose-300' : 
                                            ($status == 'Half Day' ? 'text-amber-900 bg-amber-50 border-amber-300' : 'text-indigo-900 bg-indigo-50 border-indigo-300'))
                                        }}" onchange="updateSelectStyle(this)">
                                    <option value="Present" class="text-emerald-900 font-extrabold bg-white" {{ $status == 'Present' ? 'selected' : '' }}>✓ Present</option>
                                    <option value="Absent" class="text-rose-900 font-extrabold bg-white" {{ $status == 'Absent' ? 'selected' : '' }}>✗ Absent</option>
                                    <option value="Half Day" class="text-amber-900 font-extrabold bg-white" {{ $status == 'Half Day' ? 'selected' : '' }}>🌗 Half Day</option>
                                    <option value="Leave" class="text-indigo-900 font-extrabold bg-white" {{ $status == 'Leave' ? 'selected' : '' }}>🌴 Leave</option>
                                </select>
                            </td>

                            <!-- Check-In Input -->
                            <td class="py-3.5 px-4 align-middle">
                                <input type="time" 
                                       name="attendance[{{ $emp->id }}][check_in]" 
                                       value="{{ $att->check_in ?? '' }}" 
                                       class="check-in-input border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-950 font-bold outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs transition">
                            </td>

                            <!-- Check-Out Input -->
                            <td class="py-3.5 px-4 align-middle">
                                <input type="time" 
                                       name="attendance[{{ $emp->id }}][check_out]" 
                                       value="{{ $att->check_out ?? '' }}" 
                                       class="check-out-input border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-950 font-bold outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 shadow-2xs transition">
                            </td>

                            <!-- Calendar History Link -->
                            <td class="py-3.5 px-4 pr-5 align-middle text-right">
                                <a href="{{ route('organization.attendance.show', ['employee' => $emp, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-slate-700 hover:text-slate-950 hover:bg-slate-100 rounded-lg border border-slate-200 transition text-xs font-bold shadow-2xs" title="View Monthly Attendance Calendar">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Calendar</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-xl mb-3">
                                    ⏱️
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">No active employees found</h3>
                                <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto">
                                    No staff members are registered for this branch. Add employees in the Employee module to record attendance.
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

            <!-- Elevated Sticky Save Bar -->
            @if($employees->count() > 0)
            <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-700 font-semibold">
                    Recording attendance roll-call for <strong>{{ $employees->count() }} active staff members</strong>.
                </div>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Save Daily Attendance Register</span>
                </button>
            </div>
            @endif
        </div>
    </form>

</div>

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
    select.className = 'attendance-status-select border rounded-lg text-xs font-extrabold py-2 px-3 outline-none transition w-40 cursor-pointer shadow-2xs ';
    if (val === 'Present') {
        select.className += 'text-emerald-900 bg-emerald-50 border-emerald-300';
    } else if (val === 'Absent') {
        select.className += 'text-rose-900 bg-rose-50 border-rose-300';
    } else if (val === 'Half Day') {
        select.className += 'text-amber-900 bg-amber-50 border-amber-300';
    } else {
        select.className += 'text-indigo-900 bg-indigo-50 border-indigo-300';
    }
}
</script>
@endsection
