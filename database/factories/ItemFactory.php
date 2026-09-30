<?php

namespace Database\Factories;

use App\Domains\Core\Enums\ItemType;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\TaxCategory;
use App\Domains\Core\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'business_unit_id' => BusinessUnit::factory(),
            'sku' => $this->faker->unique()->numerify('SKU-####'),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement([ItemType::PHYSICAL, ItemType::SERVICE, ItemType::PACKAGE]),
            'track_inventory' => true,
            'base_price' => $this->faker->randomFloat(2, 10, 500),
            'tax_category_id' => TaxCategory::factory(),
            'unit_id' => Unit::factory(),
            'status' => true,
        ];
    }
}
