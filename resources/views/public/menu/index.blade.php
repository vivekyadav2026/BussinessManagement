@extends('layouts.customer')

@section('header_extensions')
    <!-- Search Bar Extension -->
    <div class="px-4 py-2.5 bg-white border-t border-stone-200/60">
        <div class="relative max-w-4xl mx-auto">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="dishSearchInput" onkeyup="searchDishes()" placeholder="Search dishes, starters, beverages..." class="w-full pl-10 pr-4 py-2 bg-stone-100/80 border border-stone-200/80 rounded-2xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition-all placeholder:text-slate-400">
        </div>
    </div>

    <!-- Category Nav Extension -->
    <div class="px-4 py-2.5 flex gap-2 overflow-x-auto w-full scrollbar-hide border-t border-stone-200/60 bg-white/80 backdrop-blur-xs">
        @foreach($categories as $category)
            @if($category->items->isNotEmpty())
                <a href="#category-{{ $category->id }}" class="category-pill whitespace-nowrap px-4 py-1.5 bg-stone-100 hover:bg-slate-900 hover:text-white border border-stone-200 rounded-full text-xs font-black text-slate-800 transition-all flex items-center gap-1.5">
                    <span>{{ $category->name }}</span>
                    <span class="text-[10px] bg-white text-slate-800 px-1.5 py-0.5 rounded-full font-mono font-bold shadow-2xs border border-stone-200">{{ $category->items->count() }}</span>
                </a>
            @endif
        @endforeach
    </div>
@endsection

@section('content')
<div class="px-4 py-6 space-y-8">
    
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-950 px-4 py-3 rounded-2xl border border-emerald-200 text-xs font-extrabold shadow-2xs flex items-center justify-between gap-2">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </span>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold">&times;</button>
        </div>
    @endif

    @if($categories->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-stone-200/80 shadow-xs p-8 space-y-3">
            <div class="w-16 h-16 bg-[#0F172A] text-amber-400 rounded-2xl flex items-center justify-center mx-auto text-3xl shadow-sm">
                🍽️
            </div>
            <h3 class="text-xl font-black text-[#0F172A]">No items available</h3>
            <p class="text-xs font-semibold text-[#475569]">Please check back soon or ask restaurant staff for assistance.</p>
        </div>
    @endif

    @php
        $cartKey = 'cart_' . $location->id;
        $cart = session()->get($cartKey, []);
    @endphp

    @foreach($categories as $category)
        @if($category->items->isNotEmpty())
            <div id="category-{{ $category->id }}" class="category-block mb-8 pt-2 scroll-mt-36">
                
                <!-- Category Badge & Header -->
                <div class="flex items-center justify-between gap-3 mb-4 border-b border-stone-200/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-block bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-mono font-black px-3 py-1 rounded-full uppercase tracking-widest">
                            Category
                        </span>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $category->name }}</h2>
                    </div>
                    <span class="text-xs font-bold text-slate-500 font-mono bg-stone-100 px-3 py-1 rounded-full border border-stone-200">
                        {{ $category->items->count() }} {{ $category->items->count() === 1 ? 'Dish' : 'Dishes' }}
                    </span>
                </div>
                
                <!-- Category Items Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($category->items as $item)
                        @php
                            $inCartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                        @endphp
                        <div class="dish-card bg-white rounded-3xl border border-stone-200/80 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-5 flex justify-between gap-4 relative overflow-hidden {{ !$item->is_available ? 'opacity-60 bg-stone-50' : '' }}"
                             data-name="{{ strtolower($item->name) }}"
                             data-desc="{{ strtolower($item->description ?? '') }}">
                            
                            <!-- Left Food Details Column -->
                            <div class="flex flex-col justify-between flex-grow min-w-0 pr-1">
                                <div>
                                    <!-- Dish Name & Quick View Trigger -->
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <h3 onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")' class="text-base sm:text-lg font-black text-slate-900 leading-snug tracking-tight hover:text-indigo-600 transition cursor-pointer line-clamp-1">
                                            {{ $item->name }}
                                        </h3>
                                    </div>
                                    
                                    <!-- Price Tag -->
                                    <div class="text-base font-black text-emerald-700 font-mono mb-1.5">
                                        ₹{{ number_format($item->price, 2) }}
                                    </div>

                                    <!-- Description -->
                                    @if($item->description)
                                        <p onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")' class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-medium mb-3 cursor-pointer hover:text-slate-700">
                                            {{ $item->description }}
                                        </p>
                                    @endif
                                </div>
                                
                                <!-- Quick View Link -->
                                <div class="pt-1">
                                    <button onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")' class="inline-flex items-center gap-1 text-[11px] font-extrabold text-amber-800 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                        <span>🔍 Quick View</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Right Photo Thumbnail & Floating Swiggy/Zomato ADD Controller -->
                            <div class="relative shrink-0 flex flex-col items-center">
                                
                                <!-- Photo Container -->
                                <div onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")' class="cursor-pointer">
                                    @if($item->photo)
                                        <div class="w-28 h-28 sm:w-32 sm:h-32 bg-stone-100 rounded-2xl overflow-hidden shrink-0 border border-stone-200/90 shadow-2xs hover:opacity-95 transition">
                                            <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-28 h-28 sm:w-32 sm:h-32 bg-slate-900 text-amber-400 rounded-2xl flex items-center justify-center shrink-0 border border-slate-800 text-4xl shadow-2xs hover:scale-102 transition">
                                            🍱
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Overlaid Floating Action Button (Zomato / Swiggy Style) -->
                                <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 z-10 w-max">
                                    @if($item->is_available)
                                        @if($inCartQty > 0)
                                            <!-- In Cart Stepper Button -->
                                            <div class="flex items-center bg-slate-900 text-white rounded-xl border border-slate-800 shadow-md overflow-hidden font-mono">
                                                <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" class="px-2.5 py-1 text-amber-400 font-black hover:bg-slate-800 transition text-xs">&minus;</button>
                                                </form>
                                                <span class="px-2 font-black text-white text-xs bg-slate-800 py-1">{{ $inCartQty }}</span>
                                                <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" class="px-2.5 py-1 text-amber-400 font-black hover:bg-slate-800 transition text-xs">+</button>
                                                </form>
                                            </div>
                                        @else
                                            <!-- Initial ADD Button -->
                                            <form action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-4 py-1.5 rounded-xl text-xs flex items-center gap-1 shadow-md transition transform active:scale-95 border border-emerald-500 uppercase tracking-wider cursor-pointer">
                                                    <span>+ ADD</span>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200 shadow-xs">
                                            Sold Out
                                        </span>
                                    @endif
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        @endif
    @endforeach
</div>

<!-- Interactive Dish Quick View Modal -->
<div id="dish-modal" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col border border-stone-200">
        <!-- Dish Hero Image Banner -->
        <div class="relative w-full h-56 bg-stone-100 flex items-center justify-center">
            <img id="dish-modal-photo" src="" class="w-full h-full object-cover hidden">
            <div id="dish-modal-fallback" class="w-full h-full bg-slate-900 text-amber-400 flex items-center justify-center text-5xl font-black">
                🍱
            </div>
            <button onclick="closeDishModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-slate-900/70 text-white flex items-center justify-center font-bold text-xl backdrop-blur-md transition hover:bg-slate-900 cursor-pointer">
                &times;
            </button>
            <span id="dish-modal-category" class="absolute bottom-4 left-4 bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-mono font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-xs">
                Category
            </span>
        </div>

        <!-- Dish Content Body -->
        <div class="p-6 space-y-4 text-slate-900">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <h3 id="dish-modal-title" class="text-xl font-black tracking-tight text-slate-900">Dish Name</h3>
                    <p id="dish-modal-desc" class="text-xs text-slate-500 font-semibold leading-relaxed mt-1">Full dish description and ingredients details.</p>
                </div>
            </div>

            <!-- Price Breakdown Box -->
            <div class="flex items-center justify-between bg-stone-50 p-4 rounded-2xl border border-stone-200">
                <span class="text-xs font-black uppercase text-slate-500">Price per portion:</span>
                <span id="dish-modal-price" class="text-xl font-black text-emerald-700 font-mono">₹0.00</span>
            </div>

            <!-- Add to Cart Form -->
            <form id="dish-modal-form" action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="menu_item_id" id="dish-modal-item-id">
                
                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                    <span>+ Add Dish to Order</span>
                    <span id="dish-modal-btn-price" class="font-mono text-amber-300">(₹0.00)</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function searchDishes() {
        const query = document.getElementById('dishSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.dish-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const desc = card.getAttribute('data-desc');
            if (name.includes(query) || desc.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        // Hide empty category blocks
        const categoryBlocks = document.querySelectorAll('.category-block');
        categoryBlocks.forEach(block => {
            const visibleCards = block.querySelectorAll('.dish-card[style="display: flex;"], .dish-card:not([style*="display: none"])');
            if (visibleCards.length === 0 && query !== '') {
                block.style.display = 'none';
            } else {
                block.style.display = 'block';
            }
        });
    }

    function openDishModal(item, categoryName) {
        document.getElementById('dish-modal-item-id').value = item.id;
        document.getElementById('dish-modal-title').innerText = item.name;
        document.getElementById('dish-modal-desc').innerText = item.description || 'Freshly prepared dish crafted with authentic spices and fresh ingredients.';
        document.getElementById('dish-modal-category').innerText = categoryName || 'Menu';
        
        const formattedPrice = '₹' + parseFloat(item.price).toFixed(2);
        document.getElementById('dish-modal-price').innerText = formattedPrice;
        document.getElementById('dish-modal-btn-price').innerText = `(${formattedPrice})`;

        const photoImg = document.getElementById('dish-modal-photo');
        const fallback = document.getElementById('dish-modal-fallback');
        if (item.photo) {
            photoImg.src = `{{ asset('storage') }}/${item.photo}`;
            photoImg.classList.remove('hidden');
            fallback.classList.add('hidden');
        } else {
            photoImg.classList.add('hidden');
            fallback.classList.remove('hidden');
        }

        document.getElementById('dish-modal').classList.remove('hidden');
        document.getElementById('dish-modal').classList.add('flex');
    }

    function closeDishModal() {
        document.getElementById('dish-modal').classList.add('hidden');
        document.getElementById('dish-modal').classList.remove('flex');
    }
</script>
@endsection
