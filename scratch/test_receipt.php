<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RestaurantOrder;

$order = RestaurantOrder::first();
if ($order) {
    echo "Testing Paid receipt rendering...\n";
    $order->payment_status = 'Paid';
    $htmlPaid = view('organization.menu.receipt', ['order' => $order, 'allOrders' => collect([$order])])->render();
    echo "Paid receipt rendered successfully! Length: " . strlen($htmlPaid) . "\n\n";

    echo "Testing Pending receipt rendering...\n";
    $order->payment_status = 'Pending';
    $htmlPending = view('organization.menu.receipt', ['order' => $order, 'allOrders' => collect([$order])])->render();
    echo "Pending receipt rendered successfully! Length: " . strlen($htmlPending) . "\n";
} else {
    echo "No order found to test.\n";
}
