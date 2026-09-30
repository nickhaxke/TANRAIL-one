<?php

namespace Database\Factories;

use App\Domains\Core\Models\TaxCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxCategoryFactory extends Factory
{
    protected $model = TaxCategory::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('TAX-???'),
            'rate' => $this->faker->randomFloat(2, 0, 20),
            'description' => $this->faker->sentence(),
        ];
    }
}
