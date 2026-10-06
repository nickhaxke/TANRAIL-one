<?php

namespace Database\Factories\Domains\Modules\Cleaning\Models;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Modules\Cleaning\Models\CleaningWorker;
use Illuminate\Database\Eloquent\Factories\Factory;

class CleaningWorkerFactory extends Factory
{
    protected $model = CleaningWorker::class;

    public function definition(): array
    {
        return [
            'business_unit_id' => BusinessUnit::factory(),
            'worker_id' => 'W-'.$this->faker->unique()->randomNumber(5),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'id_number' => $this->faker->unique()->numerify('ID######'),
            'is_active' => true,
        ];
    }
}
