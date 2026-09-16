<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RestaurantTable;

$tables = RestaurantTable::all();
foreach ($tables as $t) {
    echo "ID: {$t->id} | Name: '{$t->name}' | Token: '{$t->public_token}' | URL: " . route('public.menu.table', $t->public_token) . "\n";
}
