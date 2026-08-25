@extends('layouts.sme')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Analytics & Business Health</h1>
        <div class="text-gray-500 text-sm">
            Overview for {{ session('active_location_id') ? \App\Models\Location::find(session('active_location_id'))->name : 'All Locations' }}
        </div>
    </div>

    <!-- Health Score Widget -->
    <div class="bg-white rounded-lg shadow border mb-6 flex overflow-hidden">
        <div class="w-1/4 p-6 border-r flex flex-col justify-center items-center bg-gray-50">
            <h2 class="text-lg font-bold text-gray-700 mb-2">Business Health</h2>
            <div class="text-6xl font-black {{ $health['color'] }}">{{ $health['score'] }}</div>
            <div class="text-sm text-gray-500 mt-2">/ 100</div>
        </div>
        <div class="w-3/4 p-6">
            <h3 class="font-bold text-gray-700 mb-3">Diagnostic Insights</h3>
            @if(empty($health['insights']))
                <p class="text-green-600 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Your business metrics are looking perfectly healthy across all pillars!
                </p>
            @else
                <ul class="space-y-2">
                    @foreach($health['insights'] as $insight)
                        <li class="flex items-start gap-2 text-gray-600">
                            <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            {{ $insight }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Sales -->
        <div class="bg-white p-5 rounded-lg shadow border">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Sales This Month</h3>
            <div class="text-3xl font-bold text-gray-800">${{ number_format($sales['sales_month'], 2) }}</div>
            <div class="mt-2 text-sm flex items-center gap-1 {{ $sales['sales_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($sales['sales_growth'] >= 0)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                @endif
                {{ abs($sales['sales_growth']) }}% vs last month
            </div>
        </div>

        <!-- Profit -->
        <div class="bg-white p-5 rounded-lg shadow border">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Profit This Month</h3>
            <div class="text-3xl font-bold text-gray-800">${{ number_format($sales['profit_month'], 2) }}</div>
            <div class="mt-2 text-sm flex items-center gap-1 {{ $sales['profit_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($sales['profit_growth'] >= 0)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                @endif
                {{ abs($sales['profit_growth']) }}% vs last month
            </div>
        </div>

        <!-- Inventory -->
        <div class="bg-white p-5 rounded-lg shadow border">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Inventory Value</h3>
            <div class="text-3xl font-bold text-gray-800">${{ number_format($inventory['stock_value'], 2) }}</div>
            <div class="mt-2 text-sm text-yellow-600">
                {{ $inventory['low_stock_count'] }} items low on stock
            </div>
        </div>

        <!-- Receivables -->
        <div class="bg-white p-5 rounded-lg shadow border">
            <h3 class="text-gray-500 text-sm font-medium mb-1">Outstanding Due</h3>
            <div class="text-3xl font-bold text-gray-800">${{ number_format($receivables['outstanding'], 2) }}</div>
            <div class="mt-2 text-sm text-red-600">
                ${{ number_format($receivables['overdue'], 2) }} overdue
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Line Chart -->
        <div class="bg-white p-5 rounded-lg shadow border col-span-2">
            <h3 class="font-bold text-gray-700 mb-4">30-Day Sales Trend</h3>
            <div class="relative h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="bg-white p-5 rounded-lg shadow border col-span-1">
            <h3 class="font-bold text-gray-700 mb-4">Invoice Status</h3>
            <div class="relative h-64 flex justify-center">
                <canvas id="invoiceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales['labels']) !!},
            datasets: [{
                label: 'Daily Sales ($)',
                data: {!! json_encode($dailySales['data']) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
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
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection
