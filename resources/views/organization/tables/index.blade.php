@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Table & QR Management</h1>
        <p class="text-gray-500 mt-1">Manage dining tables, generate digital QR menus, and track scannable links.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('organization.menu.tables.print') }}" target="_blank" class="btn btn-ghost text-xs">
            Print All QR Codes
        </a>
        <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="btn btn-gold py-2.5 px-6 shadow-sm">
            + Add Table
        </button>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse($tables as $table)
        <div class="panel flex flex-col relative {{ !$table->is_active ? 'opacity-70' : '' }} overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/55">
                <h2 class="text-base font-bold text-gray-900 {{ !$table->is_active ? 'line-through text-gray-400' : '' }}">{{ $table->name }}</h2>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $table->is_active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                    {{ $table->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            
            <div class="p-6 flex-grow flex flex-col items-center justify-center bg-white">
                <div class="mb-4 border-4 border-gray-50 p-3 rounded-xl bg-white shadow-sm">
                    <!-- Use simple-qrcode -->
                    {!! QrCode::size(120)->generate(route('public.menu.table', $table->public_token)) !!}
                </div>
                <p class="text-[10px] text-gray-400 text-center break-all mb-3 font-mono">{{ $table->public_token }}</p>
                <a href="{{ route('public.menu.table', $table->public_token) }}" target="_blank" class="text-xs text-indigo-600 font-bold hover:underline">Preview Menu Link</a>
            </div>

            <div class="bg-gray-50/70 p-3 border-t border-gray-100 flex justify-between items-center text-xs">
                <button onclick="openEditTableModal({{ $table->id }}, '{{ $table->name }}', {{ $table->is_active }})" class="text-indigo-600 font-bold hover:underline">Edit</button>
                
                <form action="{{ route('organization.menu.tables.regenerate', $table) }}" method="POST" onsubmit="return confirm('This will invalidate the old QR code. Are you sure?');">
                    @csrf
                    <button type="submit" class="text-yellow-600 font-bold hover:underline" title="Regenerate QR Token">Regenerate</button>
                </form>

                <form action="{{ route('organization.menu.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Delete this table?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 font-bold hover:underline">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full panel p-12 text-center border-dashed border-2 border-gray-200 shadow-sm">
            <p class="text-sm text-gray-500 mb-4 font-medium">No dining tables found for this location.</p>
            <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="btn btn-gold py-2.5 px-6 shadow-sm">
                + Add Table
            </button>
        </div>
    @endforelse
</div>

<!-- Add Table Modal -->
<div id="add-table-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-xl bg-white border-gray-100">
        <h3 class="text-lg font-bold mb-4 text-gray-900 border-b border-gray-100 pb-2">Add Table</h3>
        <form action="{{ route('organization.menu.tables.store') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Table Name/Number *</label>
                <input type="text" name="name" placeholder="e.g., Table 1, VIP A" class="w-full border-gray-300 rounded-lg text-sm" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="btn btn-ghost py-2 text-xs">Cancel</button>
                <button type="submit" class="btn btn-gold py-2 text-xs">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div id="edit-table-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-xl bg-white border-gray-100">
        <h3 class="text-lg font-bold mb-4 text-gray-900 border-b border-gray-100 pb-2">Edit Table</h3>
        <form id="edit-table-form" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Table Name/Number *</label>
                <input type="text" name="name" id="edit-table-name" class="w-full border-gray-300 rounded-lg text-sm" required>
            </div>
            <div class="mb-5 flex items-center">
                <input type="checkbox" name="is_active" id="edit-table-active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-2">
                <label for="edit-table-active" class="text-xs font-bold text-gray-700 uppercase tracking-wider">Active (scannable)</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="btn btn-ghost py-2 text-xs">Cancel</button>
                <button type="submit" class="btn btn-gold py-2 text-xs">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditTableModal(id, name, isActive) {
        document.getElementById('edit-table-form').action = '/organization/menu/tables/' + id;
        document.getElementById('edit-table-name').value = name;
        document.getElementById('edit-table-active').checked = isActive ? true : false;
        document.getElementById('edit-table-modal').classList.remove('hidden');
    }
</script>
@endsection
