<?php

namespace Database\Seeders;

use App\Domains\Core\Enums\ItemType;
use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\StockBalance;
use App\Domains\Core\Models\TaxCategory;
use App\Domains\Core\Models\Unit;
use App\Domains\Core\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Organization
        $org = Organization::firstOrCreate(
            ['code' => 'TANRAIL'],
            [
                'name' => 'TANRAIL Investments Limited',
                'trading_name' => 'TANRAIL',
                'status' => true,
                'country' => 'Tanzania',
                'city' => 'Dar es Salaam',
                'address' => 'SGR Central Station, Gerezani, Ilala',
                'email' => 'corporate@tanrail.co.tz',
                'phone' => '+255 22 211 0579',
            ]
        );

        // 2. Catering & Restaurant Business Unit
        $cateringBu = BusinessUnit::firstOrCreate(
            ['code' => 'BU-CAT-01'],
            [
                'organization_id' => $org->id,
                'name' => 'TANRAIL Catering & Dining Services',
                'category' => 'Catering & Hospitality',
                'status' => true,
                'description' => 'Station restaurants, train concourses, passenger dining, and executive onboard hospitality.',
            ]
        );

        // 3. Station Restaurant Branch
        $darRestaurant = Branch::firstOrCreate(
            ['code' => 'BR-DAR-REST'],
            [
                'business_unit_id' => $cateringBu->id,
                'name' => 'SGR Dar es Salaam Main Concourse Restaurant',
                'facility_type' => 'Station Dining & Restaurant',
                'city' => 'Dar es Salaam',
                'status' => true,
                'address' => 'SGR Magufuli Central Station, Ground Floor Dining Concourse, Dar es Salaam',
                'phone' => '+255 22 211 0590',
                'email' => 'restaurant.dar@tanrail.co.tz',
            ]
        );

        $restaurantStore = InventoryLocation::firstOrCreate(
            ['code' => 'LOC-DAR-REST-01'],
            [
                'branch_id' => $darRestaurant->id,
                'name' => 'SGR Dar Restaurant Kitchen & Bar Store',
                'status' => true,
            ]
        );

        $darRestaurant->update([
            'default_sales_location_id' => $restaurantStore->id,
        ]);

        // 4. Units of Measure
        $portionUnit = Unit::firstOrCreate(['code' => 'PORTION'], ['name' => 'Portion / Plate']);
        $bottleUnit = Unit::firstOrCreate(['code' => 'BTL'], ['name' => 'Bottle / Glass']);
        $pcsUnit = Unit::firstOrCreate(['code' => 'PCS'], ['name' => 'Piece / Item']);

        // 5. Tax Category
        $vatCategory = TaxCategory::firstOrCreate(
            ['code' => 'TRA-VAT-18'],
            [
                'organization_id' => $org->id,
                'rate' => 18.00,
                'description' => 'TRA Statutory Standard VAT 18%',
            ]
        );

        // 6. Food & Beverage Menu Items
        $menuItems = [
            [
                'sku' => 'FOOD-PIL-01',
                'name' => 'Pilau Kuku wa Kienyeji',
                'description' => 'Traditional spiced Swahili rice served with tender organic chicken, kachumbari, and homemade gravy.',
                'base_price' => 15000.00,
                'standard_cost' => 7500.00,
                'unit_id' => $portionUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'FOOD-UGL-01',
                'name' => 'Ugali Samaki wa Kukaanga',
                'description' => 'Fresh Lake Victoria Tilapia fried to golden crisp, served with piping hot ugali, mchicha, and rich tomato sauce.',
                'base_price' => 18000.00,
                'standard_cost' => 9000.00,
                'unit_id' => $portionUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'FOOD-BRY-01',
                'name' => 'Biryani ya Mbuzi Special',
                'description' => 'Aromatic slow-cooked basmati rice with succulent goat cuts in rich Zanzibar spice masala.',
                'base_price' => 16000.00,
                'standard_cost' => 8000.00,
                'unit_id' => $portionUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'FOOD-CHP-01',
                'name' => 'Chips Mayai (Zege)',
                'description' => 'Dar es Salaam famous fresh potato fries bound in double-egg omelette, served with kachumbari and pili pili.',
                'base_price' => 6000.00,
                'standard_cost' => 2500.00,
                'unit_id' => $portionUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'FOOD-MSH-01',
                'name' => 'Beef Mishkaki Skewers (3pcs)',
                'description' => 'Charcoal-grilled tender marinated beef skewers served with tamarind dip and grilled onions.',
                'base_price' => 10000.00,
                'standard_cost' => 5000.00,
                'unit_id' => $pcsUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'DRK-PAS-01',
                'name' => 'Fresh Passion Juice (Chilled)',
                'description' => '100% natural passion fruit freshly pressed daily, served chilled over ice.',
                'base_price' => 4000.00,
                'standard_cost' => 1500.00,
                'unit_id' => $bottleUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'DRK-MNG-01',
                'name' => 'Fresh Mango Puree Juice',
                'description' => 'Rich tropical mango juice made from fresh ripe Tanzanian mangoes.',
                'base_price' => 4000.00,
                'standard_cost' => 1500.00,
                'unit_id' => $bottleUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'DRK-WTR-01',
                'name' => 'Kilimanjaro Mineral Water (500ml)',
                'description' => 'Purified natural mountain mineral drinking water.',
                'base_price' => 1500.00,
                'standard_cost' => 600.00,
                'unit_id' => $bottleUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'DRK-TEA-01',
                'name' => 'Chai ya Maziwa (Spiced Tea)',
                'description' => 'Fresh cow milk boiled with ginger, cinnamon, cloves, and premium Tanzanian tea leaves.',
                'base_price' => 2500.00,
                'standard_cost' => 800.00,
                'unit_id' => $bottleUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'SNK-SAM-01',
                'name' => 'Crispy Meat Samosas (Pair)',
                'description' => 'Golden flaky pastry pockets stuffed with seasoned minced beef, garlic, and scallions.',
                'base_price' => 3000.00,
                'standard_cost' => 1200.00,
                'unit_id' => $pcsUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
            [
                'sku' => 'SNK-CHP-01',
                'name' => 'Flaky Soft Chapati',
                'description' => 'Layered Swahili pan-toasted flatbread made with sunflower oil.',
                'base_price' => 1000.00,
                'standard_cost' => 400.00,
                'unit_id' => $pcsUnit->id,
                'type' => ItemType::PHYSICAL,
            ],
        ];

        $stockQuantities = [
            'FOOD-PIL-01' => 60, // 60 plates
            'FOOD-UGL-01' => 45, // 45 plates
            'FOOD-BRY-01' => 40, // 40 plates
            'FOOD-CHP-01' => 80, // 80 portions
            'FOOD-MSH-01' => 50, // 50 skewers
            'DRK-PAS-01' => 100, // 100 bottles
            'DRK-MNG-01' => 100, // 100 bottles
            'DRK-WTR-01' => 150, // 150 bottles
            'DRK-TEA-01' => 120, // 120 cups
            'SNK-SAM-01' => 90,  // 90 pairs
            'SNK-CHP-01' => 100, // 100 chapati
        ];

        foreach ($menuItems as $itemData) {
            $item = Item::firstOrCreate(
                [
                    'business_unit_id' => $cateringBu->id,
                    'sku' => $itemData['sku'],
                ],
                array_merge($itemData, [
                    'tax_category_id' => $vatCategory->id,
                    'track_inventory' => true,
                    'status' => true,
                ])
            );

            $initialQty = $stockQuantities[$itemData['sku']] ?? 50;
            StockBalance::firstOrCreate(
                [
                    'inventory_location_id' => $restaurantStore->id,
                    'item_id' => $item->id,
                ],
                [
                    'quantity' => $initialQty,
                    'reserved_quantity' => 0,
                ]
            );
        }

        // 7. Test Users for Roles
        $cashierRole = Role::where('name', 'Restaurant Cashier')->first();
        $kitchenRole = Role::where('name', 'Kitchen Staff')->first();
        $managerRole = Role::where('name', 'Restaurant Manager')->first();

        // Cashier user
        $cashierUser = User::firstOrCreate(
            ['email' => 'cashier@tanrail.co.tz'],
            [
                'name' => 'Fatma Juma (Cashier)',
                'password' => Hash::make('password'),
            ]
        );
        if ($cashierRole && ! $cashierUser->roles()->where('role_id', $cashierRole->id)->exists()) {
            $cashierUser->assignRole($cashierRole, Branch::class, $darRestaurant->id);
        }

        // Chef / Kitchen user
        $chefUser = User::firstOrCreate(
            ['email' => 'chef@tanrail.co.tz'],
            [
                'name' => 'Chef Hamisi Bakari',
                'password' => Hash::make('password'),
            ]
        );
        if ($kitchenRole && ! $chefUser->roles()->where('role_id', $kitchenRole->id)->exists()) {
            $chefUser->assignRole($kitchenRole, Branch::class, $darRestaurant->id);
        }

        // Restaurant Manager user
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@tanrail.co.tz'],
            [
                'name' => 'Rashid Mwinyi (Restaurant Mgr)',
                'password' => Hash::make('password'),
            ]
        );
        if ($managerRole && ! $managerUser->roles()->where('role_id', $managerRole->id)->exists()) {
            $managerUser->assignRole($managerRole, Branch::class, $darRestaurant->id);
        }

        // Set Dar Restaurant Manager
        $darRestaurant->update([
            'manager_user_id' => $managerUser->id,
            'manager_name' => $managerUser->name,
        ]);
    }
}
