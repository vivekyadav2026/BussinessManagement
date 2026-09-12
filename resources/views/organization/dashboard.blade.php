@extends('layouts.sme')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $orgId = auth()->user()->organization_id;
    $hasRestaurant = \App\Services\SubscriptionService::hasFeature($orgId, 'module_restaurant');
    $hasRetail = \App\Services\SubscriptionService::hasFeature($orgId, 'module_retail');
@endphp

<div class="space-y-6">
    
    <!-- Minimalist Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white px-5 py-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 font-bold flex items-center justify-center text-lg shrink-0">
                📊
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-gray-900 tracking-tight" style="color: #0f172a !important;">Analytics & Business Health</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                        {{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : 'All Locations' }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5" style="color: #64748b !important;">Real-time sales, inventory, revenue & orders analytics</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($hasRetail)
                <a href="{{ route('organization.invoices.create') }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-2xs flex items-center gap-1">
                    <span>➕ Create Invoice</span>
                </a>
                <a href="{{ route('organization.products.index') }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl text-xs transition shadow-2xs flex items-center gap-1 border border-gray-200">
                    <span>📦 Products</span>
                </a>
            @endif

            @if($hasRestaurant)
                <a href="{{ route('organization.menu.pos.index') }}" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black rounded-xl text-xs transition shadow-2xs flex items-center gap-1">
                    <span>🍽️ Open POS</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Health Score & Insight Banner -->
    <div class="bg-white rounded-2xl shadow-2xs border border-gray-200/80 overflow-hidden flex flex-col md:flex-row">
        <div class="md:w-1/4 p-5 flex flex-col justify-center items-center bg-gray-50/50 border-b md:border-b-0 md:border-r border-gray-100">
            <h2 class="text-[10px] uppercase tracking-wider text-gray-400 font-extrabold mb-2">Business Health Score</h2>
            <div class="relative flex items-center justify-center">
                <div class="w-20 h-20 rounded-full border-4 {{ $health['border_color'] }} flex flex-col items-center justify-center bg-gray-50/50">
                    <span class="text-2xl font-black {{ $health['text_color'] }}">{{ $health['score'] }}</span>
                    <span class="text-[9px] text-gray-400 font-medium">/ 100</span>
                </div>
            </div>
            <span class="mt-2 text-[10px] font-bold px-2.5 py-0.5 {{ $health['badge_class'] }} rounded-full">{{ $health['label'] }}</span>
        </div>
        <div class="md:w-3/4 p-5 flex flex-col justify-center">
            <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-2">Diagnostic Insights</h3>
            @if(empty($health['insights']))
                <p class="text-emerald-600 flex items-center gap-2 text-xs font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Your business metrics are looking perfectly healthy across all key performance pillars!
                </p>
            @else
                <ul class="space-y-2">
                    @foreach($health['insights'] as $insight)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>{{ $insight }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- 4 KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Sales -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-gray-200/80 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[10px] font-extrabold uppercase tracking-wider">Sales This Month</h3>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($sales['sales_month'], 2) }}</div>
                <div class="text-xs flex items-center gap-1 {{ $sales['sales_growth'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-bold">
                    <span>{{ $sales['sales_growth'] >= 0 ? '↑' : '↓' }} {{ abs($sales['sales_growth']) }}% vs last month</span>
                </div>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 font-bold">
                💰
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-gray-200/80 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[10px] font-extrabold uppercase tracking-wider">Net Profit</h3>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($sales['profit_month'], 2) }}</div>
                <div class="text-xs flex items-center gap-1 {{ $sales['profit_growth'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-bold">
                    <span>{{ $sales['profit_growth'] >= 0 ? '↑' : '↓' }} {{ abs($sales['profit_growth']) }}% vs last month</span>
                </div>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600 font-bold">
                📈
            </div>
        </div>

        <!-- Inventory -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-gray-200/80 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[10px] font-extrabold uppercase tracking-wider">Inventory Stock Value</h3>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($inventory['stock_value'], 2) }}</div>
                <div class="text-xs text-amber-600 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                    {{ $inventory['low_stock_count'] }} items low stock
                </div>
            </div>
            <div class="p-3 bg-amber-50 rounded-xl text-amber-600 font-bold">
                📦
            </div>
        </div>

        <!-- Receivables -->
        <div class="bg-white p-5 rounded-2xl shadow-2xs border border-gray-200/80 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[10px] font-extrabold uppercase tracking-wider">Outstanding Due</h3>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($receivables['outstanding'], 2) }}</div>
                <div class="text-xs text-rose-600 font-bold flex items-center gap-1">
                    <span>₹{{ number_format($receivables['overdue'], 2) }} overdue</span>
                </div>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl text-rose-600 font-bold">
                ⏳
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 30-Day Sales Trend Line Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-2xs border border-gray-200/80 lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-base text-slate-900">30-Day Revenue Trend</h3>
                    <p class="text-xs text-gray-400">Daily billed revenue breakdown</p>
                </div>
                <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">Live DB</span>
            </div>
            <div class="relative h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Invoice Status Distribution Doughnut Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-2xs border border-gray-200/80 lg:col-span-1 flex flex-col justify-between space-y-4">
            <div>
                <h3 class="font-black text-base text-slate-900">Invoice Status</h3>
                <p class="text-xs text-gray-400">Distribution across active bills</p>
            </div>
            <div class="relative h-64 flex justify-center items-center my-auto">
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Dynamic Tables Section (Module Specific) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Invoices Table -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-black text-base text-slate-900">Recent Tax Invoices</h3>
                    <p class="text-xs text-gray-400">Latest customer invoices issued</p>
                </div>
                @if($hasRetail)
                    <a href="{{ route('organization.invoices.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                        View All &rarr;
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 uppercase text-gray-500 font-extrabold border-b border-gray-100">
                        <tr>
                            <th class="px-3 py-2.5 rounded-tl-xl">Invoice #</th>
                            <th class="px-3 py-2.5">Client / Phone</th>
                            <th class="px-3 py-2.5">Amount</th>
                            <th class="px-3 py-2.5">Status</th>
                            <th class="px-3 py-2.5 text-right rounded-tr-xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($recentInvoices as $inv)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="px-3 py-2.5 font-mono font-bold text-slate-900">
                                    {{ $inv->invoice_number }}
                                </td>
                                <td class="px-3 py-2.5 font-bold text-slate-800">
                                    {{ $inv->client ? $inv->client->name : 'Walk-in Customer' }}
                                </td>
                                <td class="px-3 py-2.5 font-black text-indigo-700">
                                    ₹{{ number_format($inv->grand_total, 2) }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($inv->status === 'Paid')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Paid</span>
                                    @elseif($inv->status === 'Due' || $inv->status === 'Overdue')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">{{ $inv->status }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $inv->status }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">View &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-400">No invoices issued yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Conditional Second Table (Restaurant Orders vs Low Stock Inventory) -->
        @if($hasRestaurant)
            <!-- Recent Restaurant / Counter Orders Table -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Recent POS & Counter Orders</h3>
                        <p class="text-xs text-gray-400">Live order queue & receipts</p>
                    </div>
                    <a href="{{ route('organization.menu.pos.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                        Go to POS &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-extrabold border-b border-gray-100">
                            <tr>
                                <th class="px-3 py-2.5 rounded-tl-xl">Order #</th>
                                <th class="px-3 py-2.5">Table / Token</th>
                                <th class="px-3 py-2.5">Total</th>
                                <th class="px-3 py-2.5">Status</th>
                                <th class="px-3 py-2.5 text-right rounded-tr-xl">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-3 py-2.5 font-mono font-bold text-slate-900">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-3 py-2.5 font-bold text-slate-800">
                                        {{ $order->table ? $order->table->name : ($order->customer_name ?? 'Counter Token') }}
                                    </td>
                                    <td class="px-3 py-2.5 font-black text-emerald-600">
                                        ₹{{ number_format($order->total, 2) }}
                                    </td>
                                    <td class="px-3 py-2.5">
                                        @if($order->payment_status === 'Paid' || $order->status === 'Completed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <a href="{{ route('organization.menu.pos.orders.print-receipt', $order) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs">Print Bill &rarr;</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-400">No recent orders.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Retail / Inventory Plan: Low Stock Inventory Items Alert Table -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Low Stock Inventory Alerts</h3>
                        <p class="text-xs text-gray-400">Products requiring restock attention</p>
                    </div>
                    @if($hasRetail)
                        <a href="{{ route('organization.inventory.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            Manage Stock &rarr;
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 uppercase text-gray-500 font-extrabold border-b border-gray-100">
                            <tr>
                                <th class="px-3 py-2.5 rounded-tl-xl">Product Name</th>
                                <th class="px-3 py-2.5">Current Stock</th>
                                <th class="px-3 py-2.5">Min Stock Level</th>
                                <th class="px-3 py-2.5 text-right rounded-tr-xl">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            @forelse($lowStockItems as $item)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-3 py-2.5 font-bold text-slate-900">
                                        {{ $item->name }}
                                        @if($item->sku)
                                            <span class="text-[10px] font-mono text-gray-400 block">SKU: {{ $item->sku }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 font-black text-rose-600">
                                        {{ number_format($item->quantity) }} units
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-500 font-semibold">
                                        {{ number_format($item->min_stock_level) }} units
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Reorder Soon
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-emerald-600 font-semibold">
                                        ✓ All product inventory levels are healthy!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 30-Day Sales Trend Line Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    
    const blueGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    blueGradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
    blueGradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales['labels']) !!},
            datasets: [{
                label: 'Daily Sales (₹)',
                data: {!! json_encode($dailySales['data']) !!},
                borderColor: '#4f46e5',
                backgroundColor: blueGradient,
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#4f46e5',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b', font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 10 } }
                }
            }
        }
    });

    // Invoice Status Doughnut Chart
    const invoiceCtx = document.getElementById('invoiceChart').getContext('2d');
    new Chart(invoiceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Partially Paid', 'Due', 'Overdue', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $invoiceStatuses['Paid'] }},
                    {{ $invoiceStatuses['Partially Paid'] }},
                    {{ $invoiceStatuses['Due'] }},
                    {{ $invoiceStatuses['Overdue'] }},
                    {{ $invoiceStatuses['Cancelled'] }}
                ],
                backgroundColor: [
                    '#10B981', // green
                    '#F59E0B', // yellow
                    '#3B82F6', // blue
                    '#EF4444', // red
                    '#9CA3AF'  // gray
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection
