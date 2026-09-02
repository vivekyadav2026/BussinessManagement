@extends('layouts.sme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl shadow-xs border border-gray-200">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>🍽️ Restaurant Menu Builder</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">Manage categories, dishes, prices, photos, and stock availability.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl shadow-sm text-xs font-bold transition flex items-center gap-1.5">
                <span>+ Add Category</span>
            </button>
            <a href="{{ route('public.menu', [auth()->user()->organization_id, session('active_location_id')]) }}" target="_blank" class="bg-gray-900 hover:bg-black text-white px-4 py-2.5 rounded-xl shadow-sm text-xs font-bold transition flex items-center gap-1.5">
                <span>🌐 Preview Public Menu</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-4 rounded-2xl border border-emerald-200 text-xs font-bold shadow-xs flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 p-4 rounded-2xl border border-rose-200 text-xs font-bold shadow-xs">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-4 rounded-2xl border border-rose-200 text-xs font-bold shadow-xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <!-- Menu Categories & Items List -->
    <div class="space-y-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
                <!-- Category Header -->
                <div class="bg-slate-50 border-b border-gray-200 p-4 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-bold cursor-grab">⋮⋮</span>
                        <h2 class="text-lg font-black text-gray-900 tracking-tight {{ !$category->is_active ? 'line-through text-gray-400' : '' }}">
                            {{ $category->name }}
                        </h2>
                        @if(!$category->is_active)
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-gray-200 text-gray-700">Hidden</span>
                        @else
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">Active</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="openAddItemModal({{ $category->id }}, '{{ addslashes($category->name) }}')" class="text-xs bg-indigo-50 text-indigo-700 font-bold px-3 py-1.5 rounded-xl border border-indigo-200 hover:bg-indigo-100 transition flex items-center gap-1">
                            <span>+ Add Dish Item</span>
                        </button>

                        <button onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->is_active ? 'true' : 'false' }})" class="p-1.5 text-gray-600 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition" title="Edit Category">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>

                        <form action="{{ route('organization.menu.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category and all its dishes?');" class="inline m-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Category">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Category Dish Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-gray-50/50 text-[10px] uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="p-3 w-12 text-center">#</th>
                                <th class="p-3 w-16">Photo</th>
                                <th class="p-3">Dish Name & Details</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Stock Status</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($category->items as $item)
                                <tr class="hover:bg-slate-50/80 transition {{ !$item->is_active ? 'opacity-50' : '' }}">
                                    <td class="p-3 text-center text-gray-400 font-bold">⋮⋮</td>
                                    <td class="p-3">
                                        @if($item->photo)
                                            <img src="{{ asset('storage/' . $item->photo) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-2xs">
                                        @else
                                            <div class="w-12 h-12 bg-indigo-50/60 rounded-xl flex items-center justify-center text-indigo-400 text-lg border border-indigo-100">
                                                🍱
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="font-extrabold text-gray-900 text-sm">{{ $item->name }}</div>
                                        @if($item->description)
                                            <div class="text-xs text-gray-500 font-medium line-clamp-1 max-w-sm">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-indigo-700 font-black font-mono text-base">₹{{ number_format($item->price, 2) }}</td>

                                    <td class="p-3">
                                        @if($item->is_available)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                In Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                                Out of Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick='openEditItemModal({{ $item->id }}, @json($item))' class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold rounded-lg text-xs transition border border-indigo-200">
                                                ✏️ Edit
                                            </button>

                                            <form action="{{ route('organization.menu.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this dish item?');" class="inline m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-lg text-xs transition border border-rose-200">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500 text-xs font-semibold">
                                        No dish items in this category yet. Click <b>"+ Add Dish Item"</b> to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center shadow-xs rounded-2xl border border-dashed border-gray-300 space-y-4">
                <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto text-3xl text-indigo-600">
                    📂
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">No Categories Created Yet</h3>
                    <p class="text-xs text-gray-500 font-medium">Get started by creating your first restaurant menu category (e.g. Starters, Main Course, Drinks).</p>
                </div>
                <div>
                    <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="inline-flex items-center px-5 py-2.5 border border-transparent shadow-sm text-xs font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        + Add Category
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Add Category Modal -->
<div id="add-category-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border w-full max-w-md shadow-2xl rounded-3xl bg-white">
        <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-2">Add New Menu Category</h3>
        <form action="{{ route('organization.menu.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Category Name *</label>
                <input type="text" name="name" placeholder="e.g., Starters, Main Course, Beverages" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3" required>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-extrabold rounded-xl text-xs hover:bg-indigo-700 shadow-md transition">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Category Modal -->
<div id="edit-category-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border w-full max-w-md shadow-2xl rounded-3xl bg-white">
        <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-2">Edit Category</h3>
        <form id="edit-category-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Category Name *</label>
                <input type="text" id="edit-category-name" name="name" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3" required>
            </div>

            <div class="flex items-center gap-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <input type="checkbox" id="edit-category-active" name="is_active" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                <label for="edit-category-active" class="text-xs font-bold text-gray-800 cursor-pointer">Visible on Public Menu</label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-category-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-extrabold rounded-xl text-xs hover:bg-indigo-700 shadow-md transition">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Add Item Modal -->
<div id="add-item-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border w-full max-w-lg shadow-2xl rounded-3xl bg-white">
        <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-2">Add Dish to <span id="add-item-category-name" class="text-indigo-600"></span></h3>
        <form action="{{ route('organization.menu.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="menu_category_id" id="add-item-category-id">
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Dish Item Name *</label>
                <input type="text" name="name" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3" required placeholder="e.g. Paneer Butter Masala">
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Price (₹) *</label>
                <input type="number" step="0.01" name="price" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3 font-mono" required placeholder="280.00">
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-medium p-3" placeholder="e.g. Fresh cottage cheese cooked in rich tomato gravy"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Dish Photo (Optional)</label>
                <input type="file" name="photo" accept="image/*" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs p-2 border">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-item-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl text-xs hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-extrabold rounded-xl text-xs hover:bg-indigo-700 shadow-md transition">Save Dish Item</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Edit Item Modal -->
<div id="edit-item-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border w-full max-w-lg shadow-2xl rounded-3xl bg-white">
        <h3 class="text-lg font-black text-gray-900 mb-4 border-b pb-2">Edit Dish Item</h3>
        <form id="edit-item-form" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Dish Item Name *</label>
                <input type="text" id="edit-item-name" name="name" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3" required>
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Price (₹) *</label>
                <input type="number" step="0.01" id="edit-item-price" name="price" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-bold p-3 font-mono" required>
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Description</label>
                <textarea id="edit-item-description" name="description" rows="2" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs font-medium p-3"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-1">Change Photo (Optional)</label>
                <input type="file" name="photo" accept="image/*" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs p-2 border">
            </div>

            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit-item-available" name="is_available" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-bold text-gray-800">In Stock</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit-item-active" name="is_active" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-bold text-gray-800">Visible on Menu</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-item-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-xl text-xs hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-extrabold rounded-xl text-xs hover:bg-indigo-700 shadow-md transition">Update Dish Item</button>
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
        const form = document.getElementById('edit-category-form');
        form.action = `{{ url('organization/menu/categories') }}/${id}`;
        document.getElementById('edit-category-name').value = name;
        document.getElementById('edit-category-active').checked = Boolean(isActive);
        document.getElementById('edit-category-modal').classList.remove('hidden');
    }

    function openEditItemModal(id, itemData) {
        const form = document.getElementById('edit-item-form');
        form.action = `{{ url('organization/menu/items') }}/${id}`;
        document.getElementById('edit-item-name').value = itemData.name || '';
        document.getElementById('edit-item-price').value = itemData.price || '';
        document.getElementById('edit-item-description').value = itemData.description || '';
        document.getElementById('edit-item-available').checked = Boolean(itemData.is_available);
        document.getElementById('edit-item-active').checked = Boolean(itemData.is_active);
        document.getElementById('edit-item-modal').classList.remove('hidden');
    }
</script>
@endsection
