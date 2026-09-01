@extends('layouts.sme')

@section('content')
<div class="dash-head flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
  <div>
      <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Receivables Dashboard</h1>
      <p class="text-xs text-slate-500 mt-1">Track overdue aging payments and send automated reminders.</p>
  </div>

  <div class="inline-flex gap-1 bg-slate-100 border border-slate-200 rounded-xl p-1 shadow-xs">
    <a href="{{ route('organization.receivables.index') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="font-bold text-xs px-4 py-2 rounded-lg transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">Overdue Aging</a>
  </div>
</div>

@if(session('success'))
<div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl mb-6 border border-emerald-200 text-sm font-semibold shadow-xs flex items-center gap-2">
    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl mb-6 border border-rose-200 text-sm font-semibold shadow-xs flex items-center gap-2">
    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Invoice #</th>
                    <th class="py-3.5 px-4">Client Name</th>
                    <th class="py-3.5 px-4">Due Date</th>
                    <th class="py-3.5 px-4">Aging Status</th>
                    <th class="py-3.5 px-4 text-right">Balance Due</th>
                    <th class="py-3.5 px-4 text-right">Remind & Share</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-medium">
                @forelse($invoices as $inv)
                @php $daysOverdue = now()->diffInDays($inv->due_date); @endphp
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-3.5 px-4 font-bold font-mono">
                        <a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">{{ $inv->invoice_number }}</a>
                    </td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">
                        {{ $inv->client ? $inv->client->name : 'Walk-in Client' }}
                    </td>
                    <td class="py-3.5 px-4 text-rose-600 font-bold">
                        {{ $inv->due_date->format('M d, Y') }}
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                            {{ $daysOverdue }} days late
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right font-black text-slate-900 text-sm">₹{{ number_format($inv->amount_due, 2) }}</td>
                    <td class="py-3.5 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="channel" value="email">
                                <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Send Email Reminder">
                                    <span>✉️ Email</span>
                                </button>
                            </form>
                            <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="channel" value="whatsapp">
                                <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Send WhatsApp Reminder">
                                    <span>📱 WA</span>
                                </button>
                            </form>
                            <button onclick="copyLink('{{ $inv->id }}')" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Copy Payment Link">
                                <span>🔗 Link</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-emerald-600 font-bold">Great job! No overdue invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>

<script>
function copyLink(invoiceId) {
    fetch(`/organization/invoices/${invoiceId}/payment-link`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                navigator.clipboard.writeText(data.url).then(() => {
                    alert('Secure payment link copied to clipboard!');
                });
            }
        });
}
</script>
@endsection
