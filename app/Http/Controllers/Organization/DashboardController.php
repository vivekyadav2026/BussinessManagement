<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;
use App\Services\BusinessHealthService;
use App\Models\Location;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        // The active location is handled by session, or explicit request override
        $locationId = session('active_location_id'); 

        $sales = AnalyticsService::getSalesAndProfitMetrics($orgId, $locationId);
        $inventory = AnalyticsService::getInventoryMetrics($orgId, $locationId);
        $receivables = AnalyticsService::getReceivablesMetrics($orgId, $locationId);
        $customers = AnalyticsService::getCustomerMetrics($orgId);
        
        $invoiceStatuses = AnalyticsService::getInvoiceStatusDistribution($orgId, $locationId);
        $dailySales = AnalyticsService::getDailySalesChart($orgId, $locationId);
        
        $health = BusinessHealthService::calculateScore($orgId, $locationId, $sales, $inventory, $receivables, $customers);

        return view('organization.dashboard', compact(
            'sales', 'inventory', 'receivables', 'customers', 
            'invoiceStatuses', 'dailySales', 'health'
        ));
    }
}
