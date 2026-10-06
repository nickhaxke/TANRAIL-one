<?php

use App\Domains\Core\Models\Item;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$items = Item::where('name', 'like', '%afya%')->get();
foreach ($items as $item) {
    echo "Item: {$item->name} | Status: {$item->status}\n";
    if ($item->status == 0) {
        $item->status = 1;
        $item->save();
        echo "Fixed status to 1!\n";
    }
}
