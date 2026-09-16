@extends('layouts.sme')

@section('content')
<div class="space-y-6">
    <!-- Top Bar & Breadcrumb -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Restaurant Management</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">Catalog</span>
            </div>
            <h1 class="text-2xl font-black text-slate-950 tracking-tight">Restaurant Menu Builder</h1>
            <p class="text-xs text-slate-600 mt-0.5">Manage culinary categories, dish pricing, photos, dietary tags, and live inventory status.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="document.getElementById('add-category-modal').classList.remove('hidden')" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>+ Add Category</span>
            </button>

            @php
                $previewLocationId = session('active_location_id') ?? auth()->user()->organization?->locations()->first()?->id ?? 1;
                $previewOrgId = auth()->user()->organization_id ?? 1;
            @endphp
            <a href="{{ route('public.menu', [$previewOrgId, $previewLocationId]) }}" 
               target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-950 hover:bg-slate-900 active:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition whitespace-nowrap">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Preview Customer Menu</span>
            </a>
        </div>
    </div>

    <!-- Executive KPI Strip -->
    @php
        $totalCategories = $categories->count();
        $totalItems = $categories->sum(fn($c) => $c->items->count());
        $inStockItems = $categories->sum(fn($c) => $c->items->where('is_available', true)->count());
        $outOfStockItems = $totalItems - $inStockItems;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Categories -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Menu Categories</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($totalCategories) }}</div>
            <p class="text-[11px] font-semibold text-slate-500 mt-1">Active sections in catalog</p>
        </div>

        <!-- Total Dishes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-700">Total Dishes</span>
                <span class="w-8 h-8 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($totalItems) }}</div>
            <p class="text-[11px] font-semibold text-blue-700 mt-1">Configured food & beverage items</p>
        </div>

        <!-- In Stock -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">In Stock (Serving)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($inStockItems) }}</div>
            <p class="text-[11px] font-semibold text-emerald-700 mt-1">Live & orderable on POS/QR</p>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-700">Out of Stock</span>
                <span class="w-8 h-8 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-950 mt-2">{{ number_format($outOfStockItems) }}</div>
            <p class="text-[11px] font-semibold text-rose-700 mt-1">Temporarily sold out</p>
        </div>
    </div>

    <!-- Real-time Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" 
                   id="menuItemSearch" 
                   onkeyup="filterMenuItems()" 
                   placeholder="Search dishes by name, description, or category..." 
                   class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 bg-white placeholder:text-slate-400">
        </div>

        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 shrink-0">
            <span>Filter Stock:</span>
            <button type="button" onclick="setStockFilter('all')" id="btn-filter-all" class="px-3 py-1.5 rounded-lg bg-slate-950 text-white font-extrabold text-[11px] transition whitespace-nowrap cursor-pointer">
                All Dishes
            </button>
            <button type="button" onclick="setStockFilter('in_stock')" id="btn-filter-instock" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[11px] transition whitespace-nowrap cursor-pointer">
                In Stock Only
            </button>
            <button type="button" onclick="setStockFilter('out_of_stock')" id="btn-filter-outstock" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[11px] transition whitespace-nowrap cursor-pointer">
                Out of Stock
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex items-center justify-between text-xs font-bold shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-800 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 text-xs font-bold shadow-2xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950">&times;</button>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 text-xs font-semibold shadow-2xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <!-- Menu Categories & Items List -->
    <div class="space-y-6" id="categoriesContainer">
        @forelse($categories as $category)
            <div class="category-card bg-white rounded-2xl shadow-2xs border border-slate-200/90 overflow-hidden" data-category="{{ strtolower($category->name) }}">
                <!-- Category Section Header -->
                <div class="bg-slate-50/90 border-b border-slate-200/90 p-4 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                        <h2 class="text-sm font-black text-slate-950 tracking-tight {{ !$category->is_active ? 'line-through text-slate-400' : '' }}">
                            {{ $category->name }}
                        </h2>
                        <span class="text-[11px] font-extrabold bg-white text-slate-800 px-2.5 py-0.5 rounded-lg border border-slate-200 font-mono">
                            {{ $category->items->count() }} {{ $category->items->count() === 1 ? 'Dish' : 'Dishes' }}
                        </span>
                        @if(!$category->is_active)
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-slate-200 text-slate-700 border border-slate-300">Hidden</span>
                        @else
                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-900 border border-emerald-300">Active</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                onclick="openAddItemModal({{ $category->id }}, '{{ addslashes($category->name) }}')" 
                                class="text-xs bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold px-3 py-1.5 rounded-xl shadow-2xs transition flex items-center gap-1 cursor-pointer whitespace-nowrap">
                            <span>+ Add Dish</span>
                        </button>

                        <button type="button" 
                                onclick="openEditCategoryModal({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->is_active ? 'true' : 'false' }})" 
                                class="p-1.5 border border-slate-200 bg-white text-slate-700 hover:text-slate-950 hover:bg-slate-50 rounded-xl transition cursor-pointer shadow-2xs" 
                                title="Edit Category">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>

                        <form action="{{ route('organization.menu.categories.destroy', $category) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this category and all associated dishes?');" 
                              class="inline m-0">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-1.5 border border-rose-200 bg-white text-rose-700 hover:text-rose-900 hover:bg-rose-50 rounded-xl transition cursor-pointer shadow-2xs" 
                                    title="Delete Category">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Category Dish Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-[10px] uppercase font-black text-slate-600 border-b border-slate-200/80">
                            <tr>
                                <th class="py-3 px-4 w-16 whitespace-nowrap">Photo</th>
                                <th class="py-3 px-4 whitespace-nowrap">Dish Item & Ingredients</th>
                                <th class="py-3 px-4 whitespace-nowrap">Unit Price</th>
                                <th class="py-3 px-4 whitespace-nowrap">Availability</th>
                                <th class="py-3 px-4 text-right whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                            @forelse($category->items as $item)
                                <tr class="dish-row hover:bg-slate-50/60 transition group {{ !$item->is_active ? 'opacity-60 bg-slate-50/40' : '' }}" 
                                    data-dish-name="{{ strtolower($item->name) }}"
                                    data-dish-desc="{{ strtolower($item->description ?? '') }}"
                                    data-available="{{ $item->is_available ? '1' : '0' }}">
                                    <!-- Photo Thumbnail -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($item->photo)
                                            <img src="{{ asset('storage/' . $item->photo) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200/90 shadow-2xs">
                                        @else
                                            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 text-lg border border-slate-200 shadow-2xs font-bold">
                                                🍽️
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Dish Name & Description -->
                                    <td class="py-3 px-4 max-w-sm">
                                        <div class="font-extrabold text-slate-950 text-sm flex items-center gap-2">
                                            <span>{{ $item->name }}</span>
                                            @if(!$item->is_active)
                                                <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">Hidden</span>
                                            @endif
                                        </div>
                                        @if($item->description)
                                            <div class="text-[11px] text-slate-500 font-medium line-clamp-1 mt-0.5">{{ $item->description }}</div>
                                        @endif
                                    </td>

                                    <!-- Price -->
                                    <td class="py-3 px-4 font-mono font-black text-slate-950 text-sm whitespace-nowrap">
                                        ₹{{ number_format($item->price, 2) }}
                                    </td>

                                    <!-- Stock Status Pill -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($item->is_available)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-950 border border-emerald-300 whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                In Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-950 border border-rose-300 whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                Out of Stock
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" 
                                                    onclick='openEditItemModal({{ $item->id }}, @json($item))' 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                <span>Edit</span>
                                            </button>

                                            <form action="{{ route('organization.menu.items.destroy', $item) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this dish item?');" 
                                                  class="inline m-0">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 border border-slate-200 bg-white hover:border-rose-300 hover:bg-rose-50 text-slate-500 hover:text-rose-700 rounded-xl transition cursor-pointer shadow-2xs" 
                                                        title="Delete Dish">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500 text-xs font-medium">
                                        No dish items in this category yet. Click <button type="button" onclick="openAddItemModal({{ $category->id }}, '{{ addslashes($category->name) }}')" class="font-bold text-amber-600 hover:underline cursor-pointer">+ Add Dish</button> to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center rounded-2xl border border-dashed border-slate-300 space-y-4 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-700 flex items-center justify-center mx-auto text-2xl font-bold">
                    🍽️
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-950">No Menu Categories Created Yet</h3>
                    <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto mt-1">Get started by creating your first restaurant menu category (e.g. Starters, Main Course, Beverages, Desserts).</p>
                </div>
                <div>
                    <button type="button" onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        + Add First Category
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Add Category Modal -->
<div id="add-category-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border border-slate-200/90 w-full max-w-md shadow-2xl rounded-2xl bg-white space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">Add Menu Category</h3>
            </div>
            <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
        </div>

        <form action="{{ route('organization.menu.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Category Name <span class="text-rose-600">*</span></label>
                <input type="text" 
                       name="name" 
                       placeholder="e.g. Appetizers, Tandoor & Grills, Mocktails" 
                       class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 outline-none transition bg-white" 
                       required>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold rounded-xl text-xs shadow-xs transition cursor-pointer">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Category Modal -->
<div id="edit-category-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border border-slate-200/90 w-full max-w-md shadow-2xl rounded-2xl bg-white space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">Edit Category</h3>
            </div>
            <button type="button" onclick="document.getElementById('edit-category-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
        </div>

        <form id="edit-category-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Category Name <span class="text-rose-600">*</span></label>
                <input type="text" id="edit-category-name" name="name" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 outline-none transition bg-white" required>
            </div>

            <div class="flex items-center gap-2.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <input type="checkbox" id="edit-category-active" name="is_active" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                <label for="edit-category-active" class="text-xs font-bold text-slate-900 cursor-pointer">Visible on Public & POS Menu</label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('edit-category-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold rounded-xl text-xs shadow-xs transition cursor-pointer">Update Category</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Add Item Modal -->
<div id="add-item-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border border-slate-200/90 w-full max-w-lg shadow-2xl rounded-2xl bg-white space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">
                    Add Dish to <span id="add-item-category-name" class="text-amber-700 font-extrabold"></span>
                </h3>
            </div>
            <button type="button" onclick="document.getElementById('add-item-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
        </div>

        <form action="{{ route('organization.menu.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="menu_category_id" id="add-item-category-id">
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Dish Item Name <span class="text-rose-600">*</span></label>
                <input type="text" name="name" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 outline-none transition bg-white" required placeholder="e.g. Butter Chicken / Paneer Tikka">
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Price (₹) <span class="text-rose-600">*</span></label>
                <input type="number" step="0.01" name="price" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 font-mono outline-none transition bg-white" required placeholder="280.00">
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Dish Description / Ingredients</label>
                <textarea name="description" rows="2" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-medium text-slate-950 p-3 outline-none transition bg-white" placeholder="e.g. Fresh cottage cheese simmered in slow-cooked rich tomato and cashew gravy"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Dish Photo (Optional)</label>
                <input type="file" name="photo" accept="image/*" class="w-full border border-slate-300 rounded-xl text-xs p-2.5 bg-white text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('add-item-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold rounded-xl text-xs shadow-xs transition cursor-pointer">Save Dish Item</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Edit Item Modal -->
<div id="edit-item-modal" class="hidden fixed inset-0 bg-slate-950/60 backdrop-blur-xs overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto p-6 border border-slate-200/90 w-full max-w-lg shadow-2xl rounded-2xl bg-white space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                <h3 class="text-sm font-black text-slate-950 uppercase tracking-wider">Edit Dish Item</h3>
            </div>
            <button type="button" onclick="document.getElementById('edit-item-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold">&times;</button>
        </div>

        <form id="edit-item-form" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Dish Item Name <span class="text-rose-600">*</span></label>
                <input type="text" id="edit-item-name" name="name" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 outline-none transition bg-white" required>
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Price (₹) <span class="text-rose-600">*</span></label>
                <input type="number" step="0.01" id="edit-item-price" name="price" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-bold text-slate-950 p-3 font-mono outline-none transition bg-white" required>
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Dish Description / Ingredients</label>
                <textarea id="edit-item-description" name="description" rows="2" class="w-full border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 rounded-xl text-xs font-medium text-slate-950 p-3 outline-none transition bg-white"></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1.5">Change Photo (Optional)</label>
                <input type="file" name="photo" accept="image/*" class="w-full border border-slate-300 rounded-xl text-xs p-2.5 bg-white text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">
            </div>

            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit-item-available" name="is_available" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                    <span class="text-xs font-bold text-slate-900">In Stock (Serving)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="edit-item-active" name="is_active" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                    <span class="text-xs font-bold text-slate-900">Visible on Menu</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('edit-item-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-extrabold rounded-xl text-xs shadow-xs transition cursor-pointer">Update Dish Item</button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeStockFilter = 'all';

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

    function setStockFilter(type) {
        activeStockFilter = type;
        
        const btnAll = document.getElementById('btn-filter-all');
        const btnIn = document.getElementById('btn-filter-instock');
        const btnOut = document.getElementById('btn-filter-outstock');

        [btnAll, btnIn, btnOut].forEach(btn => {
            btn.className = 'px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-[11px] transition whitespace-nowrap cursor-pointer';
        });

        if (type === 'all') {
            btnAll.className = 'px-3 py-1.5 rounded-lg bg-slate-950 text-white font-extrabold text-[11px] transition whitespace-nowrap cursor-pointer';
        } else if (type === 'in_stock') {
            btnIn.className = 'px-3 py-1.5 rounded-lg bg-slate-950 text-white font-extrabold text-[11px] transition whitespace-nowrap cursor-pointer';
        } else if (type === 'out_of_stock') {
            btnOut.className = 'px-3 py-1.5 rounded-lg bg-slate-950 text-white font-extrabold text-[11px] transition whitespace-nowrap cursor-pointer';
        }

        filterMenuItems();
    }

    function filterMenuItems() {
        const query = document.getElementById('menuItemSearch').value.toLowerCase().trim();
        const categories = document.querySelectorAll('.category-card');

        categories.forEach(cat => {
            const catName = cat.getAttribute('data-category') || '';
            const rows = cat.querySelectorAll('.dish-row');
            let hasVisibleDish = false;

            rows.forEach(row => {
                const dishName = row.getAttribute('data-dish-name') || '';
                const dishDesc = row.getAttribute('data-dish-desc') || '';
                const isAvailable = row.getAttribute('data-available') === '1';

                const matchesQuery = dishName.includes(query) || dishDesc.includes(query) || catName.includes(query) || query === '';
                let matchesStock = true;
                if (activeStockFilter === 'in_stock') {
                    matchesStock = isAvailable;
                } else if (activeStockFilter === 'out_of_stock') {
                    matchesStock = !isAvailable;
                }

                if (matchesQuery && matchesStock) {
                    row.style.display = '';
                    hasVisibleDish = true;
                } else {
                    row.style.display = 'none';
                }
            });

            if (hasVisibleDish || (catName.includes(query) && activeStockFilter === 'all')) {
                cat.style.display = '';
            } else {
                cat.style.display = 'none';
            }
        });
    }
</script>
@endsection

