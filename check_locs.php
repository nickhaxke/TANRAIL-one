<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$locs = \App\Domains\Core\Models\InventoryLocation::withoutGlobalScopes()->get();
foreach ($locs as $loc) {
    echo "Loc ID: {$loc->id}, Name: {$loc->name}, BU: {$loc->business_unit_id}\n";
}

$branch = \App\Domains\Core\Models\Branch::find(2);
echo "Branch 2 default sales location: {$branch->default_sales_location_id}\n";
