<?php

namespace App\Domains\Modules\EventManagement\Policies;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\User;
use App\Domains\Modules\EventManagement\Enums\EventBookingStatus;
use App\Domains\Modules\EventManagement\Models\EventBooking;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventBookingPolicy
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

    public function view(User $user, EventBooking $booking): bool
    {
        return $this->authorizeTenant($user, (int) $booking->organization_id, (int) $booking->business_unit_id) ||
               $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function create(User $user, ?int $businessUnitId = null): bool
    {
        if ($businessUnitId !== null && $businessUnitId > 0) {
            return $user->hasPermissionTo('manage-events', BusinessUnit::class, $businessUnitId);
        }

        return false;
    }

    public function update(User $user, EventBooking $booking): bool
    {
        $status = $booking->status instanceof EventBookingStatus ? $booking->status->value : $booking->status;
        if (in_array($status, [EventBookingStatus::COMPLETED->value, EventBookingStatus::CANCELLED->value])) {
            return false;
        }

        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function quote(User $user, EventBooking $booking): bool
    {
        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function confirm(User $user, EventBooking $booking): bool
    {
        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function start(User $user, EventBooking $booking): bool
    {
        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function complete(User $user, EventBooking $booking): bool
    {
        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function cancel(User $user, EventBooking $booking): bool
    {
        $status = $booking->status instanceof EventBookingStatus ? $booking->status->value : $booking->status;
        if (in_array($status, [EventBookingStatus::COMPLETED->value, EventBookingStatus::CANCELLED->value])) {
            return false;
        }

        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }

    public function delete(User $user, EventBooking $booking): bool
    {
        $status = $booking->status instanceof EventBookingStatus ? $booking->status->value : $booking->status;
        if (in_array($status, [EventBookingStatus::COMPLETED->value, EventBookingStatus::CONFIRMED->value])) {
            return false;
        }

        return $this->authorizeAction($user, 'manage-events', (int) $booking->organization_id, (int) $booking->business_unit_id);
    }
}
