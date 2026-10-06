<?php

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\Item;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$item = Item::where('name', 'like', '%afya%')->first();
echo 'Item BU: '.$item->business_unit_id."\n";
echo 'Branch 2 BU: '.Branch::find(2)->business_unit_id."\n";
echo 'Branch 3 BU: '.Branch::find(3)->business_unit_id."\n";
