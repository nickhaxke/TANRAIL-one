<?php

namespace Database\Factories;

use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->company(),
            'contact_details' => $this->faker->address(),
            'tax_number' => $this->faker->numerify('TAX-#######'),
            'status' => true,
        ];
    }
}
