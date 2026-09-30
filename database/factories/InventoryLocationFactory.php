<?php

namespace Database\Factories;

use App\Domains\Core\Models\InventoryLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryLocationFactory extends Factory
{
    protected $model = InventoryLocation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->numerify('LOC-####'),
            'status' => 'active',
        ];
    }
}
