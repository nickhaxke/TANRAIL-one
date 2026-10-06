<?php

use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$item = Item::where('name', 'like', '%afya%')->first();
$balance = StockBalance::where('item_id', $item->id)->first();

echo "Balance for {$item->name}: ".($balance ? $balance->quantity : 'NONE')."\n";
