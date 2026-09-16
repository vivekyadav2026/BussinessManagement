@extends('layouts.sme')

@section('title', 'Product Categories')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <span class="hover:text-slate-900 transition-colors">Inventory</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="hover:text-slate-900 transition-colors">Central Catalog</span>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Categories</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    🏷️
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Product Categories</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Organize catalog products into structured departments, menu classes, and retail tax groups.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap lg:justify-end">
            <a href="{{ route('organization.products.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs rounded-lg transition shadow-2xs">
                View Products Catalog &rarr;
            </a>

            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-300 text-emerald-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-emerald-700 text-base">✓</span>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-rose-50 border border-rose-300 text-rose-950 px-4 py-3.5 rounded-xl text-xs sm:text-sm shadow-2xs">
        <span class="font-extrabold text-rose-700 text-base">⚠️</span>
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 2. KPI Metric Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Categories -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Categories</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center text-xs font-bold">🏷️</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalCategories ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Active product taxonomies</p>
            </div>
        </div>

        <!-- Categorized Products Volume -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Categorized Items</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">📦</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-emerald-700 font-mono tracking-tight">{{ number_format($totalCategorizedProducts ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Assigned to catalog groups</p>
            </div>
        </div>

        <!-- Most Populated Category -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Top Department</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">⭐</span>
            </div>
            <div class="mt-2">
                <div class="text-lg font-black text-slate-950 truncate tracking-tight">
                    {{ $topCategory ? $topCategory->name : 'N/A' }}
                </div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                    {{ $topCategory ? $topCategory->products_count . ' products linked' : 'No items categorized' }}
                </p>
            </div>
        </div>

        <!-- Empty / Unassigned Groups -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Unused Categories</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xs font-bold">🧹</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ number_format($emptyCategoriesCount ?? 0) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">0 items &bull; Safe to delete</p>
            </div>
        </div>
    </div>

    <!-- 3. Search & Filter Bar -->
    <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs">
        <form method="GET" action="{{ route('organization.categories.index') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
            <div class="flex-1">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Search Categories</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search category name or slug identifier..." 
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2 pl-9 text-xs font-semibold text-slate-950 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition">
                    Search
                </button>

                @if(request()->filled('search'))
                    <a href="{{ route('organization.categories.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition" title="Clear search">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Categories Master Table -->
    <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Category Name</th>
                        <th class="py-3 px-4">URL / System Slug</th>
                        <th class="py-3 px-4">Associated Products</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($categories as $index => $category)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <!-- ID / Index -->
                        <td class="py-3 px-4 text-center font-mono text-slate-400 font-bold">
                            {{ $categories->firstItem() ? $categories->firstItem() + $index : $index + 1 }}
                        </td>

                        <!-- Category Name -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center text-xs font-bold shrink-0">
                                    🏷️
                                </span>
                                <div>
                                    <div class="font-extrabold text-slate-950 text-sm tracking-tight">
                                        {{ $category->name }}
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        Created {{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Slug -->
                        <td class="py-3 px-4 font-mono text-slate-600">
                            <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[11px] font-bold">
                                {{ $category->slug }}
                            </span>
                        </td>

                        <!-- Linked Products Count -->
                        <td class="py-3 px-4">
                            @if($category->products_count > 0)
                                <a href="{{ route('organization.products.index', ['category_id' => $category->id]) }}" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition" 
                                    title="View all {{ $category->products_count }} products in this category">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $category->products_count }} {{ Str::plural('Product', $category->products_count) }}</span>
                                    <span class="text-emerald-500 text-[10px]">&rarr;</span>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    0 Products (Empty)
                                </span>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-3 px-4 text-right">
                            <div class="flex justify-end gap-1.5 items-center">
                                <!-- View Products in Catalog -->
                                <a href="{{ route('organization.products.index', ['category_id' => $category->id]) }}" 
                                    class="p-1.5 text-slate-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-transparent hover:border-blue-200 transition" 
                                    title="View Category Products">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <!-- Edit Category Name -->
                                <button type="button" 
                                    onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" 
                                    class="p-1.5 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg border border-transparent hover:border-indigo-200 transition" 
                                    title="Edit Category Name">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <!-- Delete Category (Only if 0 products) -->
                                @if($category->products_count === 0)
                                    <form action="{{ route('organization.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete category \'{{ addslashes($category->name) }}\'?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition" title="Delete Category">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1.5 text-slate-300 cursor-not-allowed" title="Cannot delete: contains active products">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3.5v-1a7 7 0 00-14 0v1m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0H5"/></svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="text-3xl mb-2">🏷️</div>
                            <p class="text-sm font-extrabold text-slate-800">No categories found</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                @if(request()->filled('search'))
                                    No categories match your search keyword "{{ request('search') }}".
                                @else
                                    Create department and item categories to group products in billing and inventory reports.
                                @endif
                            </p>
                            <div class="mt-4">
                                <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                                    <span>+ Add First Category</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs text-slate-600 font-medium">
                Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
            </div>
            <div>
                {{ $categories->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-slate-950 text-white p-5 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-amber-400 text-lg">🏷️</span>
                <h3 class="text-base font-extrabold text-white">Create New Category</h3>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
        </div>

        <form action="{{ route('organization.categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label for="createNameInput" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    Category Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="createNameInput" name="name" required 
                    placeholder="e.g. Beverages, Electronics, Bakery..." 
                    class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 shadow-2xs">
                <p class="text-[11px] text-slate-500 mt-1 font-medium">
                    A system slug will be generated automatically from the name.
                </p>
            </div>

            <div class="pt-2 flex justify-end gap-2.5">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-slate-950 text-white p-5 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-amber-400 text-lg">✏️</span>
                <h3 class="text-base font-extrabold text-white">Edit Category</h3>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-lg">&times;</button>
        </div>

        <form id="editCategoryForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="editCategoryName" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    Category Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="editCategoryName" required 
                    class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm font-semibold text-slate-950 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 shadow-2xs">
            </div>

            <div class="pt-2 flex justify-end gap-2.5">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-lg shadow-xs transition">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    const modal = document.getElementById('createCategoryModal');
    modal.classList.remove('hidden');
    document.getElementById('createNameInput').focus();
}

function closeCreateModal() {
    document.getElementById('createCategoryModal').classList.add('hidden');
}

function openEditModal() {
    document.getElementById('editCategoryModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCategoryModal').classList.add('hidden');
}

function editCategory(id, name) {
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryForm').action = `{{ url('organization/categories') }}/${id}`;
    openEditModal();
}
</script>
@endsection

