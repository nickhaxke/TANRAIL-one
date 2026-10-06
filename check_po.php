<?php

use App\Domains\Core\Models\PurchaseOrder;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$po = PurchaseOrder::latest()->first();
print_r($po->toArray());
