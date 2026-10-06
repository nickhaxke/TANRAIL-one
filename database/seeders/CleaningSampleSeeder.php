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
use App\Domains\Core\Models\Unit;
use App\Domains\Core\Models\User;
use App\Domains\Modules\Cleaning\Models\CleaningWorker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleaningSampleSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::where('code', 'TANRAIL')->first();

        // 1. Cleaning Services Business Unit
        $cleaningBu = BusinessUnit::firstOrCreate(
            ['code' => 'BU-CLN-01'],
            [
                'organization_id' => $org->id,
                'name' => 'TANRAIL Cleaning Services',
                'category' => 'Facilities Management',
                'status' => true,
                'description' => 'Station and train cleaning operations.',
            ]
        );

        // 2. Branches (Stations)
        $morogoroStation = Branch::firstOrCreate(
            ['code' => 'BR-MORO-CLN'],
            [
                'business_unit_id' => $cleaningBu->id,
                'name' => 'Morogoro Station (Cleaning)',
                'facility_type' => 'Cleaning Operation',
                'city' => 'Morogoro',
                'status' => true,
                'address' => 'SGR Morogoro Station',
            ]
        );

        $dodomaStation = Branch::firstOrCreate(
            ['code' => 'BR-DOM-CLN'],
            [
                'business_unit_id' => $cleaningBu->id,
                'name' => 'Dodoma Station (Cleaning)',
                'facility_type' => 'Cleaning Operation',
                'city' => 'Dodoma',
                'status' => true,
                'address' => 'SGR Dodoma Station',
            ]
        );

        // 3. Central Cleaning Depot & Central Cleaning Store (ONLY ONE STORE)
        $centralDepot = Branch::firstOrCreate(
            ['code' => 'BR-CLN-DEPOT'],
            [
                'business_unit_id' => $cleaningBu->id,
                'name' => 'Central Cleaning Depot',
                'facility_type' => 'Logistics Depot',
                'city' => 'Dar es Salaam',
                'status' => true,
                'address' => 'SGR Central Station Yard, Gerezani',
            ]
        );

        $centralStore = InventoryLocation::firstOrCreate(
            ['code' => 'LOC-CLN-CENTRAL'],
            [
                'branch_id' => $centralDepot->id,
                'name' => 'Central Cleaning Store',
                'status' => true,
            ]
        );

        // 4. Units
        $pcsUnit = Unit::firstOrCreate(['code' => 'PCS'], ['name' => 'Piece / Item']);
        $literUnit = Unit::firstOrCreate(['code' => 'LTR'], ['name' => 'Liter']);
        $kgUnit = Unit::firstOrCreate(['code' => 'KG'], ['name' => 'Kilogram']);

        // 5. Items
        $items = [
            ['sku' => 'CLN-DET-01', 'name' => 'Industrial Detergent (5L)', 'price' => 20000, 'cost' => 15000, 'unit' => $literUnit->id],
            ['sku' => 'CLN-MOP-01', 'name' => 'Heavy Duty Mop', 'price' => 15000, 'cost' => 10000, 'unit' => $pcsUnit->id],
            ['sku' => 'CLN-GLV-01', 'name' => 'Rubber Gloves (Pair)', 'price' => 5000, 'cost' => 3000, 'unit' => $pcsUnit->id],
            ['sku' => 'CLN-BRS-01', 'name' => 'Scrubbing Brush', 'price' => 8000, 'cost' => 5000, 'unit' => $pcsUnit->id],
            ['sku' => 'CLN-BMT-01', 'name' => 'Broom with Stick', 'price' => 12000, 'cost' => 8000, 'unit' => $pcsUnit->id],
            ['sku' => 'CLN-BLE-01', 'name' => 'Bleach (Jik) 1L', 'price' => 6000, 'cost' => 4500, 'unit' => $literUnit->id],
        ];

        foreach ($items as $itemData) {
            $item = Item::firstOrCreate(
                [
                    'business_unit_id' => $cleaningBu->id,
                    'sku' => $itemData['sku'],
                ],
                [
                    'name' => $itemData['name'],
                    'base_price' => $itemData['price'],
                    'standard_cost' => $itemData['cost'],
                    'unit_id' => $itemData['unit'],
                    'type' => ItemType::PHYSICAL,
                    'track_inventory' => true,
                    'status' => true,
                ]
            );

            // Seed stock exclusively in the Central Cleaning Store
            StockBalance::firstOrCreate(
                ['inventory_location_id' => $centralStore->id, 'item_id' => $item->id],
                ['quantity' => rand(50, 200), 'reserved_quantity' => 0]
            );
        }

        // 6. Roles & Users
        $supervisorRole = Role::where('name', 'Station Supervisor')->first();
        $managerRole = Role::where('name', 'Cleaning Manager')->first();
        $coordinatorRole = Role::where('name', 'Cleaning Manager')->first();
        $storeKeeperRole = Role::where('name', 'Store Keeper')->first();

        // Supervisor - Morogoro
        $moroSupervisor = User::firstOrCreate(
            ['email' => 'supervisor.moro@tanrail.co.tz'],
            [
                'name' => 'Amani Zuberi (Moro Supervisor)',
                'password' => Hash::make('password'),
            ]
        );
        if ($supervisorRole && ! $moroSupervisor->roles()->where('role_id', $supervisorRole->id)->exists()) {
            $moroSupervisor->assignRole($supervisorRole, Branch::class, $morogoroStation->id);
        }

        // Supervisor - Dodoma
        $domSupervisor = User::firstOrCreate(
            ['email' => 'supervisor.dom@tanrail.co.tz'],
            [
                'name' => 'Rehema Said (Dom Supervisor)',
                'password' => Hash::make('password'),
            ]
        );
        if ($supervisorRole && ! $domSupervisor->roles()->where('role_id', $supervisorRole->id)->exists()) {
            $domSupervisor->assignRole($supervisorRole, Branch::class, $dodomaStation->id);
        }

        // Cleaning Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager.cleaning@tanrail.co.tz'],
            [
                'name' => 'Neema Mushi (Cleaning Manager)',
                'password' => Hash::make('password'),
            ]
        );
        if ($managerRole && ! $manager->roles()->where('role_id', $managerRole->id)->exists()) {
            $manager->assignRole($managerRole, BusinessUnit::class, $cleaningBu->id);
        }
        if ($coordinatorRole && ! $manager->roles()->where('role_id', $coordinatorRole->id)->exists()) {
            $manager->assignRole($coordinatorRole, BusinessUnit::class, $cleaningBu->id);
        }

        // Store Keeper - Central Cleaning Store
        $storeKeeper = User::firstOrCreate(
            ['email' => 'storekeeper.cleaning@tanrail.co.tz'],
            [
                'name' => 'Baraka Mtambo (Cleaning Store Keeper)',
                'password' => Hash::make('password'),
            ]
        );
        if ($storeKeeperRole && ! $storeKeeper->roles()->where('role_id', $storeKeeperRole->id)->exists()) {
            $storeKeeper->assignRole($storeKeeperRole, Branch::class, $centralDepot->id);
        }

        // 7. Cleaning Workers & Branch Assignments
        $workerData = [
            ['name' => 'Juma Ali', 'branch' => $morogoroStation, 'supervisor' => $moroSupervisor],
            ['name' => 'Salma Kassim', 'branch' => $morogoroStation, 'supervisor' => $moroSupervisor],
            ['name' => 'Kibwana Yusuph', 'branch' => $morogoroStation, 'supervisor' => $moroSupervisor],
            ['name' => 'Zawadi Nuru', 'branch' => $dodomaStation, 'supervisor' => $domSupervisor],
            ['name' => 'Baraka John', 'branch' => $dodomaStation, 'supervisor' => $domSupervisor],
        ];

        foreach ($workerData as $w) {
            $worker = CleaningWorker::firstOrCreate(
                ['worker_id' => 'CW-'.strtoupper(substr(md5($w['name']), 0, 6))],
                [
                    'business_unit_id' => $cleaningBu->id,
                    'first_name' => explode(' ', $w['name'])[0],
                    'last_name' => explode(' ', $w['name'])[1],
                    'phone_number' => '07'.rand(10000000, 99999999),
                    'id_number' => 'ID'.rand(10000, 99999),
                    'is_active' => true,
                ]
            );

            if (! $worker->current_branch_id) {
                $worker->assignTo($w['branch'], $w['supervisor'], $manager, 'Initial station operational deployment');
            }
        }
    }
}
