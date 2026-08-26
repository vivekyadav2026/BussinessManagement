@extends('layouts.sme')

@section('content')
<div class="p-6 max-w-5xl space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('organization.employees.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $employee->full_name }}</h1>
                <div class="flex gap-2 mt-1.5">
                    @if($employee->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Active Employee</span>
                    @elseif($employee->status === 'inactive')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">Inactive</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Terminated</span>
                    @endif
                    
                    @if($employee->user)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            Role: {{ $employee->user->roles->first()->name ?? 'None' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('organization.employees.edit', $employee) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">Edit Profile</a>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3 mb-4">Employee Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Employee Code</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->employee_code ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Designation</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->designation ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Email Address</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->email ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Phone Number</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->phone ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Joining Date</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : 'N/A' }}</div>
                    </div>
                    <div class="col-span-full">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Address</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $employee->address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar stats -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider border-b border-gray-50 pb-3">System Access</h3>
                @if($employee->user)
                    <div class="space-y-1">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Login Email</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $employee->user->email }}</div>
                    </div>
                    <div class="space-y-1">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Assigned Role</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $employee->user->roles->first()->name ?? 'None' }}</div>
                    </div>
                    <div class="space-y-1">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Account Created</div>
                        <div class="text-sm font-semibold text-gray-800">{{ $employee->user->created_at->format('M d, Y') }}</div>
                    </div>
                @else
                    <div class="text-sm text-gray-400 italic py-6 text-center font-medium">
                        This employee does not have a system login account.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
