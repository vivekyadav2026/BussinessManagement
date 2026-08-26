@extends('layouts.sme')

@push('styles')
<style>
  :root {
    --ink-faint: #6b7280;
    --ink-soft: #4b5563;
    --ink: #111827;
    --paper: #ffffff;
    --border-soft: #f3f4f6;
    --border: #e5e7eb;
    --radius: 0.75rem;
    --teal: #0f766e;
    --rose: #e11d48;
    --gold: #f59e0b;
    --gold-deep: #d97706;
  }
  .dash-head{display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:22px;}
  .dash-head h1{font-size:24px; font-weight:600;}
  .dash-head p{color:var(--ink-faint); font-size:13px; margin-top:4px;}

  .stat-row{display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;}
  .stat-card{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:20px;}
  .stat-card .lbl{font-family:'IBM Plex Mono'; font-size:11px; color:var(--ink-faint); text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px;}
  .stat-card .val{font-family:'Space Grotesk'; font-size:24px; font-weight:700;}
  .stat-card .delta{font-size:12px; margin-top:6px; font-weight:600;}
  .delta.up{color:var(--teal);} .delta.down{color:var(--rose);}

  .grid-2{display:grid; grid-template-columns:1.4fr 1fr; gap:16px; margin-bottom:16px;}
  .panel{background:var(--paper); border:1px solid var(--border-soft); border-radius:var(--radius); padding:22px;}
  .panel-head{display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;}
  .panel-head h3{font-size:14.5px; font-weight:600; font-family:'Space Grotesk';}
  .panel-head a{font-size:12px; color:var(--gold-deep); font-weight:600;}

  .health-gauge{display:flex; align-items:center; gap:22px;}
  .health-num{font-family:'Space Grotesk'; font-size:38px; font-weight:700; color:var(--teal);}
  .health-num span{font-size:15px; color:var(--ink-faint); font-weight:500;}
  .health-bars{flex:1;}
  .hbar-row{display:flex; align-items:center; gap:10px; margin-bottom:9px; font-size:11.5px;}

  .btn { display: inline-flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 6px; transition: all 0.2s; }
  .btn-sm { padding: 8px 16px; font-size: 13px; }
  .btn-gold { background: var(--gold); color: #111827; }
  .btn-gold:hover { background: #d97706; }
  .hbar-row .l{width:78px; color:var(--ink-soft); flex:none;}
  .hbar-track{flex:1; height:6px; background:var(--border-soft); border-radius:100px; overflow:hidden;}
  .hbar-fill{height:100%; background:var(--teal); border-radius:100px;}

  table.inv-table{width:100%; border-collapse:collapse; font-size:12.8px;}
  table.inv-table th{text-align:left; font-family:'IBM Plex Mono'; font-size:10.5px; text-transform:uppercase; color:var(--ink-faint); font-weight:500; padding:0 0 10px; letter-spacing:.04em;}
  table.inv-table td{padding:11px 0; border-top:1px solid var(--border-soft);}
  table.inv-table td.amt{font-family:'IBM Plex Mono'; font-weight:600;}
  .mini-badge{font-family:'IBM Plex Mono'; font-size:10px; padding:3px 9px; border-radius:100px; font-weight:600;}

  .quick-grid{display:grid; grid-template-columns:repeat(4,1fr); gap:14px;}
  .quick-card{background:var(--paper); border:1px dashed var(--border); border-radius:12px; padding:18px; text-align:center; cursor:pointer;}
  .quick-card:hover{border-color:var(--gold); border-style:solid;}
  .quick-card svg{width:20px; height:20px; margin:0 auto 10px; stroke:var(--ink);}
  .quick-card .t{font-size:12.5px; font-weight:600;}
</style>
@endpush

@section('content')
<div class="dash-head">
  <div>
      <h1>Namaste, {{ auth()->user()->name }} 👋</h1>
      <p>Here's how {{ auth()->user()->organization->name ?? 'your business' }} is doing today, {{ now()->format('j F') }}.</p>
  </div>
  <a class="btn btn-gold btn-sm" href="#">+ New Invoice</a>
</div>

<div class="stat-row">
  <div class="stat-card"><div class="lbl">Sales — this month</div><div class="val">₹0</div><div class="delta up">--</div></div>
  <div class="stat-card"><div class="lbl">Outstanding receivables</div><div class="val">₹0</div><div class="delta down">0 clients overdue</div></div>
  <div class="stat-card"><div class="lbl">Low stock items</div><div class="val">0</div><div class="delta down">Reorder needed</div></div>
  <div class="stat-card"><div class="lbl">Restaurant orders today</div><div class="val">0</div><div class="delta up">--</div></div>
</div>

<div class="grid-2">
  <div class="panel">
    <div class="panel-head"><h3>Sales Performance</h3></div>
    <div style="height: 220px; position: relative;">
        <canvas id="salesChart"></canvas>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>Expenses Breakdown</h3></div>
    <div style="height: 220px; position: relative; display: flex; justify-content: center;">
        <canvas id="expensesChart" style="max-width: 220px; max-height: 220px;"></canvas>
    </div>
  </div>
</div>

<div class="grid-2" style="grid-template-columns: 1.4fr 1fr;">
  <div class="panel">
    <div class="panel-head"><h3>Recent Invoices</h3><a>View all</a></div>
    <table class="inv-table">
      <thead>
        <tr><th>Invoice</th><th>Client</th><th>Amount</th><th>Status</th></tr>
      </thead>
      <tbody>
        <tr><td colspan="4" style="text-align:center; padding: 20px; color: var(--ink-faint);">No invoices found</td></tr>
      </tbody>
    </table>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>Business Health Meter</h3></div>
    <div class="health-gauge">
      <div class="health-num">98<span>/100</span></div>
      <div class="health-bars">
        <div class="hbar-row"><span class="l">Sales</span><div class="hbar-track"><div class="hbar-fill" style="width:95%"></div></div></div>
        <div class="hbar-row"><span class="l">Profit</span><div class="hbar-track"><div class="hbar-fill" style="width:90%"></div></div></div>
        <div class="hbar-row"><span class="l">Stock</span><div class="hbar-track"><div class="hbar-fill" style="width:85%;background:var(--gold)"></div></div></div>
        <div class="hbar-row"><span class="l">Receivables</span><div class="hbar-track"><div class="hbar-fill" style="width:98%;background:var(--rose)"></div></div></div>
      </div>
    </div>
  </div>
</div>

<div class="panel" style="margin-bottom:16px;">
  <div class="panel-head"><h3>Quick actions</h3></div>
  <div class="quick-grid">
    <div class="quick-card"><svg fill="none" stroke="var(--ink)" stroke-width="2" viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/></svg><div class="t">New Invoice</div></div>
    <div class="quick-card"><svg fill="none" stroke="var(--ink)" stroke-width="2" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10l-8-4V7m8 10V7"/></svg><div class="t">Add Product</div></div>
    <div class="quick-card"><svg fill="none" stroke="var(--ink)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="4" height="16"/><rect x="9" y="4" width="1" height="16"/><rect x="12" y="4" width="3" height="16"/><rect x="17" y="4" width="4" height="16"/></svg><div class="t">Scan Barcode</div></div>
    <div class="quick-card"><svg fill="none" stroke="var(--ink)" stroke-width="2" viewBox="0 0 24 24"><path d="M3 11l9-8 9 8M5 10v10h14V10"/></svg><div class="t">Restaurant Orders</div></div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Sales Line Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Monthly Sales (₹)',
          data: [25000, 39000, 48000, 62000, 58000, 75000],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.05)',
          tension: 0.4,
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

    // Expenses Doughnut Chart
    const expCtx = document.getElementById('expensesChart').getContext('2d');
    new Chart(expCtx, {
      type: 'doughnut',
      data: {
        labels: ['Salary', 'Inventory', 'Rent', 'Marketing'],
        datasets: [{
          data: [40, 25, 20, 15],
          backgroundColor: [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444'
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
