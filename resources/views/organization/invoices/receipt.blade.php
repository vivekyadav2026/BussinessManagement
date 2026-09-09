<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Helvetica Neue', Arial, sans-serif;
            width: 78mm;
            margin: 0 auto;
            padding: 8px 6px;
            color: #000;
            background: #fff;
            font-size: 11px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .receipt-container {
            width: 100%;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-left: left; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .uppercase { text-transform: uppercase; }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .divider-solid {
            border-top: 1px solid #000;
            margin: 6px 0;
        }
        
        .header-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        .header-sub {
            font-size: 10px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }
        table.items-table th {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            padding: 3px 0;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.items-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10px;
        }

        table.summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.summary-table td {
            padding: 2px 0;
            font-size: 10px;
        }

        .badge-status {
            display: inline-block;
            padding: 1px 5px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
        }

        @media print {
            .no-print { display: none !important; }
            body {
                width: 100% !important;
                padding: 4px !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <!-- Top Toolbar (No Print) -->
    <div class="no-print" style="margin-bottom: 15px; background: #0f172a; color: white; padding: 10px; border-radius: 8px; font-family: sans-serif;">
        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; font-weight: bold; text-align: center;">
                Receipt Preview: <span style="font-family: monospace; color: #a5f3fc;">{{ $invoice->invoice_number }}</span>
            </div>
            <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: center;">
                <button onclick="window.print()" style="padding: 6px 12px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 11px; cursor: pointer; display: inline-flex; items-center; gap: 4px;">
                    🖨️ Print Receipt
                </button>
                <a href="{{ route('organization.invoices.print', $invoice) }}" style="padding: 6px 12px; background: #334155; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 11px; cursor: pointer; text-decoration: none;">
                    📄 Standard A4 Tax Invoice
                </a>
                <a href="{{ route('organization.invoices.show', $invoice) }}" style="padding: 6px 12px; background: #475569; color: white; border: none; border-radius: 6px; font-weight: bold; font-size: 11px; cursor: pointer; text-decoration: none;">
                    &larr; Invoice Details
                </a>
            </div>
        </div>
    </div>

    <div class="receipt-container">
        <!-- Business Header -->
        <div class="text-center">
            @if($invoice->organization->logo)
                <img src="{{ Storage::url($invoice->organization->logo) }}" style="max-height: 38px; margin: 0 auto 4px auto; display: block;">
            @endif
            <div class="header-title uppercase">{{ $invoice->organization->name }}</div>
            
            <div class="header-sub">
                @if($invoice->location && $invoice->location->name)
                    <div><b>Branch:</b> {{ $invoice->location->name }}</div>
                @endif
                @if($invoice->organization->address)
                    <div>{{ $invoice->organization->address }}</div>
                @endif
                <div>
                    @if($invoice->organization->phone) Ph: {{ $invoice->organization->phone }} @endif
                    @if($invoice->organization->email) | {{ $invoice->organization->email }} @endif
                </div>
                @if($invoice->organization->gst_number)
                    <div class="font-bold">GSTIN: {{ $invoice->organization->gst_number }}</div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- Receipt Metadata -->
        <div style="font-size: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <span><b>INVOICE RECEIPT</b></span>
                <span class="badge-status">{{ $invoice->status }}</span>
            </div>
            <div><b>Invoice #:</b> {{ $invoice->invoice_number }}</div>
            <div><b>Date:</b> {{ $invoice->invoice_date->format('d/m/Y') }} {{ $invoice->created_at->format('h:i A') }}</div>
            
            @if($invoice->client)
                <div class="divider-solid"></div>
                <div><b>Customer:</b> {{ $invoice->client->name }}</div>
                @if($invoice->client->phone)<div><b>Phone:</b> {{ $invoice->client->phone }}</div>@endif
                @if($invoice->client->gst_number)<div><b>Client GST:</b> {{ $invoice->client->gst_number }}</div>@endif
            @else
                <div><b>Customer:</b> Cash / Walk-in Customer</div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 45%;">ITEM</th>
                    <th class="text-center" style="width: 15%;">QTY</th>
                    <th class="text-right" style="width: 20%;">PRICE</th>
                    <th class="text-right" style="width: 20%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="text-left font-bold">
                            {{ $item->product_name_snapshot }}
                            @if($item->product && $item->product->sku)
                                <div style="font-size: 8px; font-weight: normal;">SKU: {{ $item->product->sku }}</div>
                            @endif
                        </td>
                        <td class="text-center font-bold">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td class="text-left">Subtotal:</td>
                <td class="text-right font-bold">₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td class="text-left">Tax / GST:</td>
                <td class="text-right">₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td class="text-left">Discount (-):</td>
                <td class="text-right">-₹{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
                <td class="text-left font-black" style="font-size: 12px; padding: 4px 0;">GRAND TOTAL:</td>
                <td class="text-right font-black" style="font-size: 13px; padding: 4px 0;">₹{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
            <tr>
                <td class="text-left">Amount Paid:</td>
                <td class="text-right font-bold">₹{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td class="text-left">Balance Due:</td>
                <td class="text-right font-bold">₹{{ number_format($invoice->amount_due, 2) }}</td>
            </tr>
        </table>

        @if($invoice->notes)
            <div class="divider"></div>
            <div style="font-size: 9px;">
                <b>Notes:</b> {{ $invoice->notes }}
            </div>
        @endif

        <div class="divider"></div>

        <!-- Digital Verification QR & Footer -->
        <div class="text-center" style="margin-top: 6px;">
            <div id="receiptQrCode" style="display: flex; justify-content: center; margin: 4px 0;"></div>
            <div style="font-size: 9px; font-weight: bold; margin-top: 2px;">Thank you for your business!</div>
            <div style="font-size: 8px; color: #333; margin-top: 2px;">E.&O.E. | Computer Generated Receipt</div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("receiptQrCode"), {
            text: "{{ route('organization.invoices.show', $invoice) }}",
            width: 54,
            height: 54,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    });
    </script>

</body>
</html>
