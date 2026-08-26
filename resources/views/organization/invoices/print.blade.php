<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 8px; vertical-align: top; }
        table tr td:nth-child(n+2) { text-align: right; }
        
        .header table td { padding-bottom: 20px; }
        .title { font-size: 40px; font-weight: bold; color: #333; }
        .company-details { text-align: right; }
        
        .info-row table td { padding-bottom: 40px; }
        .client-info strong { font-size: 16px; }
        
        .items-table th { padding: 10px 8px; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .items-table td { padding: 10px 8px; border-bottom: 1px solid #eee; }
        .items-table th:nth-child(n+2) { text-align: right; }
        
        .totals-table { width: 40%; float: right; margin-top: 20px; }
        .totals-table td { padding: 6px 8px; }
        .totals-table tr.total-row td { border-top: 2px solid #333; font-weight: bold; font-size: 16px; }
        .totals-table tr.due-row td { color: #d9534f; font-weight: bold; }
        
        .footer { margin-top: 50px; clear: both; text-align: center; color: #777; font-size: 12px; padding-top: 20px; border-top: 1px solid #eee; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .invoice-box { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 20px; background:#f9f9f9; border-bottom:1px solid #ddd; margin-bottom: 30px;">
        <button onclick="window.print()" style="padding:10px 20px; font-size:16px; background:#4f46e5; color:#fff; border:none; border-radius:5px; cursor:pointer;">Print / Save as PDF</button>
    </div>

    <div class="invoice-box">
        <table class="header">
            <tr>
                <td class="title">INVOICE</td>
                <td class="company-details">
                    <strong>{{ $invoice->organization->name }}</strong><br>
                    Location: {{ $invoice->location->name ?? 'Head Office' }}<br>
                    Date: {{ $invoice->invoice_date->format('F d, Y') }}<br>
                    Invoice #: {{ $invoice->invoice_number }}
                </td>
            </tr>
        </table>
        
        <table class="info-row">
            <tr>
                <td class="client-info">
                    <span style="color:#777; text-transform:uppercase; font-size:12px;">Bill To:</span><br>
                    @if($invoice->client)
                        <strong>{{ $invoice->client->name }}</strong><br>
                        @if($invoice->client->address){{ $invoice->client->address }}<br>@endif
                        @if($invoice->client->phone)Phone: {{ $invoice->client->phone }}<br>@endif
                        @if($invoice->client->email)Email: {{ $invoice->client->email }}<br>@endif
                        @if($invoice->client->gst_number)GST: {{ $invoice->client->gst_number }}<br>@endif
                    @else
                        <strong>Walk-in Client / General Customer</strong>
                    @endif
                </td>
                <td style="text-align:right;">
                    <span style="color:#777; text-transform:uppercase; font-size:12px;">Status:</span><br>
                    <strong>{{ $invoice->status }}</strong><br><br>
                    @if($invoice->due_date)
                    <span style="color:#777; text-transform:uppercase; font-size:12px;">Due Date:</span><br>
                    <strong>{{ $invoice->due_date->format('F d, Y') }}</strong>
                    @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="text-align:left;">Item Description</th>
                    <th>Qty</th>
                    <th>Unit Price (₹)</th>
                    <th>Tax (₹)</th>
                    <th>Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="text-align:left;">
                        <strong>{{ $item->product_name_snapshot }}</strong>
                        @if($item->product && $item->product->sku)
                            <br><span style="color:#777; font-size:12px;">SKU: {{ $item->product->sku }}</span>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->tax, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td style="text-align:left;">Subtotal</td>
                <td>₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align:left;">Tax</td>
                <td>₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td style="text-align:left;">Discount</td>
                <td>-₹{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td style="text-align:left;">Grand Total</td>
                <td>₹{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align:left;">Amount Paid</td>
                <td>₹{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr class="due-row">
                <td style="text-align:left;">Balance Due</td>
                <td>₹{{ number_format($invoice->amount_due, 2) }}</td>
            </tr>
        </table>

        <div style="clear:both;"></div>

        @if($invoice->notes)
        <div style="margin-top:40px; padding:15px; background:#f9f9f9; border-left:4px solid #ddd;">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
        @endif

        <div class="footer">
            Thank you for your business!<br>
            This is a computer generated invoice.
        </div>
    </div>
</body>
</html>
