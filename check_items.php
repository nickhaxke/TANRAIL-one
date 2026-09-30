<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Domains\Core\Models\Item::where('name', 'like', '%afya%')->get();
foreach ($items as $item) {
    echo "Item: {$item->name} | Status: {$item->status}\n";
    if ($item->status == 0) {
        $item->status = 1;
        $item->save();
        echo "Fixed status to 1!\n";
    }
}
