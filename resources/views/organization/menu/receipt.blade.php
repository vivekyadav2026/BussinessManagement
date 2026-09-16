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
        <button onclick="window.print()" style="padding: 9px 18px; background: #020617; color: white; border: none; border-radius: 8px; font-weight: 900; cursor: pointer; font-size: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">🖨️ Print Bill Receipt</button>
        <button onclick="window.close()" style="padding: 9px 16px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: bold; cursor: pointer; margin-left: 8px; font-size: 12px;">Close Window</button>
    </div>

    <!-- Header -->
    <div class="text-center">
        @if($order->organization && $order->organization->logo)
            <img src="{{ asset('storage/' . $order->organization->logo) }}" style="max-height: 40px; margin-bottom: 5px;">
        @else
            <img src="{{ asset('images/logo.png') }}" style="max-height: 34px; margin-bottom: 5px; display: inline-block;" alt="Vyapaargo">
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
            @php
                $itemsToPrint = isset($allOrders) ? $allOrders->pluck('items')->flatten() : $order->items;
                $grossSubtotal = isset($allOrders) ? $allOrders->sum('subtotal') : $order->subtotal;
                $grossCgst = isset($allOrders) ? $allOrders->sum('cgst') : $order->cgst;
                $grossSgst = isset($allOrders) ? $allOrders->sum('sgst') : $order->sgst;
            @endphp
            @foreach($itemsToPrint as $item)
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
            <td class="text-right bold">₹{{ number_format($grossSubtotal, 2) }}</td>
        </tr>
        @if($grossCgst > 0)
        <tr>
            <td class="text-left">CGST ({{ (float)auth()->user()->organization->cgst_percent }}%):</td>
            <td class="text-right">₹{{ number_format($grossCgst, 2) }}</td>
        </tr>
        @endif
        @if($grossSgst > 0)
        <tr>
            <td class="text-left">SGST ({{ (float)auth()->user()->organization->sgst_percent }}%):</td>
            <td class="text-right">₹{{ number_format($grossSgst, 2) }}</td>
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

    @php
        $orgUpi = $order->organization->upi_id ?? 'pay@upi';
        $orderUpiString = "upi://pay?pa=" . rawurlencode($orgUpi) . "&pn=" . rawurlencode($order->organization->name ?? 'POS') . "&am=" . number_format($order->total, 2, '.', '') . "&cu=INR&tn=" . rawurlencode('Order #' . $order->order_number);
    @endphp

    <div class="text-center" style="margin-top: 6px;">
        <div style="font-size: 9px; font-weight: bold;">SCAN TO PAY EXACT BILL AMOUNT</div>
        <div style="font-size: 12px; font-weight: 900; margin-top: 2px;">₹{{ number_format($order->total, 2) }}</div>
        <div id="orderUpiQrCode" style="display: flex; justify-content: center; margin: 4px 0;"></div>
        <div style="font-size: 8px; color: #333;">GPay | PhonePe | Paytm | BHIM</div>
    </div>

    <div class="text-center" style="margin-top: 10px;">
        <div class="bold">Thank you for dining with us!</div>
        <div style="font-size: 10px; margin-top: 3px;">Please Visit Again 🙏</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("orderUpiQrCode"), {
            text: "{{ $orderUpiString }}",
            width: 72,
            height: 72,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    });
    </script>

</body>
</html>
