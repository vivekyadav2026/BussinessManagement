<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vyapaargo - Handover Documentation</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.5;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #146356;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #146356;
        }
        .subtitle {
            font-size: 14px;
            color: #666666;
            margin-top: 5px;
        }
        h1 {
            font-size: 22px;
            color: #146356;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 5px;
            margin-top: 25px;
        }
        h2 {
            font-size: 16px;
            color: #D99A2B;
            margin-top: 20px;
        }
        h3 {
            font-size: 14px;
            color: #333333;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            color: #666666;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-active { background-color: #d4edda; color: #155724; }
        .badge-trial { background-color: #cce5ff; color: #004085; }
        .code {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f4f4f4;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .page-break {
            page-break-after: always;
        }
        .step {
            margin-bottom: 10px;
        }
        .step-num {
            font-weight: bold;
            color: #146356;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">Vyapaargo</div>
        <div class="subtitle">Complete Handover Documentation & User Manual</div>
        <div style="font-size: 11px; color: #999999; margin-top: 10px;">Generated on: {{ date('Y-m-d H:i') }}</div>
    </div>

    <h1>1. Platform Architecture</h1>
    <p>Vyapaargo is a multi-tenant business management SaaS application designed for retail stores, warehouses, and restaurant outlets. Operation is divided into four main access panels to ensure role security and clean workflow management.</p>
    
    <table>
        <thead>
            <tr>
                <th>Panel Name</th>
                <th>Primary User</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Super Admin</strong></td>
                <td>System Operator / Owner</td>
                <td>Manages business tenants, setup monthly/yearly pricing models, and manages organization subscriptions.</td>
            </tr>
            <tr>
                <td><strong>Merchant Admin</strong></td>
                <td>Business Owner / Manager</td>
                <td>Registers locations, employees, salary structures, catalog products, and views aging receivables.</td>
            </tr>
            <tr>
                <td><strong>POS Desk</strong></td>
                <td>Cashiers / Counter Staff</td>
                <td>Issues billing invoices, prints receipts, handles barcode scanning, and generates Razorpay payment links.</td>
            </tr>
            <tr>
                <td><strong>Kitchen Screen (KDS)</strong></td>
                <td>Cooks / Chefs</td>
                <td>Real-time screen tracking pending order items, table details, and cooking statuses.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h1>2. Testing Credentials</h1>
    <p>You can access the system panels using these default credentials:</p>
    
    <table>
        <thead>
            <tr>
                <th>Role Context</th>
                <th>Target URL</th>
                <th>Login Email</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Super Admin</strong></td>
                <td><span class="code">/login</span></td>
                <td>superadmin@example.com</td>
                <td>password</td>
            </tr>
            <tr>
                <td><strong>Retail Owner</strong></td>
                <td><span class="code">/login</span></td>
                <td>admin@techcorp.com</td>
                <td>password</td>
            </tr>
            <tr>
                <td><strong>Restaurant Owner</strong></td>
                <td><span class="code">/login</span></td>
                <td>admin@spicekitchen.com</td>
                <td>password</td>
            </tr>
            <tr>
                <td><strong>Kitchen Cook</strong></td>
                <td><span class="code">/login</span></td>
                <td>kitchen@spicekitchen.com</td>
                <td>password</td>
            </tr>
        </tbody>
    </table>

    <h1>3. Basic Operational Modules</h1>
    
    <h2>🏢 Locations (Branches)</h2>
    <p>Allows businesses to expand across multiple physical outlets. Switch active outlets instantly from the header switcher. All sales, menu lists, table settings, and inventories automatically group under the selected outlet scope.</p>

    <h2>👥 Roles & Granular Permissions</h2>
    <p>Assign custom permissions (e.g. products, billing, restaurant) to specific employee logins. Prevent cashiers or staff from canceling bills or reviewing total margin reports.</p>

    <h2>💵 Attendance & Monthly Payroll</h2>
    <p>Register employees and mark attendance daily. At the end of the month, generate payroll contextually. The system auto-calculates base pay deductions for absent days. Administrators can add custom allowances or deductions and release payslips.</p>

    <h2>📦 Products & Low Stock Alerts</h2>
    <p>Organize products into categories. Set up minimum stock limits. When stock counts drop below this threshold, the catalog flags red to warn cashiers to place replenishment orders.</p>

    <div class="page-break"></div>

    <h1>4. Restaurant & Billing Operations</h1>

    <h2>🍽️ QR Ordering & Tables</h2>
    <p>Create restaurant table assets. The system generates custom QR codes. Print and paste them on the physical tables. Customers scan with their phones to order from the digital menu.</p>

    <h2>🍳 KDS (Kitchen Display System)</h2>
    <p>Cooks view dining orders in real-time. Chefs toggle statuses to "Preparing" or "Ready" to alert waiters.</p>

    <h2>🔗 Payment Reminders & Webhook Links</h2>
    <p>Click "Send Reminder" next to unpaid invoices to generate custom checkout web links. Clients pay via card, UPI, or wallet, updating invoice status automatically.</p>

    <h1>5. Technical Server Run Guide</h1>
    <p>To run or reset the server locally:</p>
    <ul>
        <li class="step"><span class="step-num">Step A:</span> Run database migrations and seeds: <span class="code">php artisan migrate:fresh --seed --seeder=DummyDataSeeder</span></li>
        <li class="step"><span class="step-num">Step B:</span> Start PHP application: <span class="code">php artisan serve</span></li>
        <li class="step"><span class="step-num">Step C:</span> Compile Vite asset bundles: <span class="code">npm run build</span></li>
    </ul>

</body>
</html>
