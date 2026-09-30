<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Domains\Core\Models\Item::withoutGlobalScopes()
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
echo "Found in query? " . ($found ? "Yes" : "No") . "\n";

$sql = \App\Domains\Core\Models\Item::withoutGlobalScopes()
    ->where('business_unit_id', 2)
    ->where('status', true)
    ->orderBy('category_id')
    ->orderBy('name')
    ->toSql();
echo "SQL: " . $sql . "\n";
