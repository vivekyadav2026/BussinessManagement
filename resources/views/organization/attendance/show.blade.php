@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumbs & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">HR &amp; Staff</span>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.attendance.index') }}" class="hover:text-slate-900 transition-colors">Attendance</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $employee->full_name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                    {{ strtoupper(substr($employee->first_name ?? 'E', 0, 1) . substr($employee->last_name ?? 'M', 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2 flex-wrap">
                        <span>{{ $employee->full_name }}</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-300 font-mono">
                            #{{ $employee->employee_code ?? $employee->id }}
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        <span>{{ $employee->designation ?? 'Staff Member' }}</span>
                        @if($employee->location)
                        <span class="mx-1">•</span>
                        <span>{{ $employee->location->name }}</span>
                        @endif
                        <span class="mx-1">•</span>
                        <span class="text-slate-950 font-bold">{{ $dateObj->format('F Y') }} Calendar</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.attendance.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Daily Register</span>
            </a>
            <a href="{{ route('organization.attendance.report', ['month' => $dateObj->month, 'year' => $dateObj->year]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>Monthly Summary</span>
            </a>
            <a href="{{ route('organization.employees.show', $employee) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <span>View Profile</span>
            </a>
        </div>
    </div>

    <!-- 2. Main 2-Column Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT SIDEBAR: Monthly Breakdown & Filter (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Month Summary Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">
                            {{ $dateObj->format('F Y') }} Summary
                        </h3>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Staff roll-call performance</p>
                    </div>
                    <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-slate-200 text-slate-800">
                        {{ $summary['total_recorded_days'] }} Days
                    </span>
                </div>

                <div class="p-5 space-y-3.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-700 font-semibold flex items-center gap-1.5">
                            <span class="text-emerald-600 font-bold">✓</span> Present Days
                        </span>
                        <span class="font-extrabold text-emerald-800 text-sm">{{ $summary['present'] }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-700 font-semibold flex items-center gap-1.5">
                            <span class="text-rose-600 font-bold">✗</span> Absent Days
                        </span>
                        <span class="font-extrabold text-rose-800 text-sm">{{ $summary['absent'] }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-700 font-semibold flex items-center gap-1.5">
                            <span class="text-amber-600 font-bold">🌗</span> Half Days
                        </span>
                        <span class="font-extrabold text-amber-800 text-sm">{{ $summary['half_days'] }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                        <span class="text-slate-700 font-semibold flex items-center gap-1.5">
                            <span class="text-indigo-600 font-bold">🌴</span> Approved Leaves
                        </span>
                        <span class="font-extrabold text-indigo-800 text-sm">{{ $summary['leaves'] }}</span>
                    </div>

                    <!-- Effective Working Days Highlight -->
                    <div class="p-3.5 bg-amber-500/15 border border-amber-400/40 rounded-xl flex justify-between items-center mt-2">
                        <div>
                            <div class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Effective Days</div>
                            <div class="text-[11px] text-slate-700 font-medium">For payroll compensation</div>
                        </div>
                        <span class="text-2xl font-black text-slate-950">{{ $summary['effective_working_days'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Month & Year Selector Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3.5">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">
                    Select Month &amp; Year
                </h3>

                <form method="GET" action="{{ route('organization.attendance.show', $employee) }}" class="space-y-3">
                    <div>
                        <label for="month" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Month</label>
                        <select name="month" id="month" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-950 py-2 px-3 bg-white outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="year" class="block text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1">Year</label>
                        <select name="year" id="year" class="w-full border border-slate-300 rounded-lg text-xs font-bold text-slate-950 py-2 px-3 bg-white outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                            @for($i = max(2030, now()->year); $i >= 2024; $i--)
                                <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                        View Calendar
                    </button>
                </form>
            </div>

        </div>

        <!-- RIGHT: Monthly Attendance Calendar (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">
                            {{ $dateObj->format('F Y') }} Calendar Grid
                        </h2>
                        <p class="text-[11px] text-slate-600 font-medium mt-0.5">Day-by-day attendance shifts and roll-call status</p>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-bold">
                        <span class="inline-flex items-center gap-1 text-emerald-800"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Present</span>
                        <span class="inline-flex items-center gap-1 text-rose-800"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Absent</span>
                        <span class="inline-flex items-center gap-1 text-amber-800"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Half Day</span>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
                        <!-- Days of Week Header -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="p-2 text-center text-xs font-extrabold text-slate-800 uppercase tracking-wider bg-slate-100 rounded-lg">
                            {{ $day }}
                        </div>
                        @endforeach

                        <!-- Empty slots before 1st of month -->
                        @for($i = 0; $i < $dateObj->copy()->startOfMonth()->dayOfWeek; $i++)
                        <div class="p-2 min-h-[85px] bg-slate-50/50 rounded-lg border border-slate-100"></div>
                        @endfor

                        <!-- Days of the Month -->
                        @for($day = 1; $day <= $dateObj->daysInMonth; $day++)
                            @php 
                                $currentDate = $dateObj->copy()->day($day)->format('Y-m-d');
                                $att = $attendances[$currentDate] ?? null;
                                $isWeekend = $dateObj->copy()->day($day)->isWeekend();
                            @endphp
                            <a href="{{ route('organization.attendance.index', ['date' => $currentDate]) }}" class="block p-2 min-h-[85px] rounded-lg border flex flex-col justify-between transition-colors cursor-pointer {{ 
                                $isWeekend ? 'bg-slate-50/70 border-slate-200/60' : 'bg-white border-slate-200 hover:border-amber-400 hover:ring-1 hover:ring-amber-400' 
                            }}" title="Click to edit attendance for {{ $currentDate }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold {{ $isWeekend ? 'text-slate-500' : 'text-slate-900' }}">
                                        {{ $day }}
                                    </span>
                                    @if($currentDate === now()->toDateString())
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" title="Today"></span>
                                    @endif
                                </div>

                                @if($att)
                                    @php
                                        $badgeClass = match($att->status) {
                                            'Present' => 'bg-emerald-50 text-emerald-900 border-emerald-300',
                                            'Absent' => 'bg-rose-50 text-rose-900 border-rose-300',
                                            'Half Day' => 'bg-amber-50 text-amber-900 border-amber-300',
                                            'Leave' => 'bg-indigo-50 text-indigo-900 border-indigo-300',
                                            default => 'bg-slate-100 text-slate-800 border-slate-300'
                                        };
                                    @endphp
                                    <div class="my-auto">
                                        <div class="px-1.5 py-0.5 rounded text-[10px] font-extrabold text-center border {{ $badgeClass }}">
                                            {{ $att->status }}
                                        </div>
                                        @if($att->check_in || $att->check_out)
                                        <div class="text-[9px] text-slate-600 font-mono text-center mt-1 font-semibold">
                                            {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('g:i A') : '--' }}
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-[10px] text-slate-400 font-medium text-center my-auto">
                                        {{ $isWeekend ? 'Weekend' : 'Not marked' }}
                                    </div>
                                @endif
                            </a>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
