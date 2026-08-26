@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Receivables Dashboard</h1>
      <p class="text-gray-500 mt-1">Track outstanding payments and send reminders.</p>
  </div>
</div>

<div class="mb-6 flex justify-between items-center">
  <div class="flex gap-1 bg-[var(--border-color)] border border-[var(--border-hard)] rounded-full p-1">
    <a href="{{ route('organization.receivables.index') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.index') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.client_report') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="font-bold text-xs px-4 py-2 rounded-full transition-all {{ request()->routeIs('organization.receivables.overdue_report') ? 'bg-[var(--text-main)] text-white font-semibold' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">Overdue Aging</a>
  </div>
</div>

@if(session('success'))
<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 text-sm font-medium">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 text-sm font-medium">
    {{ session('error') }}
</div>
@endif

<div class="panel p-6 shadow-sm">
    <div class="overflow-x-auto">
        <table class="inv-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Invoice #</th>
                    <th class="text-left">Client</th>
                    <th class="text-left">Due Date</th>
                    <th class="text-left">Aging</th>
                    <th class="text-right">Balance Due</th>
                    <th class="text-right">Remind</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                @php $daysOverdue = now()->diffInDays($inv->due_date); @endphp
                <tr>
                    <td class="font-bold"><a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                    <td>{{ $inv->client ? $inv->client->name : 'Walk-in Client' }}</td>
                    <td class="text-[var(--rose)] font-bold">{{ $inv->due_date->format('M d, Y') }}</td>
                    <td>
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">{{ $daysOverdue }} days late</span>
                    </td>
                    <td class="text-right font-bold text-gray-900">₹{{ number_format($inv->amount_due, 2) }}</td>
                    <td class="text-right flex justify-end gap-2">
                        <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST">
                            @csrf
                            <input type="hidden" name="channel" value="email">
                            <button type="submit" class="btn btn-ghost py-1 px-3 text-xs" title="Send Email Reminder">✉️ Email</button>
                        </form>
                        <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST">
                            @csrf
                            <input type="hidden" name="channel" value="whatsapp">
                            <button type="submit" class="btn btn-ghost border border-green-200 hover:bg-green-50 text-green-600 py-1 px-3 text-xs" title="Send WhatsApp Reminder">📱 WA</button>
                        </form>
                        <button onclick="copyLink('{{ $inv->id }}')" class="btn btn-ghost border border-indigo-200 hover:bg-indigo-50 text-indigo-600 py-1 px-3 text-xs" title="Copy Payment Link">🔗 Link</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-6 text-green-600 font-bold">Great job! No overdue invoices found.</td></tr>
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
