<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = \App\Domains\Core\Models\Item::where('name', 'like', '%afya%')->first();
$balance = \App\Domains\Core\Models\StockBalance::where('item_id', $item->id)->first();

echo "Balance for {$item->name}: " . ($balance ? $balance->quantity : 'NONE') . "\n";
