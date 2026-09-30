<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$locations = \App\Domains\Core\Models\InventoryLocation::withoutGlobalScopes()->get()->toArray();
print_r($locations);
