<?php

namespace Database\Factories;

use App\Domains\Core\Models\StockBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockBalanceFactory extends Factory
{
    protected $model = StockBalance::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->randomFloat(2, 0, 1000),
            'reserved_quantity' => 0,
        ];
    }
}
