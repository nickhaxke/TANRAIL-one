<?php

use App\Domains\Core\Models\Item;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$item = Item::withoutGlobalScopes()->where('name', 'like', '%afya%')->first();
echo 'track_inventory: '.$item->track_inventory."\n";
