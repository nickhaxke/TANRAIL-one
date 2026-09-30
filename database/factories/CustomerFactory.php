<?php

namespace Database\Factories;

use App\Domains\Core\Enums\CustomerType;
use App\Domains\Core\Models\Customer;
use App\Domains\Core\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'type' => $this->faker->randomElement([CustomerType::INDIVIDUAL, CustomerType::CORPORATE]),
            'status' => true,
        ];
    }
}
