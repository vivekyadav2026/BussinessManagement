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
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body {
            font-family: 'Consolas', 'Courier New', Courier, monospace, sans-serif;
            width: 78mm;
            margin: 0 auto;
            padding: 8px;
            color: #000000 !important;
            background: #ffffff !important;
            font-size: 12px;
            line-height: 1.35;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: 900; }
        .divider {
            border-top: 1.5px dashed #000000;
            margin: 8px 0;
        }
        .header-title {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000000 !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        th {
            border-bottom: 1.5px solid #000000;
            border-top: 1.5px solid #000000;
            padding: 4px 0;
            text-align: left;
            font-size: 11px;
            font-weight: 900;
            color: #000000 !important;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11.5px;
            color: #000000 !important;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            background: #000000;
            color: #ffffff !important;
            font-size: 10.5px;
            font-weight: 900;
            border-radius: 3px;
            border: 1px solid #000000;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1.5px solid #000000;
            font-weight: 900;
            font-size: 11px;
            text-transform: uppercase;
        }
        .paid-badge {
            background: #000000 !important;
            color: #ffffff !important;
        }
        .pending-badge {
            background: #ffffff !important;
            color: #000000 !important;
        }
        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 100% !important;
                margin: 0 !important;
                padding: 4px !important;
                background: #ffffff !important;
                color: #000000 !important;
            }
            * {
                color: #000000 !important;
                text-shadow: none !important;
                box-shadow: none !important;
            }
            .paid-badge {
                background: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

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
        <div class="bold">{{ $order->location->name ?? 'Main Branch' }}</div>
        @if($order->location->address)
            <div>{{ $order->location->address }}</div>
        @endif
        @if($order->location->phone)
            <div class="bold">Ph: {{ $order->location->phone }}</div>
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
                $cgstPercent = (float)($order->organization->cgst_percent ?? 0);
                $sgstPercent = (float)($order->organization->sgst_percent ?? 0);
            @endphp
            @foreach($itemsToPrint as $item)
            <tr>
                <td class="text-left bold">{{ $item->name_snapshot }}</td>
                <td class="text-center bold">{{ $item->quantity }}</td>
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
            <td class="text-left">CGST ({{ $cgstPercent }}%):</td>
            <td class="text-right">₹{{ number_format($grossCgst, 2) }}</td>
        </tr>
        @endif
        @if($grossSgst > 0)
        <tr>
            <td class="text-left">SGST ({{ $sgstPercent }}%):</td>
            <td class="text-right">₹{{ number_format($grossSgst, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td class="text-left header-title" style="font-size: 13px;">GRAND TOTAL:</td>
            <td class="text-right header-title" style="font-size: 14px;">₹{{ number_format($order->total, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left bold">Payment Status:</td>
            <td class="text-right bold">
                @if($order->payment_status === 'Paid')
                    <span class="status-badge paid-badge">PAID ✅</span>
                @else
                    <span class="status-badge pending-badge">PENDING ⏳</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    @if($order->payment_status === 'Paid')
        <div class="text-center" style="margin: 8px 0; padding: 6px; border: 2px solid #000000; border-radius: 4px; background: #ffffff;">
            <div style="font-size: 12px; font-weight: 900; color: #000000 !important; text-transform: uppercase;">✅ FULLY PAID</div>
            <div style="font-size: 9.5px; font-weight: bold; color: #000000 !important; margin-top: 2px;">AMOUNT DUE: ₹0.00 (NO PAYMENT REQUIRED)</div>
        </div>
    @else
        @php
            $orgUpi = $order->organization->upi_id ?? 'pay@upi';
            $orderUpiString = "upi://pay?pa=" . rawurlencode($orgUpi) . "&pn=" . rawurlencode($order->organization->name ?? 'POS') . "&am=" . number_format($order->total, 2, '.', '') . "&cu=INR&tn=" . rawurlencode('Order #' . $order->order_number);
        @endphp

        <div class="text-center" style="margin-top: 6px;">
            <div style="font-size: 9.5px; font-weight: 900; color: #000000 !important; text-transform: uppercase;">SCAN TO PAY EXACT BILL AMOUNT</div>
            <div style="font-size: 14px; font-weight: 900; margin-top: 2px; color: #000000 !important;">₹{{ number_format($order->total, 2) }}</div>
            <div style="display: flex; justify-content: center; margin: 6px 0;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(90)->margin(0)->generate($orderUpiString) !!}
            </div>
            <div style="font-size: 8.5px; font-weight: bold; color: #000000 !important;">GPay | PhonePe | Paytm | BHIM</div>
        </div>
    @endif

    <div class="text-center" style="margin-top: 10px;">
        <div class="bold" style="color: #000000 !important;">Thank you for dining with us!</div>
        <div style="font-size: 10px; margin-top: 3px; color: #000000 !important;">Please Visit Again 🙏</div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() { window.print(); }, 300);
    });
    </script>

</body>
</html>

