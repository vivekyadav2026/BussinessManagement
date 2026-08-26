<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Location;
use App\Models\MenuItem;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PublicOrderController extends Controller
{
    public function addToCart(Request $request, Organization $organization, Location $location)
    {
        $request->validate(['menu_item_id' => 'required|exists:menu_items,id']);
        
        $item = MenuItem::where('menu_category_id', '!=', null)->findOrFail($request->menu_item_id);
        
        $cartKey = 'cart_' . $location->id;
        $cart = session()->get($cartKey, []);

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity']++;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1
            ];
        }

        session()->put($cartKey, $cart);

        return back()->with('success', 'Item added to cart.');
    }

    public function cart(Organization $organization, Location $location)
    {
        $cart = session()->get('cart_' . $location->id, []);
        return view('public.menu.cart', compact('organization', 'location', 'cart'));
    }

    public function removeFromCart(Request $request, Organization $organization, Location $location, $itemId)
    {
        $cartKey = 'cart_' . $location->id;
        $cart = session()->get($cartKey, []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put($cartKey, $cart);
        }

        return back();
    }

    public function checkout(Organization $organization, Location $location)
    {
        $cart = session()->get('cart_' . $location->id, []);
        if (empty($cart)) {
            return redirect()->route('public.menu', [$organization->id, $location->id]);
        }

        return view('public.menu.checkout', compact('organization', 'location', 'cart'));
    }

    public function placeOrder(Request $request, Organization $organization, Location $location)
    {
        $cartKey = 'cart_' . $location->id;
        $cart = session()->get($cartKey, []);
        
        if (empty($cart)) {
            return redirect()->route('public.menu', [$organization->id, $location->id]);
        }

        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'special_notes' => 'nullable|string|max:1000',
        ];

        $tableId = session('restaurant_table_id');
        if (!$tableId) {
            $rules['order_type'] = 'required|in:Takeaway,Online';
        }

        $request->validate($rules);

        $orderNumber = 'ORD-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            $subtotal = 0;
            
            // Re-calculate pricing server side to avoid cart tampering
            foreach ($cart as $cartItem) {
                $item = MenuItem::findOrFail($cartItem['id']);
                $subtotal += $item->price * $cartItem['quantity'];
            }

            $tax = $subtotal * 0.05; // Dummy 5% tax
            $total = $subtotal + $tax;

            $order = RestaurantOrder::create([
                'organization_id' => $organization->id,
                'location_id' => $location->id,
                'restaurant_table_id' => $tableId,
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'order_type' => $tableId ? 'Dine-in' : $request->order_type,
                'special_notes' => $request->special_notes,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'Received',
                'payment_status' => 'Pending'
            ]);

            foreach ($cart as $cartItem) {
                $item = MenuItem::findOrFail($cartItem['id']);
                RestaurantOrderItem::create([
                    'restaurant_order_id' => $order->id,
                    'menu_item_id' => $item->id,
                    'name_snapshot' => $item->name,
                    'price_snapshot' => $item->price,
                    'quantity' => $cartItem['quantity'],
                    'total' => $item->price * $cartItem['quantity']
                ]);
            }

            DB::commit();
            session()->forget($cartKey);

            return redirect()->route('public.order.track', [$organization->id, $location->id, $orderNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error placing order. Please try again.');
        }
    }

    public function track(Organization $organization, Location $location, $orderNumber)
    {
        $order = RestaurantOrder::with('items')->where('order_number', $orderNumber)->where('organization_id', $organization->id)->firstOrFail();
        
        return view('public.menu.track', compact('organization', 'location', 'order'));
    }

    public function updateQuantity(Request $request, Organization $organization, Location $location, $itemId)
    {
        $request->validate(['action' => 'required|in:increase,decrease']);
        
        $cartKey = 'cart_' . $location->id;
        $cart = session()->get($cartKey, []);

        if (isset($cart[$itemId])) {
            if ($request->action === 'increase') {
                $cart[$itemId]['quantity']++;
            } elseif ($request->action === 'decrease') {
                $cart[$itemId]['quantity']--;
                if ($cart[$itemId]['quantity'] <= 0) {
                    unset($cart[$itemId]);
                }
            }
            session()->put($cartKey, $cart);
        }

        return back();
    }
}
