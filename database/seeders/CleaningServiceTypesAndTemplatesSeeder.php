<?php

namespace Database\Seeders;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Modules\Cleaning\Models\CleaningServiceType;
use Illuminate\Database\Seeder;

class CleaningServiceTypesAndTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bu = BusinessUnit::firstOrCreate(
            ['code' => 'CLN'],
            ['name' => 'Cleaning Services', 'organization_id' => 1]
        );

        $types = [
            ['code' => 'facility_cleaning', 'name' => 'Facility Cleaning'],
            ['code' => 'sgr_coach_cleaning', 'name' => 'SGR Coach Cleaning'],
            ['code' => 'fumigation', 'name' => 'Fumigation'],
        ];

        foreach ($types as $typeData) {
            $type = CleaningServiceType::firstOrCreate(
                ['code' => $typeData['code']],
                ['name' => $typeData['name'], 'business_unit_id' => $bu->id, 'is_active' => true]
            );

            // Create initial template if none exists
            if ($type->templates()->count() === 0) {
                $templateName = $typeData['name'].' Standard';
                if ($type->code === 'facility_cleaning') {
                    $templateName = 'Facility Daily Cleaning';
                }
                if ($type->code === 'sgr_coach_cleaning') {
                    $templateName = 'SGR Coach Cleaning';
                }
                if ($type->code === 'fumigation') {
                    $templateName = 'Fumigation Service';
                }

                $template = $type->templates()->create([
                    'name' => $templateName,
                    'version' => 1,
                    'description' => 'Standard template for '.$typeData['name'],
                    'is_active' => true,
                ]);

                // Create some basic generic items
                $template->items()->createMany([
                    ['label' => 'Preparation & Safety Check', 'sort_order' => 10, 'is_required' => true],
                    ['label' => 'Main Execution', 'sort_order' => 20, 'is_required' => true],
                    ['label' => 'Final Inspection', 'sort_order' => 30, 'is_required' => true],
                ]);
            }
        }
    }
}
