<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$plan = \App\Models\Plan::create([
    'name' => 'Employee Management Add-on',
    'price_monthly' => 199,
    'price_yearly' => 1990,
    'description' => 'Add advanced HR and Payroll modules to your base plan.',
    'type' => 'addon',
    'is_active' => true
]);
\App\Models\PlanFeature::create(['plan_id' => $plan->id, 'feature_code' => 'module_payroll', 'feature_value' => 'true']);
\App\Models\PlanFeature::create(['plan_id' => $plan->id, 'feature_code' => 'max_employees', 'feature_value' => '50']);
echo 'Addon Created';
