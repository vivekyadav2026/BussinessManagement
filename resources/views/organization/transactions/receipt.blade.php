<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $transaction->invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; margin: 0; padding: 0; }
        .receipt-box { max-width: 600px; margin: auto; padding: 30px; border: 1px solid #eee; margin-top: 40px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0 0 10px 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 0; color: #555; }
        
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { padding: 8px 0; }
        .details-table td:nth-child(2) { text-align: right; font-weight: bold; }

        .amount-box { background: #f9f9f9; padding: 20px; text-align: center; margin-bottom: 20px; border-radius: 8px; }
        .amount-box h2 { margin: 0; font-size: 32px; color: #16a34a; }
        .amount-box p { margin: 5px 0 0 0; color: #555; text-transform: uppercase; font-size: 12px; font-weight: bold; }

        .footer { text-align: center; color: #777; font-size: 12px; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .receipt-box { box-shadow: none; border: none; margin-top: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 20px; background:#f9f9f9; border-bottom:1px solid #ddd; margin-bottom: 30px;">
        <button onclick="window.print()" style="padding:10px 20px; font-size:16px; background:#4f46e5; color:#fff; border:none; border-radius:5px; cursor:pointer;">Print Receipt</button>
    </div>

    <div class="receipt-box">
        <div class="header">
            <h1>Payment Receipt</h1>
            <p>{{ $transaction->organization->name }}</p>
            <p>{{ $transaction->location->name ?? 'Head Office' }}</p>
        </div>

        <div class="amount-box">
            <h2>₹{{ number_format($transaction->amount, 2) }}</h2>
            <p>Amount Paid</p>
        </div>

        <table class="details-table">
            <tr>
                <td>Payment Date</td>
                <td>{{ $transaction->payment_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>{{ $transaction->payment_method }}</td>
            </tr>
            @if($transaction->reference_number)
            <tr>
                <td>Reference Number</td>
                <td>{{ $transaction->reference_number }}</td>
            </tr>
            @endif
            <tr>
                <td>Invoice Number</td>
                <td>{{ $transaction->invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Received From</td>
                <td>{{ $transaction->invoice->client->name }}</td>
            </tr>
            <tr>
                <td>Remaining Invoice Balance</td>
                <td>₹{{ number_format($transaction->invoice->amount_due, 2) }}</td>
            </tr>
        </table>

        @if($transaction->notes)
        <div style="padding-top: 20px; border-top: 1px solid #eee;">
            <strong>Notes:</strong><br>
            {{ $transaction->notes }}
        </div>
        @endif

        <div class="footer">
            Thank you for your payment!<br>
            This is a computer generated receipt.
        </div>
    </div>
</body>
</html>
