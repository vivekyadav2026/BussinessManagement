<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\PlanFeatureService;

$user = User::where('email', 'admin@spicekitchen.com')->first();
auth()->login($user);

echo "=== VERIFYING USER ACCESS FOR admin@spicekitchen.com ===\n";
echo "User: {$user->email} | Role: " . ($user->role ? $user->role->name : 'No Role') . "\n";
echo "Organization: " . $user->organization->name . "\n";

$hasRestaurantFeature = PlanFeatureService::hasFeature('module_restaurant', $user->organization_id);
echo "module_restaurant Feature Active: " . ($hasRestaurantFeature ? "YES ✅" : "NO ❌") . "\n";

$hasKitchenFeature = PlanFeatureService::hasFeature('kitchen_display', $user->organization_id);
echo "kitchen_display Feature Active: " . ($hasKitchenFeature ? "YES ✅" : "NO ❌") . "\n";

echo "Role Permissions Count: " . ($user->role ? $user->role->permissions->count() : 0) . "\n";
