@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <a href="{{ route('organization.attendance.index') }}" class="text-xs text-slate-500 hover:text-indigo-600 mb-1 inline-block">&larr; Back to Daily Attendance</a>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $employee->full_name }} - Attendance Calendar</h1>
        <p class="text-xs text-slate-500 mt-0.5">{{ $employee->designation ?? 'Staff Member' }} • <span class="font-mono">{{ $employee->employee_code ?? 'EMP' }}</span></p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Summary Sidebar -->
    <div class="md:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-indigo-100 p-6 shadow-xs bg-gradient-to-b from-indigo-50/40 to-white">
            <h3 class="font-bold text-indigo-900 border-b border-indigo-100 pb-3 mb-4 text-sm">{{ $dateObj->format('F Y') }} Summary</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">✓ Present Days</span>
                    <span class="font-black text-emerald-700 text-sm">{{ $summary['present'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">✗ Absent Days</span>
                    <span class="font-black text-rose-700 text-sm">{{ $summary['absent'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">🌗 Half Days</span>
                    <span class="font-black text-amber-700 text-sm">{{ $summary['half_days'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">🌴 Approved Leaves</span>
                    <span class="font-black text-indigo-700 text-sm">{{ $summary['leaves'] }}</span>
                </div>
                <div class="pt-3 border-t border-indigo-100 mt-2">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-900 text-xs">Effective Working Days</span>
                        <span class="font-black text-indigo-700 text-xl">{{ $summary['effective_working_days'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('organization.attendance.show', $employee) }}" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
            <h3 class="font-bold border-b border-gray-100 pb-3 mb-4 text-xs uppercase tracking-wider text-slate-500">Select Month & Year</h3>
            <div class="space-y-3 mb-4">
                <select name="month" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 py-2">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
                <select name="year" class="w-full border-gray-300 rounded-xl text-xs font-semibold text-slate-800 py-2">
                    @for($i = max(2030, now()->year); $i >= 2024; $i--)
                        <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>

            </div>
            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">View Month Calendar</button>
        </form>
    </div>

    <!-- Calendar Grid View -->
    <div class="md:col-span-3 bg-white rounded-2xl border border-gray-100 p-6 shadow-xs">
        <h3 class="font-bold text-lg text-slate-900 mb-4">{{ $dateObj->format('F Y') }} Calendar</h3>
        
        <div class="grid grid-cols-7 gap-px bg-slate-200 border border-slate-200 rounded-xl overflow-hidden shadow-xs">
            <!-- Days Header -->
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="bg-slate-100 p-2.5 text-center text-xs font-extrabold text-slate-600 uppercase">{{ $day }}</div>
            @endforeach

            <!-- Empty slots before month start -->
            @for($i = 0; $i < $dateObj->copy()->startOfMonth()->dayOfWeek; $i++)
                <div class="bg-slate-50 p-2 min-h-[90px]"></div>
            @endfor

            <!-- Days -->
            @for($day = 1; $day <= $dateObj->daysInMonth; $day++)
                @php 
                    $currentDate = $dateObj->copy()->day($day)->format('Y-m-d');
                    $att = $attendances[$currentDate] ?? null;
                    $isWeekend = $dateObj->copy()->day($day)->isWeekend();
                @endphp
                <div class="bg-white p-2 min-h-[90px] flex flex-col justify-between {{ $isWeekend ? 'bg-slate-50/70' : '' }}">
                    <div class="text-right text-xs font-bold text-slate-400 mb-1">{{ $day }}</div>
                    @if($att)
                        @php
                            $bg = match($att->status) {
                                'Present' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'Absent' => 'bg-rose-50 text-rose-800 border-rose-200',
                                'Half Day' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'Leave' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                default => 'bg-slate-100 text-slate-800 border-slate-200'
                            };
                        @endphp
                        <div class="{{ $bg }} border px-2 py-1 rounded-lg text-[11px] font-extrabold text-center">
                            {{ $att->status }}
                        </div>
                        @if($att->check_in || $att->check_out)
                            <div class="text-[10px] text-slate-500 font-mono text-center mt-1">
                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '--' }} - 
                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '--' }}
                            </div>
                        @endif
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
