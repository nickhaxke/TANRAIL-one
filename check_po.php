<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$po = \App\Domains\Core\Models\PurchaseOrder::latest()->first();
print_r($po->toArray());
