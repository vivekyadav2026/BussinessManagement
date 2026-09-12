<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AnalyticsService;
use App\Services\BusinessHealthService;
use App\Models\Organization;

$org = Organization::first();
$orgId = $org->id;
$locationId = 1;

echo "Organization: " . $org->name . "\n";

$sales = AnalyticsService::getSalesAndProfitMetrics($orgId, $locationId);
$inventory = AnalyticsService::getInventoryMetrics($orgId, $locationId);
$receivables = AnalyticsService::getReceivablesMetrics($orgId, $locationId);
$customers = AnalyticsService::getCustomerMetrics($orgId);

$invoiceStatuses = AnalyticsService::getInvoiceStatusDistribution($orgId, $locationId);

$health = BusinessHealthService::calculateScore($orgId, $locationId, $sales, $inventory, $receivables, $customers);

print_r([
    'health_score' => $health['score'],
    'health_label' => $health['label'],
    'insights' => $health['insights'],
    'sales_month' => $sales['sales_month'],
    'profit_month' => $sales['profit_month'],
    'inventory_value' => $inventory['stock_value'],
    'low_stock' => $inventory['low_stock_count'],
    'outstanding' => $receivables['outstanding'],
    'overdue' => $receivables['overdue'],
    'invoice_statuses' => $invoiceStatuses
]);
