<?php

namespace Database\Factories;

use App\Domains\Core\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->lexify('ORG-???'),
            'status' => true,
        ];
    }
}
