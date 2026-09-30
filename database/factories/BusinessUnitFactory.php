<?php

namespace Database\Factories;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessUnitFactory extends Factory
{
    protected $model = BusinessUnit::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->companySuffix(),
            'code' => $this->faker->unique()->lexify('BU-???'),
            'status' => true,
        ];
    }
}
