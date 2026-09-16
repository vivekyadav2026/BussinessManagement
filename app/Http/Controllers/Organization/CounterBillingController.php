<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\LocationManager;
use App\Services\InvoiceService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CounterBillingController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        if (!$locationId) {
            return redirect()->route('organization.dashboard')->with('error', 'Please select a location to access Counter Billing.');
        }

        // Fetch Categories & Menu Items
        $categories = MenuCategory::with(['items' => function($q) {
            $q->where('is_available', true)->orderBy('name');
        }])->where('organization_id', $orgId)
           ->where('location_id', $locationId)
           ->orderBy('sort_order')
           ->get();

        $org = \App\Models\Organization::find($orgId);

        return view('organization.menu.counter', compact('categories', 'org'));
    }

    public function fetchActiveOrders()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        $activeOrders = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->where('restaurant_table_id', null) // Counter orders have no tables
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->where('payment_status', 'Pending')
            ->latest()
            ->get();

        return response()->json($activeOrders);
    }

    public function fetchCompletedOrders()
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        $completedOrders = RestaurantOrder::with('items')
            ->where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->where('restaurant_table_id', null)
            ->where('status', 'Completed')
            ->whereDate('created_at', now()->toDateString()) // Only today's
            ->latest()
            ->get();

        return response()->json($completedOrders);
    }

    public function saveOrder(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();

        if (!$locationId) {
            return response()->json(['success' => false, 'message' => 'No active location selected.'], 400);
        }

        $request->validate([
            'order_id' => 'nullable|exists:restaurant_orders,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($request, $orgId, $locationId) {
                
                $order = null;
                if ($request->order_id) {
                    $order = RestaurantOrder::where('id', $request->order_id)
                        ->where('organization_id', $orgId)
                        ->first();
                }

                if (!$order) {
                    $lastOrder = RestaurantOrder::where('organization_id', $orgId)
                        ->where('location_id', $locationId)
                        ->whereDate('created_at', \Carbon\Carbon::today())
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    $nextNumber = 1;
                    if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_number, $matches)) {
                        $nextNumber = intval($matches[1]) + 1;
                    }
                    
                    $orderNumber = 'TKN-' . $nextNumber;
                    $order = RestaurantOrder::create([
                        'organization_id' => $orgId,
                        'location_id' => $locationId,
                        'restaurant_table_id' => null, // No table
                        'order_number' => $orderNumber,
                        'customer_name' => $request->customer_name ?? 'Token ' . rand(100, 999),
                        'customer_phone' => $request->customer_phone,
                        'order_type' => $request->order_type ?? 'Takeaway',
                        'status' => 'Received',
                        'payment_status' => 'Pending',
                        'special_notes' => $request->notes,
                    ]);
                } else {
                    $order->update([
                        'customer_name' => $request->customer_name ?? $order->customer_name,
                        'customer_phone' => $request->customer_phone ?? $order->customer_phone,
                        'order_type' => $request->order_type ?? $order->order_type,
                        'special_notes' => $request->notes,
                    ]);
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

                $org = \App\Models\Organization::find($orgId);
                $cgstPercent = $org ? (float)$org->cgst_percent : 0;
                $sgstPercent = $org ? (float)$org->sgst_percent : 0;

                $cgstAmount = round(($subtotal * $cgstPercent) / 100, 2);
                $sgstAmount = round(($subtotal * $sgstPercent) / 100, 2);

                $tax = $cgstAmount + $sgstAmount;
                $grandTotal = round($subtotal + $tax, 2);

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
                'message' => 'Token saved successfully!',
                'order' => $order->load(['items']),
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
            'payment_method' => 'required|in:Cash,UPI,Card',
            'discount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($order, $request, $orgId) {
                $discount = floatval($request->discount ?? 0);
                $finalTotal = max(0, $order->total - $discount);

                $order->update([
                    'payment_status' => 'Paid',
                    'status' => 'Completed',
                    'total' => $finalTotal
                ]);

                // Create invoice if not exists
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
                            'notes' => "Counter Order #{$order->order_number} ({$order->customer_name})"
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

        $order->update(['status' => 'Cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled.'
        ]);
    }
}
