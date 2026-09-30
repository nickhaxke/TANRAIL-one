<?php

use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ItemCategory;
use App\Domains\Core\Models\BusinessUnit;
use Illuminate\Support\Str;

$bu = BusinessUnit::first();

if (!$bu) {
    echo "No Business Unit found!\n";
    return;
}

// 1. Create Categories
$categories = [
    ['name' => 'Raw Ingredients', 'description' => 'Uncooked food and supplies'],
    ['name' => 'Beverages', 'description' => 'Soft drinks, water, and juices'],
    ['name' => 'Equipment & Utensils', 'description' => 'Trays, jugs, plates, spoons'],
    ['name' => 'Packaging', 'description' => 'Takeaway boxes, cups, napkins'],
];

$catIds = [];
foreach ($categories as $cat) {
    $c = ItemCategory::firstOrCreate([
        'business_unit_id' => $bu->id,
        'name' => $cat['name']
    ], [
        'description' => $cat['description'],
        'slug' => Str::slug($cat['name'])
    ]);
    $catIds[$cat['name']] = $c->id;
}

// 2. Create Items
$items = [
    // Ingredients
    ['sku' => 'ING-RICE-01', 'name' => 'Mbeya Premium Rice (50kg)', 'type' => 'physical', 'cat' => 'Raw Ingredients', 'cost' => 120000],
    ['sku' => 'ING-OIL-01', 'name' => 'Sunflower Cooking Oil (20L)', 'type' => 'physical', 'cat' => 'Raw Ingredients', 'cost' => 65000],
    ['sku' => 'ING-ONION-01', 'name' => 'Red Onions (Sack)', 'type' => 'physical', 'cat' => 'Raw Ingredients', 'cost' => 80000],
    ['sku' => 'ING-BEEF-01', 'name' => 'Premium Beef (1kg)', 'type' => 'physical', 'cat' => 'Raw Ingredients', 'cost' => 12000],
    ['sku' => 'ING-CHCK-01', 'name' => 'Whole Chicken (Broiler)', 'type' => 'physical', 'cat' => 'Raw Ingredients', 'cost' => 9000],
    
    // Beverages
    ['sku' => 'BEV-H2O-500', 'name' => 'Kilimanjaro Water (500ml - Box of 24)', 'type' => 'physical', 'cat' => 'Beverages', 'cost' => 9500],
    ['sku' => 'BEV-SODA-COKE', 'name' => 'Coca-Cola (500ml - Box of 12)', 'type' => 'physical', 'cat' => 'Beverages', 'cost' => 11000],
    ['sku' => 'BEV-JUICE-AZAM', 'name' => 'Azam Mango Juice (1L - Box of 12)', 'type' => 'physical', 'cat' => 'Beverages', 'cost' => 24000],

    // Equipment & Utensils
    ['sku' => 'EQP-TRAY-01', 'name' => 'Serving Trays (Stainless Steel)', 'type' => 'physical', 'cat' => 'Equipment & Utensils', 'cost' => 15000],
    ['sku' => 'EQP-JUG-01', 'name' => 'Water Jugs (2 Liters)', 'type' => 'physical', 'cat' => 'Equipment & Utensils', 'cost' => 8500],
    ['sku' => 'EQP-PLATE-01', 'name' => 'Ceramic Dining Plates', 'type' => 'physical', 'cat' => 'Equipment & Utensils', 'cost' => 5000],
    ['sku' => 'EQP-FORK-01', 'name' => 'Dining Forks (Dozen)', 'type' => 'physical', 'cat' => 'Equipment & Utensils', 'cost' => 12000],

    // Packaging
    ['sku' => 'PKG-BOX-01', 'name' => 'Takeaway Food Boxes (Pack of 100)', 'type' => 'physical', 'cat' => 'Packaging', 'cost' => 35000],
    ['sku' => 'PKG-CUP-01', 'name' => 'Paper Coffee Cups with Lids (Pack of 50)', 'type' => 'physical', 'cat' => 'Packaging', 'cost' => 15000],
    ['sku' => 'PKG-NAPKIN-01', 'name' => 'Table Napkins (Bundle of 500)', 'type' => 'physical', 'cat' => 'Packaging', 'cost' => 10000],
];

$unit = \App\Domains\Core\Models\Unit::first();
if (!$unit) {
    echo "No units found in the database. Creating one...\n";
    $unit = \App\Domains\Core\Models\Unit::create(['name' => 'Piece', 'abbreviation' => 'pcs', 'business_unit_id' => $bu->id]);
}

foreach ($items as $it) {
    Item::updateOrCreate([
        'sku' => $it['sku'],
        'business_unit_id' => $bu->id
    ], [
        'name' => $it['name'],
        'type' => $it['type'],
        'category_id' => $catIds[$it['cat']],
        'track_inventory' => true,
        'base_price' => $it['cost'] * 1.5, // Sell price dummy
        'standard_cost' => $it['cost'],
        'status' => 'active',
        'unit_id' => $unit->id
    ]);
}

echo "Successfully populated TANRAIL items!";
