<?php

namespace Database\Factories;

use App\Domains\Core\Enums\MovementType;
use App\Domains\Core\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'type' => MovementType::RECEIVE,
            'source_location_id' => null,
            'destination_location_id' => null,
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'reference_type' => null,
            'reference_id' => null,
            'user_id' => null,
            'reason' => null,
        ];
    }
}
