@extends('layouts.super-admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Platform Overview</h1>
    <p class="text-gray-500 mt-1">High-level statistics across all organizations.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Total Organizations</div>
        <div class="text-3xl font-bold text-gray-900">{{ $orgCount }}</div>
        <div class="text-sm text-green-600 mt-2 font-medium">{{ $activeOrgs }} active</div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Total Platform Users</div>
        <div class="text-3xl font-bold text-gray-900">{{ $userCount }}</div>
        <div class="text-sm text-gray-500 mt-2 font-medium">Across all tenants</div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-2">Platform Revenue</div>
        <div class="text-3xl font-bold text-gray-900">₹0</div>
        <div class="text-sm text-gray-500 mt-2 font-medium">This month (Placeholder)</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Monthly Recurring Revenue (MRR)</h3>
        <div style="height: 240px; position: relative;">
            <canvas id="mrrChart"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4">Subscription Breakdown</h3>
        <div style="height: 240px; position: relative; display: flex; justify-content: center;">
            <canvas id="subsChart" style="max-width: 240px; max-height: 240px;"></canvas>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
    <div class="flex gap-4">
        <a href="{{ route('super-admin.organizations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 transition">Add New Organization</a>
        <a href="{{ route('super-admin.organizations.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition">View All Organizations</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // MRR Bar Chart
    const mrrCtx = document.getElementById('mrrChart').getContext('2d');
    new Chart(mrrCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Platform Revenue (₹)',
          data: [15000, 24000, 35000, 48000, 52000, 68000],
          backgroundColor: '#3b82f6',
          borderRadius: 6
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

    // Subscriptions Breakdown
    const subsCtx = document.getElementById('subsChart').getContext('2d');
    new Chart(subsCtx, {
      type: 'pie',
      data: {
        labels: ['Free Trial', 'Pro Plan', 'Restaurant Plan'],
        datasets: [{
          data: [55, 30, 15],
          backgroundColor: [
            '#94a3b8',
            '#3b82f6',
            '#10b981'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
        }
      }
    });
  });
</script>
@endpush
