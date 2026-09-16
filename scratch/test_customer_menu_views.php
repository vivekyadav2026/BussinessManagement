<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\Location;
use App\Models\RestaurantOrder;
use App\Models\Category;

foreach (Organization::all() as $o) {
    echo "ID: {$o->id} | Name: {$o->name} | CGST: " . var_export($o->cgst_percent, true) . " | SGST: " . var_export($o->sgst_percent, true) . "\n";
}
$org = Organization::where('name', 'like', '%WILLIAM%')->first() ?? Organization::first();
$location = Location::where('organization_id', $org->id)->first();
$order = RestaurantOrder::where('organization_id', $org->id)->with('items')->first();

if (!$order) {
    echo "No order found, creating mock...\n";
    $order = new RestaurantOrder([
        'order_number' => 'ORD-1001',
        'customer_name' => 'John Doe',
        'customer_phone' => '9876543210',
        'status' => 'Preparing',
        'payment_status' => 'Pending',
        'subtotal' => 450,
        'tax' => 22.5,
        'total' => 472.5,
    ]);
    $order->organization_id = $org->id;
    $order->location_id = $location->id;
    $order->created_at = now();
    $order->items = collect([]);
}

$cart = [
    1 => [
        'id' => 1,
        'name' => 'gargeours',
        'price' => 222,
        'quantity' => 1,
    ]
];

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

echo "Testing public.menu.cart rendering for {$org->name}...\n";
$htmlCart = view('public.menu.cart', [
    'organization' => $org,
    'location' => $location,
    'cart' => $cart
])->render();

if (str_contains($htmlCart, '₹233.10')) {
    echo "ERROR: ₹233.10 found in cart! Tax was incorrectly added!\n";
} elseif (str_contains($htmlCart, '₹222.00')) {
    echo "SUCCESS: Grand Total is exactly ₹222.00! No ghost 2.5% tax added!\n";
}
if (str_contains($htmlCart, 'Central GST (2.5%)')) {
    echo "ERROR: Central GST 2.5% is still shown!\n";
} else {
    echo "SUCCESS: Central GST 2.5% is NOT shown!\n";
}

echo "Testing public.menu.checkout rendering...\n";
$htmlCheckout = view('public.menu.checkout', [
    'organization' => $org,
    'location' => $location,
    'cart' => $cart
])->render();
echo "Checkout view rendered: " . strlen($htmlCheckout) . " bytes\n";

echo "Testing public.menu.track rendering...\n";
$htmlTrack = view('public.menu.track', [
    'organization' => $org,
    'location' => $location,
    'order' => $order,
    'payment' => null,
    'key' => 'rzp_test_xxxx'
])->render();
echo "Track view rendered: " . strlen($htmlTrack) . " bytes\n";

echo "Testing public.menu.index rendering...\n";
$categories = \App\Models\MenuCategory::with('items')->where('organization_id', $org->id)->get();
$activeOrders = collect([$order]);
$htmlIndex = view('public.menu.index', [
    'organization' => $org,
    'location' => $location,
    'categories' => $categories,
    'table' => null,
    'activeOrders' => $activeOrders
])->render();
echo "Index view rendered: " . strlen($htmlIndex) . " bytes\n";

echo "ALL PUBLIC MENU VIEWS RENDERED SUCCESSFULLY!\n";
