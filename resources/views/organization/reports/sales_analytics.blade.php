@extends('layouts.sme')

@section('title', 'Sales & Performance Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Operations</span>
                <span>&rsaquo;</span>
                <span class="text-slate-900 font-bold">Sales & Analytics</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span>📊 Sales & Performance Analytics</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Showing data for: <strong class="text-slate-800 font-bold">{{ $rangeTitle }}</strong>
            </p>
        </div>

        <!-- Filter Presets & Export Actions -->
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 text-xs font-bold">
                <a href="{{ route('organization.sales-analytics.index', ['range' => 'today']) }}" class="px-3 py-1.5 rounded-lg transition {{ $range === 'today' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">Today</a>
                <a href="{{ route('organization.sales-analytics.index', ['range' => 'this_week']) }}" class="px-3 py-1.5 rounded-lg transition {{ $range === 'this_week' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">This Week</a>
                <a href="{{ route('organization.sales-analytics.index', ['range' => 'this_month']) }}" class="px-3 py-1.5 rounded-lg transition {{ $range === 'this_month' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">This Month</a>
                <a href="{{ route('organization.sales-analytics.index', ['range' => 'last_month']) }}" class="px-3 py-1.5 rounded-lg transition {{ $range === 'last_month' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">Last Month</a>
                <a href="{{ route('organization.sales-analytics.index', ['range' => 'this_year']) }}" class="px-3 py-1.5 rounded-lg transition {{ $range === 'this_year' ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-600 hover:text-slate-900' }}">This Year</a>
            </div>

            <a href="{{ route('organization.sales-analytics.export', ['range' => $range, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-2xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Custom Date Filter Bar -->
    <form method="GET" action="{{ route('organization.sales-analytics.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs flex flex-wrap items-center gap-3">
        <input type="hidden" name="range" value="custom">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
            <span>📅 Custom Filter:</span>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 font-medium">
            <span class="text-xs text-slate-400">to</span>
            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 font-medium">
        </div>
        <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-lg shadow-2xs transition">Apply Custom Range</button>
    </form>

    <!-- Top 4 KPI Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Sales Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Sales Revenue</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 font-bold text-sm">💰</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-950">
                ₹{{ number_format($totalSales, 2) }}
            </div>
            <div class="flex items-center gap-1.5 text-xs font-bold">
                <span class="{{ $salesGrowth >= 0 ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-rose-600 bg-rose-50 border-rose-200' }} px-2 py-0.5 rounded-full border text-[11px]">
                    {{ $salesGrowth >= 0 ? '↑ +' : '↓ ' }}{{ $salesGrowth }}%
                </span>
                <span class="text-slate-400 font-normal">vs prev period</span>
            </div>
        </div>

        <!-- 2. Estimated Net Profit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Est. Gross Profit</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-sm">📈</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-600">
                ₹{{ number_format($totalProfit, 2) }}
            </div>
            <div class="text-xs text-slate-500 font-medium">
                Selling Price minus Purchase Cost
            </div>
        </div>

        <!-- 3. Invoices & Average Order Value -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Invoices</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm">🧾</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-950">
                {{ number_format($totalInvoices) }}
            </div>
            <div class="text-xs text-slate-600 font-semibold">
                Avg. Order Value (AOV): <b class="text-slate-950">₹{{ number_format($avgOrderValue, 2) }}</b>
            </div>
        </div>

        <!-- 4. Total GST Tax Collected -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">GST Tax Collected</span>
                <span class="p-2 rounded-xl bg-sky-50 text-sky-600 font-bold text-sm">🏛️</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-slate-950">
                ₹{{ number_format($totalTax, 2) }}
            </div>
            <div class="text-xs text-slate-500 font-medium">
                Combined CGST & SGST Amount
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Line/Bar Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>📈 Revenue Trend</span>
                </h3>
                <span class="text-xs text-slate-400 font-medium">Daily Sales Revenue</span>
            </div>
            <div class="h-64 relative">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Payment Methods Donut Breakdown (1 col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>💳 Payment Methods</span>
                </h3>
                <span class="text-xs text-slate-400 font-medium">Collection Share</span>
            </div>
            
            <div class="space-y-3">
                @foreach($methodBreakdown as $method => $info)
                    @php
                        $percentage = $totalSales > 0 ? round(($info['amount'] / $totalSales) * 100, 1) : 0;
                        $badgeColor = match($method) {
                            'Cash' => 'bg-emerald-500',
                            'UPI' => 'bg-indigo-500',
                            'Card' => 'bg-amber-500',
                            'Bank Transfer' => 'bg-sky-500',
                            default => 'bg-slate-400',
                        };
                    @endphp
                    <div class="space-y-1">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $badgeColor }}"></span>
                                {{ $method }}
                            </span>
                            <span class="font-black text-slate-950">₹{{ number_format($info['amount'], 2) }} <span class="text-slate-400 font-normal">({{ $percentage }}%)</span></span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="{{ $badgeColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Category Sales & Top 10 Best Sellers Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 10 Best Selling Products -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🏆 Top 10 Best Selling Products</span>
                </h3>
                <span class="text-xs text-slate-400 font-medium">By Revenue</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-extrabold uppercase text-[10px] tracking-wider">
                            <th class="py-2 px-2">Product</th>
                            <th class="py-2 px-2 text-center">Qty Sold</th>
                            <th class="py-2 px-2 text-right">Revenue</th>
                            <th class="py-2 px-2 text-right">Est. Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                        @forelse($topProducts as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-2">
                                    <div class="font-bold text-slate-950">{{ $item->product_name }}</div>
                                    @if($item->sku)<div class="text-[10px] text-slate-400 font-mono">SKU: {{ $item->sku }}</div>@endif
                                </td>
                                <td class="py-2.5 px-2 text-center font-bold">{{ $item->units_sold }}</td>
                                <td class="py-2.5 px-2 text-right font-black text-slate-950">₹{{ number_format($item->total_revenue, 2) }}</td>
                                <td class="py-2.5 px-2 text-right font-bold text-emerald-600">₹{{ number_format($item->total_profit, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400 text-xs">No product sales recorded in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales by Category & Top Customers Grid -->
        <div class="space-y-6">
            <!-- Category Revenue Breakdown -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🏷️ Sales by Category</span>
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($categorySales as $cat)
                        @php
                            $catShare = $totalSales > 0 ? round(($cat->total_revenue / $totalSales) * 100, 1) : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-900">{{ $cat->category_name }}</span>
                                <span class="font-black text-slate-950">₹{{ number_format($cat->total_revenue, 2) }} <span class="text-slate-400 font-normal">({{ $catShare }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $catShare }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-slate-400 text-xs">No category sales found.</div>
                    @endforelse
                </div>
            </div>

            <!-- Top Clients -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>👑 Top High-Value Clients</span>
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($topClients as $client)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-950">{{ $client->client_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $client->phone ?? 'No Phone' }} &bull; {{ $client->invoice_count }} Invoices</div>
                            </div>
                            <div class="text-right font-black text-slate-950">
                                ₹{{ number_format($client->total_spent, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-slate-400 text-xs">No client transactions found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    const labels = @json($chartData['labels']);
    const data = @json($chartData['data']);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: data,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#f59e0b',
                pointRadius: 3.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, family: 'Inter' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { size: 10, family: 'Inter' },
                        callback: function(value) { return '₹' + value; }
                    }
                }
            }
        }
    });
});
</script>
@endsection
