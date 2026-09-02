@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-xs border border-gray-200">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📍 Branch Workspace: {{ $location->name }}</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">Location profile, staff roster, and operational status.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('organization.locations.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl shadow-xs transition">
                &larr; Back to Locations
            </a>
            <a href="{{ route('organization.locations.edit', $location) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-sm transition">
                ✏️ Edit Location
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info Workspace -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Information Card -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider border-b pb-3">Location Specifications</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Branch Outlet Name</label>
                        <div class="mt-1 text-base font-black text-gray-900">{{ $location->name }}</div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Contact Phone</label>
                        <div class="mt-1 text-sm font-black font-mono text-gray-900">{{ $location->phone ?: 'Not provided' }}</div>
                    </div>
                </div>
                
                <div class="bg-slate-50 p-4 rounded-2xl border border-gray-100">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider">Physical Address</label>
                    <div class="mt-1 text-xs text-gray-800 font-semibold leading-relaxed">{{ $location->address ?: 'No physical address recorded.' }}</div>
                </div>
            </div>
            
            <!-- Staff Roster Card -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Staff Assigned to Branch</h3>
                    @php $employees = $location->employees; @endphp
                    <span class="text-xs font-bold font-mono text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $employees ? $employees->count() : 0 }} Members</span>
                </div>

                @if($employees && $employees->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-400 border-b">
                                <tr>
                                    <th class="p-3">Employee Name</th>
                                    <th class="p-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($employees as $emp)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-3 font-extrabold text-gray-900">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                        <td class="p-3 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $emp->status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $emp->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-xs font-medium py-4 text-center">No employees are currently assigned to this branch location.</p>
                @endif
            </div>
        </div>
        
        <!-- Sidebar Actions Panel -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider border-b pb-3">Branch Status & Actions</h3>
                
                <div class="bg-slate-50 p-4 rounded-2xl border border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Current Status:</span>
                    @if($location->is_active)
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">Active</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-rose-100 text-rose-800 border border-rose-200">Inactive</span>
                    @endif
                </div>
                
                <div class="space-y-3 pt-2">
                    <form action="{{ route('organization.locations.toggle-status', $location) }}" method="POST" class="m-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full text-center px-4 py-3 border border-gray-300 text-xs font-extrabold rounded-2xl text-gray-800 bg-white hover:bg-gray-50 shadow-2xs transition">
                            Toggle Active Status
                        </button>
                    </form>
                    
                    @if(!$employees || $employees->count() === 0)
                        <form action="{{ route('organization.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-center px-4 py-3 border border-rose-200 text-xs font-extrabold rounded-2xl text-rose-700 bg-rose-50 hover:bg-rose-100 transition">
                                Delete Branch Location
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
