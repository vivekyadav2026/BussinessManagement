<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ORG:\n";
echo json_encode(Illuminate\Support\Facades\Schema::getColumnListing('organizations')) . "\n";
echo "ORDERS:\n";
echo json_encode(Illuminate\Support\Facades\Schema::getColumnListing('restaurant_orders')) . "\n";
echo "INVOICES:\n";
echo json_encode(Illuminate\Support\Facades\Schema::getColumnListing('invoices')) . "\n";
echo "INVOICE ITEMS:\n";
echo json_encode(Illuminate\Support\Facades\Schema::getColumnListing('invoice_items')) . "\n";
