@extends('layouts.sme')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Settings</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.locations.index') }}" class="hover:text-slate-900 transition-colors">Locations &amp; Outlets</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.locations.show', $location) }}" class="hover:text-slate-900 transition-colors">{{ $location->name }}</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Edit</span>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('organization.locations.index') }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 shadow-2xs transition">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight flex items-center gap-2">
                        <span>Edit Location: {{ $location->name }}</span>
                        @if($location->is_active)
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-300">
                            Active
                        </span>
                        @else
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-300">
                            Inactive
                        </span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-700 font-medium mt-0.5">
                        Modify operational contact specifications, billing address, or name.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('organization.locations.show', $location) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>View Profile</span>
            </a>
            <a href="{{ route('organization.locations.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-lg shadow-2xs transition">
                <span>Cancel</span>
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if(isset($errors) && $errors->any())
    <div class="bg-rose-50 border border-rose-300 rounded-xl p-4 text-xs text-rose-950">
        <div class="font-extrabold flex items-center gap-1.5 mb-1 text-sm">
            <span>⚠️</span> Please correct the following errors:
        </div>
        <ul class="list-disc pl-5 space-y-0.5 font-medium">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 2. Main 2-Column Form Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT FORM COLUMN (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <form action="{{ route('organization.locations.update', $location) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Card: Branch Details -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <h2 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Branch Details</h2>
                            <p class="text-[11px] text-slate-600 font-medium mt-0.5">Edit store identification and physical address.</p>
                        </div>
                        <span class="text-xs font-extrabold text-rose-600">* Required</span>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Branch Name -->
                        <div>
                            <label for="name" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Branch / Outlet Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $location->name) }}" 
                                   required 
                                   class="w-full border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-950 outline-none transition @error('name') border-rose-400 @enderror">
                            @error('name') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Contact Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                                    📞
                                </div>
                                <input type="text" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $location->phone) }}" 
                                       placeholder="e.g. +91 98765 43210" 
                                       class="w-full pl-9 pr-4 py-2.5 border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-xs font-semibold text-slate-950 outline-none transition @error('phone') border-rose-400 @enderror">
                            </div>
                            @error('phone') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Physical Address -->
                        <div>
                            <label for="address" class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1.5">
                                Full Physical Address
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3" 
                                      class="w-full border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-950 outline-none transition @error('address') border-rose-400 @enderror">{{ old('address', $location->address) }}</textarea>
                            @error('address') 
                                <span class="text-xs font-bold text-rose-600 mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="bg-slate-50 border-t border-slate-200 p-4 flex items-center justify-between">
                        <a href="{{ route('organization.locations.index') }}" class="px-4 py-2 border border-slate-300 text-slate-800 bg-white hover:bg-slate-50 rounded-lg font-bold text-xs shadow-2xs transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 rounded-lg font-extrabold text-xs shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Location Changes</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- RIGHT SIDEBAR: Status & Controls (4 cols) -->
        <div class="lg:col-span-4 space-y-5">

            <!-- Operational Status Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Branch Status</h3>
                    @if($location->is_active)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-900 border border-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Operational
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Inactive
                    </span>
                    @endif
                </div>

                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    {{ $location->is_active ? 'This branch is currently accepting POS orders, staff check-ins, and inventory records.' : 'This branch is currently marked inactive and hidden from day-to-day operations.' }}
                </p>

                <form action="{{ route('organization.locations.toggle-status', $location) }}" method="POST" onsubmit="return confirm('Toggle operational status for this location?');" class="pt-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-slate-300 text-xs font-bold rounded-lg text-slate-800 bg-white hover:bg-slate-50 transition shadow-2xs">
                        {{ $location->is_active ? 'Deactivate Branch' : 'Activate Branch' }}
                    </button>
                </form>
            </div>

            <!-- Staff Deployment Summary -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-950 uppercase tracking-wider">Assigned Staff</h3>
                    <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-800 border border-indigo-200">
                        {{ $location->employees_count ?? $location->employees()->count() }} Staff
                    </span>
                </div>
                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    Staff members currently assigned to this branch location.
                </p>
                <a href="{{ route('organization.employees.index', ['location_id' => $location->id]) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-amber-800 hover:text-amber-900">
                    <span>Manage branch staff</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- Danger Zone / Delete Branch -->
            @php $empCount = $location->employees_count ?? $location->employees()->count(); @endphp
            @if($empCount === 0)
            <div class="bg-rose-50/60 rounded-xl border border-rose-200/90 p-5 space-y-3">
                <div>
                    <h4 class="text-xs font-extrabold text-rose-950 uppercase tracking-wider">Delete Location</h4>
                    <p class="text-[11px] text-rose-800 font-medium mt-1 leading-relaxed">
                        This location has no active employees attached and can be permanently removed.
                    </p>
                </div>
                <form action="{{ route('organization.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Permanently delete {{ $location->name }}? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-center px-4 py-2 border border-rose-300 text-xs font-bold rounded-lg text-rose-700 bg-white hover:bg-rose-100 hover:border-rose-400 focus:outline-none transition shadow-2xs">
                        Delete Location Permanently
                    </button>
                </form>
            </div>
            @else
            <div class="bg-slate-50 rounded-xl border border-slate-200/80 p-4">
                <div class="flex items-start gap-2 text-xs text-slate-700">
                    <span class="text-slate-500 font-bold">🔒</span>
                    <p class="text-[11px] leading-relaxed">
                        This location has <strong>{{ $empCount }} active employee(s)</strong> attached. You must reassign or remove them before this location can be deleted.
                    </p>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection
