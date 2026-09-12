<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\Plan;
use App\Models\OrganizationSubscription;
use App\Services\SubscriptionService;

// Create dummy org
$org = Organization::create([
    'name' => 'Addon Test Org',
    'phone' => '9998887776',
    'email' => 'addon@test.com'
]);

// 1. Assign Base Plan (Free) which has max_invoices_per_month = 100
$freePlan = Plan::where('name', 'Free')->first();
if(!$freePlan) {
    $freePlan = Plan::create(['name' => 'Free', 'type' => 'base', 'price_monthly'=>0, 'price_yearly'=>0]);
    \App\Models\PlanFeature::create(['plan_id' => $freePlan->id, 'feature_code' => 'max_invoices_per_month', 'feature_value' => '100']);
}

$controller = app()->make(\App\Http\Controllers\Organization\SubscriptionController::class);

// Activate base plan manually via internal method reflection or DB
OrganizationSubscription::create([
    'organization_id' => $org->id,
    'plan_id' => $freePlan->id,
    'status' => 'Active',
    'starts_at' => now(),
    'ends_at' => now()->addYear()
]);

// Check initial limit
$initialLimit = SubscriptionService::getFeatureValue($org->id, 'max_invoices_per_month', 0);
echo "Initial Invoice Limit: " . $initialLimit . "\n";

// 2. Buy Addon (Extra 5000 Invoices)
$addonPlan = Plan::where('name', 'Extra 5000 Invoices (Add-on)')->first();
OrganizationSubscription::create([
    'organization_id' => $org->id,
    'plan_id' => $addonPlan->id,
    'status' => 'Active',
    'starts_at' => now(),
    'ends_at' => now()->addMonth()
]);

// Check new limit
$newLimit = SubscriptionService::getFeatureValue($org->id, 'max_invoices_per_month', 0);
echo "New Invoice Limit after Addon: " . $newLimit . "\n";

// Buy Payroll Addon (boolean)
$payrollAddon = Plan::where('name', 'Employee Management Add-on')->first();
OrganizationSubscription::create([
    'organization_id' => $org->id,
    'plan_id' => $payrollAddon->id,
    'status' => 'Active',
    'starts_at' => now(),
    'ends_at' => now()->addMonth()
]);

$hasPayroll = SubscriptionService::hasFeature($org->id, 'module_payroll') ? 'Yes' : 'No';
echo "Has Payroll Feature: " . $hasPayroll . "\n";

// Ensure active subscriptions count is 3 (Base + 2 Addons)
$activeCount = SubscriptionService::getActiveSubscriptions($org->id)->count();
echo "Active Subscriptions Count: " . $activeCount . "\n";

$org->delete();
