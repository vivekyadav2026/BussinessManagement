<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(3);

$req = \Illuminate\Http\Request::create('/organization/menu/pos/orders/26/settle', 'POST', ['payment_method'=>'Cash']);
$req->headers->set('Accept', 'application/json');

$res = app()->handle($req);
echo "STATUS: " . $res->getStatusCode() . "\n";
echo $res->getContent();
