<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantOrder;

class KitchenOrderController extends Controller
{
    public function index()
    {
        if (!session('active_location_id')) {
            return redirect()->route('organization.dashboard')->with('error', 'Select a location first.');
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
