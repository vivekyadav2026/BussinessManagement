<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            width: 78mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            background: #fff;
            font-size: 12px;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        th {
            border-bottom: 1px solid #000;
            padding: 4px 0;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            background: #000;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="no-print" style="margin-bottom: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">🖨️ Print Bill Receipt</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-left: 8px;">Close Window</button>
    </div>

    <!-- Header -->
    <div class="text-center">
        @if($order->organization && $order->organization->logo)
            <img src="{{ asset('storage/' . $order->organization->logo) }}" style="max-height: 40px; margin-bottom: 5px;">
        @endif
        <div class="header-title">{{ $order->organization->name ?? 'RESTRO POS' }}</div>
        <div>{{ $order->location->name ?? 'Main Branch' }}</div>
        @if($order->location->address)
            <div>{{ $order->location->address }}</div>
        @endif
        @if($order->location->phone)
            <div>Ph: {{ $order->location->phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Order Info -->
    <div>
        <div><span class="bold">Order #:</span> {{ $order->order_number }}</div>
        <div><span class="bold">Date:</span> {{ $order->created_at->format('d/m/Y h:i A') }}</div>
        <div><span class="bold">Table #:</span> <span class="badge">{{ $order->table->name ?? 'TAKEAWAY' }}</span></div>
        <div><span class="bold">Customer:</span> {{ $order->customer_name ?? 'Guest' }}</div>
        @if($order->customer_phone)
            <div><span class="bold">Phone:</span> {{ $order->customer_phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">ITEM</th>
                <th class="text-center" style="width: 15%;">QTY</th>
                <th class="text-right" style="width: 15%;">PRICE</th>
                <th class="text-right" style="width: 20%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="text-left bold">{{ $item->name_snapshot }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price_snapshot, 2) }}</td>
                <td class="text-right bold">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Summary -->
    <table style="margin-top: 5px;">
        <tr>
            <td class="text-left">Subtotal:</td>
            <td class="text-right bold">₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->tax > 0)
        <tr>
            <td class="text-left">GST / Tax:</td>
            <td class="text-right">₹{{ number_format($order->tax, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td class="text-left header-title" style="font-size: 13px;">GRAND TOTAL:</td>
            <td class="text-right header-title" style="font-size: 14px;">₹{{ number_format($order->total, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left">Payment Status:</td>
            <td class="text-right bold" style="text-transform: uppercase;">{{ $order->payment_status }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 10px;">
        <div class="bold">Thank you for dining with us!</div>
        <div style="font-size: 10px; margin-top: 3px;">Please Visit Again 🙏</div>
    </div>

</body>
</html>
