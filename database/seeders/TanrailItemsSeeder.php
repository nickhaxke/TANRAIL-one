<?php

namespace Database\Seeders;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\ItemCategory;
use App\Domains\Core\Models\Unit;
use Illuminate\Database\Seeder;

class TanrailItemsSeeder extends Seeder
{
    public function run(): void
    {
        $bu = BusinessUnit::first();

        if (! $bu) {
            $this->command->warn('No Business Unit found — skipping TanrailItemsSeeder.');

            return;
        }

        // 1. Units of Measure
        $unit = Unit::first() ?? Unit::create([
            'name' => 'Piece',
            'abbreviation' => 'pcs',
        ]);

        // 2. Categories
        $categories = [
            ['name' => 'Raw Ingredients',    'description' => 'Uncooked food and supplies'],
            ['name' => 'Beverages',           'description' => 'Soft drinks, water, and juices'],
            ['name' => 'Equipment & Utensils', 'description' => 'Trays, jugs, plates, spoons'],
            ['name' => 'Packaging',           'description' => 'Takeaway boxes, cups, napkins'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $c = ItemCategory::firstOrCreate(
                ['business_unit_id' => $bu->id, 'name' => $cat['name']],
                ['description' => $cat['description']]
            );
            $catIds[$cat['name']] = $c->id;
        }

        // 3. Items
        $items = [
            // Ingredients
            ['sku' => 'ING-RICE-01',   'name' => 'Mbeya Premium Rice (50kg)',              'cat' => 'Raw Ingredients',     'cost' => 120000],
            ['sku' => 'ING-OIL-01',    'name' => 'Sunflower Cooking Oil (20L)',             'cat' => 'Raw Ingredients',     'cost' => 65000],
            ['sku' => 'ING-ONION-01',  'name' => 'Red Onions (Sack)',                       'cat' => 'Raw Ingredients',     'cost' => 80000],
            ['sku' => 'ING-BEEF-01',   'name' => 'Premium Beef (1kg)',                      'cat' => 'Raw Ingredients',     'cost' => 12000],
            ['sku' => 'ING-CHCK-01',   'name' => 'Whole Chicken (Broiler)',                 'cat' => 'Raw Ingredients',     'cost' => 9000],
            // Beverages
            ['sku' => 'BEV-H2O-500',   'name' => 'Kilimanjaro Water (500ml - Box of 24)',  'cat' => 'Beverages',           'cost' => 9500],
            ['sku' => 'BEV-SODA-COKE', 'name' => 'Coca-Cola (500ml - Box of 12)',           'cat' => 'Beverages',           'cost' => 11000],
            ['sku' => 'BEV-JUICE-AZAM', 'name' => 'Azam Mango Juice (1L - Box of 12)',       'cat' => 'Beverages',           'cost' => 24000],
            // Equipment
            ['sku' => 'EQP-TRAY-01',   'name' => 'Serving Trays (Stainless Steel)',         'cat' => 'Equipment & Utensils', 'cost' => 15000],
            ['sku' => 'EQP-JUG-01',    'name' => 'Water Jugs (2 Liters)',                   'cat' => 'Equipment & Utensils', 'cost' => 8500],
            ['sku' => 'EQP-PLATE-01',  'name' => 'Ceramic Dining Plates',                   'cat' => 'Equipment & Utensils', 'cost' => 5000],
            ['sku' => 'EQP-FORK-01',   'name' => 'Dining Forks (Dozen)',                    'cat' => 'Equipment & Utensils', 'cost' => 12000],
            // Packaging
            ['sku' => 'PKG-BOX-01',    'name' => 'Takeaway Food Boxes (Pack of 100)',       'cat' => 'Packaging',           'cost' => 35000],
            ['sku' => 'PKG-CUP-01',    'name' => 'Paper Coffee Cups with Lids (Pack of 50)', 'cat' => 'Packaging',          'cost' => 15000],
            ['sku' => 'PKG-NAPKIN-01', 'name' => 'Table Napkins (Bundle of 500)',            'cat' => 'Packaging',           'cost' => 10000],
        ];

        foreach ($items as $it) {
            Item::updateOrCreate(
                ['sku' => $it['sku'], 'business_unit_id' => $bu->id],
                [
                    'name' => $it['name'],
                    'type' => 'physical',
                    'category_id' => $catIds[$it['cat']],
                    'track_inventory' => true,
                    'base_price' => $it['cost'] * 1.5,
                    'standard_cost' => $it['cost'],
                    'status' => true,
                    'unit_id' => $unit->id,
                    'can_be_purchased' => true,
                    'can_be_sold' => false,
                ]
            );
        }

        $this->command->info('✅ TANRAIL items seeded successfully ('.count($items).' items).');
    }
}
