<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get primary sales and profit metrics for dashboard cards.
     */
    public static function getSalesAndProfitMetrics($orgId, $locationId = null)
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $query = Invoice::where('organization_id', $orgId)->where('status', '!=', 'Cancelled');
        if ($locationId) $query->where('location_id', $locationId);

        $salesToday = (clone $query)->whereDate('invoice_date', $today)->sum('grand_total');
        $salesThisWeek = (clone $query)->whereBetween('invoice_date', [$startOfWeek, Carbon::now()])->sum('grand_total');
        $salesThisMonth = (clone $query)->whereBetween('invoice_date', [$startOfMonth, Carbon::now()])->sum('grand_total');
        $salesLastMonth = (clone $query)->whereBetween('invoice_date', [$startOfLastMonth, $endOfLastMonth])->sum('grand_total');

        $growthPercent = $salesLastMonth > 0 
            ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 2) 
            : ($salesThisMonth > 0 ? 100 : 0);

        // Profit logic: Sum of (invoice_item.quantity * (invoice_item.unit_price - product.purchase_price))
        $profitQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.organization_id', $orgId)
            ->where('invoices.status', '!=', 'Cancelled');
            
        if ($locationId) $profitQuery->where('invoices.location_id', $locationId);

        $profitThisMonth = (clone $profitQuery)->whereBetween('invoices.invoice_date', [$startOfMonth, Carbon::now()])
            ->sum(DB::raw('invoice_items.quantity * (invoice_items.unit_price - products.purchase_price)'));

        $profitLastMonth = (clone $profitQuery)->whereBetween('invoices.invoice_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum(DB::raw('invoice_items.quantity * (invoice_items.unit_price - products.purchase_price)'));

        $profitGrowthPercent = $profitLastMonth > 0 
            ? round((($profitThisMonth - $profitLastMonth) / $profitLastMonth) * 100, 2) 
            : ($profitThisMonth > 0 ? 100 : 0);

        return [
            'sales_today' => $salesToday,
            'sales_week' => $salesThisWeek,
            'sales_month' => $salesThisMonth,
            'sales_last_month' => $salesLastMonth,
            'sales_growth' => $growthPercent,
            'profit_month' => $profitThisMonth,
            'profit_last_month' => $profitLastMonth,
            'profit_growth' => $profitGrowthPercent,
        ];
    }

    public static function getInventoryMetrics($orgId, $locationId = null)
    {
        $query = DB::table('inventory_stocks')
            ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
            ->where('inventory_stocks.organization_id', $orgId);
            
        if ($locationId) $query->where('inventory_stocks.location_id', $locationId);

        $totalValue = (clone $query)->sum(DB::raw('inventory_stocks.quantity * products.purchase_price'));
        $lowStockCount = (clone $query)->whereColumn('inventory_stocks.quantity', '<=', 'products.min_stock_level')->count();
        $totalItems = (clone $query)->count();

        return [
            'stock_value' => $totalValue,
            'low_stock_count' => $lowStockCount,
            'total_items' => $totalItems
        ];
    }

    public static function getReceivablesMetrics($orgId, $locationId = null)
    {
        $query = Invoice::where('organization_id', $orgId)->where('status', '!=', 'Cancelled');
        if ($locationId) $query->where('location_id', $locationId);

        $outstanding = (clone $query)->sum(DB::raw('grand_total - amount_paid'));
        
        $overdue = (clone $query)->whereDate('due_date', '<', Carbon::today())->sum(DB::raw('grand_total - amount_paid'));

        return [
            'outstanding' => $outstanding,
            'overdue' => $overdue,
        ];
    }

    public static function getCustomerMetrics($orgId)
    {
        $totalCustomers = Client::where('organization_id', $orgId)->count();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $newThisMonth = Client::where('organization_id', $orgId)->whereBetween('created_at', [$startOfMonth, Carbon::now()])->count();
        $newLastMonth = Client::where('organization_id', $orgId)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();

        $growthPercent = $newLastMonth > 0 
            ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 2) 
            : ($newThisMonth > 0 ? 100 : 0);

        return [
            'total' => $totalCustomers,
            'new_month' => $newThisMonth,
            'new_last_month' => $newLastMonth,
            'growth' => $growthPercent
        ];
    }

    public static function getInvoiceStatusDistribution($orgId, $locationId = null)
    {
        $query = DB::table('invoices')->where('organization_id', $orgId);
        if ($locationId) $query->where('location_id', $locationId);
        
        $statuses = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'Total' => array_sum($statuses),
            'Paid' => $statuses['Paid'] ?? 0,
            'Partially Paid' => $statuses['Partially Paid'] ?? 0,
            'Due' => $statuses['Due'] ?? 0,
            'Overdue' => $statuses['Overdue'] ?? 0,
            'Cancelled' => $statuses['Cancelled'] ?? 0,
        ];
    }

    /**
     * Get chart data for the last 30 days
     */
    public static function getDailySalesChart($orgId, $locationId = null)
    {
        $thirtyDaysAgo = Carbon::today()->subDays(29);
        
        $query = DB::table('invoices')
            ->where('organization_id', $orgId)
            ->where('status', '!=', 'Cancelled')
            ->whereDate('invoice_date', '>=', $thirtyDaysAgo);
            
        if ($locationId) $query->where('location_id', $locationId);

        $salesData = $query->select(
                DB::raw('DATE(invoice_date) as date'),
                DB::raw('SUM(grand_total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill in missing days
        $labels = [];
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $dateStr = (clone $thirtyDaysAgo)->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($dateStr)->format('M d');
            $data[] = $salesData[$dateStr] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
