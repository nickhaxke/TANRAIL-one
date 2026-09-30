<?php

namespace App\Domains\Core\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\PurchaseReceipt;
use App\Domains\Core\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseReceiptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasAccessToScope(BusinessUnit::class);
    }

    public function view(User $user, PurchaseReceipt $receipt)
    {
        return clone $this->viewAny($user);
    }

    public function create(User $user)
    {
        return clone $this->viewAny($user);
    }

    public function update(User $user, PurchaseReceipt $receipt)
    {
        return false; // Append only
    }

    public function delete(User $user, PurchaseReceipt $receipt)
    {
        return false; // Physical deletion is prohibited
    }
}
