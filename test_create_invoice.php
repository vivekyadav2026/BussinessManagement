<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\User;
use App\Models\Product;
use App\Models\Client;
use App\Models\Location;
use App\Services\LocationManager;
use App\Services\InvoiceService;

$org = Organization::first();
$user = User::where('organization_id', $org->id)->first();
$location = Location::where('organization_id', $org->id)->first();

auth()->login($user);
LocationManager::setActiveLocationId($location->id);

$product = Product::where('organization_id', $org->id)->first();
$client = Client::where('organization_id', $org->id)->first();

echo "Testing Invoice Creation for Org: " . $org->name . " (User: " . $user->name . ")\n";
echo "Active Location: " . $location->name . " (ID: " . $location->id . ")\n";
echo "Selected Product: " . $product->name . " (Current Stock: " . $product->stock . ")\n";

$payload = [
    'client_id' => $client ? $client->id : null,
    'invoice_date' => date('Y-m-d'),
    'due_date' => date('Y-m-d', strtotime('+7 days')),
    'items' => [
        [
            'product_id' => $product->id,
            'quantity' => 1,
        ]
    ],
    'discount' => 0,
    'amount_paid' => $product->selling_price,
    'status' => 'Paid',
    'notes' => 'Test invoice generation',
];

try {
    $invoice = InvoiceService::createInvoice($payload);
    echo "✅ SUCCESS: Created Invoice #" . $invoice->invoice_number . " (Grand Total: ₹" . $invoice->grand_total . ")\n";
} catch (\Exception $e) {
    echo "❌ INVOICE CREATION FAILED: " . $e->getMessage() . "\n";
}
