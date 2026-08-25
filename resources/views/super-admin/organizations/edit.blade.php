@extends('layouts.super-admin')

@section('content')
<div class="mb-8">
    <a href="{{ route('super-admin.organizations.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Organizations</a>
    <h1 class="text-2xl font-bold text-gray-900">Edit Organization: {{ $organization->name }}</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <form action="{{ route('super-admin.organizations.update', $organization) }}" method="POST" class="p-6 md:p-8">
        @csrf
        @method('PUT')
        
        <h3 class="text-lg font-bold mb-4 border-b pb-2">Business Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Organization Name *</label>
                <input type="text" name="name" value="{{ old('name', $organization->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">GST Number</label>
                <input type="text" name="gst_number" value="{{ old('gst_number', $organization->gst_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('gst_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                <input type="email" name="email" value="{{ old('email', $organization->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('super-admin.organizations.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 transition">Save Changes</button>
        </div>
    </form>
</div>
@endsection
