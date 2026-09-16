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

    public function fetchTablesStatus()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        if (!$locationId) {
            return response()->json([]);
        }

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
                    ->latest()
                    ->first();

                $table->active_order = $activeOrder;
                $table->is_occupied = $activeOrder ? true : false;
                return $table;
            });

        return response()->json($tables);
    }

    public function getTableOrder(RestaurantTable $table)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($table->organization_id !== $orgId, 403);

        $locationId = LocationManager::getActiveLocationId();

        $activeOrders = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->where('restaurant_table_id', $table->id)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->oldest()
            ->get();

        return response()->json([
            'table' => $table,
            'active_orders' => $activeOrders
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
            'customer_phone' => ['nullable', 'string', 'regex:/^(?:\+91[\-\s]?|0)?[6-9][0-9]{9}$/'],
            'notes' => 'nullable|string|max:500'
        ], [
            'customer_phone.regex' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
        ]);

        try {
            $order = DB::transaction(function () use ($request, $orgId, $locationId) {
                $tableId = $request->restaurant_table_id;

                $orderNumber = RestaurantOrder::generateNextOrderNumber($orgId, $locationId);
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
                    'special_notes' => $request->notes
                ]);

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

                $org = \App\Models\Organization::find($orgId);
                $cgstPercent = $org ? (float)$org->cgst_percent : 0;
                $sgstPercent = $org ? (float)$org->sgst_percent : 0;

                $cgstAmount = ($subtotal * $cgstPercent) / 100;
                $sgstAmount = ($subtotal * $sgstPercent) / 100;

                $tax = $cgstAmount + $sgstAmount;
                $grandTotal = $subtotal + $tax;

                $order->update([
                    'subtotal' => $subtotal,
                    'cgst' => $cgstAmount,
                    'sgst' => $sgstAmount,
                    'tax' => $tax,
                    'total' => $grandTotal
                ]);

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order sent to Kitchen successfully!',
                'order' => $order->load(['items', 'table']),
                'print_kot_url' => route('organization.menu.pos.orders.print-kot', $order),
                'print_receipt_url' => route('organization.menu.pos.orders.print-receipt', $order)
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
            'payment_method' => 'nullable|in:Cash,UPI,Card',
            'discount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($order, $request, $orgId) {
                $discount = floatval($request->discount ?? 0);
                
                $ordersToSettle = collect([$order]);
                if ($order->restaurant_table_id) {
                    $otherOrders = RestaurantOrder::with('items')
                        ->where('organization_id', $orgId)
                        ->where('restaurant_table_id', $order->restaurant_table_id)
                        ->whereNotIn('status', ['Cancelled', 'Completed'])
                        ->where('id', '!=', $order->id)
                        ->get();
                    $ordersToSettle = $ordersToSettle->concat($otherOrders);
                } elseif ($request->has('extra_order_ids') && is_array($request->extra_order_ids)) {
                    $extraOrders = RestaurantOrder::with('items')
                        ->where('organization_id', $orgId)
                        ->whereIn('id', $request->extra_order_ids)
                        ->whereNotIn('status', ['Cancelled', 'Completed'])
                        ->get();
                    $ordersToSettle = $ordersToSettle->concat($extraOrders);
                }

                $grossTotal = $ordersToSettle->sum('total');
                $finalTotal = max(0, $grossTotal - $discount);

                // Mark all pending orders as Completed & Paid
                foreach ($ordersToSettle as $o) {
                    $o->update([
                        'payment_status' => 'Paid',
                        'status' => 'Completed',
                        // Store the final discounted total only on the primary order
                        'total' => ($o->id === $order->id) ? $finalTotal : 0 
                    ]);
                }

                // Generate Official Organization Invoice for accounting & ledger tracking safely
                if (!$order->invoice_id) {
                    try {
                        $invoiceItems = [];
                        foreach ($ordersToSettle as $o) {
                            foreach ($o->items as $item) {
                                $invoiceItems[] = [
                                    'name' => $item->name_snapshot,
                                    'unit_price' => $item->price_snapshot,
                                    'quantity' => $item->quantity,
                                    'tax_rate' => 0
                                ];
                            }
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
                        
                        // Link invoice to all settled orders
                        foreach ($ordersToSettle as $o) {
                            $o->update(['invoice_id' => $invoice->id]);
                        }
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
        
        // Fetch all orders from the same invoice to combine their items on the receipt
        $allOrders = collect([$order]);
        if ($order->invoice_id) {
            $otherOrders = RestaurantOrder::with('items')
                ->where('invoice_id', $order->invoice_id)
                ->where('id', '!=', $order->id)
                ->get();
            $allOrders = $allOrders->concat($otherOrders);
        }

        return view('organization.menu.receipt', compact('order', 'allOrders'));
    }

    public function printKot(RestaurantOrder $order)
    {
        $orgId = auth()->user()->organization_id;
        abort_if($order->organization_id !== $orgId, 403);

        $order->load(['items', 'table', 'organization', 'location']);
        return view('organization.menu.kot_print', compact('order'));
    }
}
