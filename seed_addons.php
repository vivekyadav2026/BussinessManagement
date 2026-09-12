<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plan;
use App\Models\PlanFeature;

// 1. Advanced Analytics
$analytics = Plan::create([
    'name' => 'Advanced Analytics (Add-on)',
    'price_monthly' => 99,
    'price_yearly' => 990,
    'description' => 'Get deep insights with advanced business reports and analytics.',
    'type' => 'addon',
    'is_active' => true
]);
PlanFeature::create(['plan_id' => $analytics->id, 'feature_code' => 'advanced_analytics', 'feature_value' => 'true']);

// 2. Extra Invoicing
$invoicing = Plan::create([
    'name' => 'Extra 5000 Invoices (Add-on)',
    'price_monthly' => 149,
    'price_yearly' => 1490,
    'description' => 'Add 5000 more invoices per month to your existing limit.',
    'type' => 'addon',
    'is_active' => true
]);
PlanFeature::create(['plan_id' => $invoicing->id, 'feature_code' => 'max_invoices_per_month', 'feature_value' => '5000']);

// 3. Extra Locations
$locations = Plan::create([
    'name' => 'Multi-Branch Pack (Add-on)',
    'price_monthly' => 299,
    'price_yearly' => 2990,
    'description' => 'Manage up to 5 additional branches from a single dashboard.',
    'type' => 'addon',
    'is_active' => true
]);
PlanFeature::create(['plan_id' => $locations->id, 'feature_code' => 'max_locations', 'feature_value' => '5']);

echo 'Dummy Addons Created';
