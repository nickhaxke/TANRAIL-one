<?php

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Models\User;
use App\Domains\Core\Services\ContextManager;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::find(3);
Auth::login($user);

$branch = Branch::with(['businessUnit'])->find(2);
app(ContextManager::class)->setActiveBranch($branch);
if ($branch->businessUnit) {
    app(ContextManager::class)->setActiveBusinessUnit($branch->businessUnit);
}

$cateringBu = $branch->businessUnit;
$location = InventoryLocation::withoutGlobalScopes()->find($branch->default_sales_location_id);

$stockBalances = [];
if ($location) {
    $stockBalances = StockBalance::withoutGlobalScopes()
        ->where('inventory_location_id', $location->id)
        ->pluck('quantity', 'item_id')
        ->toArray();
}

$itemsQuery = Item::withoutGlobalScopes()->with('category')->where('status', true);
if ($cateringBu) {
    $itemsQuery->where('business_unit_id', $cateringBu->id);
}

$items = $itemsQuery->get()->map(function ($item) use ($stockBalances) {
    $inStock = $item->track_inventory ? (float) ($stockBalances[$item->id] ?? 0) : 9999;

    return [
        'id' => $item->id,
        'name' => $item->name,
        'stock' => $inStock,
        'category' => $item->category?->name ?? 'Uncategorized',
    ];
});

echo 'Total items fetched: '.count($items)."\n";
print_r($items->toArray());
