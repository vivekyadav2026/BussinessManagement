<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use App\Services\BusinessHealthService;
use App\Models\Location;
use App\Models\Invoice;
use App\Models\RestaurantOrder;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id'); 

        $sales = AnalyticsService::getSalesAndProfitMetrics($orgId, $locationId);
        $inventory = AnalyticsService::getInventoryMetrics($orgId, $locationId);
        $receivables = AnalyticsService::getReceivablesMetrics($orgId, $locationId);
        $customers = AnalyticsService::getCustomerMetrics($orgId);
        
        $invoiceStatuses = AnalyticsService::getInvoiceStatusDistribution($orgId, $locationId);
        $dailySales = AnalyticsService::getDailySalesChart($orgId, $locationId);
        
        $health = BusinessHealthService::calculateScore($orgId, $locationId, $sales, $inventory, $receivables, $customers);

        // Fetch recent invoices
        $recentInvoices = Invoice::with('client')
            ->where('organization_id', $orgId)
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->latest()
            ->take(5)
            ->get();

        // Fetch recent restaurant/counter orders (if restaurant module enabled)
        $recentOrders = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->latest()
            ->take(5)
            ->get();

        // Fetch low stock items for retail/inventory view
        $lowStockItems = DB::table('inventory_stocks')
            ->join('products', 'inventory_stocks.product_id', '=', 'products.id')
            ->where('inventory_stocks.organization_id', $orgId)
            ->when($locationId, fn($q) => $q->where('inventory_stocks.location_id', $locationId))
            ->whereColumn('inventory_stocks.quantity', '<=', 'products.min_stock_level')
            ->select('products.name', 'products.sku', 'inventory_stocks.quantity', 'products.min_stock_level')
            ->take(5)
            ->get();

        // Fetch recent clients
        $recentClients = Client::where('organization_id', $orgId)
            ->latest()
            ->take(5)
            ->get();

        return view('organization.dashboard', compact(
            'sales', 'inventory', 'receivables', 'customers', 
            'invoiceStatuses', 'dailySales', 'health',
            'recentInvoices', 'recentOrders', 'lowStockItems', 'recentClients'
        ));
    }
}
