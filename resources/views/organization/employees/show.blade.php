@extends('layouts.sme')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.employees.index') }}" class="hover:text-slate-900 transition-colors">Employees</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">{{ $employee->full_name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center transition shadow-2xs" title="Back to Directory">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">{{ $employee->full_name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">{{ $employee->designation ?? 'Staff Member' }} &bull; Profile &amp; Access Record</p>
                </div>
            </div>
        </div>

        <!-- Header Action CTAs -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <a href="{{ route('organization.employees.edit', $employee) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Profile</span>
            </a>

            <form action="{{ route('organization.employees.toggle-status', $employee) }}" method="POST" onsubmit="return confirm('Toggle status for {{ $employee->full_name }}?');" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-xs rounded-lg transition shadow-2xs">
                    {{ $employee->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    <!-- 2. Compact Identity Banner -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-lg flex items-center justify-center shrink-0 shadow-2xs">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-950 tracking-tight truncate">
                            {{ $employee->full_name }}
                        </h2>
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-900 border border-slate-300">
                            {{ $employee->employee_code ?? 'EMP-'.$employee->id }}
                        </span>

                        @if($employee->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 text-emerald-950 border border-emerald-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active Staff
                            </span>
                        @elseif($employee->status === 'inactive')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-100 text-amber-950 border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Inactive
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-bold bg-rose-100 text-rose-950 border border-rose-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Terminated
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-700 font-semibold mt-1 flex-wrap">
                        <span>{{ $employee->designation ?? 'Staff Member' }}</span>
                        @if($employee->email)
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-900 font-medium">{{ $employee->email }}</span>
                        @endif
                        @if($employee->phone)
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-900 font-medium">{{ $employee->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Branch Indicator -->
            <div class="shrink-0 self-start sm:self-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-900 font-bold text-xs border border-slate-300">
                    <span>📍</span> {{ $employee->location ? $employee->location->name : 'All Branches' }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Details Grid (Two-Column on Desktop) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Personal & Employment Details (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-2xs divide-y divide-slate-100 overflow-hidden">
            <!-- Section: Personal Info -->
            <div class="p-5 sm:p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-950">Employee Personal Profile</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Full Legal Name</span>
                        <span class="font-bold text-slate-950 text-sm">{{ $employee->full_name }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Designation</span>
                        <span class="font-bold text-slate-950 text-sm">{{ $employee->designation ?? '—' }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Email Address</span>
                        <span class="font-bold text-slate-950 text-sm">{{ $employee->email ?? '—' }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Phone Number</span>
                        <span class="font-bold text-slate-950 text-sm">{{ $employee->phone ?? '—' }}</span>
                    </div>

                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Date of Joining</span>
                        <span class="font-bold text-slate-950 text-sm">
                            {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : 'Not specified' }}
                        </span>
                    </div>

                    <div class="bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Employee Code</span>
                        <span class="font-mono font-bold text-slate-950 text-sm">{{ $employee->employee_code ?? 'EMP-'.$employee->id }}</span>
                    </div>

                    <div class="col-span-full bg-slate-50/70 p-3.5 rounded-lg border border-slate-200/80">
                        <span class="block text-slate-500 font-medium text-[11px] uppercase tracking-wider mb-1">Residential Address</span>
                        <span class="font-medium text-slate-950 text-xs sm:text-sm leading-relaxed">{{ $employee->address ?? 'No physical address provided.' }}</span>
                    </div>
                </div>
            </div>

            <!-- Section: Branch Location Assignment -->
            <div class="p-5 sm:p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-950">Assigned Workspace Branches</h3>
                @php
                    $assignedLocations = $employee->user ? $employee->user->locations : collect([$employee->location])->filter();
                @endphp

                @if($assignedLocations->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($assignedLocations as $loc)
                            <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-slate-50/60">
                                <span class="w-8 h-8 rounded-md bg-amber-500/10 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">📍</span>
                                <div>
                                    <div class="font-bold text-slate-950 text-xs">{{ $loc->name }}</div>
                                    <div class="text-[11px] text-slate-600 truncate">{{ $loc->address ?? 'Primary Branch' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        This employee is currently assigned to all workspace locations.
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN: System Access & Permissions (4 cols) -->
        <div class="lg:col-span-4 space-y-5">
            <!-- System Login Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-4">
                <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">System Credentials</h3>

                @if($employee->user)
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Login Username:</span>
                            <span class="font-bold text-slate-950 truncate max-w-[180px]">{{ $employee->user->email }}</span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Assigned Role:</span>
                            <span class="font-bold text-indigo-950 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                {{ $employee->user->roles->first()->name ?? 'Staff' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Account Created:</span>
                            <span class="font-semibold text-slate-900">{{ $employee->user->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Access Status:</span>
                            <span class="font-bold text-emerald-900 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Authorized
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('organization.employees.edit', $employee) }}" class="mt-4 w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-xs font-bold text-slate-900 transition">
                        <span>Modify Role &amp; Password</span>
                        <span>&rarr;</span>
                    </a>
                @else
                    <div class="py-6 text-center space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-base mx-auto">
                            🔒
                        </div>
                        <h4 class="text-xs font-bold text-slate-900">No Portal Login Access</h4>
                        <p class="text-[11px] text-slate-600 max-w-xs mx-auto">
                            This staff member does not have credentials to sign in to the POS or admin backend.
                        </p>
                        <a href="{{ route('organization.employees.edit', $employee) }}" class="inline-block mt-2 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-2xs">
                            Enable Login Credentials
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Navigation Note -->
            @if(Route::has('organization.attendance.index'))
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-2">
                <div class="font-bold text-slate-950 text-xs flex items-center gap-1.5">
                    <span>⏱️</span> Attendance &amp; Timesheets
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    View automated biometric logs, manual punches, and monthly shift performance reports.
                </p>
                <a href="{{ route('organization.attendance.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-800 hover:text-amber-950 hover:underline pt-1">
                    <span>View Attendance Records</span>
                    <span>&rarr;</span>
                </a>
            </div>
            @endif
        </div>

    </div>

</div>
@endsection
