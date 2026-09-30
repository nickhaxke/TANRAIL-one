<?php

namespace Database\Factories;

use App\Domains\Core\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('??'),
            'name' => $this->faker->word(),
        ];
    }
}
