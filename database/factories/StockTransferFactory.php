<?php

namespace Database\Factories;

use App\Domains\Core\Enums\TransferStatus;
use App\Domains\Core\Models\StockTransfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransferFactory extends Factory
{
    protected $model = StockTransfer::class;

    public function definition(): array
    {
        return [
            'requested_qty' => $this->faker->randomFloat(2, 1, 100),
            'approved_qty' => 0,
            'status' => TransferStatus::PENDING,
        ];
    }
}
