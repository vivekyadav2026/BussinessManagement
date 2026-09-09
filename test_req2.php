<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(3); // User 3 is in Org 2

$req = \Illuminate\Http\Request::create('/organization/menu/pos/orders/24/settle', 'POST', ['payment_method'=>'Cash']);
$req->headers->set('Accept', 'application/json');

// Bypass CSRF by removing the middleware or we will just hit the controller directly again but with NO fake request.
$c = app()->make(\App\Http\Controllers\Organization\WaiterPosController::class);
$order = \App\Models\RestaurantOrder::find(24);
if (!$order) {
    echo "ORDER NOT FOUND!\n";
} else {
    $res = $c->settleOrder($order, $req);
    echo "RESULT:\n";
    echo json_encode($res);
}
