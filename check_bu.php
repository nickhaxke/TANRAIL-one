<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = \App\Domains\Core\Models\Item::where('name', 'like', '%afya%')->first();
echo "Item BU: " . $item->business_unit_id . "\n";
echo "Branch 2 BU: " . \App\Domains\Core\Models\Branch::find(2)->business_unit_id . "\n";
echo "Branch 3 BU: " . \App\Domains\Core\Models\Branch::find(3)->business_unit_id . "\n";
