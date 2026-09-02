<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\LocationManager;
use App\Services\InvoiceService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaiterPosController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        if (!$locationId) {
            return redirect()->route('organization.dashboard')->with('error', 'Please select a location to access Waiter POS.');
        }

        // Fetch tables with active pending orders
        $tables = RestaurantTable::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->orderBy('name')
            ->get()
            ->map(function ($table) use ($orgId, $locationId) {
                $activeOrder = RestaurantOrder::with('items')
                    ->where('organization_id', $orgId)
                    ->where('location_id', $locationId)
                    ->where('restaurant_table_id', $table->id)
                    ->whereNotIn('status', ['Cancelled', 'Completed'])
                    ->where('payment_status', 'Pending')
                    ->latest()
                    ->first();

                $table->active_order = $activeOrder;
                $table->is_occupied = $activeOrder ? true : false;
                return $table;
            });

        // Fetch Categories & Menu Items
        $categories = MenuCategory::with(['items' => function($q) {
            $q->where('is_available', true)->orderBy('name');
        }])->where('organization_id', $orgId)
           ->where('location_id', $locationId)
           ->orderBy('sort_order')
           ->get();

        return view('organization.menu.pos', compact('tables', 'categories'));
    }

    public function getTableOrder(RestaurantTable $table)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($table->organization_id !== $orgId, 403);

        $locationId = LocationManager::getActiveLocationId();

        $activeOrder = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->where('restaurant_table_id', $table->id)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where('payment_status', 'Pending')
            ->latest()
            ->first();

        return response()->json([
            'table' => $table,
            'active_order' => $activeOrder
        ]);
    }

    public function saveOrder(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        if (!$locationId) {
            return response()->json(['success' => false, 'message' => 'No active location selected.'], 400);
        }

        $request->validate([
            'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
            'order_type' => 'required|in:Dine-in,Takeaway',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $order = DB::transaction(function () use ($request, $orgId, $locationId) {
                $tableId = $request->restaurant_table_id;

                // Check if existing pending order exists for this table
                $order = null;
                if ($tableId) {
                    $order = RestaurantOrder::where('organization_id', $orgId)
                        ->where('location_id', $locationId)
                        ->where('restaurant_table_id', $tableId)
                        ->whereNotIn('status', ['Cancelled', 'Completed'])
                        ->where('payment_status', 'Pending')
                        ->latest()
                        ->first();
                }

                if (!$order) {
                    $orderNumber = 'ORD-' . strtoupper(Str::random(6));
                    $order = RestaurantOrder::create([
                        'organization_id' => $orgId,
                        'location_id' => $locationId,
                        'restaurant_table_id' => $tableId,
                        'order_number' => $orderNumber,
                        'customer_name' => $request->customer_name ?? 'Guest',
                        'customer_phone' => $request->customer_phone,
                        'order_type' => $request->order_type,
                        'status' => 'Received',
                        'payment_status' => 'Pending',
                        'notes' => $request->notes
                    ]);
                } else {
                    $order->update([
                        'customer_name' => $request->customer_name ?? $order->customer_name,
                        'customer_phone' => $request->customer_phone ?? $order->customer_phone,
                        'notes' => $request->notes ?? $order->notes,
                        'status' => 'Received' // Re-flag as received for kitchen update
                    ]);
                    // Clear previous items to update with current cart ticket
                    $order->items()->delete();
                }

                $subtotal = 0;
                foreach ($request->items as $itemData) {
                    $menuItem = MenuItem::findOrFail($itemData['menu_item_id']);
                    $itemTotal = $menuItem->price * $itemData['quantity'];
                    $subtotal += $itemTotal;

                    RestaurantOrderItem::create([
                        'restaurant_order_id' => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'name_snapshot' => $menuItem->name,
                        'price_snapshot' => $menuItem->price,
                        'quantity' => $itemData['quantity'],
                        'total' => $itemTotal
                    ]);
                }

                $tax = 0; // Tax can be dynamically calculated or included
                $grandTotal = $subtotal + $tax;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $grandTotal
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order sent to Kitchen successfully!',
                'order' => $order->load(['items', 'table']),
                'print_kot_url' => route('organization.menu.pos.orders.print-kot', $order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function settleOrder(RestaurantOrder $order, Request $request)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($order->organization_id !== $orgId, 403);

        $request->validate([
            'payment_method' => 'required|in:Cash,UPI,Card',
            'discount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($order, $request, $orgId) {
                $discount = floatval($request->discount ?? 0);
                $finalTotal = max(0, $order->total - $discount);

                // Mark current order and all pending orders on this table as Completed & Paid
                $order->update([
                    'payment_status' => 'Paid',
                    'status' => 'Completed',
                    'total' => $finalTotal
                ]);

                if ($order->restaurant_table_id) {
                    RestaurantOrder::where('organization_id', $orgId)
                        ->where('restaurant_table_id', $order->restaurant_table_id)
                        ->whereNotIn('status', ['Cancelled', 'Completed'])
                        ->update(['status' => 'Completed', 'payment_status' => 'Paid']);
                }

                // Generate Official Organization Invoice for accounting & ledger tracking safely
                if (!$order->invoice_id) {
                    try {
                        $invoiceItems = [];
                        foreach ($order->items as $item) {
                            $invoiceItems[] = [
                                'name' => $item->name_snapshot,
                                'unit_price' => $item->price_snapshot,
                                'quantity' => $item->quantity,
                                'tax_rate' => 0
                            ];
                        }

                        $invoiceData = [
                            'organization_id' => $orgId,
                            'location_id' => $order->location_id,
                            'client_id' => null,
                            'invoice_date' => now()->toDateString(),
                            'items' => $invoiceItems,
                            'discount' => $discount,
                            'amount_paid' => $finalTotal,
                            'status' => 'Paid',
                            'notes' => "Restaurant Order #{$order->order_number} (" . ($order->table->name ?? 'Takeaway') . ")"
                        ];

                        $invoice = InvoiceService::createInvoice($invoiceData);
                        $order->update(['invoice_id' => $invoice->id]);
                    } catch (\Exception $ex) {
                        Log::error('Invoice auto-creation note: ' . $ex->getMessage());
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment settled & Bill generated successfully!',
                'print_receipt_url' => route('organization.menu.pos.orders.print-receipt', $order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function cancelOrder(RestaurantOrder $order)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($order->organization_id !== $orgId, 403);

        try {
            DB::transaction(function () use ($order, $orgId) {
                // Cancel current order
                $order->update([
                    'status' => 'Cancelled'
                ]);

                // Also cancel any uncompleted active orders for this table to ensure table is 100% vacant
                if ($order->restaurant_table_id) {
                    RestaurantOrder::where('organization_id', $orgId)
                        ->where('restaurant_table_id', $order->restaurant_table_id)
                        ->whereNotIn('status', ['Cancelled', 'Completed'])
                        ->update(['status' => 'Cancelled']);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully. Table is now vacant!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function printReceipt(RestaurantOrder $order)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($order->organization_id !== $orgId, 403);

        $order->load(['items', 'table', 'organization', 'location']);
        return view('organization.menu.receipt', compact('order'));
    }

    public function printKot(RestaurantOrder $order)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($order->organization_id !== $orgId, 403);

        $order->load(['items', 'table', 'organization', 'location']);
        return view('organization.menu.kot_print', compact('order'));
    }
}
