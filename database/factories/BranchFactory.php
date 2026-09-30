<?php

namespace Database\Factories;

use App\Domains\Core\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'business_unit_id' => null,
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->lexify('???-###'),
            'status' => 'active',
        ];
    }
}
