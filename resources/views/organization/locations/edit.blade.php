@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.locations.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Locations</a>
  <h1 class="text-2xl font-bold text-gray-900">Edit Location: {{ $location->name }}</h1>
</div>

<form action="{{ route('organization.locations.update', $location) }}" method="POST" class="max-w-2xl">
    @csrf
    @method('PUT')
    
    <div class="panel">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Location Name / Branch Code *</label>
            <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $location->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
            @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">{{ old('address', $location->address) }}</textarea>
            @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <div class="pt-4 border-t flex justify-end gap-3">
            <a href="{{ route('organization.locations.index') }}" class="btn bg-white border border-gray-300 text-gray-700">Cancel</a>
            <button type="submit" class="btn btn-gold">Update Location</button>
        </div>
    </div>
</form>
@endsection
