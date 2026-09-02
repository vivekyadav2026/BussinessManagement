<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT Ticket #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Consolas', 'Courier New', Courier, 'DejaVu Sans Mono', monospace;
            background-color: #1e293b;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        
        /* Thermal Slip Paper Card Container */
        .thermal-slip {
            width: 80mm;
            background: #ffffff;
            color: #000000;
            padding: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            font-size: 13px;
            line-height: 1.35;
            border-radius: 4px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: 900; }
        .uppercase { text-transform: uppercase; }
        .font-mono { font-family: monospace; }
        
        .dash-line {
            border-top: 2px dashed #000000;
            margin: 8px 0;
        }
        .double-line {
            border-top: 3px double #000000;
            margin: 8px 0;
        }
        .solid-line {
            border-top: 2px solid #000000;
            margin: 6px 0;
        }

        /* Giant Table Badge */
        .table-header-badge {
            border: 3px solid #000000;
            background: #000000;
            color: #ffffff;
            font-size: 20px;
            font-weight: 900;
            padding: 6px 10px;
            margin: 6px 0;
            display: block;
            letter-spacing: 1px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 2px;
        }

        /* Items List Table */
        .kot-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        .kot-table th {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            font-size: 12px;
            font-weight: 900;
        }
        .kot-table td {
            padding: 6px 0;
            vertical-align: middle;
            border-bottom: 1px dashed #ccc;
        }

        .qty-badge {
            font-size: 16px;
            font-weight: 900;
            background: #000;
            color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .item-title {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.3px;
        }

        .notes-box {
            border: 2px solid #000000;
            padding: 6px 8px;
            margin-top: 8px;
            background: #f8fafc;
        }

        /* Print Media Overrides */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                display: block !important;
            }
            .thermal-slip {
                width: 78mm !important;
                box-shadow: none !important;
                padding: 4px !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .table-header-badge {
                background: #000 !important;
                color: #fff !important;
            }
            .qty-badge {
                background: #000 !important;
                color: #fff !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <!-- Thermal Paper Slip Container -->
    <div class="thermal-slip">
        
        <!-- Screen Action Buttons -->
        <div class="no-print" style="margin-bottom: 15px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 900; font-size: 13px; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                🖨️ Print Thermal KOT Slip
            </button>
            <button onclick="window.close()" style="padding: 10px 16px; background: #64748b; color: white; border: none; border-radius: 6px; font-weight: 900; font-size: 13px; cursor: pointer; margin-left: 8px;">
                Close
            </button>
        </div>

        <!-- Header -->
        <div class="text-center">
            <div class="bold uppercase" style="font-size: 13px;">{{ auth()->user()->organization->name ?? 'RESTAURANT KITCHEN' }}</div>
            <div class="bold uppercase" style="font-size: 18px; letter-spacing: 1px; margin-top: 2px;">KITCHEN ORDER TICKET</div>
            <div style="font-size: 11px; font-weight: bold;">(KOT DISPATCH SLIP)</div>
        </div>

        <!-- Giant Table Badge -->
        <div class="text-center">
            <div class="table-header-badge uppercase">
                {{ $order->table ? $order->table->name : ($order->order_type ?? 'TAKEAWAY') }}
            </div>
        </div>

        <div class="dash-line"></div>

        <!-- Order Metadata -->
        <div class="meta-row">
            <span class="bold">KOT Ticket #:</span>
            <span class="bold font-mono">{{ $order->order_number }}</span>
        </div>
        <div class="meta-row">
            <span class="bold">Order Type:</span>
            <span class="bold uppercase">{{ $order->order_type ?? 'Dine-In' }}</span>
        </div>
        <div class="meta-row">
            <span class="bold">Date & Time:</span>
            <span>{{ $order->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        <div class="meta-row">
            <span class="bold">Order Taken By:</span>
            <span class="uppercase">{{ auth()->user()->name ?? 'Waiter' }}</span>
        </div>

        <div class="solid-line"></div>

        <!-- Items List -->
        <table class="kot-table">
            <thead>
                <tr>
                    <th class="text-left">QTY</th>
                    <th class="text-left" style="padding-left: 8px;">DISH / ITEM NAME</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="text-left" style="width: 25%;">
                        <span class="qty-badge">x{{ $item->quantity }}</span>
                    </td>
                    <td class="text-left" style="padding-left: 8px;">
                        <span class="item-title uppercase">{{ $item->name_snapshot }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Cooking Instructions / Notes -->
        @if($order->notes)
            <div class="notes-box">
                <div class="bold uppercase" style="font-size: 11px; text-decoration: underline; margin-bottom: 2px;">⚠️ COOKING INSTRUCTIONS:</div>
                <div class="bold uppercase" style="font-size: 13px;">{{ $order->notes }}</div>
            </div>
        @endif

        <div class="double-line"></div>

        <!-- Cut Line Marker -->
        <div class="text-center bold uppercase" style="font-size: 11px; margin-top: 4px;">
            *** KITCHEN DISPATCH COPY ***
        </div>

    </div>

</body>
</html>
