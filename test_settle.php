<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(2);

$order = \App\Models\RestaurantOrder::find(23);
$req = new \Illuminate\Http\Request();
$req->merge(['payment_method' => 'Cash', 'discount' => 0]);

$res = app()->make(\App\Http\Controllers\Organization\WaiterPosController::class)->settleOrder($order, $req);
echo json_encode($res);
