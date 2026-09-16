@extends('layouts.sme')

@section('title', 'Restaurant Sales & Analytics')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-2 sm:pt-4 pb-20" x-data="{ 
    activeTab: '{{ request()->has('cust_page') ? 'customers' : 'dishes' }}', 
    customerSearch: '', 
    showCustomDates: {{ $filter === 'custom' ? 'true' : 'false' }},
    printOpen: false 
}">

    <!-- 1. Breadcrumb & Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2" aria-label="Breadcrumb">
                <a href="{{ route('organization.dashboard') }}" class="hover:text-slate-900 transition-colors">Dashboard</a>
                <span class="text-slate-400 font-bold">/</span>
                <a href="{{ route('organization.menu.index') }}" class="hover:text-slate-900 transition-colors">Restaurant</a>
                <span class="text-slate-400 font-bold">/</span>
                <span class="text-slate-950 font-extrabold">Sales & Analytics</span>
            </nav>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 text-xl font-black shadow-sm shrink-0">
                    📊
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight">Restaurant Sales & Analytics</h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">
                        Performance overview from <span class="font-bold text-slate-900">{{ $from->format('d M Y, h:i A') }}</span> to <span class="font-bold text-slate-900">{{ $to->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header Action: Print / Export Options -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap no-print">
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-lg shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Reports</span>
                    <svg class="w-3.5 h-3.5 transition-transform text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs font-semibold">
                    <button type="button" @click="printReport('all'); open = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 font-bold text-slate-900 flex items-center gap-2 transition">
                        <span>📊 Print Executive Summary & Sales</span>
                    </button>
                    <button type="button" @click="printReport('items'); open = false" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 font-bold text-slate-900 flex items-center gap-2 border-t border-slate-100 transition">
                        <span>🥘 Print Only Dish Volume Report</span>
                    </button>
                </div>
            </div>

            <a href="{{ route('organization.menu.pos.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 font-bold text-xs rounded-lg transition shadow-2xs">
                <span>🪑 Floor POS</span>
            </a>
            <a href="{{ route('organization.menu.counter.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-950 border border-amber-300 font-extrabold text-xs rounded-lg transition shadow-2xs">
                <span>⚡ Counter POS</span>
            </a>
        </div>
    </div>

    <!-- 2. Interactive Filter & Date Range Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs no-print">
        <form method="GET" action="{{ route('organization.menu.reports.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <!-- Left Preset Quick Filter Pills -->
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg border border-slate-200 text-xs font-bold">
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'today'])) }}" 
                   class="px-3 py-1.5 rounded-md transition {{ $filter === 'today' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Today
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'yesterday'])) }}" 
                   class="px-3 py-1.5 rounded-md transition {{ $filter === 'yesterday' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Yesterday
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'this_week'])) }}" 
                   class="px-3 py-1.5 rounded-md transition {{ $filter === 'this_week' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    This Week
                </a>
                <a href="{{ route('organization.menu.reports.index', array_merge(request()->query(), ['period' => 'this_month'])) }}" 
                   class="px-3 py-1.5 rounded-md transition {{ $filter === 'this_month' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    This Month
                </a>
                <button type="button" @click="showCustomDates = !showCustomDates"
                   class="px-3 py-1.5 rounded-md transition {{ $filter === 'custom' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Custom Range
                </button>
            </div>

            <!-- Right Order Type Selector -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">Channel:</label>
                <select name="order_type" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 cursor-pointer">
                    <option value="all" {{ $orderType === 'all' ? 'selected' : '' }}>🍽️ All Channels</option>
                    <option value="Dine-in" {{ $orderType === 'Dine-in' ? 'selected' : '' }}>🛋️ Dine-in Table Service</option>
                    <option value="Takeaway" {{ $orderType === 'Takeaway' ? 'selected' : '' }}>🛍️ Takeaway / Counter</option>
                </select>
            </div>

            <!-- Custom Date Range Sub-Bar -->
            <div x-show="showCustomDates" x-cloak class="w-full flex flex-wrap items-center gap-3 pt-3 border-t border-slate-100 mt-1">
                <input type="hidden" name="period" value="custom">
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-extrabold text-slate-700">From:</span>
                    <input type="date" name="start_date" value="{{ $startDate ?? now()->toDateString() }}" class="border border-slate-300 rounded-lg px-2.5 py-1 text-xs font-medium focus:border-amber-500">
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-extrabold text-slate-700">To:</span>
                    <input type="date" name="end_date" value="{{ $endDate ?? now()->toDateString() }}" class="border border-slate-300 rounded-lg px-2.5 py-1 text-xs font-medium focus:border-amber-500">
                </div>
                <button type="submit" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-lg text-xs transition shadow-xs">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 3. KPI Metric Strip (5 Executive Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 print-kpi-section">
        <!-- Total Net Revenue -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Net Revenue</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-xs font-bold">₹</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">₹{{ number_format($totalRevenue, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Gross billing across all channels</p>
            </div>
        </div>

        <!-- Total Orders Completed -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Total Orders</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center text-xs font-bold">🛒</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalOrders) }}</div>
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-600 mt-0.5">
                    <span class="text-indigo-600">Dine: {{ $dineInCount }}</span>
                    <span>•</span>
                    <span class="text-amber-600">Takeaway: {{ $takeawayCount }}</span>
                </div>
            </div>
        </div>

        <!-- Average Ticket Value (AOV) -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Avg Ticket Value</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center text-xs font-bold">📈</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">₹{{ number_format($avgOrderValue, 2) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Average spend per completed ticket</p>
            </div>
        </div>

        <!-- Unique Guests / Customers -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Guests Served</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center text-xs font-bold">👤</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalCustomersCount) }}</div>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Unique guests / telephone profiles</p>
            </div>
        </div>

        <!-- Total Food Portions Cooked -->
        <div class="bg-white border border-slate-200/90 p-4 rounded-xl shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Portions Cooked</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center text-xs font-bold">🍲</span>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-950 font-mono tracking-tight">{{ number_format($totalItemsSold) }}</div>
                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-600 mt-0.5">
                    <span class="text-emerald-700">🟢 Veg: {{ number_format($vegQuantity) }}</span>
                    <span>•</span>
                    <span class="text-rose-700">🔴 Non-Veg: {{ number_format($nonVegQuantity) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Visual Analytics: Trend Chart & Channel Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 print-breakdown-section">
        
        <!-- Daily Sales & Revenue Trend Chart (8 Cols) -->
        <div class="lg:col-span-8 bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2">
                        <span>📈 Sales & Revenue Trajectory</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daily gross earnings over the selected reporting period</p>
                </div>
                <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                    {{ count($trendData) }} Data Points
                </span>
            </div>

            <div class="h-64 relative">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Dining Channel & Dietary Ratio Card (4 Cols) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span>🍽️ Channel & Dietary Mix</span>
                </h3>

                <!-- Dining Channel Split -->
                <div class="space-y-3 mt-4">
                    @php
                        $dinePercent = $totalRevenue > 0 ? (($dineInRevenue / $totalRevenue) * 100) : 0;
                        $takePercent = $totalRevenue > 0 ? (($takeawayRevenue / $totalRevenue) * 100) : 0;
                        $vegPercent = ($vegRevenue + $nonVegRevenue) > 0 ? (($vegRevenue / ($vegRevenue + $nonVegRevenue)) * 100) : 100;
                        $nonVegPercent = 100 - $vegPercent;
                    @endphp

                    <!-- Channel Bar -->
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                            <span>Dine-in Service</span>
                            <span class="font-mono text-indigo-700">₹{{ number_format($dineInRevenue, 2) }} ({{ number_format($dinePercent, 0) }}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $dinePercent }}%"></div>
                        </div>

                        <div class="flex justify-between items-center text-xs font-bold text-slate-700 pt-2 border-t border-slate-200/60">
                            <span>Takeaway / Parcel</span>
                            <span class="font-mono text-amber-700">₹{{ number_format($takeawayRevenue, 2) }} ({{ number_format($takePercent, 0) }}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $takePercent }}%"></div>
                        </div>
                    </div>

                    <!-- Dietary Share -->
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Dietary Revenue Share</span>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-emerald-800 flex items-center gap-1">🟢 Veg Sales:</span>
                            <span class="font-mono text-emerald-900 font-extrabold">₹{{ number_format($vegRevenue, 2) }} ({{ number_format($vegPercent, 0) }}%)</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-rose-800 flex items-center gap-1">🔴 Non-Veg Sales:</span>
                            <span class="font-mono text-rose-900 font-extrabold">₹{{ number_format($nonVegRevenue, 2) }} ({{ number_format($nonVegPercent, 0) }}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden flex">
                            <div class="bg-emerald-500 h-2" style="width: {{ $vegPercent }}%"></div>
                            <div class="bg-rose-500 h-2" style="width: {{ $nonVegPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peak Ordering Hours Summary -->
            <div class="pt-3 border-t border-slate-100 text-xs text-slate-500">
                <span class="font-bold text-slate-700">Peak Ordering Hours:</span>
                @if(count($hourlyDistribution) > 0)
                    @php 
                        $busiestHour = $hourlyDistribution->sortByDesc('orders_count')->first();
                        $hourFormatted = \Carbon\Carbon::createFromTime($busiestHour->hour, 0)->format('g:i A');
                    @endphp
                    <span class="text-slate-900 font-bold ml-1">{{ $hourFormatted }} ({{ $busiestHour->orders_count }} orders)</span>
                @else
                    <span class="text-slate-400 ml-1">No traffic yet</span>
                @endif
            </div>
        </div>

    </div>

    <!-- 5. Top 5 Best Sellers Leaderboard -->
    @if(count($topDishes) > 0)
    <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2">
                    <span>🏆 Top Selling Dishes Leaderboard</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Most popular dishes ranked by total revenue contribution</p>
            </div>
            <span class="text-[11px] font-bold text-slate-600 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-md">
                Top 5 High Performers
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach($topDishes as $rank => $dish)
                @php
                    $rankMedal = $rank === 0 ? '🥇' : ($rank === 1 ? '🥈' : ($rank === 2 ? '🥉' : '#' . ($rank + 1)));
                    $dishVeg = (bool)$dish->is_veg;
                @endphp
                <div class="p-3 rounded-lg border border-slate-200/90 bg-slate-50/70 hover:bg-slate-50 transition flex flex-col justify-between space-y-2">
                    <div class="flex items-start justify-between gap-1.5">
                        <span class="text-base font-black">{{ $rankMedal }}</span>
                        <span class="text-[10px] font-bold font-mono px-1.5 py-0.5 rounded {{ $dishVeg ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ $dishVeg ? 'Veg' : 'Non-Veg' }}
                        </span>
                    </div>
                    <div>
                        <div class="font-extrabold text-xs text-slate-900 line-clamp-1" title="{{ $dish->name_snapshot }}">{{ $dish->name_snapshot }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ number_format($dish->total_quantity) }} portions sold</div>
                    </div>
                    <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500">Revenue</span>
                        <span class="text-xs font-black text-emerald-700 font-mono">₹{{ number_format($dish->total_revenue, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 6. Navigation Tabs Bar -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-1 no-print">
        <button type="button" @click="activeTab = 'dishes'" 
            :class="activeTab === 'dishes' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-bold bg-slate-100 hover:bg-slate-200'" 
            class="py-2 px-4 text-xs rounded-lg transition flex items-center gap-2">
            <span>🥘 Dish Sales & Volume</span>
            <span class="px-2 py-0.5 rounded-md bg-white/20 text-white font-mono text-[10px]">{{ $itemSales->total() }} Dishes</span>
        </button>

        <button type="button" @click="activeTab = 'customers'" 
            :class="activeTab === 'customers' ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-bold bg-slate-100 hover:bg-slate-200'" 
            class="py-2 px-4 text-xs rounded-lg transition flex items-center gap-2">
            <span>👤 Customer Order & Spend History</span>
            <span class="px-2 py-0.5 rounded-md bg-white/20 text-white font-mono text-[10px]">{{ $customerSummary->total() }} Customers</span>
        </button>
    </div>

    <!-- TAB 1: DISH SALES BREAKDOWN -->
    <div x-show="activeTab === 'dishes'" class="space-y-4 print-dishes-tab">
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4 print-dishes-section">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2">
                        <span>🥘 Dish-wise Volume & Profitability Breakdown</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Itemized performance ranking dishes by revenue and sales volume</p>
                </div>
                <span class="text-xs font-extrabold text-slate-700 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 font-mono">
                    {{ $itemSales->total() }} Unique Dishes
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="p-3 w-12">#</th>
                            <th class="p-3">Dish / Item Name</th>
                            <th class="p-3 text-center">Diet</th>
                            <th class="p-3 text-right">Unit Price</th>
                            <th class="p-3 text-center">Portions Sold</th>
                            <th class="p-3 text-right">Total Revenue</th>
                            <th class="p-3 text-right">Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($itemSales as $index => $item)
                            @php 
                                $sharePercent = $totalRevenue > 0 ? (($item->total_revenue / $totalRevenue) * 100) : 0;
                                $isVeg = (bool)$item->is_veg;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3 font-bold text-slate-400 font-mono">{{ $itemSales->firstItem() + $index }}</td>
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        @if($isVeg)
                                            <span title="Vegetarian" class="w-3.5 h-3.5 rounded border border-emerald-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span></span>
                                        @else
                                            <span title="Non-Vegetarian" class="w-3.5 h-3.5 rounded border border-rose-500 flex items-center justify-center p-0.5 shrink-0"><span class="w-1.5 h-1.5 bg-rose-500 rotate-45"></span></span>
                                        @endif
                                        <span class="font-extrabold text-slate-950">{{ $item->name_snapshot }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isVeg ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $isVeg ? 'Veg' : 'Non-Veg' }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-mono text-slate-600">₹{{ number_format($item->price_snapshot, 2) }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-900 font-black font-mono border border-slate-200">
                                        {{ number_format($item->total_quantity) }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-black text-emerald-700 font-mono text-sm">
                                    ₹{{ number_format($item->total_revenue, 2) }}
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-bold text-slate-700 font-mono">{{ number_format($sharePercent, 1) }}%</span>
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200">
                                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, $sharePercent) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400 text-xs font-semibold">
                                    🍽️ No restaurant dish sales recorded for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Dish Table Pagination Links -->
            <div class="pt-3 border-t border-slate-100 no-print">
                {{ $itemSales->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 2: CUSTOMER ORDER & SPEND HISTORY -->
    <div x-show="activeTab === 'customers'" class="space-y-4 print-customers-tab" x-cloak>
        <div class="bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs space-y-4">
            <!-- Header & Search -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-950 uppercase tracking-wider flex items-center gap-2">
                        <span>👤 Guest & Customer Order History</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Track repeat guest visits, total lifetime spend, and dish preferences</p>
                </div>

                <div class="w-full sm:w-72 no-print">
                    <input type="text" x-model="customerSearch" placeholder="🔍 Search guest name or phone..." 
                        class="w-full border-slate-300 rounded-lg text-xs py-2 px-3 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-medium">
                </div>
            </div>

            <!-- Customer Report Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-extrabold text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="p-3 w-12">#</th>
                            <th class="p-3">Guest Profile</th>
                            <th class="p-3 text-center">Visits / Orders</th>
                            <th class="p-3 text-right">Total Spend</th>
                            <th class="p-3">Dishes Ordered</th>
                            <th class="p-3 text-right">Last Dining Time</th>
                            <th class="p-3 text-right">Receipt / Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($customerSummary as $index => $c)
                            <tr x-show="!customerSearch || '{{ strtolower($c['customer_name']) }}'.includes(customerSearch.toLowerCase()) || '{{ strtolower($c['customer_phone']) }}'.includes(customerSearch.toLowerCase())" 
                                class="hover:bg-slate-50/80 transition">
                                
                                <td class="p-3 font-bold text-slate-400 font-mono">{{ $customerSummary->firstItem() + $index }}</td>
                                
                                <!-- Customer Info -->
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-amber-500/15 text-slate-950 font-black flex items-center justify-center text-xs shrink-0 border border-amber-500/30">
                                            {{ strtoupper(substr($c['customer_name'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-950 text-xs">{{ $c['customer_name'] }}</div>
                                            <div class="text-[11px] font-mono text-slate-500">{{ $c['customer_phone'] }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Orders -->
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-900 font-black font-mono border border-slate-200">
                                        {{ $c['total_orders'] }} {{ $c['total_orders'] === 1 ? 'Order' : 'Orders' }}
                                    </span>
                                </td>

                                <!-- Total Spend -->
                                <td class="p-3 text-right font-black text-emerald-700 font-mono text-sm">
                                    ₹{{ number_format($c['total_spend'], 2) }}
                                </td>

                                <!-- Dishes Purchased List -->
                                <td class="p-3 max-w-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($c['items_ordered'] as $dishName => $qty)
                                            <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-md bg-slate-50 text-slate-800 font-semibold border border-slate-200">
                                                <span>{{ $dishName }}</span>
                                                <span class="font-mono font-bold text-amber-700 bg-amber-50 px-1 rounded">x{{ $qty }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Last Order Date -->
                                <td class="p-3 text-right font-mono text-xs text-slate-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($c['last_order_at'])->format('d M Y, h:i A') }}
                                </td>

                                <!-- Receipt / Action Buttons -->
                                <td class="p-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        @foreach($c['orders'] as $ord)
                                            <a href="{{ route('organization.menu.pos.orders.print-receipt', $ord->id) }}" target="_blank" 
                                               title="View/Print Receipt for Order {{ $ord->token_number ? '#'.$ord->token_number : '#'.$ord->id }} (₹{{ number_format($ord->total, 2) }})"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-500 hover:text-white transition">
                                                🧾 {{ $ord->token_number ? '#'.$ord->token_number : 'Receipt' }}
                                            </a>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400 text-xs font-semibold">
                                    👤 No customer order records found in the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Customer Table Pagination Links -->
            <div class="pt-3 border-t border-slate-100 no-print">
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
    const ordersCount = trendData.map(d => parseInt(d.orders_count));

    const canvasEl = document.getElementById('salesTrendChart');
    if (canvasEl) {
        const ctx = canvasEl.getContext('2d');
        
        // Gradient fill for smooth modern look
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(245, 158, 11, 0.35)');
        gradient.addColorStop(1, 'rgba(245, 158, 11, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['No Data'],
                datasets: [
                    {
                        label: 'Gross Sales (₹)',
                        data: revenues.length > 0 ? revenues : [0],
                        borderColor: '#f59e0b',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#d97706',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#fbbf24',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return ' Gross Sales: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            color: '#64748b',
                            callback: function(value) { return '₹' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    }
});

function printReport(mode) {
    if (mode === 'items') {
        document.body.classList.add('print-items-only');
    } else {
        document.body.classList.remove('print-items-only');
    }
    window.print();
    setTimeout(function() {
        document.body.classList.remove('print-items-only');
    }, 1000);
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    
    body:not(.print-items-only) .print-dishes-tab,
    body:not(.print-items-only) .print-customers-tab {
        display: block !important;
    }

    body.print-items-only .print-kpi-section,
    body.print-items-only .print-breakdown-section,
    body.print-items-only .print-customers-tab {
        display: none !important;
    }

    body.print-items-only .print-dishes-tab,
    body.print-items-only .print-dishes-section {
        display: block !important;
    }

    body { background: white !important; font-size: 11px !important; }
    .shadow-2xs, .shadow-xs, .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; }
    .border { border-color: #cbd5e1 !important; }
}
</style>
@endsection
