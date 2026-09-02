@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-xs border border-gray-200">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📍 Locations & Branches</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">Manage your multi-tenant business branches and operational sites.</p>
        </div>
        <a href="{{ route('organization.locations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl shadow-sm text-xs font-bold transition flex items-center gap-1.5">
            <span>+ Add New Location</span>
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-2xl border border-emerald-200 text-xs font-bold shadow-xs flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 px-4 py-3 rounded-2xl border border-rose-200 text-xs font-bold shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    <!-- Location Quota Limit Summary Banner -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 flex flex-wrap justify-between items-center gap-4 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-black text-xl shadow-2xs">
                🏢
            </div>
            <div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Subscription Branch Quota</div>
                <div class="text-base font-black text-gray-900 font-mono">
                    <span>{{ $locations->count() }} of {{ strtolower($maxLocations) === 'unlimited' ? 'Unlimited' : $maxLocations }} Locations Used</span>
                </div>
            </div>
        </div>

        @if(strtolower($maxLocations) !== 'unlimited' && $locations->count() >= (int)$maxLocations)
            <div class="flex items-center gap-2">
                <span class="text-xs text-amber-800 font-bold bg-amber-50 border border-amber-200 px-3.5 py-1.5 rounded-xl">
                    ⚠️ Plan Limit Reached ({{ $locations->count() }}/{{ $maxLocations }})
                </span>
                <a href="{{ route('organization.subscription.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-extrabold text-xs shadow-md transition">
                    ⚡ Upgrade Plan
                </a>
            </div>
        @endif
    </div>

    <!-- Locations Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-xs font-black text-gray-700 uppercase tracking-wider">Active Outlets Registry</h3>
            <span class="text-xs font-bold text-gray-500 font-mono">Total: {{ $locations->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50/50 text-[10px] uppercase font-bold text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="p-3.5 pl-5">Location Name</th>
                        <th class="p-3.5">Address & Phone</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 pl-5">
                                <div class="font-extrabold text-gray-900 text-sm flex items-center gap-2">
                                    <span>{{ $loc->name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="text-xs font-semibold text-gray-700">{{ $loc->address ?? 'No address specified' }}</div>
                                <div class="text-[11px] font-mono text-gray-500 mt-0.5">{{ $loc->phone ? '📞 '.$loc->phone : 'No phone' }}</div>
                            </td>
                            <td class="p-3.5">
                                @if($loc->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right pr-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('organization.locations.show', $loc) }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="View Location Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('organization.locations.edit', $loc) }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="Edit Location">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('organization.locations.toggle-status', $loc) }}" method="POST" onsubmit="return confirm('Toggle location status?');" class="inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 {{ $loc->is_active ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }} rounded-xl transition" title="{{ $loc->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($loc->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    @if($loc->employees_count === 0)
                                        <form action="{{ route('organization.locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this location?');" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete Location">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500 text-xs font-semibold">
                                No locations added yet. Click <b>"+ Add New Location"</b> to add your primary branch.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($locations->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

