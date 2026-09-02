@extends('layouts.sme')

@section('title', 'Restaurant Sales & Customer Analytics')

@section('content')
<div class="space-y-6" x-data="{ activeTab: '{{ request()->has('cust_page') ? 'customers' : 'dishes' }}', customerSearch: '', showCustomDates: {{ $filter === 'custom' ? 'true' : 'false' }} }">

    <!-- Header & Date Filter Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <h1 class="text-xl font-black text-gray-900 flex items-center gap-2 tracking-tight">
                <span>📊 Restro Sales & Customer Analytics</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Showing data from <span class="font-bold text-gray-800">{{ $from->format('d M Y, h:i A') }}</span> to <span class="font-bold text-gray-800">{{ $to->format('d M Y, h:i A') }}</span>
            </p>
        </div>

        <!-- Dynamic Filter Form -->
        <form method="GET" action="{{ route('organization.menu.reports.index') }}" class="flex flex-wrap items-center gap-2">
            <!-- Quick Preset Period -->
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl border border-gray-200 text-xs font-semibold">
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'today'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $filter === 'today' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Today
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'yesterday'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $filter === 'yesterday' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Yesterday
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'this_week'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $filter === 'this_week' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    This Week
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'this_month'])) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $filter === 'this_month' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    This Month
                </a>
                <button type="button" @click="showCustomDates = !showCustomDates"
                   class="px-3 py-1.5 rounded-lg transition {{ $filter === 'custom' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-gray-600 hover:text-gray-900' }}">
                    Custom Range
                </button>
            </div>

            <!-- Dynamic Order Type Selector -->
            <select name="order_type" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-gray-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                <option value="all" {{ $orderType === 'all' ? 'selected' : '' }}>🍽️ All Order Types</option>
                <option value="Dine-in" {{ $orderType === 'Dine-in' ? 'selected' : '' }}>🛋️ Dine-in Service</option>
                <option value="Takeaway" {{ $orderType === 'Takeaway' ? 'selected' : '' }}>🛍️ Takeaway / Parcel</option>
            </select>

            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition flex items-center gap-1.5 shadow-xs">
                🖨️ Print
            </button>

            <!-- Custom Date Range Sub-Bar -->
            <div x-show="showCustomDates" x-cloak class="w-full flex items-center gap-2 pt-2 border-t border-gray-100 mt-2">
                <input type="hidden" name="period" value="custom">
                <div class="flex items-center gap-1 text-xs">
                    <span class="font-bold text-gray-600">From:</span>
                    <input type="date" name="start_date" value="{{ $startDate ?? now()->toDateString() }}" class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-1 text-xs">
                    <span class="font-bold text-gray-600">To:</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? now()->toDateString() }}" class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:border-indigo-500">
                </div>
                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white font-bold rounded-lg text-xs hover:bg-indigo-700 transition">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 p-5 rounded-2xl shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Total Revenue</p>
                    <h3 class="text-2xl font-black text-emerald-950 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-700 flex items-center justify-center text-xl font-bold">
                    💵
                </div>
            </div>
            <p class="text-[11px] text-emerald-700 mt-2 font-medium">Gross sales generated in selected period</p>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 p-5 rounded-2xl shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Total Orders</p>
                    <h3 class="text-2xl font-black text-amber-950 mt-1">{{ number_format($totalOrders) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-700 flex items-center justify-center text-xl font-bold">
                    🛒
                </div>
            </div>
            <p class="text-[11px] text-amber-700 mt-2 font-medium">Completed Dine-in & Takeaway tickets</p>
        </div>

        <!-- Total Customers Served -->
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200/80 p-5 rounded-2xl shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-indigo-800">Unique Customers</p>
                    <h3 class="text-2xl font-black text-indigo-950 mt-1">{{ number_format($totalCustomersCount) }} <span class="text-xs font-semibold text-indigo-600">Guests</span></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-700 flex items-center justify-center text-xl font-bold">
                    👤
                </div>
            </div>
            <p class="text-[11px] text-indigo-700 mt-2 font-medium">Unique guests served in period</p>
        </div>

        <!-- Average Order Value (AOV) -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200/80 p-5 rounded-2xl shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-800">Avg Order Value</p>
                    <h3 class="text-2xl font-black text-purple-950 mt-1">₹{{ number_format($avgOrderValue, 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/20 text-purple-700 flex items-center justify-center text-xl font-bold">
                    📈
                </div>
            </div>
            <p class="text-[11px] text-purple-700 mt-2 font-medium">Average ticket revenue per table/order</p>
        </div>

    </div>

    <!-- Navigation Tabs Bar -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-1">
        <button type="button" @click="activeTab = 'dishes'" 
            :class="activeTab === 'dishes' ? 'border-indigo-600 text-indigo-600 font-black' : 'border-transparent text-gray-500 hover:text-gray-700 font-semibold'" 
            class="py-2.5 px-4 text-xs border-b-2 transition flex items-center gap-2">
            <span>🥘 Dish Sales & Revenue Trend</span>
            <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-bold text-[10px]">{{ $itemSales->total() }} Dishes</span>
        </button>

        <button type="button" @click="activeTab = 'customers'" 
            :class="activeTab === 'customers' ? 'border-indigo-600 text-indigo-600 font-black' : 'border-transparent text-gray-500 hover:text-gray-700 font-semibold'" 
            class="py-2.5 px-4 text-xs border-b-2 transition flex items-center gap-2">
            <span>👤 Customer Order & Purchase History</span>
            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">{{ $customerSummary->total() }} Customers</span>
        </button>
    </div>

    <!-- TAB 1: DISH SALES & REVENUE ANALYTICS -->
    <div x-show="activeTab === 'dishes'" class="space-y-6">

        <!-- Order Types Breakdown & Revenue Trend -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Order Type Breakdown (4 Cols) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                    <span>🍽️ Order Types Breakdown</span>
                </h3>

                <div class="space-y-4">
                    <!-- Dine-In -->
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-gray-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Dine-in (Table Service)
                            </span>
                            <span class="font-black text-indigo-600">{{ $dineInCount }} Orders</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-xs text-gray-500">Revenue:</span>
                            <span class="font-black text-gray-900">₹{{ number_format($dineInRevenue, 2) }}</span>
                        </div>
                    </div>

                    <!-- Takeaway / Parcel -->
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-gray-800 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Takeaway / Parcel
                            </span>
                            <span class="font-black text-emerald-600">{{ $takeawayCount }} Orders</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-xs text-gray-500">Revenue:</span>
                            <span class="font-black text-gray-900">₹{{ number_format($takeawayRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Trend Chart (8 Cols) -->
            <div class="lg:col-span-8 bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>📈 Daily Revenue Trend</span>
                    </h3>
                </div>

                <div class="h-64 relative">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Detailed Food Item Sales Table -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                        <span>🥘 Dish-wise Sales & Revenue Breakdown</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Comprehensive report of all food dishes prepared and sold</p>
                </div>
                <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200">
                    {{ $itemSales->total() }} Unique Items Sold
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold text-[10px] border-b border-gray-200">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Dish / Item Name</th>
                            <th class="p-3 text-center">Unit Price (₹)</th>
                            <th class="p-3 text-center">Qty Sold</th>
                            <th class="p-3 text-right">Total Revenue (₹)</th>
                            <th class="p-3 text-right">% Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                        @forelse($itemSales as $index => $item)
                            @php 
                                $sharePercent = $totalRevenue > 0 ? (($item->total_revenue / $totalRevenue) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="p-3 font-bold text-gray-400">{{ $itemSales->firstItem() + $index }}</td>
                                <td class="p-3 font-bold text-gray-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span>{{ $item->name_snapshot }}</span>
                                </td>
                                <td class="p-3 text-center font-mono text-gray-600">₹{{ number_format($item->price_snapshot, 2) }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 font-black font-mono border border-indigo-200/60">
                                        {{ number_format($item->total_quantity) }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-black text-emerald-600 text-sm">
                                    ₹{{ number_format($item->total_revenue, 2) }}
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-bold text-gray-700 font-mono">{{ number_format($sharePercent, 1) }}%</span>
                                        <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $sharePercent) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-400 text-xs">
                                    🍽️ No restaurant sales recorded for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Dish Table Pagination Links -->
            <div class="pt-2 border-t border-gray-100">
                {{ $itemSales->links() }}
            </div>
        </div>

    </div>

    <!-- TAB 2: CUSTOMER ORDER & PURCHASE HISTORY -->
    <div x-show="activeTab === 'customers'" class="space-y-6" x-cloak>
        
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs space-y-4">
            
            <!-- Header & Search -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b pb-4">
                <div>
                    <h3 class="text-base font-black text-gray-900 flex items-center gap-2">
                        <span>👤 Customer Order & Item Purchase History</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">See exactly which customer ordered what dishes and how much they spent</p>
                </div>

                <div class="w-full sm:w-72">
                    <input type="text" x-model="customerSearch" placeholder="🔍 Search customer name or phone..." 
                        class="w-full border-gray-300 rounded-xl text-xs py-2 px-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Customer Report Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold text-[10px] border-b border-gray-200">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Customer Info</th>
                            <th class="p-3 text-center">Orders Count</th>
                            <th class="p-3 text-right">Total Spent (₹)</th>
                            <th class="p-3">Dishes / Items Purchased</th>
                            <th class="p-3 text-right">Last Order Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                        @forelse($customerSummary as $index => $c)
                            <tr x-show="!customerSearch || '{{ strtolower($c['customer_name']) }}'.includes(customerSearch.toLowerCase()) || '{{ strtolower($c['customer_phone']) }}'.includes(customerSearch.toLowerCase())" 
                                class="hover:bg-gray-50/80 transition">
                                
                                <td class="p-3 font-bold text-gray-400">{{ $customerSummary->firstItem() + $index }}</td>
                                
                                <!-- Customer Info -->
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($c['customer_name'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-xs">{{ $c['customer_name'] }}</div>
                                            <div class="text-[11px] font-mono text-gray-500">{{ $c['customer_phone'] }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Orders -->
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 font-extrabold font-mono border border-amber-200/70">
                                        {{ $c['total_orders'] }} {{ $c['total_orders'] === 1 ? 'Order' : 'Orders' }}
                                    </span>
                                </td>

                                <!-- Total Spend -->
                                <td class="p-3 text-right font-black text-emerald-600 text-sm">
                                    ₹{{ number_format($c['total_spend'], 2) }}
                                </td>

                                <!-- Dishes Purchased List -->
                                <td class="p-3 max-w-xs">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($c['items_ordered'] as $dishName => $qty)
                                            <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-800 font-semibold border border-gray-200">
                                                <span>{{ $dishName }}</span>
                                                <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-1 rounded">x{{ $qty }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Last Order Date -->
                                <td class="p-3 text-right font-mono text-xs text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($c['last_order_at'])->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-400 text-xs">
                                    👤 No customer order records found in selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Customer Table Pagination Links -->
            <div class="pt-2 border-t border-gray-100">
                {{ $customerSummary->links() }}
            </div>

        </div>

    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const trendData = @json($trendData);

    const labels = trendData.map(d => d.date);
    const revenues = trendData.map(d => parseFloat(d.revenue));

    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length > 0 ? labels : ['No Data'],
            datasets: [{
                label: 'Revenue (₹)',
                data: revenues.length > 0 ? revenues : [0],
                backgroundColor: 'rgba(79, 70, 229, 0.85)',
                borderColor: '#4f46e5',
                borderWidth: 1.5,
                borderRadius: 8
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
                    ticks: {
                        callback: function(value) { return '₹' + value; }
                    }
                }
            }
        }
    });
});
</script>
@endsection
