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
            <div class="flex items-center justify-end gap-1">
                <a href="{{ route('organization.locations.show', $loc) }}" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View Location">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('organization.locations.edit', $loc) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit Location">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('organization.locations.toggle-status', $loc) }}" method="POST" onsubmit="return confirm('Toggle location status?');" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="p-1.5 {{ $loc->is_active ? 'text-gray-500 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-500 hover:text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition" title="{{ $loc->is_active ? 'Deactivate Location' : 'Activate Location' }}">
                        @if($loc->is_active)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </button>
                </form>
                @if($loc->employees()->count() === 0)
                    <form action="{{ route('organization.locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Location">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                @endif
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
