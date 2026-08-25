<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header td { vertical-align: bottom; }
        .logo { max-width: 150px; }
        .title { font-size: 24px; font-weight: bold; text-align: right; color: #555; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .box { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
        .salary-table { width: 100%; border-collapse: collapse; }
        .salary-table th, .salary-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .salary-table th { text-align: left; }
        .total-row { font-weight: bold; border-top: 2px solid #333; }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td width="50%">
            @if($organization->logo)
                <img src="{{ public_path('storage/'.$organization->logo) }}" class="logo">
            @else
                <h2>{{ $organization->name }}</h2>
            @endif
        </td>
        <td width="50%" style="text-align: right;">
            <div class="title">PAYSLIP</div>
            <div>Month: {{ date("F", mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}</div>
        </td>
    </tr>
</table>

<table class="info-table">
    <tr>
        <td><strong>Employee Name:</strong></td>
        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
        <td><strong>Employee ID:</strong></td>
        <td>EMP-{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</td>
    </tr>
    <tr>
        <td><strong>Designation:</strong></td>
        <td>{{ $employee->designation ?? 'N/A' }}</td>
        <td><strong>Payment Date:</strong></td>
        <td>{{ $payroll->payment_date ?? 'Pending' }}</td>
    </tr>
</table>

<div class="box">
    <table class="salary-table">
        <tr>
            <th>Earnings</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td style="text-align: right;">{{ number_format($payroll->basic_salary, 2) }}</td>
        </tr>
        <tr>
            <td>Allowances</td>
            <td style="text-align: right;">{{ number_format($payroll->allowances, 2) }}</td>
        </tr>
        <tr>
            <td>Manual Adjustment (Addition)</td>
            <td style="text-align: right;">{{ number_format(max(0, $payroll->manual_adjustment), 2) }}</td>
        </tr>
        <tr>
            <th>Deductions</th>
            <th></th>
        </tr>
        <tr>
            <td>Standard Deductions</td>
            <td style="text-align: right;">-{{ number_format($payroll->deductions, 2) }}</td>
        </tr>
        <tr>
            <td>Unpaid Leave Deductions</td>
            <td style="text-align: right;">-{{ number_format($payroll->leave_deductions ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Manual Adjustment (Deduction)</td>
            <td style="text-align: right;">-{{ number_format(abs(min(0, $payroll->manual_adjustment)), 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Net Salary</td>
            <td style="text-align: right;">{{ number_format($payroll->net_salary, 2) }}</td>
        </tr>
    </table>
</div>

<p>
    <strong>Status:</strong> {{ $payroll->status }}<br>
    <strong>Method:</strong> {{ $payroll->payment_method ?? 'N/A' }}<br>
    @if($payroll->adjustment_reason)
    <strong>Notes:</strong> {{ $payroll->adjustment_reason }}
    @endif
</p>

</body>
</html>
