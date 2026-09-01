@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Employees</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage your organization's staff profiles, location assignments, and system login access.</p>
        </div>
        <a href="{{ route('organization.employees.create') }}" class="px-4 py-2.5 bg-[var(--theme-active)] text-[var(--theme-active-text)] rounded-xl font-semibold text-sm hover:opacity-95 shadow-sm transition">+ Add Employee</a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Filters -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <form method="GET" action="{{ route('organization.employees.index') }}" class="flex flex-col sm:flex-row gap-3 w-full max-w-3xl">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, email..." class="border border-gray-200 focus:border-[var(--theme-active)] focus:ring-2 focus:ring-[var(--theme-active)]/20 rounded-xl px-4 py-2 text-sm w-full sm:w-64 outline-none transition">
                
                <select name="status" class="border border-gray-200 focus:border-[var(--theme-active)] rounded-xl px-4 py-2 text-sm outline-none bg-white transition">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
                
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition shadow-sm">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm flex items-center font-medium">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 text-[10px] uppercase text-gray-400 font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">Designation</th>
                        <th class="px-6 py-4">System Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
                            <div class="text-[11px] text-gray-400 font-mono mt-0.5">Code: {{ $emp->employee_code ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800 font-medium">{{ $emp->email ?? '—' }}</div>
                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $emp->phone ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-700">{{ $emp->designation ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($emp->user && $emp->user->roles->count() > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $emp->user->roles->first()->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">No Login Access</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($emp->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @elseif($emp->status === 'inactive')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">Inactive</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Terminated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('organization.employees.show', $emp) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Profile">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('organization.employees.edit', $emp) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Employee">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('organization.employees.toggle-status', $emp) }}" method="POST" onsubmit="return confirm('Toggle this employee status?');" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 {{ $emp->status === 'active' ? 'text-gray-500 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $emp->status === 'active' ? 'Deactivate Employee' : 'Activate Employee' }}">
                                        @if($emp->status === 'active')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400 font-medium">No employees found in this criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
