<?php

namespace App\Domains\Modules\Production\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Modules\Production\Enums\ProductionOrderStatus;
use App\Domains\Modules\Production\Models\ProductionOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductionOrderPolicy
{
    use HandlesAuthorization;

    protected function authorizeTenant(User $user, int $organizationId, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasAccessToScope(BusinessUnit::class, $businessUnitId);
        }

        return $user->hasAccessToScope(Organization::class, $organizationId);
    }

    protected function authorizeAction(User $user, string $permission, int $organizationId, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasPermissionTo($permission, BusinessUnit::class, $businessUnitId);
        }

        return $user->hasPermissionTo($permission, Organization::class, $organizationId);
    }

    public function viewAny(User $user): bool
    {
        return $user->roles()->exists();
    }

    public function view(User $user, ProductionOrder $order): bool
    {
        return $this->authorizeTenant($user, (int) $order->organization_id, (int) $order->business_unit_id) ||
               $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasPermissionTo('manage-production', BusinessUnit::class, $businessUnitId);
        }

        return false;
    }

    public function update(User $user, ProductionOrder $order): bool
    {
        $status = $order->status instanceof ProductionOrderStatus ? $order->status->value : $order->status;
        if (in_array($status, [ProductionOrderStatus::COMPLETED->value, ProductionOrderStatus::CANCELLED->value])) {
            return false;
        }

        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function confirm(User $user, ProductionOrder $order): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function start(User $user, ProductionOrder $order): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function complete(User $user, ProductionOrder $order): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function cancel(User $user, ProductionOrder $order): bool
    {
        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }

    public function delete(User $user, ProductionOrder $order): bool
    {
        $status = $order->status instanceof ProductionOrderStatus ? $order->status->value : $order->status;
        if ($status === ProductionOrderStatus::COMPLETED->value) {
            return false;
        }

        return $this->authorizeAction($user, 'manage-production', (int) $order->organization_id, (int) $order->business_unit_id);
    }
}
