@extends('layouts.super-admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Platform Overview</h1>
    <p class="text-gray-500 mt-1">High-level statistics across all organizations.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Total Organizations</div>
        <div class="text-3xl font-bold text-gray-900">{{ $orgCount }}</div>
        <div class="text-sm text-green-600 mt-2 font-medium">{{ $activeOrgs }} active</div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Total Platform Users</div>
        <div class="text-3xl font-bold text-gray-900">{{ $userCount }}</div>
        <div class="text-sm text-gray-500 mt-2 font-medium">Across all tenants</div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Platform Revenue</div>
        <div class="text-3xl font-bold text-gray-900">₹0</div>
        <div class="text-sm text-gray-500 mt-2 font-medium">This month (Placeholder)</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
    <div class="flex gap-4">
        <a href="{{ route('super-admin.organizations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 transition">Add New Organization</a>
        <a href="{{ route('super-admin.organizations.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition">View All Organizations</a>
    </div>
</div>
@endsection
