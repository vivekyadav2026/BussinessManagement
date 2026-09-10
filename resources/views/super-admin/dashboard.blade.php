@extends('layouts.super-admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-black text-gray-900 tracking-tight" style="color: #0f172a !important;">Platform Analytics & Tenants Overview</h1>
                <span class="bg-indigo-50 text-indigo-700 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full border border-indigo-200">Super Admin</span>
            </div>
            <p class="text-xs text-gray-500 mt-0.5" style="color: #64748b !important;">Real-time metrics, subscription revenue & tenant activity across all organizations</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('super-admin.organizations.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-2xs flex items-center gap-1.5">
                <span>➕ Add Organization</span>
            </a>
            <a href="{{ route('super-admin.plans.index') }}" class="px-3.5 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition border border-gray-200">
                ⚙️ Plans
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Organizations -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">Total Organizations</h3>
                <div class="text-2xl font-black text-slate-900">{{ number_format($orgCount) }}</div>
                <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    {{ $activeOrgs }} Active ({{ $trialOrgs }} in Trial)
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0 font-bold">
                🏢
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">Total Platform Users</h3>
                <div class="text-2xl font-black text-slate-900">{{ number_format($userCount) }}</div>
                <div class="text-xs font-medium text-gray-500">Across all tenant accounts</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0 font-bold">
                👥
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">This Month Revenue</h3>
                <div class="text-2xl font-black text-emerald-600">₹{{ number_format($thisMonthRevenue, 2) }}</div>
                <div class="text-xs font-bold {{ $revenueGrowth >= 0 ? 'text-emerald-600' : 'text-rose-600' }} flex items-center gap-1">
                    <span>{{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}% vs last month</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 font-bold">
                📈
            </div>
        </div>

        <!-- Lifetime Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <h3 class="text-gray-400 text-[11px] font-bold uppercase tracking-wider">Total Platform Revenue</h3>
                <div class="text-2xl font-black text-slate-900">₹{{ number_format($totalRevenue, 2) }}</div>
                <div class="text-xs font-semibold text-gray-500">Lifetime paid receipts</div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0 font-bold">
                💳
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Revenue Trend Bar Chart -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-base text-slate-900">Monthly Revenue Trend</h3>
                    <p class="text-xs text-gray-400">Past 6 months platform revenue trajectory</p>
                </div>
                <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">Live DB</span>
            </div>
            <div class="relative h-72">
                <canvas id="mrrChart"></canvas>
            </div>
        </div>

        <!-- Subscription Plan Distribution Pie Chart -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs lg:col-span-1 space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-black text-base text-slate-900">Plan Distribution</h3>
                <p class="text-xs text-gray-400">Tenant active subscription tiers</p>
            </div>
            <div class="relative h-64 flex items-center justify-center my-auto">
                <canvas id="subsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Organizations Table -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-black text-base text-slate-900">Recently Registered Organizations</h3>
                <p class="text-xs text-gray-500">Latest tenant onboarding activity</p>
            </div>
            <a href="{{ route('super-admin.organizations.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                View All Tenants &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 font-extrabold border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">Organization</th>
                        <th class="px-4 py-3">Owner User</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Registered On</th>
                        <th class="px-4 py-3 text-right rounded-tr-xl">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($recentOrganizations as $org)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-900">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-black flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($org->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $org->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">ID #{{ $org->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($org->users->first())
                                    <div class="font-bold text-slate-800">{{ $org->users->first()->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $org->users->first()->email }}</div>
                                @else
                                    <span class="text-gray-400 italic">No admin assigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($org->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Suspended
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 font-mono">
                                {{ $org->created_at ? $org->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('super-admin.organizations.show', $org) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 rounded-lg text-[11px] font-bold transition">
                                    Manage &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400">No organizations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // MRR Revenue Bar Chart
    const mrrCtx = document.getElementById('mrrChart').getContext('2d');
    
    const blueGradient = mrrCtx.createLinearGradient(0, 0, 0, 300);
    blueGradient.addColorStop(0, 'rgba(79, 70, 229, 0.85)');
    blueGradient.addColorStop(1, 'rgba(79, 70, 229, 0.20)');

    new Chart(mrrCtx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($mrrLabels) !!},
        datasets: [{
          label: 'Revenue (₹)',
          data: {!! json_encode($mrrData) !!},
          backgroundColor: blueGradient,
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
            grid: { color: '#f1f5f9' },
            ticks: { color: '#64748b', font: { size: 11 } }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#64748b', font: { size: 11 } }
          }
        }
      }
    });

    // Subscriptions Breakdown Pie Chart
    const subsCtx = document.getElementById('subsChart').getContext('2d');
    new Chart(subsCtx, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode($planLabels) !!},
        datasets: [{
          data: {!! json_encode($planData) !!},
          backgroundColor: [
            '#6366f1',
            '#10b981',
            '#f59e0b',
            '#ec4899',
            '#64748b'
          ],
          borderWidth: 3,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } }
        },
        cutout: '68%'
      }
    });
  });
</script>
@endpush
