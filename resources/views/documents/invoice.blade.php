<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .logo { max-width: 150px; }
        .org-details, .client-details { line-height: 1.5; }
        .title { font-size: 24px; font-weight: bold; text-align: right; color: #555; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.items th, table.items td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table.items th { background-color: #f9f9f9; }
        .totals { width: 100%; margin-top: 20px; }
        .totals td { padding: 5px 10px; text-align: right; }
        .totals .label { font-weight: bold; }
        .status { margin-top: 20px; font-weight: bold; padding: 10px; border: 2px solid; display: inline-block; }
        .paid { color: green; border-color: green; }
        .due { color: red; border-color: red; }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td width="50%">
            @if($organization->logo)
                @php
                    $logoPath = file_exists(public_path('storage/'.$organization->logo)) 
                        ? public_path('storage/'.$organization->logo) 
                        : storage_path('app/public/'.$organization->logo);
                @endphp
                <img src="{{ $logoPath }}" class="logo">
            @else
                <h2>{{ $organization->name }}</h2>
            @endif
            <div class="org-details">
                Location: {{ $location->name ?? 'HQ' }}<br>
                GSTIN: {{ $organization->tax_id ?? 'N/A' }}<br>
                Phone: {{ $organization->phone ?? '' }}<br>
            </div>
        </td>
        <td width="50%" style="text-align: right;">
            <div class="title">INVOICE</div>
            <div><strong>#{{ $invoice->invoice_number }}</strong></div>
            <div>Date: {{ $invoice->invoice_date }}</div>
            <div>Due Date: {{ $invoice->due_date ?? 'N/A' }}</div>
        </td>
    </tr>
</table>

<div class="client-details">
    <strong>Billed To:</strong><br>
    {{ $client ? $client->name : 'Walk-in Customer' }}<br>
    {{ $client ? $client->email : '' }}<br>
    {{ $client ? $client->phone : '' }}
</div>

<table class="items">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Tax</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->product_name_snapshot }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price, 2) }}</td>
            <td>{{ number_format($item->tax, 2) }}</td>
            <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td width="70%"></td>
        <td width="15%" class="label">Subtotal:</td>
        <td width="15%">{{ number_format($invoice->subtotal, 2) }}</td>
    </tr>
    @php
        $cgstVal = $invoice->effective_cgst;
        $sgstVal = $invoice->effective_sgst;
        $cgstRate = (float)($invoice->organization->cgst_percent ?? 0);
        $sgstRate = (float)($invoice->organization->sgst_percent ?? 0);
        if ($cgstRate <= 0 && $invoice->subtotal > 0 && $cgstVal > 0) {
            $cgstRate = round(($cgstVal / $invoice->subtotal) * 100, 2);
        }
        if ($sgstRate <= 0 && $invoice->subtotal > 0 && $sgstVal > 0) {
            $sgstRate = round(($sgstVal / $invoice->subtotal) * 100, 2);
        }
    @endphp
    @if($cgstVal > 0)
    <tr>
        <td></td>
        <td class="label">CGST ({{ $cgstRate }}%):</td>
        <td>{{ number_format($cgstVal, 2) }}</td>
    </tr>
    @endif
    @if($sgstVal > 0)
    <tr>
        <td></td>
        <td class="label">SGST ({{ $sgstRate }}%):</td>
        <td>{{ number_format($sgstVal, 2) }}</td>
    </tr>
    @endif
    @if($cgstVal <= 0 && $sgstVal <= 0 && $invoice->tax > 0)
    <tr>
        <td></td>
        <td class="label">Tax:</td>
        <td>{{ number_format($invoice->tax, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td></td>
        <td class="label">Discount:</td>
        <td>{{ number_format($invoice->discount, 2) }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="label"><strong>Grand Total:</strong></td>
        <td><strong>{{ number_format($invoice->grand_total, 2) }}</strong></td>
    </tr>
    <tr>
        <td></td>
        <td class="label">Amount Paid:</td>
        <td>{{ number_format($invoice->amount_paid, 2) }}</td>
    </tr>
    <tr>
        <td></td>
        <td class="label">Balance Due:</td>
        <td>{{ number_format($invoice->grand_total - $invoice->amount_paid, 2) }}</td>
    </tr>
</table>

<div style="margin-top: 30px;">
    Payment Status: <strong>{{ $invoice->status }}</strong>
</div>

</body>
</html>
