<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantOrder;
use App\Models\RestaurantTable;
use Illuminate\Support\Str;

class KitchenOrderController extends Controller
{
    public function index()
    {
        $locationId = session('active_location_id');
        if (!$locationId) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned to any location. Please contact your administrator.');
        }

        return view('organization.restaurant.kitchen');
    }

    public function fetchOrders()
    {
        $orders = RestaurantOrder::select([
                'id', 'organization_id', 'location_id', 'restaurant_table_id', 
                'order_number', 'customer_name', 'customer_phone', 'order_type', 
                'subtotal', 'tax', 'total', 'payment_status', 'special_notes', 
                'status', 'created_at'
            ])
            ->with([
                'items:id,restaurant_order_id,name_snapshot,price_snapshot,quantity,total',
                'table:id,name'
            ])
            ->where('organization_id', auth()->user()->organization_id)
            ->where('location_id', session('active_location_id'))
            ->whereIn('status', ['Received', 'Preparing', 'Ready'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($orders);
    }



    public function updateStatus(Request $request, RestaurantOrder $order)
    {
        if ($order->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:Preparing,Ready,Served,Cancelled']);

        $order->update(['status' => $request->status]);

        if ($request->status === 'Served' && !$order->invoice_id) {
            // Generate Invoice via InvoiceService
            $invoiceData = [
                'client_id' => null, // Walk-in or unregistered
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'status' => 'Paid', // Typically, served meals are paid or expected to be paid immediately
                'amount_paid' => $order->total,
                'notes' => 'Generated from Restaurant Order: ' . $order->order_number,
                'items' => []
            ];

            foreach ($order->items as $item) {
                // We resolve the original MenuItem to get the tax_rate, though it doesn't have one
                // Wait, if it doesn't have tax_rate, we can just pass the unit_price, and tax_rate = 5 (hardcoded from PublicOrderController)
                // Let's pass the raw values
                $invoiceData['items'][] = [
                    'name' => $item->name_snapshot,
                    'unit_price' => $item->price_snapshot, // actually wait, RestaurantOrderItem has price_snapshot?
                    // Let's check RestaurantOrderItem model fields: menu_item_id, name_snapshot, price_snapshot, quantity, total
                    // Actually let's use the DB values
                    'quantity' => $item->quantity,
                    'tax_rate' => 5 // hardcoded 5% as per earlier
                ];
            }

            // Note: the InvoiceService expects LocationManager::getActiveLocationId() or session
            // But we can bypass or set it. InvoiceService uses LocationManager::getActiveLocationId() 
            // which usually looks at session. Let's ensure it's available.
            $invoice = \App\Services\InvoiceService::createInvoice($invoiceData);

            $order->update(['invoice_id' => $invoice->id]);
        }

        return response()->json(['success' => true]);
    }

    public function todaySummary()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = session('active_location_id') ?? \App\Services\LocationManager::getActiveLocationId();

        $todayOrders = RestaurantOrder::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->whereDate('created_at', now()->toDateString())
            ->whereNotIn('status', ['Cancelled']);

        $totalOrdersCount = (clone $todayOrders)->count();
        $totalRevenue = (clone $todayOrders)->sum('total');

        // Fetch Item-wise aggregation for Today
        $itemSales = \App\Models\RestaurantOrderItem::join('restaurant_orders', 'restaurant_order_items.restaurant_order_id', '=', 'restaurant_orders.id')
            ->where('restaurant_orders.organization_id', $orgId)
            ->where('restaurant_orders.location_id', $locationId)
            ->whereDate('restaurant_orders.created_at', now()->toDateString())
            ->whereNotIn('restaurant_orders.status', ['Cancelled'])
            ->select(
                'restaurant_order_items.name_snapshot',
                'restaurant_order_items.price_snapshot',
                \Illuminate\Support\Facades\DB::raw('SUM(restaurant_order_items.quantity) as total_quantity'),
                \Illuminate\Support\Facades\DB::raw('SUM(restaurant_order_items.total) as total_amount')
            )
            ->groupBy('restaurant_order_items.name_snapshot', 'restaurant_order_items.price_snapshot')
            ->orderByDesc('total_quantity')
            ->get();

        $totalItemsCount = $itemSales->sum('total_quantity');

        return response()->json([
            'total_orders' => $totalOrdersCount,
            'total_revenue' => $totalRevenue,
            'total_items_sold' => $totalItemsCount,
            'items' => $itemSales
        ]);
    }
}
