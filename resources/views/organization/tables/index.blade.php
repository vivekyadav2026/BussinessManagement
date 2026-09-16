@extends('layouts.sme')

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Breadcrumb -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Restaurant Management</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">Dining Areas</span>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-2xl font-black text-slate-950 tracking-tight">Table & QR Management</h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                    <span>📍</span> {{ session('active_location_name') ?? 'Main Floor' }}
                </span>
            </div>
            <p class="text-xs text-slate-600 mt-0.5">Manage floor tables, generate contactless QR code menus, and print table tent standees.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('organization.menu.tables.print') }}" target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs rounded-xl border border-slate-200/90 shadow-2xs transition whitespace-nowrap">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print All Standees</span>
            </a>

            <button type="button" onclick="document.getElementById('add-table-modal').classList.remove('hidden')" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-xs transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>+ Add Dining Table</span>
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-center justify-between text-xs font-bold shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 text-xs font-bold shadow-2xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-rose-500/20 text-rose-800 flex items-center justify-center shrink-0">
                    ✕
                </div>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 font-bold">&times;</button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    @php
        $totalCount = $tables->count();
        $activeCount = $tables->where('is_active', true)->count();
        $disabledCount = $tables->where('is_active', false)->count();
        $coverageRate = $totalCount > 0 ? round(($activeCount / $totalCount) * 100) : 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tables -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Floor Tables</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                    🪑
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2 font-mono">{{ number_format($totalCount) }}</div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Configured dining spaces</p>
        </div>

        <!-- Active QR Menus -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700">Live QR Menus</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-2xl font-black text-slate-950 font-mono">{{ number_format($activeCount) }}</span>
                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Scannable
                </span>
            </div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Guests can self-order online</p>
        </div>

        <!-- Disabled Tables -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Offline / Disabled</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2 font-mono">{{ number_format($disabledCount) }}</div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Temporarily shut down</p>
        </div>

        <!-- Active Coverage Rate -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-700">Active QR Coverage</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-xs">
                    ⚡
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2 font-mono">{{ $coverageRate }}%</div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Operational table ratio</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row justify-between items-center gap-3">
        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" 
                   id="tableSearchInput" 
                   onkeyup="filterTables()" 
                   placeholder="Search tables by name or token..." 
                   class="w-full pl-10 pr-9 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white placeholder:text-slate-400 outline-none transition">
            <button type="button" onclick="clearSearch()" id="clearSearchBtn" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400 hover:text-slate-700 hidden">
                ✕
            </button>
        </div>

        <!-- Filter Buttons & View Mode -->
        <div class="flex items-center justify-between md:justify-end gap-2.5 w-full md:w-auto">
            <!-- Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto">
                <button type="button" onclick="filterStatus('all')" id="btnFilterAll" class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-slate-950 text-white transition whitespace-nowrap cursor-pointer">
                    All ({{ $totalCount }})
                </button>
                <button type="button" onclick="filterStatus('active')" id="btnFilterActive" class="px-3.5 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition whitespace-nowrap cursor-pointer">
                    Active ({{ $activeCount }})
                </button>
                <button type="button" onclick="filterStatus('inactive')" id="btnFilterInactive" class="px-3.5 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition whitespace-nowrap cursor-pointer">
                    Disabled ({{ $disabledCount }})
                </button>
            </div>

            <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

            <!-- View Toggle: Grid vs List -->
            <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 shrink-0">
                <button type="button" onclick="switchView('grid')" id="viewBtnGrid" class="p-1.5 rounded-lg bg-white text-slate-950 shadow-xs transition cursor-pointer" title="Grid View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button type="button" onclick="switchView('list')" id="viewBtnList" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition cursor-pointer" title="List View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- VIEW 1: TABLES GRID CONTAINER -->
    <div id="tablesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($tables as $table)
            @php
                $tablePublicUrl = route('public.menu.table', $table->public_token);
            @endphp
            <div class="table-card bg-white rounded-3xl border border-slate-200/90 shadow-2xs hover:shadow-lg hover:border-amber-400/80 transition-all duration-200 overflow-hidden flex flex-col justify-between relative {{ !$table->is_active ? 'opacity-70 bg-slate-50/40' : '' }}" 
                 data-name="{{ strtolower($table->name) }}" 
                 data-token="{{ strtolower($table->public_token) }}"
                 data-active="{{ $table->is_active ? 'active' : 'inactive' }}">
                
                <!-- Card Header -->
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/70">
                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $table->is_active ? 'bg-amber-500' : 'bg-slate-400' }} shrink-0"></span>
                        <h2 class="text-sm font-black text-slate-950 truncate {{ !$table->is_active ? 'line-through text-slate-400' : '' }}" title="{{ $table->name }}">
                            {{ $table->name }}
                        </h2>
                    </div>

                    @if($table->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300 whitespace-nowrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-200 text-slate-800 border border-slate-300 whitespace-nowrap">
                            Disabled
                        </span>
                    @endif
                </div>
                
                <!-- Card Body (QR Code & Quick Links) -->
                <div class="p-5 flex-grow flex flex-col items-center justify-center bg-white space-y-3.5">
                    <!-- QR Box Frame -->
                    <div class="p-3.5 bg-slate-50 border border-slate-200/90 rounded-2xl shadow-inner flex flex-col items-center justify-center w-full max-w-[200px]">
                        <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-2xs">
                            {!! QrCode::size(125)->margin(1)->generate($tablePublicUrl) !!}
                        </div>
                        <span class="text-[10px] font-black text-slate-500 tracking-wider uppercase mt-2 font-mono">
                            Scan to Order
                        </span>
                    </div>

                    <!-- Token Code Chip -->
                    <div class="text-center w-full px-2">
                        <span class="inline-block bg-slate-100 text-slate-700 text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg border border-slate-200 max-w-full truncate" title="{{ $table->public_token }}">
                            Token: {{ substr($table->public_token, 0, 14) }}...
                        </span>
                    </div>

                    <!-- Direct Quick Action Links -->
                    <div class="grid grid-cols-2 gap-2 pt-1 w-full">
                        <button type="button" 
                                onclick="copyTableLink('{{ $tablePublicUrl }}')" 
                                class="py-2 bg-slate-100 hover:bg-slate-200 text-slate-900 text-xs font-bold rounded-xl border border-slate-200 transition flex justify-center items-center gap-1.5 cursor-pointer whitespace-nowrap" 
                                title="Copy Menu Link">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Copy Link</span>
                        </button>
                        
                        <a href="{{ $tablePublicUrl }}" target="_blank" 
                           class="py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black rounded-xl transition flex justify-center items-center gap-1.5 whitespace-nowrap shadow-xs" 
                           title="Preview Digital Menu">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Preview</span>
                        </a>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="bg-slate-50/80 px-4 py-2.5 border-t border-slate-100 flex items-center justify-between gap-1 text-xs">
                    <!-- Print Individual Standee -->
                    <a href="{{ route('organization.menu.tables.print', ['table_id' => $table->id]) }}" target="_blank"
                       class="p-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-950 rounded-xl transition cursor-pointer shadow-2xs flex items-center gap-1"
                       title="Print QR Standee Card">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </a>

                    <!-- Edit Table Details -->
                    <button type="button" 
                            onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->name) }}', {{ $table->is_active ? 1 : 0 }})" 
                            class="p-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-950 rounded-xl transition cursor-pointer shadow-2xs flex items-center gap-1" 
                            title="Edit Table">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    
                    <!-- Regenerate Token -->
                    <form action="{{ route('organization.menu.tables.regenerate', $table) }}" method="POST" onsubmit="return confirm('Regenerating will invalidate the existing printed QR code. Continue?');" class="m-0">
                        @csrf
                        <button type="submit" 
                                class="p-2 border border-amber-200 bg-white hover:bg-amber-50 text-amber-800 rounded-xl transition cursor-pointer shadow-2xs flex items-center gap-1" 
                                title="Regenerate QR Token">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </form>

                    <!-- Delete Table -->
                    <form action="{{ route('organization.menu.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?');" class="m-0">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 border border-rose-200 bg-white hover:bg-rose-50 text-rose-700 rounded-xl transition cursor-pointer shadow-2xs flex items-center gap-1" 
                                title="Delete Table">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-slate-300 text-center shadow-2xs space-y-4">
                <div class="w-16 h-16 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center mx-auto text-3xl font-bold">
                    🪑
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-950">No Dining Tables Configured</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 font-medium">Create your first dining table to generate scannable contactless QR codes for your restaurant menu.</p>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow-xs transition cursor-pointer">
                        + Add Dining Table
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- VIEW 2: TABLES LIST TABLE (HIDDEN BY DEFAULT) -->
    <div id="tablesList" class="hidden bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider font-extrabold text-[10px]">
                        <th class="py-3.5 px-5">Table Name</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">QR Preview</th>
                        <th class="py-3.5 px-4">Direct Token & URL</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tables as $table)
                        @php
                            $tablePublicUrl = route('public.menu.table', $table->public_token);
                        @endphp
                        <tr class="table-list-row hover:bg-slate-50/60 transition {{ !$table->is_active ? 'opacity-60 bg-slate-50/30' : '' }}"
                            data-name="{{ strtolower($table->name) }}"
                            data-token="{{ strtolower($table->public_token) }}"
                            data-active="{{ $table->is_active ? 'active' : 'inactive' }}">
                            
                            <!-- Table Name -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $table->is_active ? 'bg-amber-500' : 'bg-slate-400' }}"></span>
                                    <div>
                                        <span class="font-black text-slate-950 block text-xs {{ !$table->is_active ? 'line-through text-slate-400' : '' }}">{{ $table->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">ID #{{ $table->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($table->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-200 text-slate-800 border border-slate-300">
                                        Disabled
                                    </span>
                                @endif
                            </td>

                            <!-- QR Thumbnail -->
                            <td class="py-3.5 px-4">
                                <div class="w-10 h-10 bg-white p-1 rounded-lg border border-slate-200 shadow-2xs flex items-center justify-center">
                                    {!! QrCode::size(34)->margin(0)->generate($tablePublicUrl) !!}
                                </div>
                            </td>

                            <!-- Token & URL -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-1">
                                    <span class="font-mono text-[11px] font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                        {{ substr($table->public_token, 0, 16) }}...
                                    </span>
                                    <div class="flex items-center gap-2 pt-0.5">
                                        <button type="button" onclick="copyTableLink('{{ $tablePublicUrl }}')" class="text-amber-600 hover:text-amber-800 font-bold text-[11px] cursor-pointer">
                                            Copy Link
                                        </button>
                                        <span class="text-slate-300">&bull;</span>
                                        <a href="{{ $tablePublicUrl }}" target="_blank" class="text-slate-600 hover:text-slate-900 font-bold text-[11px]">
                                            Preview &rarr;
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('organization.menu.tables.print', ['table_id' => $table->id]) }}" target="_blank" 
                                       class="p-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl transition shadow-2xs" title="Print Standee">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>

                                    <button type="button" 
                                            onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->name) }}', {{ $table->is_active ? 1 : 0 }})" 
                                            class="p-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl transition shadow-2xs" title="Edit Table">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <form action="{{ route('organization.menu.tables.regenerate', $table) }}" method="POST" onsubmit="return confirm('Regenerating will invalidate the existing printed QR code. Continue?');" class="m-0">
                                        @csrf
                                        <button type="submit" class="p-2 border border-amber-200 bg-white hover:bg-amber-50 text-amber-800 rounded-xl transition shadow-2xs" title="Regenerate QR">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('organization.menu.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?');" class="m-0">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-rose-200 bg-white hover:bg-rose-50 text-rose-700 rounded-xl transition shadow-2xs" title="Delete Table">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-bold">No tables found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Add Table Modal -->
<div id="add-table-modal" class="hidden fixed inset-0 bg-slate-950/70 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-md p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">Add Dining Table</h3>
            </div>
            <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm transition">&times;</button>
        </div>

        <form action="{{ route('organization.menu.tables.store') }}" method="POST" class="space-y-4 m-0">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-950 uppercase tracking-wider mb-1.5">Table Name / Floor Identifier <span class="text-rose-600">*</span></label>
                <input type="text" 
                       name="name" 
                       placeholder="e.g. Table 01, VIP Lounge 2, Terrace 4" 
                       class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition bg-white placeholder:text-slate-400" 
                       required autofocus>
                <p class="text-[11px] text-slate-500 font-medium mt-1">This name will be printed on the QR standee and customer order receipts.</p>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="px-4 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black rounded-xl text-xs shadow-xs transition cursor-pointer uppercase tracking-wider">Save Table & Generate QR</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div id="edit-table-modal" class="hidden fixed inset-0 bg-slate-950/70 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-md p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">Edit Table Details</h3>
            </div>
            <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm transition">&times;</button>
        </div>

        <form id="edit-table-form" method="POST" class="space-y-4 m-0">
            @csrf 
            @method('PUT')
            
            <div>
                <label class="block text-xs font-black text-slate-950 uppercase tracking-wider mb-1.5">Table Name / Identifier <span class="text-rose-600">*</span></label>
                <input type="text" name="name" id="edit-table-name" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-950 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none transition bg-white" required>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-between">
                <div>
                    <label for="edit-table-active" class="text-xs font-black text-slate-950 cursor-pointer block">Active (Scannable Menu)</label>
                    <p class="text-[11px] font-medium text-slate-500">Allow customers to open menu and place orders from this table</p>
                </div>
                <input type="checkbox" name="is_active" id="edit-table-active" value="1" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-500 cursor-pointer">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="px-4 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-slate-950 font-black rounded-xl text-xs shadow-xs transition cursor-pointer uppercase tracking-wider">Update Table</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification Container -->
<div id="copyToast" class="fixed bottom-6 right-6 bg-slate-950 text-white px-4 py-3 rounded-2xl shadow-2xl z-50 hidden transition-all flex items-center gap-2.5 text-xs font-black border border-slate-800">
    <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs">
        ✓
    </div>
    <span>Table menu link copied to clipboard!</span>
</div>

<script>
    let currentStatusFilter = 'all';

    function openEditTableModal(id, name, isActive) {
        document.getElementById('edit-table-form').action = '/organization/menu/tables/' + id;
        document.getElementById('edit-table-name').value = name;
        document.getElementById('edit-table-active').checked = Boolean(isActive);
        document.getElementById('edit-table-modal').classList.remove('hidden');
    }

    function copyTableLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            const toast = document.getElementById('copyToast');
            toast.classList.remove('hidden');
            setTimeout(function() {
                toast.classList.add('hidden');
            }, 2500);
        }).catch(function(err) {
            alert('Menu URL: ' + url);
        });
    }

    function clearSearch() {
        document.getElementById('tableSearchInput').value = '';
        document.getElementById('clearSearchBtn').classList.add('hidden');
        filterTables();
    }

    function switchView(mode) {
        const grid = document.getElementById('tablesGrid');
        const list = document.getElementById('tablesList');
        const btnGrid = document.getElementById('viewBtnGrid');
        const btnList = document.getElementById('viewBtnList');

        if (mode === 'grid') {
            grid.classList.remove('hidden');
            list.classList.add('hidden');
            btnGrid.className = "p-1.5 rounded-lg bg-white text-slate-950 shadow-xs transition cursor-pointer";
            btnList.className = "p-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition cursor-pointer";
        } else {
            grid.classList.add('hidden');
            list.classList.remove('hidden');
            btnGrid.className = "p-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition cursor-pointer";
            btnList.className = "p-1.5 rounded-lg bg-white text-slate-950 shadow-xs transition cursor-pointer";
        }
    }

    function filterTables() {
        const query = document.getElementById('tableSearchInput').value.toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (query) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }

        const cards = document.querySelectorAll('.table-card');
        const listRows = document.querySelectorAll('.table-list-row');

        const filterElement = (el) => {
            const name = el.getAttribute('data-name') || '';
            const token = el.getAttribute('data-token') || '';
            const active = el.getAttribute('data-active');

            const matchesQuery = !query || name.includes(query) || token.includes(query);
            const matchesStatus = currentStatusFilter === 'all' || active === currentStatusFilter;

            return matchesQuery && matchesStatus;
        };

        cards.forEach(card => {
            card.style.display = filterElement(card) ? 'flex' : 'none';
        });

        listRows.forEach(row => {
            row.style.display = filterElement(row) ? '' : 'none';
        });
    }

    function filterStatus(status) {
        currentStatusFilter = status;
        const btnAll = document.getElementById('btnFilterAll');
        const btnActive = document.getElementById('btnFilterActive');
        const btnInactive = document.getElementById('btnFilterInactive');

        [btnAll, btnActive, btnInactive].forEach(btn => {
            btn.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition whitespace-nowrap cursor-pointer";
        });

        if (status === 'all') {
            btnAll.className = "px-3.5 py-1.5 rounded-xl text-xs font-black bg-slate-950 text-white transition whitespace-nowrap cursor-pointer";
        } else if (status === 'active') {
            btnActive.className = "px-3.5 py-1.5 rounded-xl text-xs font-black bg-slate-950 text-white transition whitespace-nowrap cursor-pointer";
        } else if (status === 'inactive') {
            btnInactive.className = "px-3.5 py-1.5 rounded-xl text-xs font-black bg-slate-950 text-white transition whitespace-nowrap cursor-pointer";
        }

        filterTables();
    }
</script>
@endsection
