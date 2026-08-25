@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
      <p class="text-gray-500 mt-1">Manage what your staff can access.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.roles.create') }}">+ New Role</a>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="panel">
  <table class="inv-table w-full">
    <thead>
      <tr>
        <th>Role Name</th>
        <th>Assigned Users</th>
        <th>Created</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($roles as $role)
      <tr>
        <td class="font-bold">{{ $role->name }}</td>
        <td>{{ $role->users_count }} users</td>
        <td>{{ $role->created_at->format('M d, Y') }}</td>
        <td class="text-right">
          @if($role->name !== 'Organization Admin')
            <a href="{{ route('organization.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit Permissions</a>
          @else
            <span class="text-gray-400 text-xs italic">System Admin (Full Access)</span>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-6 text-gray-500">No custom roles created yet.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
