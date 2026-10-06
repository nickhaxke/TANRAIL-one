<?php

use App\Domains\Core\Models\InventoryLocation;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$locations = InventoryLocation::withoutGlobalScopes()->get()->toArray();
print_r($locations);
