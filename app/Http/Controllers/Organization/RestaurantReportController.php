<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Services\LocationManager;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestaurantReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasPermission('restaurant.reports') && !$user->hasPermission('restaurant.view') && !$user->hasPermission('restaurant.orders')) {
            abort(403, 'Unauthorized access to Restro Sales & Analytics.');
        }

        $orgId = $user->organization_id;
        $locationId = session('active_location_id') ?? LocationManager::getActiveLocationId();

        if (!$locationId) {
            return redirect()->route('organization.dashboard')->with('error', 'Please select a location to view reports.');
        }

        // Date & Dynamic Filtering Logic
        $filter = $request->get('period', 'today'); // today, yesterday, this_week, this_month, custom
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $orderType = $request->get('order_type', 'all'); // all, Dine-in, Takeaway

        $now = Carbon::now();

        if ($filter === 'today') {
            $from = $now->copy()->startOfDay();
            $to = $now->copy()->endOfDay();
        } elseif ($filter === 'yesterday') {
            $from = $now->copy()->subDay()->startOfDay();
            $to = $now->copy()->subDay()->endOfDay();
        } elseif ($filter === 'this_week') {
            $from = $now->copy()->startOfWeek();
            $to = $now->copy()->endOfWeek();
        } elseif ($filter === 'this_month') {
            $from = $now->copy()->startOfMonth();
            $to = $now->copy()->endOfMonth();
        } elseif ($filter === 'custom' && $startDate && $endDate) {
            $from = Carbon::parse($startDate)->startOfDay();
            $to = Carbon::parse($endDate)->endOfDay();
        } else {
            $from = $now->copy()->startOfDay();
            $to = $now->copy()->endOfDay();
            $filter = 'today';
        }

        // Base Query
        $baseQuery = RestaurantOrder::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['Cancelled']);

        if ($orderType !== 'all') {
            $baseQuery->where('order_type', $orderType);
        }

        // KPI Calculations
        $totalOrders = (clone $baseQuery)->count();
        $totalRevenue = (clone $baseQuery)->sum('total');
        $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        // Order Types Breakdown (Dine-in vs Takeaway)
        $dineInQuery = RestaurantOrder::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['Cancelled']);
        
        $dineInCount = (clone $dineInQuery)->where('order_type', 'Dine-in')->count();
        $dineInRevenue = (clone $dineInQuery)->where('order_type', 'Dine-in')->sum('total');
        $takeawayCount = (clone $dineInQuery)->where('order_type', 'Takeaway')->count();
        $takeawayRevenue = (clone $dineInQuery)->where('order_type', 'Takeaway')->sum('total');

        // Paginated Item-wise Sales Aggregation
        $itemSalesQuery = RestaurantOrderItem::join('restaurant_orders', 'restaurant_order_items.restaurant_order_id', '=', 'restaurant_orders.id')
            ->where('restaurant_orders.organization_id', $orgId)
            ->where('restaurant_orders.location_id', $locationId)
            ->whereBetween('restaurant_orders.created_at', [$from, $to])
            ->whereNotIn('restaurant_orders.status', ['Cancelled']);

        if ($orderType !== 'all') {
            $itemSalesQuery->where('restaurant_orders.order_type', $orderType);
        }

        $itemSalesQuery->select(
            'restaurant_order_items.name_snapshot',
            'restaurant_order_items.price_snapshot',
            DB::raw('SUM(restaurant_order_items.quantity) as total_quantity'),
            DB::raw('SUM(restaurant_order_items.total) as total_revenue')
        )
        ->groupBy('restaurant_order_items.name_snapshot', 'restaurant_order_items.price_snapshot')
        ->orderByDesc('total_revenue');

        $totalItemsSold = (clone $itemSalesQuery)->get()->sum('total_quantity');
        $itemSales = $itemSalesQuery->paginate(15, ['*'], 'item_page')->withQueryString();

        // Chart Data: Daily Sales Trend over selected period
        $trendData = (clone $baseQuery)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(id) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Customer-wise Orders & Items Purchased Analysis with Pagination
        $customerCollection = (clone $baseQuery)
            ->with(['items', 'table'])
            ->get()
            ->groupBy(function ($order) {
                $phone = trim($order->customer_phone);
                $name = trim($order->customer_name);
                return $phone ? $phone : ($name ? strtolower($name) : 'walk-in-guest');
            })
            ->map(function ($orders) {
                $first = $orders->first();
                $totalSpend = $orders->sum('total');
                $totalOrdersCount = $orders->count();
                $lastOrderDate = $orders->max('created_at');

                // Aggregate items purchased by this customer
                $itemsAggregated = [];
                foreach ($orders as $order) {
                    foreach ($order->items as $item) {
                        $name = $item->name_snapshot;
                        $itemsAggregated[$name] = ($itemsAggregated[$name] ?? 0) + $item->quantity;
                    }
                }
                arsort($itemsAggregated);

                return [
                    'customer_name' => $first->customer_name ?: 'Walk-in Guest',
                    'customer_phone' => $first->customer_phone ?: '-',
                    'total_orders' => $totalOrdersCount,
                    'total_spend' => $totalSpend,
                    'last_order_at' => $lastOrderDate,
                    'items_ordered' => $itemsAggregated,
                    'orders' => $orders
                ];
            })
            ->sortByDesc('total_spend')
            ->values();

        $totalCustomersCount = $customerCollection->count();
        $customerPage = $request->get('cust_page', 1);
        $perPage = 15;
        $customerSummary = new \Illuminate\Pagination\LengthAwarePaginator(
            $customerCollection->slice(($customerPage - 1) * $perPage, $perPage)->values(),
            $totalCustomersCount,
            $perPage,
            $customerPage,
            ['path' => $request->url(), 'pageName' => 'cust_page', 'query' => $request->query()]
        );

        return view('organization.menu.reports', compact(
            'filter',
            'startDate',
            'endDate',
            'orderType',
            'from',
            'to',
            'totalOrders',
            'totalRevenue',
            'avgOrderValue',
            'totalItemsSold',
            'totalCustomersCount',
            'dineInCount',
            'dineInRevenue',
            'takeawayCount',
            'takeawayRevenue',
            'itemSales',
            'trendData',
            'customerSummary'
        ));
    }
}
