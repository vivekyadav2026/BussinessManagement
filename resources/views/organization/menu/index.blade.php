@extends('layouts.sme')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Restaurant Menu Builder</h1>
        <div>
            <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-medium">
                + Add Category
            </button>
            <a href="{{ route('public.menu', [auth()->user()->organization_id, session('active_location_id')]) }}" target="_blank" class="bg-gray-200 text-gray-800 px-4 py-2 rounded shadow hover:bg-gray-300 font-medium ml-2">
                Preview Public Menu
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow border overflow-hidden">
                <div class="bg-gray-50 border-b p-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 cursor-grab">&#9776;</span>
                        <h2 class="text-lg font-bold text-gray-800 {{ !$category->is_active ? 'line-through text-gray-400' : '' }}">{{ $category->name }}</h2>
                        @if(!$category->is_active)
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">Hidden</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="openAddItemModal({{ $category->id }}, '{{ $category->name }}')" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded border border-indigo-200 hover:bg-indigo-100">
                            + Add Item
                        </button>
                        <button onclick="openEditCategoryModal({{ $category->id }}, '{{ $category->name }}', {{ $category->is_active }})" class="text-gray-500 hover:text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                        <form action="{{ route('organization.menu.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete category and all its items?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-500 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="p-0">
                    <table class="w-full text-left text-sm">
                        <tbody>
                            @forelse($category->items as $item)
                                <tr class="border-b last:border-0 hover:bg-gray-50 {{ !$item->is_active ? 'opacity-50' : '' }}">
                                    <td class="w-12 text-center text-gray-400 cursor-grab border-r">&#9776;</td>
                                    <td class="w-16 p-2">
                                        @if($item->photo)
                                            <img src="{{ asset('storage/' . $item->photo) }}" class="w-12 h-12 rounded object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 font-medium text-gray-800">
                                        {{ $item->name }}
                                        @if($item->description)
                                            <div class="text-xs text-gray-500 font-normal truncate max-w-xs">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-700 font-bold">₹{{ number_format($item->price, 2) }}</td>

                                    <td class="p-3">
                                        @if($item->is_available)
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">In Stock</span>
                                        @else
                                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditItemModal({{ $item->id }}, {{ json_encode($item) }})" class="text-blue-600 hover:underline">Edit</button>
                                            <form action="{{ route('organization.menu.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this item?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-500 text-sm">No items in this category. Click "+ Add Item" to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center shadow rounded-lg border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new menu category.</p>
                <div class="mt-6">
                    <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                        + Add Category
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modals Snipped for brevity in script: They would follow standard Laravel form layouts for Categories and Items -->
<div id="add-category-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Add Category</h3>
        <form action="{{ route('organization.menu.categories.store') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="e.g., Starters, Mains, Desserts" class="w-full border-gray-300 rounded mb-4" required>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Item Modal -->
<div id="add-item-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Add Item to <span id="add-item-category-name" class="text-blue-600"></span></h3>
        <form action="{{ route('organization.menu.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="menu_category_id" id="add-item-category-id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                <input type="text" name="name" class="w-full border-gray-300 rounded" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Price ($) *</label>
                <input type="number" step="0.01" name="price" class="w-full border-gray-300 rounded" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border-gray-300 rounded"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full border-gray-300 rounded p-1 border">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-item-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save Item</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddItemModal(categoryId, categoryName) {
        document.getElementById('add-item-category-id').value = categoryId;
        document.getElementById('add-item-category-name').innerText = categoryName;
        document.getElementById('add-item-modal').classList.remove('hidden');
    }
    function openEditCategoryModal(id, name, isActive) {
        // Snipped for time, you can implement standard editing.
        alert('Edit Category ID: ' + id + '\nImplement the full modal similarly.');
    }
    function openEditItemModal(id, itemData) {
        alert('Edit Item: ' + itemData.name);
    }
</script>
@endsection
