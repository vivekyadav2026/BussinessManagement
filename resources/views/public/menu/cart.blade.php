@extends('layouts.customer')

@section('content')
<div class="px-4 py-6 max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="text-gray-500 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Your Cart</h1>
    </div>

    @if(empty($cart))
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added anything yet.</p>
            <a href="{{ route('public.menu', [$organization->id, $location->id]) }}" class="inline-block bg-blue-600 text-white font-medium px-6 py-2.5 rounded-lg hover:bg-blue-700 transition">
                Browse Menu
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            @php $subtotal = 0; @endphp
            @foreach($cart as $id => $item)
                @php $subtotal += $item['price'] * $item['quantity']; @endphp
                <div class="p-4 border-b border-gray-100 flex items-center justify-between last:border-0">
                    <div class="flex-grow">
                        <h3 class="font-bold text-gray-900">{{ $item['name'] }}</h3>
                        <p class="text-sm text-gray-500">${{ number_format($item['price'], 2) }} each</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="font-bold text-gray-900">
                            {{ $item['quantity'] }}x
                        </div>
                        <div class="font-bold text-gray-900 w-16 text-right">
                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                        <form action="{{ route('public.order.remove', [$organization->id, $location->id, $id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-red-500 hover:bg-red-50 p-1.5 rounded-full transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-gray-100 rounded-xl p-4 mb-8">
            <div class="flex justify-between mb-2 text-sm text-gray-600">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between mb-4 text-sm text-gray-600">
                <span>Tax (Estimated 5%)</span>
                <span>${{ number_format($subtotal * 0.05, 2) }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-300 pt-3 font-bold text-lg text-gray-900">
                <span>Total</span>
                <span>${{ number_format($subtotal * 1.05, 2) }}</span>
            </div>
        </div>

        <a href="{{ route('public.order.checkout', [$organization->id, $location->id]) }}" class="block w-full text-center bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-blue-700 transition transform active:scale-95">
            Proceed to Checkout
        </a>
    @endif
</div>
@endsection
