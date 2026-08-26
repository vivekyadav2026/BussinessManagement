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

        $orgId = auth()->user()->organization_id;

        // Auto-generate some dummy orders if there are none in the active location
        $ordersCount = RestaurantOrder::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->count();

        if ($ordersCount === 0) {
            $table = RestaurantTable::where('organization_id', $orgId)
                ->where('location_id', $locationId)
                ->first();

            if (!$table) {
                $table = RestaurantTable::create([
                    'organization_id' => $orgId,
                    'location_id' => $locationId,
                    'name' => 'Table 1',
                    'public_token' => Str::random(32),
                    'is_active' => true,
                ]);
            }

            // Order 1: Received status
            $o1 = RestaurantOrder::create([
                'organization_id' => $orgId,
                'location_id' => $locationId,
                'restaurant_table_id' => $table->id,
                'order_number' => 'ORD-' . rand(1000, 9999),
                'customer_name' => 'John Doe',
                'order_type' => 'Dine-in',
                'subtotal' => 450.00,
                'tax' => 22.50,
                'total' => 472.50,
                'status' => 'Received',
            ]);
            $o1->items()->createMany([
                ['name_snapshot' => 'Paneer Tikka Masala', 'price_snapshot' => 280.00, 'quantity' => 1, 'total' => 280.00],
                ['name_snapshot' => 'Butter Naan', 'price_snapshot' => 45.00, 'quantity' => 3, 'total' => 135.00],
                ['name_snapshot' => 'Masala Papad', 'price_snapshot' => 35.00, 'quantity' => 1, 'total' => 35.00]
            ]);

            // Order 2: Preparing status
            $o2 = RestaurantOrder::create([
                'organization_id' => $orgId,
                'location_id' => $locationId,
                'restaurant_table_id' => $table->id,
                'order_number' => 'ORD-' . rand(1000, 9999),
                'customer_name' => 'Sarah Smith',
                'order_type' => 'Dine-in',
                'subtotal' => 310.00,
                'tax' => 15.50,
                'total' => 325.50,
                'status' => 'Preparing',
            ]);
            $o2->items()->createMany([
                ['name_snapshot' => 'Veg Hakka Noodles', 'price_snapshot' => 180.00, 'quantity' => 1, 'total' => 180.00],
                ['name_snapshot' => 'Chilli Paneer Dry', 'price_snapshot' => 130.00, 'quantity' => 1, 'total' => 130.00]
            ]);

            // Order 3: Ready status
            $o3 = RestaurantOrder::create([
                'organization_id' => $orgId,
                'location_id' => $locationId,
                'restaurant_table_id' => $table->id,
                'order_number' => 'ORD-' . rand(1000, 9999),
                'customer_name' => 'Mike Johnson',
                'order_type' => 'Takeaway',
                'subtotal' => 120.00,
                'tax' => 6.00,
                'total' => 126.00,
                'status' => 'Ready',
            ]);
            $o3->items()->createMany([
                ['name_snapshot' => 'Chocolate Brownie Fudge', 'price_snapshot' => 120.00, 'quantity' => 1, 'total' => 120.00]
            ]);
        }

        return view('organization.restaurant.kitchen');
    }

    public function fetchOrders()
    {
        $orders = RestaurantOrder::with(['items', 'table'])
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
}
