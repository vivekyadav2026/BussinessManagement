@extends('layouts.customer')

@section('header_extensions')
    <div class="px-4 py-2.5 flex gap-2 overflow-x-auto w-full scrollbar-hide border-t border-stone-200/60 bg-white">
        @foreach($categories as $category)
            @if($category->items->isNotEmpty())
                <a href="#category-{{ $category->id }}" class="whitespace-nowrap px-4 py-1.5 bg-[#F8F8F6] hover:bg-[#0F172A] hover:text-white border border-stone-200 rounded-full text-xs font-black text-[#0F172A] transition">
                    {{ $category->name }}
                </a>
            @endif
        @endforeach
    </div>
@endsection

@section('content')
<div class="px-4 py-6 space-y-6">
    
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-950 px-4 py-3 rounded-2xl border border-emerald-200 text-xs font-extrabold shadow-2xs flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
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
            <div id="category-{{ $category->id }}" class="mb-10 pt-2 scroll-mt-36">
                
                <!-- Category Badge & Header -->
                <div class="flex items-center justify-between gap-3 mb-4 border-b border-stone-200 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-block bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full uppercase tracking-widest">
                            Category
                        </span>
                        <h2 class="text-xl font-black text-[#0F172A] tracking-tight">{{ $category->name }}</h2>
                    </div>
                    <span class="text-xs font-bold text-[#475569] font-mono bg-stone-100 px-3 py-1 rounded-full border border-stone-200">
                        {{ $category->items->count() }} {{ $category->items->count() === 1 ? 'Dish' : 'Dishes' }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($category->items as $item)
                        @php
                            $inCartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                        @endphp
                        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-xs p-5 flex gap-4 transition hover:shadow-md hover:border-stone-300 {{ !$item->is_available ? 'opacity-60 bg-stone-50' : '' }}">
                            
                            <!-- Food Photo Thumbnail (Clickable Quick View) -->
                            <div onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")' class="cursor-pointer">
                                @if($item->photo)
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-stone-100 rounded-2xl overflow-hidden shrink-0 border border-stone-200 shadow-2xs hover:opacity-90 transition">
                                        <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-[#0F172A] text-amber-400 rounded-2xl flex items-center justify-center shrink-0 border border-slate-800 text-3xl shadow-xs hover:scale-105 transition">
                                        🍱
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Food Content Details -->
                            <div class="flex flex-col flex-grow min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1 cursor-pointer" onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")'>
                                    <h3 class="text-base font-black text-[#0F172A] leading-snug tracking-tight line-clamp-1 hover:text-indigo-600 transition">{{ $item->name }}</h3>
                                    <span class="text-[10px] font-bold text-amber-800 bg-[#FEF3C7] border border-[#FDE68A] px-1.5 py-0.5 rounded-md shrink-0">🔍 Quick View</span>
                                </div>
                                
                                <div class="text-base font-black text-[#0F172A] font-mono mb-1">
                                    ₹{{ number_format($item->price, 2) }}
                                </div>

                                @if($item->description)
                                    <p class="text-xs text-[#475569] mb-3 line-clamp-2 leading-relaxed font-semibold cursor-pointer" onclick='openDishModal(@json($item), "{{ addslashes($category->name) }}")'>{{ $item->description }}</p>
                                @endif
                                
                                <div class="mt-auto pt-1 flex justify-between items-center">
                                    @if($item->is_available)
                                        @if($inCartQty > 0)
                                            <!-- Stepper Button -->
                                            <div class="flex items-center border-2 border-[#0F172A] bg-white rounded-xl overflow-hidden shadow-2xs">
                                                <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" class="px-3 py-1.5 text-[#0F172A] font-black hover:bg-stone-100 transition text-xs">&minus;</button>
                                                </form>
                                                <span class="px-2.5 font-black text-[#0F172A] text-xs font-mono bg-stone-100 border-x border-stone-200 py-1">{{ $inCartQty }}</span>
                                                <form action="{{ route('public.order.update-quantity', [$organization->id, $location->id, $item->id]) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" class="px-3 py-1.5 text-[#0F172A] font-black hover:bg-stone-100 transition text-xs">+</button>
                                                </form>
                                            </div>
                                        @else
                                            <!-- Add Button -->
                                            <form action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                                <button type="submit" class="bg-[#0F172A] hover:bg-black text-white font-black px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition transform active:scale-95 uppercase tracking-wider">
                                                    <span>+ ADD</span>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
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
<div id="dish-modal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden flex flex-col border border-stone-200">
        <!-- Dish Hero Image Banner -->
        <div class="relative w-full h-56 bg-stone-100 flex items-center justify-center">
            <img id="dish-modal-photo" src="" class="w-full h-full object-cover hidden">
            <div id="dish-modal-fallback" class="w-full h-full bg-[#0F172A] text-amber-400 flex items-center justify-center text-5xl font-black">
                🍱
            </div>
            <button onclick="closeDishModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 text-white flex items-center justify-center font-bold text-xl backdrop-blur-md transition hover:bg-black">
                &times;
            </button>
            <span id="dish-modal-category" class="absolute bottom-4 left-4 bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A] text-[10px] font-mono font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-sm">
                Category
            </span>
        </div>

        <!-- Dish Content Body -->
        <div class="p-6 space-y-4 text-[#0F172A]">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <h3 id="dish-modal-title" class="text-xl font-black tracking-tight text-[#0F172A]">Dish Name</h3>
                    <p id="dish-modal-desc" class="text-xs text-[#475569] font-semibold leading-relaxed mt-1">Full dish description and ingredients details.</p>
                </div>
            </div>

            <!-- Price Breakdown Box -->
            <div class="flex items-center justify-between bg-[#F8F8F6] p-4 rounded-2xl border border-stone-200">
                <span class="text-xs font-black uppercase text-[#475569]">Price per portion:</span>
                <span id="dish-modal-price" class="text-xl font-black text-[#0F172A] font-mono">₹0.00</span>
            </div>

            <!-- Add to Cart Form -->
            <form id="dish-modal-form" action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="menu_item_id" id="dish-modal-item-id">
                
                <button type="submit" class="w-full bg-[#0F172A] hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl transition transform active:scale-95 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>+ Add Dish to Order</span>
                    <span id="dish-modal-btn-price" class="font-mono text-amber-300">(₹0.00)</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
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
