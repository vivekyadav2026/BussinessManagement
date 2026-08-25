@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('organization.employees.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Employees</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
        <div class="flex gap-2 mt-2">
            @if($employee->status === 'active')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active Employee</span>
            @elseif($employee->status === 'inactive')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Inactive</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Terminated</span>
            @endif
            
            @if($employee->user)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Role: {{ $employee->user->roles->first()->name ?? 'None' }}
                </span>
            @endif
        </div>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('organization.employees.edit', $employee) }}" class="btn bg-white border border-gray-300 text-gray-700">Edit Profile</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="panel mb-6">
            <h3 class="text-lg font-bold mb-4 pb-2 border-b">Employee Information</h3>
            <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Employee Code</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->employee_code ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Designation</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->designation ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Email</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->email ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Phone</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->phone ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Joining Date</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : 'N/A' }}</div>
                </div>
                <div class="col-span-2">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Address</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->address ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="panel bg-gray-50 border-gray-200">
            <h3 class="text-lg font-bold mb-4 pb-2 border-b">System Access</h3>
            @if($employee->user)
                <div class="mb-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Login Email</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->user->email }}</div>
                </div>
                <div class="mb-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Assigned Role</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->user->roles->first()->name ?? 'None' }}</div>
                </div>
                <div class="mb-4">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Account Created</div>
                    <div class="text-sm font-medium mt-1">{{ $employee->user->created_at->format('M d, Y') }}</div>
                </div>
            @else
                <div class="text-sm text-gray-600 italic py-4 text-center">
                    This employee does not have a system login account.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
