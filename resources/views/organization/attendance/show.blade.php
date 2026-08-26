@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <a href="{{ route('organization.attendance.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Daily Attendance</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }} - Attendance</h1>
        <p class="text-gray-500 mt-1">{{ $employee->designation }} • {{ $employee->employee_code ?? 'No Code' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Summary Sidebar -->
    <div class="md:col-span-1 space-y-6">
        <div class="panel bg-indigo-50 border border-indigo-100 p-6 shadow-sm">
            <h3 class="font-bold text-indigo-900 border-b border-indigo-200 pb-2 mb-4">{{ $dateObj->format('F Y') }} Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Present</span>
                    <span class="font-bold text-green-600">{{ $summary['present'] }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Absent</span>
                    <span class="font-bold text-red-600">{{ $summary['absent'] }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Half Days</span>
                    <span class="font-bold text-orange-600">{{ $summary['half_days'] }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Leaves</span>
                    <span class="font-bold text-blue-600">{{ $summary['leaves'] }}</span>
                </div>
                <div class="pt-3 border-t border-indigo-200 mt-2">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-indigo-900 text-sm">Effective Working Days</span>
                        <span class="font-black text-indigo-700 text-xl">{{ $summary['effective_working_days'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('organization.attendance.show', $employee) }}" class="panel p-6 shadow-sm">
            <h3 class="font-bold border-b pb-2 mb-4">Change Month</h3>
            <div class="mb-3">
                <select name="month" class="w-full border-gray-300 rounded-lg text-sm mb-2">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $dateObj->month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
                <select name="year" class="w-full border-gray-300 rounded-lg text-sm">
                    @for($i = now()->year; $i >= now()->year - 5; $i--)
                        <option value="{{ $i }}" {{ $dateObj->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn btn-ghost w-full justify-center text-xs py-2">View Calendar</button>
        </form>
    </div>

    <!-- Calendar View -->
    <div class="md:col-span-3 panel p-6 shadow-sm">
        <h3 class="font-bold text-lg mb-4">{{ $dateObj->format('F Y') }}</h3>
        
        <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden">
            <!-- Days Header -->
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">{{ $day }}</div>
            @endforeach

            <!-- Padding for first day -->
            @for($i = 0; $i < $dateObj->copy()->startOfMonth()->dayOfWeek; $i++)
                <div class="bg-white p-2 min-h-[100px]"></div>
            @endfor

            <!-- Days -->
            @for($day = 1; $day <= $dateObj->daysInMonth; $day++)
                @php 
                    $currentDate = $dateObj->copy()->day($day)->format('Y-m-d');
                    $att = $attendances[$currentDate] ?? null;
                    $isWeekend = $dateObj->copy()->day($day)->isWeekend();
                @endphp
                <div class="bg-white p-2 min-h-[100px] flex flex-col {{ $isWeekend ? 'bg-gray-50' : '' }}">
                    <div class="text-right text-sm font-medium text-gray-400 mb-1">{{ $day }}</div>
                    @if($att)
                        @php
                            $bg = match($att->status) {
                                'Present' => 'bg-green-100 text-green-800 border-green-200',
                                'Absent' => 'bg-red-100 text-red-800 border-red-200',
                                'Half Day' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'Leave' => 'bg-blue-100 text-blue-800 border-blue-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <div class="{{ $bg }} border px-2 py-1 rounded text-xs font-bold text-center mb-1">
                            {{ $att->status }}
                        </div>
                        @if($att->check_in || $att->check_out)
                            <div class="text-[10px] text-gray-500 text-center mt-auto leading-tight">
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
