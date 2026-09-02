@extends('layouts.sme')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
        <a href="{{ route('organization.locations.index') }}" class="p-2.5 rounded-2xl border border-gray-200 bg-white text-gray-700 hover:text-gray-900 hover:bg-gray-50 shadow-xs transition" title="Back to Locations">
            &larr;
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Location: {{ $location->name }}</h1>
            <p class="text-xs text-gray-500 font-medium">Modify branch information parameters or update location specifications.</p>
        </div>
    </div>

    <!-- Form Container Card -->
    <form action="{{ route('organization.locations.update', $location) }}" method="POST" class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 space-y-5">
            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                <h2 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Branch Details</h2>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Branch / Outlet Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-2xl px-4 py-3 text-xs font-bold text-gray-900 outline-none transition @error('name') border-rose-300 @enderror">
                    @error('name') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Contact Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $location->phone) }}" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-2xl px-4 py-3 text-xs font-bold text-gray-900 outline-none transition @error('phone') border-rose-300 @enderror">
                    @error('phone') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1.5">Full Physical Address</label>
                    <textarea name="address" rows="3" class="w-full border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-2xl px-4 py-3 text-xs font-medium text-gray-900 outline-none transition @error('address') border-rose-300 @enderror">{{ old('address', $location->address) }}</textarea>
                    @error('address') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Footer Actions -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
            <a href="{{ route('organization.locations.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 bg-white rounded-xl font-bold text-xs hover:bg-gray-100 shadow-xs transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-extrabold text-xs hover:bg-indigo-700 shadow-md transition">Update Location</button>
        </div>
    </form>
</div>
@endsection
