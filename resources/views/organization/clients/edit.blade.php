@extends('layouts.sme')

@section('content')
<div class="dash-head mb-6">
  <a href="{{ route('organization.clients.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-2 inline-block">&larr; Back to Clients</a>
  <h1 class="text-2xl font-bold text-gray-900">Edit Client: {{ $client->name }}</h1>
</div>

<form action="{{ route('organization.clients.update', $client) }}" method="POST" class="max-w-3xl">
    @csrf
    @method('PUT')
    <div class="panel">
        <h3 class="text-lg font-bold mb-4 border-b pb-2">Client Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name / Company Name *</label>
                <input type="text" name="name" value="{{ old('name', $client->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">
                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GST Number</label>
                <input type="text" name="gst_number" value="{{ old('gst_number', $client->gst_number) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500 font-mono">
                @error('gst_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">{{ old('address', $client->address) }}</textarea>
            @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-indigo-500">{{ old('notes', $client->notes) }}</textarea>
            @error('notes') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">Client is Active</span>
            </label>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <a href="{{ route('organization.clients.index') }}" class="btn border">Cancel</a>
            <button type="submit" class="btn btn-gold">Update Client</button>
        </div>
    </div>
</form>
@endsection
