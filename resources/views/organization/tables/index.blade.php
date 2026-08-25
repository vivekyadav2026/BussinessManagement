@extends('layouts.sme')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Table & QR Management</h1>
        <div class="flex gap-2">
            <a href="{{ route('organization.menu.tables.print') }}" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-medium">
                Print All QR Codes
            </a>
            <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-medium">
                + Add Table
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($tables as $table)
            <div class="bg-white rounded-lg shadow border overflow-hidden flex flex-col relative {{ !$table->is_active ? 'opacity-70' : '' }}">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 {{ !$table->is_active ? 'line-through' : '' }}">{{ $table->name }}</h2>
                    <span class="px-2 py-1 text-xs rounded {{ $table->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $table->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <div class="p-6 flex-grow flex flex-col items-center justify-center bg-white">
                    <div class="mb-4 border-4 border-gray-100 p-2 rounded-lg bg-white shadow-sm">
                        <!-- Use simple-qrcode -->
                        {!! QrCode::size(120)->generate(route('public.menu.table', $table->public_token)) !!}
                    </div>
                    <p class="text-xs text-gray-400 text-center break-all mb-2">{{ $table->public_token }}</p>
                    <a href="{{ route('public.menu.table', $table->public_token) }}" target="_blank" class="text-sm text-blue-600 hover:underline">Preview Link</a>
                </div>

                <div class="bg-gray-50 p-3 border-t flex justify-between items-center text-sm">
                    <button onclick="openEditTableModal({{ $table->id }}, '{{ $table->name }}', {{ $table->is_active }})" class="text-blue-600 hover:underline">Edit</button>
                    
                    <form action="{{ route('organization.menu.tables.regenerate', $table) }}" method="POST" onsubmit="return confirm('This will invalidate the old QR code. Are you sure?');">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:underline" title="Regenerate QR Token">Regenerate</button>
                    </form>

                    <form action="{{ route('organization.menu.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Delete this table?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center shadow rounded-lg border border-dashed border-gray-300">
                <p class="text-sm text-gray-500 mb-4">No tables found for this location.</p>
                <button onclick="document.getElementById('add-table-modal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-medium">
                    + Add Table
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Table Modal -->
<div id="add-table-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Add Table</h3>
        <form action="{{ route('organization.menu.tables.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name/Number *</label>
                <input type="text" name="name" placeholder="e.g., Table 1, VIP A" class="w-full border-gray-300 rounded" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-table-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div id="edit-table-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Edit Table</h3>
        <form id="edit-table-form" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name/Number *</label>
                <input type="text" name="name" id="edit-table-name" class="w-full border-gray-300 rounded" required>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_active" id="edit-table-active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-2">
                <label for="edit-table-active" class="text-sm font-medium text-gray-700">Active (scannable)</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-table-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
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
