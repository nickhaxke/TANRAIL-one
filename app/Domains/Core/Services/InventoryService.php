<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\MovementType;
use App\Domains\Core\Models\InventoryLocation;
use App\Domains\Core\Models\Item;
use App\Domains\Core\Models\StockBalance;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * @throws \Exception
     */
    public function receive(Item $item, InventoryLocation $location, float $quantity, ?string $referenceType = null, ?int $referenceId = null, ?int $userId = null): void
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than zero.');
        }

        $this->validateBusinessUnitIsolation($item, $location);

        DB::transaction(function () use ($item, $location, $quantity, $referenceType, $referenceId, $userId) {
            // Pessimistic lock or create
            $balance = StockBalance::firstOrCreate(
                ['inventory_location_id' => $location->id, 'item_id' => $item->id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            // Re-fetch with lock
            $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();

            $balance->quantity += $quantity;
            $balance->save();

            $balance->item->stockMovements()->create([
                'type' => MovementType::RECEIVE,
                'source_location_id' => null,
                'destination_location_id' => $location->id,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => $userId,
            ]);
        });
    }

    /**
     * @throws \Exception
     */
    public function issue(Item $item, InventoryLocation $location, float $quantity, ?string $referenceType = null, ?int $referenceId = null, ?int $userId = null): void
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than zero.');
        }

        $this->validateBusinessUnitIsolation($item, $location);

        DB::transaction(function () use ($item, $location, $quantity, $referenceType, $referenceId, $userId) {
            $balance = StockBalance::where('inventory_location_id', $location->id)
                ->where('item_id', $item->id)
                ->lockForUpdate()
                ->first();

            if (! $balance) {
                throw new \Exception('Insufficient stock. Balance record does not exist.');
            }

            if ($balance->available_quantity < $quantity) {
                // STRICT REJECTION ENFORCEMENT
                throw new \Exception("Insufficient stock. Available: {$balance->available_quantity}, Requested: {$quantity}");
            }

            $balance->quantity -= $quantity;
            $balance->save();

            $balance->item->stockMovements()->create([
                'type' => MovementType::ISSUE,
                'source_location_id' => $location->id,
                'destination_location_id' => null,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => $userId,
            ]);
        });
    }

    /**
     * @throws \Exception
     */
    public function transfer(Item $item, InventoryLocation $source, InventoryLocation $destination, float $quantity, ?int $userId = null): void
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity must be greater than zero.');
        }

        $this->validateCrossBusinessUnitTransfer($item, $source, $destination);

        DB::transaction(function () use ($item, $source, $destination, $quantity, $userId) {
            // First Issue
            $this->issue($item, $source, $quantity, null, null, $userId);

            // Then Receive
            $this->receive($item, $destination, $quantity, null, null, $userId);
        });
    }

    /**
     * @throws \Exception
     */
    public function adjust(Item $item, InventoryLocation $location, float $difference, string $reason, ?int $userId = null): void
    {
        if ($difference === 0.0) {
            return;
        }

        if (empty(trim($reason))) {
            throw new \Exception('Adjustment reason is required.');
        }

        $this->validateBusinessUnitIsolation($item, $location);

        DB::transaction(function () use ($item, $location, $difference, $reason, $userId) {
            $balance = StockBalance::firstOrCreate(
                ['inventory_location_id' => $location->id, 'item_id' => $item->id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();

            $newQuantity = $balance->quantity + $difference;

            if (($newQuantity - $balance->reserved_quantity) < 0) {
                throw new \Exception('Adjustment would result in negative available stock.');
            }

            $balance->quantity = $newQuantity;
            $balance->save();

            $balance->item->stockMovements()->create([
                'type' => MovementType::ADJUST,
                'source_location_id' => $difference < 0 ? $location->id : null,
                'destination_location_id' => $difference > 0 ? $location->id : null,
                'quantity' => abs($difference),
                'reference_type' => null,
                'reference_id' => null,
                'user_id' => $userId,
                'reason' => $reason,
            ]);
        });
    }

    /**
     * @throws \Exception
     */
    private function validateBusinessUnitIsolation(Item $item, InventoryLocation $location): void
    {
        if ($item->business_unit_id !== $location->branch->business_unit_id) {
            throw new \Exception('Cross-BU inventory relationships are strictly forbidden.');
        }
    }

    /**
     * @throws \Exception
     */
    private function validateCrossBusinessUnitTransfer(Item $item, InventoryLocation $source, InventoryLocation $destination): void
    {
        if ($source->branch->business_unit_id !== $destination->branch->business_unit_id) {
            throw new \Exception('Cross-Business-Unit transfers are strictly forbidden.');
        }

        if ($item->business_unit_id !== $source->branch->business_unit_id) {
            throw new \Exception('Item Business Unit does not match the Transfer Locations.');
        }
    }
}
