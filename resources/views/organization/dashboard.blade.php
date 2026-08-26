@extends('layouts.sme')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Analytics & Business Health</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Overview for <span class="font-semibold text-gray-700">{{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : 'All Locations' }}</span>
            </p>
        </div>
    </div>

    <!-- Health Score & Insight Banner -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
        <div class="md:w-1/4 p-6 flex flex-col justify-center items-center bg-gray-50/50 border-b md:border-b-0 md:border-r border-gray-100">
            <h2 class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-3">Health Score</h2>
            <div class="relative flex items-center justify-center">
                <!-- Circular progress ring look (simplified with Tailwind + Text) -->
                <div class="w-24 h-24 rounded-full border-4 border-emerald-500 flex flex-col items-center justify-center bg-emerald-50/30">
                    <span class="text-3xl font-black text-emerald-600">{{ $health['score'] }}</span>
                    <span class="text-[10px] text-gray-400 font-medium">/ 100</span>
                </div>
            </div>
            <span class="mt-3 text-xs font-semibold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full">Strong Performance</span>
        </div>
        <div class="md:w-3/4 p-6 flex flex-col justify-center">
            <h3 class="font-bold text-gray-800 text-sm mb-3">Diagnostic Insights</h3>
            @if(empty($health['insights']))
                <p class="text-emerald-600 flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Your business metrics are looking perfectly healthy across all pillars! Keep up the great work.
                </p>
            @else
                <ul class="space-y-2.5">
                    @foreach($health['insights'] as $insight)
                        <li class="flex items-start gap-2.5 text-xs text-gray-600">
                            <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span>{{ $insight }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Sales -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Sales This Month</h3>
                <div class="text-2xl font-bold text-gray-900">₹{{ number_format($sales['sales_month'], 2) }}</div>
                <div class="text-[11px] flex items-center gap-1 {{ $sales['sales_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                    @if($sales['sales_growth'] >= 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                    @endif
                    {{ abs($sales['sales_growth']) }}% vs last month
                </div>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Net Profit</h3>
                <div class="text-2xl font-bold text-gray-900">₹{{ number_format($sales['profit_month'], 2) }}</div>
                <div class="text-[11px] flex items-center gap-1 {{ $sales['profit_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                    @if($sales['profit_growth'] >= 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                    @endif
                    {{ abs($sales['profit_growth']) }}% vs last month
                </div>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <!-- Inventory -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Inventory Value</h3>
                <div class="text-2xl font-bold text-gray-900">₹{{ number_format($inventory['stock_value'], 2) }}</div>
                <div class="text-[11px] text-amber-600 font-semibold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block animate-pulse"></span>
                    {{ $inventory['low_stock_count'] }} items low on stock
                </div>
            </div>
            <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>

        <!-- Receivables -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Outstanding Due</h3>
                <div class="text-2xl font-bold text-gray-900">₹{{ number_format($receivables['outstanding'], 2) }}</div>
                <div class="text-[11px] text-rose-600 font-semibold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span>
                    ₹{{ number_format($receivables['overdue'], 2) }} overdue
                </div>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Line Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
            <h3 class="font-bold text-gray-800 text-sm mb-4">30-Day Sales Trend</h3>
            <div class="relative h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-1 flex flex-col">
            <h3 class="font-bold text-gray-800 text-sm mb-4">Invoice Status</h3>
            <div class="relative h-64 flex-1 flex justify-center items-center">
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    
    // Create soft gradient for premium line fill
    const blueGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.00)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales['labels']) !!},
            datasets: [{
                label: 'Daily Sales (₹)',
                data: {!! json_encode($dailySales['data']) !!},
                borderColor: '#2563eb',
                backgroundColor: blueGradient,
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#2563eb',
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
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#9ca3af', font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { size: 10 } }
                }
            }
        }
    });

    // Invoice Status Chart
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
                borderWidth: 4,
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
                    labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                }
            },
            cutout: '72%'
        }
    });
});
</script>
@endsection
