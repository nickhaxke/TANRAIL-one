<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domains\Core\Models\PurchaseOrder;
use App\Domains\Core\Models\PurchaseOrderLine;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ItemCategory;
use App\Domains\Core\Models\Unit;
use App\Domains\Core\Models\BusinessUnit;
use Illuminate\Support\Str;

$bu = BusinessUnit::find(2); // Restaurant
$supplierId = 1;
$branchId = 2; // MAGUFURI
$userId = 3; // Fatma

$categories = ItemCategory::where('business_unit_id', 2)->get();
if ($categories->isEmpty()) {
    echo "No categories found for BU 2.\n";
    exit;
}

$rawCat = $categories->where('name', 'Raw Materials')->first() ?? $categories->first();
$beverageCat = $categories->where('name', 'Beverages')->first() ?? $categories->first();
$foodCat = $categories->where('name', 'Food items')->first() ?? $categories->first();

$unitKg = Unit::where('code', 'KG')->first() ?? Unit::first();
$unitL = Unit::where('code', 'L')->first() ?? Unit::first();
$unitPc = Unit::where('code', 'PC')->first() ?? Unit::first();
$unitBox = Unit::where('code', 'BOX')->first() ?? Unit::first();

$itemsToCreate = [
    ['name' => 'Unga wa Ngano (Azam)', 'cat' => $rawCat->id, 'unit' => $unitKg->id, 'price' => 2000, 'qty' => 50],
    ['name' => 'Mafuta ya Kupikia (Korie)', 'cat' => $rawCat->id, 'unit' => $unitL->id, 'price' => 5500, 'qty' => 20],
    ['name' => 'Mchele Super (Mbeya)', 'cat' => $rawCat->id, 'unit' => $unitKg->id, 'price' => 2800, 'qty' => 100],
    ['name' => 'Maji Uhai 0.5L', 'cat' => $beverageCat->id, 'unit' => $unitPc->id, 'price' => 300, 'qty' => 100],
    ['name' => 'Soda Coca-Cola 350ml', 'cat' => $beverageCat->id, 'unit' => $unitPc->id, 'price' => 600, 'qty' => 48],
    ['name' => 'Soda Fanta Orange 350ml', 'cat' => $beverageCat->id, 'unit' => $unitPc->id, 'price' => 600, 'qty' => 48],
    ['name' => 'Kuku Mzima', 'cat' => $rawCat->id, 'unit' => $unitPc->id, 'price' => 8000, 'qty' => 30],
    ['name' => 'Nyama ya Ng\'ombe', 'cat' => $rawCat->id, 'unit' => $unitKg->id, 'price' => 9000, 'qty' => 20],
    ['name' => 'Kitunguu Maji', 'cat' => $rawCat->id, 'unit' => $unitKg->id, 'price' => 1500, 'qty' => 15],
    ['name' => 'Nyanya', 'cat' => $rawCat->id, 'unit' => $unitKg->id, 'price' => 2500, 'qty' => 20]
];

$po = new PurchaseOrder();
$po->business_unit_id = $bu->id;
$po->branch_id = $branchId;
$po->supplier_id = $supplierId;
$po->status = 'draft';
$po->created_by = $userId;
$po->save();
$po->reference_number = 'REQ-REST-' . str_pad($po->id, 4, '0', STR_PAD_LEFT);

$total = 0;

foreach ($itemsToCreate as $data) {
    // Check if item exists by name in this BU, if not create it
    $item = Item::firstOrCreate([
        'name' => $data['name'],
        'business_unit_id' => $bu->id
    ], [
        'sku' => 'ITM-'.strtoupper(Str::random(6)),
        'type' => 'physical',
        'category_id' => $data['cat'],
        'unit_id' => $data['unit'],
        'track_inventory' => true,
        'base_price' => $data['price'] * 1.5, // Fake selling price
        'standard_cost' => $data['price'],
        'status' => 1
    ]);

    $poLine = new PurchaseOrderLine();
    $poLine->purchase_order_id = $po->id;
    $poLine->item_id = $item->id;
    $poLine->quantity = $data['qty'];
    $poLine->unit_price = $data['price'];
    $poLine->subtotal = $data['qty'] * $data['price'];
    $poLine->tax_amount = 0;
    $poLine->total = $poLine->subtotal;
    $poLine->received_quantity = 0;
    $poLine->save();

    $total += $poLine->total;
}

$po->subtotal = $total;
$po->tax_total = 0;
$po->total = $total;
$po->save();

echo "Successfully created Draft PO #{$po->reference_number} with " . count($itemsToCreate) . " items!\n";
