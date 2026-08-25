@extends('layouts.sme')

@section('content')
<div class="dash-head flex justify-between items-end mb-6">
  <div>
      <h1 class="text-2xl font-bold text-gray-900">Receivables Dashboard</h1>
      <p class="text-gray-500 mt-1">Track outstanding payments and send reminders.</p>
  </div>
</div>

<div class="mb-6 border-b border-gray-200">
  <nav class="-mb-px flex space-x-8">
    <a href="{{ route('organization.receivables.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Dashboard</a>
    <a href="{{ route('organization.receivables.client_report') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Client Report</a>
    <a href="{{ route('organization.receivables.overdue_report') }}" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Overdue Aging</a>
  </nav>
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

<div class="panel">
    <table class="inv-table w-full">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Due Date</th>
                <th>Aging</th>
                <th class="text-right">Balance Due</th>
                <th class="text-right">Remind</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            @php $daysOverdue = now()->diffInDays($inv->due_date); @endphp
            <tr>
                <td class="font-bold"><a href="{{ route('organization.invoices.show', $inv) }}" class="text-indigo-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                <td>{{ $inv->client->name }}</td>
                <td class="text-red-600 font-bold">{{ $inv->due_date->format('M d, Y') }}</td>
                <td>
                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">{{ $daysOverdue }} days late</span>
                </td>
                <td class="text-right font-bold text-gray-900">₹{{ number_format($inv->amount_due, 2) }}</td>
                <td class="text-right flex justify-end gap-2">
                    <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST">
                        @csrf
                        <input type="hidden" name="channel" value="email">
                        <button type="submit" class="btn bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 btn-sm text-xs" title="Send Email Reminder">✉️ Email</button>
                    </form>
                    <form action="{{ route('organization.invoices.remind', $inv) }}" method="POST">
                        @csrf
                        <input type="hidden" name="channel" value="whatsapp">
                        <button type="submit" class="btn bg-white border border-gray-300 hover:bg-green-50 text-green-600 btn-sm text-xs" title="Send WhatsApp Reminder">📱 WA</button>
                    </form>
                    <button onclick="copyLink('{{ $inv->id }}')" class="btn bg-white border border-gray-300 hover:bg-indigo-50 text-indigo-600 btn-sm text-xs" title="Copy Payment Link">🔗 Link</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-6 text-green-600 font-bold">Great job! No overdue invoices found.</td></tr>
            @endforelse
        </tbody>
    </table>
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
