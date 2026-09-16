<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax_Invoice_{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @media print {
            .no-print { display: none !important; }
            body { background-color: #ffffff !important; padding: 0 !important; }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="py-8 px-4 sm:px-6">

    @php
        function numberToWords($number) {
            $no = floor($number);
            $point = round($number - $no, 2) * 100;
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();
            $words = array(
                '0' => '', '1' => 'One', '2' => 'Two',
                '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
                '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
                '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
                '13' => 'Thirteen', '14' => 'Fourteen',
                '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
                '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
                '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
                '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty',
                '90' => 'Ninety'
            );
            $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
            while ($i < $digits_1) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : '';
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred
                        : $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
                } else $str[] = null;
            }
            $str = array_reverse($str);
            $result = implode('', $str);
            $points = ($point > 0) ? " and " . ($words[floor($point / 10) * 10] . " " . $words[$point % 10]) . " Paise" : '';
            return ($result ? "Rupees " . trim($result) : "Rupees Zero") . ($points ? $points : "") . " Only";
        }
        $amountInWords = numberToWords($invoice->grand_total);
        $orgUpi = $invoice->organization->upi_id ?? '';
        $payAmount = $invoice->amount_due > 0 ? $invoice->amount_due : $invoice->grand_total;
        $upiString = $orgUpi ? "upi://pay?pa=" . rawurlencode($orgUpi) . "&pn=" . rawurlencode($invoice->organization->name) . "&am=" . number_format($payAmount, 2, '.', '') . "&cu=INR&tn=" . rawurlencode('Invoice ' . $invoice->invoice_number) : '';
    @endphp

    <!-- Top Floating Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.invoices.show', $invoice) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">&larr; Back to Details</a>
            <span class="text-xs text-slate-400">Commercial Tax Invoice: <b class="text-white font-mono">{{ $invoice->invoice_number }}</b></span>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('organization.invoices.receipt', $invoice) }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-amber-300 font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 border border-slate-700">
                <span>🧾 Small Machine Receipt</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                <span>🖨️ Print / Save PDF</span>
            </button>
        </div>
    </div>

    <!-- Commercial GST Tax Invoice Box -->
    <div class="invoice-card max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-300 shadow-sm space-y-6">
        
        <!-- Header Header Banner -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b-2 border-slate-900 pb-6 gap-6">
            <!-- Company Information Left -->
            <div class="space-y-1.5 max-w-md">
                @if($invoice->organization->logo)
                    <img src="{{ Storage::url($invoice->organization->logo) }}" class="h-14 w-auto object-contain mb-3">
                @else
                    <img src="{{ asset('images/logo.png') }}" class="h-12 w-auto object-contain mb-3" alt="Vyapaargo">
                @endif
                <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">{{ $invoice->organization->name }}</h1>
                <div class="text-xs text-slate-600 leading-normal font-medium space-y-0.5">
                    @if($invoice->organization->address)<div>{{ $invoice->organization->address }}</div>@endif
                    <div>Phone: <b>{{ $invoice->organization->phone ?? 'N/A' }}</b> | Email: <b>{{ $invoice->organization->email ?? 'N/A' }}</b></div>
                    @if($invoice->organization->gst_number)<div class="font-bold text-slate-900 pt-0.5">GSTIN: <span class="font-mono">{{ $invoice->organization->gst_number }}</span></div>@endif
                </div>
            </div>

            <!-- Tax Invoice Right Banner -->
            <div class="sm:text-right space-y-2 shrink-0">
                <div class="inline-block bg-slate-900 text-white px-4 py-1.5 rounded text-xs font-black tracking-widest uppercase shadow-xs">
                    TAX INVOICE
                </div>
                
                <div class="text-xs text-slate-700 space-y-1 font-semibold pt-1">
                    <div>Invoice No: <b class="font-mono text-slate-900 text-sm ml-1">{{ $invoice->invoice_number }}</b></div>
                    <div>Invoice Date: <span class="font-bold text-slate-900">{{ $invoice->invoice_date->format('d/m/Y') }}</span></div>
                    @if($invoice->due_date)
                        <div>Due Date: <span class="font-bold text-slate-900">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
                    @endif
                    <div>Place of Supply: <span class="font-bold text-slate-900">{{ $invoice->location->name ?? 'State Warehouse' }}</span></div>
                    
                    <div class="pt-1">
                        Payment Status: 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wide
                            {{ $invoice->status === 'Paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($invoice->status === 'Due' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill To & Ship To Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border border-slate-200 rounded-xl overflow-hidden text-xs">
            <!-- Bill To Card -->
            <div class="p-4 bg-slate-50/80 border-r border-slate-200 space-y-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1 mb-2">Billed To (Customer Details)</div>
                @if($invoice->client)
                    <div class="text-sm font-extrabold text-slate-900">{{ $invoice->client->name }}</div>
                    @if($invoice->client->address)<div class="text-slate-600 font-medium">{{ $invoice->client->address }}</div>@endif
                    <div class="text-slate-600 font-medium">Phone: <b>{{ $invoice->client->phone ?? 'N/A' }}</b></div>
                    @if($invoice->client->email)<div class="text-slate-600 font-medium">Email: {{ $invoice->client->email }}</div>@endif
                    @if($invoice->client->gst_number)<div class="font-bold text-slate-800 pt-1">GSTIN: <span class="font-mono">{{ $invoice->client->gst_number }}</span></div>@endif
                @else
                    <div class="text-sm font-bold text-slate-900">Walk-in Client / Counter Customer</div>
                    <div class="text-slate-500 italic">General Cash Sale</div>
                @endif
            </div>

            <!-- Ship To & Warehouse Card + Dynamic UPI QR -->
            <div class="p-4 bg-white space-y-2 flex flex-col justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1 mb-2">Dispatch Branch & Payment QR</div>
                    <div class="text-xs font-semibold text-slate-800">Dispatch Location: <b>{{ $invoice->location->name ?? 'Head Office' }}</b></div>
                </div>
                
                @php
                    $upiVpa = $invoice->organization->upi_id ?? 'pay@upi';
                    $payAmount = $invoice->amount_due > 0 ? $invoice->amount_due : $invoice->grand_total;
                    $upiNote = 'Invoice ' . $invoice->invoice_number;
                    $upiString = "upi://pay?pa=" . rawurlencode($upiVpa) . "&pn=" . rawurlencode($invoice->organization->name) . "&am=" . number_format($payAmount, 2, '.', '') . "&cu=INR&tn=" . rawurlencode($upiNote);
                @endphp

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                    <div>
                        <div class="text-[10px] font-black text-slate-900 uppercase">Scan to Pay Exact Amount</div>
                        <div class="text-[11px] font-black text-emerald-700 font-mono">₹{{ number_format($payAmount, 2) }}</div>
                        <div class="text-[9px] text-slate-500 mt-0.5">GPay | PhonePe | Paytm | BHIM</div>
                    </div>
                    <div id="upiPayQrCode" class="p-1 bg-white border border-slate-300 rounded-lg shadow-2xs"></div>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3 border-r border-slate-800 w-10 text-center">#</th>
                        <th class="py-3 px-4 border-r border-slate-800">Item Description / SKU</th>
                        <th class="py-3 px-3 border-r border-slate-800 text-center w-16">Qty</th>
                        <th class="py-3 px-3 border-r border-slate-800 text-right w-24">Unit Price (₹)</th>
                        <th class="py-3 px-3 border-r border-slate-800 text-right w-20">Tax (₹)</th>
                        <th class="py-3 px-4 text-right w-28">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium">
                    @foreach($invoice->items as $index => $item)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="py-3 px-3 border-r border-slate-200 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border-r border-slate-200">
                                <div class="font-bold text-slate-900 text-xs">{{ $item->product_name_snapshot }}</div>
                                @if($item->product && $item->product->sku)
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 border-r border-slate-200 text-center font-bold text-slate-900 text-xs">{{ $item->quantity }}</td>
                            <td class="py-3 px-3 border-r border-slate-200 text-right text-slate-700">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 px-3 border-r border-slate-200 text-right text-slate-600">₹{{ number_format($item->tax, 2) }}</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Amount in Words Banner -->
        <div class="bg-slate-100 p-3 rounded-xl border border-slate-200 text-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <span class="font-bold text-slate-600 uppercase text-[10px] tracking-wider block">Total Amount in Words:</span>
                <span class="font-extrabold text-slate-900 text-xs italic">{{ $amountInWords }}</span>
            </div>
            <div class="text-right font-bold text-slate-800 text-xs">
                Total Items: <span class="bg-white px-2 py-0.5 rounded border border-slate-300 ml-1">{{ $invoice->items->count() }}</span>
            </div>
        </div>

        <!-- Summary & Terms Footer Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
            <!-- Left: Bank Details & Terms -->
            <div class="space-y-4 text-xs">
                <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50/50 flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="font-bold text-slate-800 uppercase tracking-wider text-[10px] border-b border-slate-200 pb-1 mb-1.5">Payment Details</div>
                        <div class="text-slate-600 font-medium">Mode: <b>Cash / Bank / UPI</b></div>
                        @if($invoice->organization->upi_id)
                            <div class="text-slate-700 font-medium">Merchant UPI: <b class="font-mono text-slate-900">{{ $invoice->organization->upi_id }}</b></div>
                            <div class="text-[10px] text-slate-500">Scan QR via GPay, PhonePe, Paytm or BHIM</div>
                        @else
                            <div class="text-slate-600 font-medium">Bank Account: <b>Available on Request</b></div>
                        @endif
                    </div>
                    @if($invoice->organization->upi_id)
                        <div class="flex flex-col items-center shrink-0">
                            <div id="upiPayQrCode" class="p-1 bg-white border border-slate-200 rounded-lg shadow-2xs"></div>
                            <span class="text-[9px] font-bold text-slate-600 mt-1">Scan to Pay</span>
                        </div>
                    @endif
                </div>

                @if($invoice->notes)
                    <div class="border border-slate-200 rounded-xl p-3.5 bg-slate-50/50 space-y-1">
                        <div class="font-bold text-slate-800 uppercase tracking-wider text-[10px] border-b border-slate-200 pb-1 mb-1.5">Notes & Reference</div>
                        <div class="text-slate-600 leading-relaxed">{{ $invoice->notes }}</div>
                    </div>
                @endif

                <div class="text-[10px] text-slate-500 leading-normal space-y-0.5">
                    <div><b>Terms & Conditions:</b></div>
                    <div>1. All disputes are subject to local city jurisdiction.</div>
                    <div>2. Goods once sold will be accepted strictly under company return policy.</div>
                </div>
            </div>

            <!-- Right: Calculation Breakdown -->
            <div class="border border-slate-300 rounded-xl p-4 bg-white space-y-2 text-xs">
                <div class="flex justify-between text-slate-600 font-medium">
                    <span>Subtotal</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                
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
                <div class="flex justify-between text-slate-600 font-medium">
                    <span>CGST ({{ $cgstRate }}%)</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($cgstVal, 2) }}</span>
                </div>
                @endif
                
                @if($sgstVal > 0)
                <div class="flex justify-between text-slate-600 font-medium">
                    <span>SGST ({{ $sgstRate }}%)</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($sgstVal, 2) }}</span>
                </div>
                @endif

                @if($invoice->discount > 0)
                    <div class="flex justify-between text-emerald-700 font-semibold border-t border-slate-100 pt-1.5">
                        <span>Discount (-)</span>
                        <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-baseline pt-2.5 border-t-2 border-slate-900 text-slate-900">
                    <span class="font-black text-sm uppercase tracking-wider">Grand Total</span>
                    <span class="text-2xl font-black">₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>

                <div class="flex justify-between pt-2 text-slate-700 font-medium border-t border-slate-100">
                    <span>Amount Paid</span>
                    <span class="font-bold text-emerald-700">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                </div>

                <div class="flex justify-between pt-1 border-t border-slate-200 text-slate-900 font-extrabold">
                    <span>Balance Due</span>
                    <span class="{{ $invoice->amount_due > 0 ? 'text-rose-600' : 'text-slate-900' }}">₹{{ number_format($invoice->amount_due, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Authorized Signatory Stamp & Footer -->
        <div class="border-t-2 border-slate-900 pt-6 flex flex-col sm:flex-row justify-between items-end gap-6">
            <div class="text-[10px] text-slate-400 space-y-0.5">
                <div>E. & O.E. | Computer Generated Tax Invoice</div>
                <div>Powered by {{ config('app.name', 'Vyapaargo') }} Cloud ERP</div>
            </div>

            <div class="text-right space-y-8 shrink-0">
                <div class="text-xs font-bold text-slate-900">For {{ $invoice->organization->name }}</div>
                <div class="border-t border-slate-400 pt-1 text-[11px] font-semibold text-slate-600">Authorized Signatory / Stamp</div>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const upiEl = document.getElementById("upiPayQrCode");
        if (upiEl) {
            new QRCode(upiEl, {
                text: "{{ $upiString }}",
                width: 58,
                height: 58,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        }
    });
    </script>
</body>
</html>
