<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use App\Services\BusinessHealthService;
use App\Models\Location;
use App\Models\Invoice;
use App\Models\RestaurantOrder;

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

        // Fetch recent invoices & restaurant/counter orders
        $recentInvoices = Invoice::with('client')
            ->where('organization_id', $orgId)
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->when($locationId, fn($q) => $q->where('location_id', $locationId))
            ->latest()
            ->take(5)
            ->get();

        return view('organization.dashboard', compact(
            'sales', 'inventory', 'receivables', 'customers', 
            'invoiceStatuses', 'dailySales', 'health',
            'recentInvoices', 'recentOrders'
        ));
    }
}
