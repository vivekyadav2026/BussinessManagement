<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\Client;
use App\Models\User;
use App\Services\LocationManager;

$org = Organization::first();
$user = User::where('organization_id', $org->id)->first();
auth()->login($user);

LocationManager::setActiveLocationId(1);

echo "=== TESTING CLIENT REPORT LOGIC ===\n";
$clients = Client::where('organization_id', $org->id)
    ->with(['invoices' => function($q) {
        $q->whereNotIn('status', ['Cancelled', 'Paid']);
    }])
    ->get();

foreach ($clients as $client) {
    $sumDirect = $client->invoices->sum('amount_due'); // Fails on accessor!
    $sumCallback = $client->invoices->sum(function($inv) { return $inv->amount_due; }); // Works!
    
    echo "Client: {$client->name} | Invoices Count: {$client->invoices->count()}\n";
    echo "  - sum('amount_due') DIRECT: {$sumDirect}\n";
    echo "  - sum(callback): {$sumCallback}\n";
}
