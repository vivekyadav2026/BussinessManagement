@extends('layouts.sme')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Table & QR Management</h1>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">Generate digital scannable QR menus and manage restaurant dining tables.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('organization.menu.tables.print') }}" target="_blank" class="px-4 py-2.5 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print All QR Sheet</span>
            </a>
            <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add Table</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl border border-emerald-200 text-xs font-bold flex items-center justify-between shadow-xs">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 px-4 py-3 rounded-xl border border-rose-200 text-xs font-bold flex items-center justify-between shadow-xs">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </span>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl">
                🪑
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Tables</span>
                <span class="text-2xl font-black text-gray-900">{{ $tables->count() }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                📱
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Active QR Menus</span>
                <span class="text-2xl font-black text-emerald-600">{{ $tables->where('is_active', true)->count() }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xl">
                🚫
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Disabled Tables</span>
                <span class="text-2xl font-black text-slate-700">{{ $tables->where('is_active', false)->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="relative w-full sm:w-80">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="tableSearchInput" onkeyup="filterTables()" placeholder="Search tables by name..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50/50">
        </div>

        <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
            <button onclick="filterStatus('all')" id="btnFilterAll" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white transition-all">All Tables</button>
            <button onclick="filterStatus('active')" id="btnFilterActive" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Active Only</button>
            <button onclick="filterStatus('inactive')" id="btnFilterInactive" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Inactive</button>
        </div>
    </div>

    <!-- Tables Grid Container -->
    <div id="tablesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($tables as $table)
            @php
                $tablePublicUrl = route('public.menu.table', $table->public_token);
            @endphp
            <div class="table-card bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between relative {{ !$table->is_active ? 'opacity-80' : '' }}" 
                 data-name="{{ strtolower($table->name) }}" 
                 data-active="{{ $table->is_active ? 'active' : 'inactive' }}">
                
                <!-- Card Header -->
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-slate-50/60">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <span class="text-lg">🪑</span>
                        <h2 class="text-sm font-extrabold text-gray-900 truncate {{ !$table->is_active ? 'line-through text-gray-400' : '' }}" title="{{ $table->name }}">
                            {{ $table->name }}
                        </h2>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-tight uppercase shrink-0
                        {{ $table->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $table->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                        {{ $table->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </div>
                
                <!-- Card Body (QR Code & Actions) -->
                <div class="p-5 flex-grow flex flex-col items-center justify-center bg-white space-y-3">
                    
                    <!-- QR Box Frame -->
                    <div class="p-3.5 bg-gradient-to-b from-slate-50 to-indigo-50/30 border border-indigo-100 rounded-2xl shadow-inner flex flex-col items-center justify-center">
                        <div class="bg-white p-2 rounded-xl border border-gray-100 shadow-sm">
                            {!! QrCode::size(130)->margin(1)->generate($tablePublicUrl) !!}
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mt-2">Scan for Digital Menu</span>
                    </div>

                    <!-- Token Code Chip -->
                    <div class="text-center w-full px-2">
                        <span class="inline-block bg-slate-100 text-slate-600 text-[10px] font-mono font-semibold px-2.5 py-1 rounded-md max-w-full truncate border border-slate-200" title="{{ $table->public_token }}">
                            Token: {{ substr($table->public_token, 0, 14) }}...
                        </span>
                    </div>

                    <!-- Direct Quick Action Links -->
                    <div class="flex items-center gap-2 pt-1">
                        <button onclick="copyTableLink('{{ $tablePublicUrl }}')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition-colors flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Copy Link</span>
                        </button>
                        
                        <a href="{{ $tablePublicUrl }}" target="_blank" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-200 transition-colors flex items-center gap-1.5" title="Preview Digital Menu">
                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Preview</span>
                        </a>
                    </div>

                </div>

                <!-- Card Action Footer -->
                <div class="bg-slate-50/80 px-4 py-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    <button onclick="openEditTableModal({{ $table->id }}, '{{ addslashes($table->name) }}', {{ $table->is_active ? 1 : 0 }})" 
                            class="px-2.5 py-1 text-xs font-bold text-slate-700 hover:text-indigo-600 hover:bg-white rounded-md border border-slate-200 transition-all flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit</span>
                    </button>
                    
                    <form action="{{ route('organization.menu.tables.regenerate', $table) }}" method="POST" onsubmit="return confirm('Regenerating will invalidate the existing printed QR code. Continue?');" class="m-0">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 text-xs font-bold text-amber-700 hover:bg-white rounded-md border border-amber-200 transition-all flex items-center gap-1" title="Generate fresh QR Token">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Regenerate</span>
                        </button>
                    </form>

                    <form action="{{ route('organization.menu.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this table?');" class="m-0">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="px-2.5 py-1 text-xs font-bold text-rose-600 hover:bg-white rounded-md border border-rose-200 transition-all flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-2xl border-2 border-dashed border-gray-200 text-center shadow-xs">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    🪑
                </div>
                <h3 class="text-lg font-extrabold text-gray-900 mb-1">No Dining Tables Found</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mb-5">Create your first restaurant table to generate digital QR codes for your menu.</p>
                <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                    + Add New Table
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- Add Table Modal -->
<div id="add-table-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-md p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <span>🪑</span> Add Dining Table
            </h3>
            <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
        </div>

        <form action="{{ route('organization.menu.tables.store') }}" method="POST" class="space-y-4 m-0">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Table Name / Number *</label>
                <input type="text" name="name" placeholder="e.g., Table 01, VIP Corner, Terrace A" class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            
            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Save Table</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div id="edit-table-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-md p-6 space-y-5">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <span>✏️</span> Edit Table Details
            </h3>
            <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">&times;</button>
        </div>

        <form id="edit-table-form" method="POST" class="space-y-4 m-0">
            @csrf 
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Table Name / Number *</label>
                <input type="text" name="name" id="edit-table-name" class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 flex items-center justify-between">
                <div>
                    <label for="edit-table-active" class="text-xs font-extrabold text-gray-800 cursor-pointer">Active (Scannable Menu)</label>
                    <p class="text-[10px] font-medium text-gray-500">Allow customers to open menu via QR</p>
                </div>
                <input type="checkbox" name="is_active" id="edit-table-active" value="1" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">Update Table</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="copyToast" class="fixed bottom-6 right-6 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-2xl z-50 hidden transition-all flex items-center gap-2 text-xs font-bold">
    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>Menu link copied to clipboard! 📋</span>
</div>

<script>
    function openEditTableModal(id, name, isActive) {
        document.getElementById('edit-table-form').action = '/organization/menu/tables/' + id;
        document.getElementById('edit-table-name').value = name;
        document.getElementById('edit-table-active').checked = isActive ? true : false;
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

    function filterTables() {
        const query = document.getElementById('tableSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.table-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterStatus(status) {
        const btnAll = document.getElementById('btnFilterAll');
        const btnActive = document.getElementById('btnFilterActive');
        const btnInactive = document.getElementById('btnFilterInactive');

        btnAll.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all " + (status === 'all' ? "bg-indigo-600 text-white" : "bg-gray-100 text-gray-600 hover:bg-gray-200");
        btnActive.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all " + (status === 'active' ? "bg-indigo-600 text-white" : "bg-gray-100 text-gray-600 hover:bg-gray-200");
        btnInactive.className = "px-3 py-1.5 rounded-lg text-xs font-bold transition-all " + (status === 'inactive' ? "bg-indigo-600 text-white" : "bg-gray-100 text-gray-600 hover:bg-gray-200");

        const cards = document.querySelectorAll('.table-card');
        cards.forEach(card => {
            const activeState = card.getAttribute('data-active');
            if (status === 'all' || activeState === status) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
