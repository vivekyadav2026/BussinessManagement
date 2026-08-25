@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
      <p class="text-gray-500 mt-1">Manage your organization's staff and access.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.employees.create') }}">+ Add Employee</a>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel">
    <div class="mb-6 flex gap-4">
        <form method="GET" action="{{ route('organization.employees.index') }}" class="flex gap-4 w-full max-w-2xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, email..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-indigo-500 outline-none">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('organization.employees.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm flex items-center">Clear</a>
            @endif
        </form>
    </div>

  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Employee</th>
        <th>Contact</th>
        <th>Designation</th>
        <th>Role</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($employees as $emp)
      <tr>
        <td>
            <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
            <div class="text-xs text-gray-500 mono">{{ $emp->employee_code ?? 'N/A' }}</div>
        </td>
        <td>
            <div class="text-sm">{{ $emp->email ?? '—' }}</div>
            <div class="text-xs text-gray-500">{{ $emp->phone ?? '—' }}</div>
        </td>
        <td>{{ $emp->designation ?? '—' }}</td>
        <td>
            @if($emp->user && $emp->user->roles->count() > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ $emp->user->roles->first()->name }}
                </span>
            @else
                <span class="text-xs text-gray-400 italic">No Login</span>
            @endif
        </td>
        <td>
            @if($emp->status === 'active')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
            @elseif($emp->status === 'inactive')
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Inactive</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Terminated</span>
            @endif
        </td>
        <td class="text-right">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('organization.employees.show', $emp) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">View</a>
                <a href="{{ route('organization.employees.edit', $emp) }}" class="text-gray-500 hover:text-gray-900 font-medium text-xs">Edit</a>
                <form action="{{ route('organization.employees.toggle-status', $emp) }}" method="POST" onsubmit="return confirm('Toggle this employee\'s status?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-medium {{ $emp->status === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                        {{ $emp->status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center py-6 text-gray-500">No employees found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  
  <div class="mt-4">
      {{ $employees->links() }}
  </div>
</div>
@endsection
