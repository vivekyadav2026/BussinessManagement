@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Location Details</h1>
      <p class="text-gray-500 mt-1">Details for branch: {{ $location->name }}</p>
  </div>
  <div class="flex gap-2">
      <a class="btn bg-gray-100 text-gray-700 btn-sm" href="{{ route('organization.locations.index') }}">Back to Locations</a>
      <a class="btn btn-gold btn-sm" href="{{ route('organization.locations.edit', $location) }}">Edit Location</a>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="panel mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Location Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Branch Name</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $location->name }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase">Phone Number</label>
                    <div class="mt-1 text-sm font-medium text-gray-900">{{ $location->phone ?: 'Not provided' }}</div>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Address</label>
                <div class="mt-1 text-sm text-gray-700 leading-relaxed">{{ $location->address ?: 'No address provided.' }}</div>
            </div>
        </div>
        
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Employees at this Branch</h3>
            @php
                $employees = $location->employees;
            @endphp
            @if($employees && $employees->count() > 0)
                <table class="inv-table w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                            <tr>
                                <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $emp->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $emp->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-sm">No employees are assigned to this branch.</p>
            @endif
        </div>
    </div>
    
    <div>
        <div class="panel">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Status & Actions</h3>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 uppercase">Active Status</label>
                <div class="mt-1">
                    @if($location->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                    @endif
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100">
                <form action="{{ route('organization.locations.toggle-status', $location) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Toggle Active Status
                    </button>
                </form>
                
                @if(!$employees || $employees->count() === 0)
                    <form action="{{ route('organization.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                            Delete Location
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
