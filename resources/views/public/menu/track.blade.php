@extends('layouts.customer')

@section('content')
<div class="px-4 py-8 max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-center p-8 mb-6">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        
        <h1 class="text-2xl font-black text-gray-900 mb-2">Order {{ $order->order_number }}</h1>
        <p class="text-gray-500 mb-8">Hi {{ $order->customer_name }}, your order has been received.</p>
        
        <div class="bg-gray-50 rounded-xl p-4 inline-block mx-auto border mb-6">
            <div class="text-xs uppercase tracking-wider font-bold text-gray-500 mb-1">Current Status</div>
            <div class="text-xl font-black text-blue-600">{{ strtoupper($order->status) }}</div>
        </div>
        
        @if($order->status !== 'Served' && $order->status !== 'Cancelled')
            <p class="text-sm text-gray-400 mb-4 animate-pulse">This page will automatically update.</p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900 mb-4 border-b pb-2">Order Summary</h2>
        <div class="space-y-3 mb-6">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-700"><span class="font-bold mr-2">{{ $item->quantity }}x</span> {{ $item->name_snapshot }}</span>
                    <span class="text-gray-900 font-medium font-mono">₹{{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-gray-100 pt-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-mono">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Tax</span>
                <span class="font-mono">₹{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-gray-900 pt-2 border-t mt-2">
                <span>Total</span>
                <span class="font-mono">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>
</div>

@if($order->status !== 'Served' && $order->status !== 'Cancelled')
<script>
    setTimeout(function() {
        window.location.reload();
    }, 15000); // Reload every 15 seconds to check status
</script>
@endif
@endsection
