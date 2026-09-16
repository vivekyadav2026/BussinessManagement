<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Organization;
use App\Models\Location;
use App\Models\RestaurantTable;

$user = User::whereNotNull('organization_id')->first();
auth()->login($user);

$org = $user->organization;
$location = Location::where('organization_id', $org->id)->first();
session(['active_location_id' => $location->id]);
session(['active_location_name' => $location->name]);

$tables = RestaurantTable::where('organization_id', $org->id)->where('location_id', $location->id)->get();

if ($tables->isEmpty()) {
    echo "Creating a mock table for testing...\n";
    $table = RestaurantTable::create([
        'organization_id' => $org->id,
        'location_id' => $location->id,
        'name' => 'Table 01',
        'is_active' => true,
    ]);
    $tables = collect([$table]);
}

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

echo "Rendering organization.tables.index...\n";
$htmlIndex = view('organization.tables.index', compact('tables'))->render();
echo "Tables index rendered: " . strlen($htmlIndex) . " bytes\n";

echo "Rendering organization.tables.print...\n";
$htmlPrint = view('organization.tables.print', compact('tables'))->render();
echo "Tables print rendered: " . strlen($htmlPrint) . " bytes\n";

echo "ALL TABLES AND QR VIEWS RENDERED CLEANLY!\n";
