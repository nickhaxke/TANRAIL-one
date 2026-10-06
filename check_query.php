<?php

use App\Domains\Core\Models\Item;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$items = Item::withoutGlobalScopes()
    ->where('business_unit_id', 2)
    ->where('status', true)
    ->orderBy('category_id')
    ->orderBy('name')
    ->get();

$found = false;
foreach ($items as $item) {
    if (strpos(strtolower($item->name), 'afya') !== false) {
        $found = true;
    }
}
echo 'Found in query? '.($found ? 'Yes' : 'No')."\n";

$sql = Item::withoutGlobalScopes()
    ->where('business_unit_id', 2)
    ->where('status', true)
    ->orderBy('category_id')
    ->orderBy('name')
    ->toSql();
echo 'SQL: '.$sql."\n";
