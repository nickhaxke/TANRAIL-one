<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\StockTransfer;
use App\Domains\Core\Models\User;

class StockTransferPolicy
{
    public function viewAny(User $user)
    {
        return true; // Scope handles this
    }

    public function view(User $user, StockTransfer $transfer)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $transfer->item->business_unit_id);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, StockTransfer $transfer)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $transfer->item->business_unit_id);
    }

    public function delete(User $user, StockTransfer $transfer)
    {
        return $user->hasAccessToScope(BusinessUnit::class, $transfer->item->business_unit_id);
    }
}
