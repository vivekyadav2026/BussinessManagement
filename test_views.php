<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$org = \App\Models\Organization::first();
$user = $org->users()->first();
Auth::login($user);

try {
    view('organization.subscription.index', [
        'currentSubscription' => $org->activeSubscription,
        'plans' => \App\Models\Plan::with('features')->get(),
        'key' => 'test'
    ])->render();
    echo "Dashboard Render OK\n";
    
    view('pages.pricing', [
        'plans' => \App\Models\Plan::with('features')->get(),
        'type' => 'business'
    ])->render();
    echo "Pricing Render OK\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
