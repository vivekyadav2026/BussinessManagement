@extends('layouts.customer')

@section('header_extensions')
    <div class="px-4 py-2 flex gap-2 overflow-x-auto w-full scrollbar-hide border-t bg-gray-50">
        @foreach($categories as $category)
            <a href="#category-{{ $category->id }}" class="whitespace-nowrap px-4 py-1.5 bg-white border hover:bg-gray-100 rounded-full text-sm font-medium text-gray-700 transition">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
@endsection

@section('content')
<div class="px-4 py-6">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($categories->isEmpty())
        <div class="text-center py-20 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <p>No menu items available at the moment.</p>
        </div>
    @endif

    @foreach($categories as $category)
        @if($category->items->isNotEmpty())
            <div id="category-{{ $category->id }}" class="mb-10 pt-2 scroll-mt-32">
                <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">{{ $category->name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($category->items as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col sm:flex-row {{ !$item->is_available ? 'opacity-60' : '' }}">
                            @if($item->photo)
                                <div class="w-full sm:w-32 h-40 sm:h-full bg-gray-200 shrink-0">
                                    <img src="{{ asset('storage/' . $item->photo) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $item->name }}</h3>
                                    <span class="text-lg font-bold text-gray-900 ml-3">₹{{ number_format($item->price, 2) }}</span>
                                </div>
                                
                                @if($item->description)
                                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $item->description }}</p>
                                @endif
                                
                                <div class="mt-auto pt-2 flex justify-between items-center">
                                    @if($item->is_available)
                                        <form action="{{ route('public.order.add', [$organization->id, $location->id]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-4 py-1.5 rounded-lg text-sm flex items-center gap-1 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                                Add
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
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
@endsection
