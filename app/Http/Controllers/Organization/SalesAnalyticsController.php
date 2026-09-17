<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        $range = $request->get('range', 'this_month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        [$startDate, $endDate, $prevStartDate, $prevEndDate, $rangeTitle] = $this->parseDateRange($range, $customStart, $customEnd);

        // Base query for invoices in current period
        $invoiceQuery = Invoice::where('organization_id', $orgId)
            ->where('status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $totalSales = (clone $invoiceQuery)->sum('grand_total');
        $totalInvoices = (clone $invoiceQuery)->count();
        $totalTax = (clone $invoiceQuery)->sum('tax');
        $avgOrderValue = $totalInvoices > 0 ? round($totalSales / $totalInvoices, 2) : 0;

        // Base query for previous period for comparison
        $prevInvoiceQuery = Invoice::where('organization_id', $orgId)
            ->where('status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('invoice_date', [$prevStartDate->format('Y-m-d'), $prevEndDate->format('Y-m-d')]);

        $prevTotalSales = (clone $prevInvoiceQuery)->sum('grand_total');
        $salesGrowth = $prevTotalSales > 0 
            ? round((($totalSales - $prevTotalSales) / $prevTotalSales) * 100, 1) 
            : ($totalSales > 0 ? 100 : 0);

        // Calculate Net Profit: sum of item quantity * (unit_price - product.purchase_price)
        $profitQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.organization_id', $orgId)
            ->where('invoices.status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('invoices.location_id', $locationId))
            ->whereBetween('invoices.invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $totalProfit = (clone $profitQuery)->sum(DB::raw('invoice_items.quantity * (invoice_items.unit_price - COALESCE(products.purchase_price, 0))'));

        // Payment Method Breakdown (From recorded transactions)
        $paymentMethods = DB::table('transactions')
            ->join('invoices', 'transactions.invoice_id', '=', 'invoices.id')
            ->where('invoices.organization_id', $orgId)
            ->where('invoices.status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('invoices.location_id', $locationId))
            ->whereBetween('invoices.invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select('transactions.payment_method', DB::raw('SUM(transactions.amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('transactions.payment_method')
            ->get()
            ->keyBy('payment_method');

        $methodBreakdown = [
            'Cash' => ['amount' => $paymentMethods['Cash']->total_amount ?? 0, 'count' => $paymentMethods['Cash']->count ?? 0],
            'UPI' => ['amount' => $paymentMethods['UPI']->total_amount ?? 0, 'count' => $paymentMethods['UPI']->count ?? 0],
            'Card' => ['amount' => $paymentMethods['Card']->total_amount ?? 0, 'count' => $paymentMethods['Card']->count ?? 0],
            'Bank Transfer' => ['amount' => $paymentMethods['Bank Transfer']->total_amount ?? 0, 'count' => $paymentMethods['Bank Transfer']->count ?? 0],
        ];

        // Sales Status Distribution
        $statusDistribution = (clone $invoiceQuery)
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as amount'))
            ->groupBy('status')
            ->pluck('amount', 'status')
            ->toArray();

        // Daily / Periodic Chart Data
        $chartData = $this->generateChartData($orgId, $locationId, $startDate, $endDate);

        // Top 10 Best Selling Products
        $topProducts = (clone $profitQuery)
            ->select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(invoice_items.quantity) as units_sold'),
                DB::raw('SUM(invoice_items.total) as total_revenue'),
                DB::raw('SUM(invoice_items.quantity * (invoice_items.unit_price - COALESCE(products.purchase_price, 0))) as total_profit')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // Sales Breakdown by Category
        $categorySales = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('invoices.organization_id', $orgId)
            ->where('invoices.status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('invoices.location_id', $locationId))
            ->whereBetween('invoices.invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw("COALESCE(categories.name, 'Uncategorized') as category_name"),
                DB::raw('SUM(invoice_items.total) as total_revenue'),
                DB::raw('SUM(invoice_items.quantity) as units_sold')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Top 10 Clients by Spending
        $topClients = DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->where('invoices.organization_id', $orgId)
            ->where('invoices.status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('invoices.location_id', $locationId))
            ->whereBetween('invoices.invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                'clients.name as client_name',
                'clients.phone',
                DB::raw('COUNT(invoices.id) as invoice_count'),
                DB::raw('SUM(invoices.grand_total) as total_spent')
            )
            ->groupBy('clients.id', 'clients.name', 'clients.phone')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        return view('organization.reports.sales_analytics', compact(
            'range', 'startDate', 'endDate', 'rangeTitle',
            'totalSales', 'prevTotalSales', 'salesGrowth',
            'totalInvoices', 'totalProfit', 'totalTax', 'avgOrderValue',
            'methodBreakdown', 'statusDistribution', 'chartData',
            'topProducts', 'categorySales', 'topClients'
        ));
    }

    public function export(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id');

        $range = $request->get('range', 'this_month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        [$startDate, $endDate] = $this->parseDateRange($range, $customStart, $customEnd);

        $invoices = Invoice::with(['client', 'items.product', 'transactions'])
            ->where('organization_id', $orgId)
            ->where('status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $filename = "sales_analytics_report_" . $startDate->format('Ymd') . "_" . $endDate->format('Ymd') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice #', 'Date', 'Client Name', 'Status', 'Payment Method', 'Subtotal', 'Tax', 'Discount', 'Grand Total', 'Amount Paid', 'Balance Due']);

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->invoice_number,
                    $inv->invoice_date->format('Y-m-d'),
                    $inv->client->name ?? 'Walk-in Client',
                    $inv->status,
                    $inv->transactions->pluck('payment_method')->first() ?? 'Cash',
                    $inv->subtotal,
                    $inv->tax,
                    $inv->discount,
                    $inv->grand_total,
                    $inv->amount_paid,
                    $inv->amount_due,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseDateRange($range, $start = null, $end = null)
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $s = Carbon::today();
                $e = Carbon::today();
                $ps = Carbon::yesterday();
                $pe = Carbon::yesterday();
                $title = "Today (" . $s->format('M d, Y') . ")";
                break;
            case 'yesterday':
                $s = Carbon::yesterday();
                $e = Carbon::yesterday();
                $ps = Carbon::today()->subDays(2);
                $pe = Carbon::today()->subDays(2);
                $title = "Yesterday (" . $s->format('M d, Y') . ")";
                break;
            case 'this_week':
                $s = Carbon::now()->startOfWeek();
                $e = Carbon::now()->endOfWeek();
                $ps = (clone $s)->subWeek();
                $pe = (clone $e)->subWeek();
                $title = "This Week (" . $s->format('M d') . " - " . $e->format('M d, Y') . ")";
                break;
            case 'last_month':
                $s = Carbon::now()->subMonth()->startOfMonth();
                $e = Carbon::now()->subMonth()->endOfMonth();
                $ps = (clone $s)->subMonth();
                $pe = (clone $e)->subMonth();
                $title = "Last Month (" . $s->format('M Y') . ")";
                break;
            case 'this_year':
                $s = Carbon::now()->startOfYear();
                $e = Carbon::now()->endOfYear();
                $ps = (clone $s)->subYear();
                $pe = (clone $e)->subYear();
                $title = "This Year (" . $s->format('Y') . ")";
                break;
            case 'custom':
                $s = $start ? Carbon::parse($start) : Carbon::now()->startOfMonth();
                $e = $end ? Carbon::parse($end) : Carbon::now();
                $days = $s->diffInDays($e) + 1;
                $ps = (clone $s)->subDays($days);
                $pe = (clone $e)->subDays($days);
                $title = "Custom Range (" . $s->format('M d, Y') . " - " . $e->format('M d, Y') . ")";
                break;
            case 'this_month':
            default:
                $s = Carbon::now()->startOfMonth();
                $e = Carbon::now();
                $ps = Carbon::now()->subMonth()->startOfMonth();
                $pe = Carbon::now()->subMonth()->endOfMonth();
                $title = "This Month (" . $s->format('M Y') . ")";
                break;
        }

        return [$s, $e, $ps, $pe, $title];
    }

    private function generateChartData($orgId, $locationId, $startDate, $endDate)
    {
        $daysDiff = $startDate->diffInDays($endDate);

        $sales = DB::table('invoices')
            ->where('organization_id', $orgId)
            ->where('status', '!=', 'Cancelled')
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->whereBetween('invoice_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(DB::raw('DATE(invoice_date) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $data = [];

        $curr = clone $startDate;
        while ($curr->lte($endDate)) {
            $dateStr = $curr->format('Y-m-d');
            $labels[] = $curr->format($daysDiff > 60 ? 'M Y' : 'M d');
            $data[] = round($sales[$dateStr] ?? 0, 2);
            $curr->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
