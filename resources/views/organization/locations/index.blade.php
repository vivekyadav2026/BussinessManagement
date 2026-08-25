@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Locations (Branches)</h1>
      <p class="text-gray-500 mt-1">Manage your business branches and operational sites.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="{{ route('organization.locations.create') }}">+ Add Location</a>
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
        <th>Location Name</th>
        <th>Address & Contact</th>
        <th>Status</th>
        <th class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($locations as $loc)
      <tr>
        <td class="font-bold">{{ $loc->name }}</td>
        <td>
            <div class="text-sm">{{ $loc->address ?? 'No address' }}</div>
            <div class="text-xs text-gray-500">{{ $loc->phone ?? 'No phone' }}</div>
        </td>
        <td>
            @if($loc->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
            @endif
        </td>
        <td class="text-right">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('organization.locations.edit', $loc) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs">Edit</a>
                <form action="{{ route('organization.locations.toggle-status', $loc) }}" method="POST" onsubmit="return confirm('Toggle this location\'s status?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-medium {{ $loc->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                        {{ $loc->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center py-6 text-gray-500">No locations added yet. Add your primary location to get started.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
