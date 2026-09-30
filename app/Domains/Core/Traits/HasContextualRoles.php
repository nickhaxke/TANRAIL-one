<?php

namespace App\Domains\Core\Traits;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasContextualRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot(['scope_type', 'scope_id'])
            ->withTimestamps();
    }

    public function assignRole(Role $role, string $scopeType, int $scopeId): void
    {
        if (! class_exists($scopeType)) {
            throw new \InvalidArgumentException("Invalid scope type: {$scopeType}");
        }

        try {
            $scopeType::findOrFail($scopeId);
        } catch (ModelNotFoundException $e) {
            throw new \InvalidArgumentException("Invalid scope_id {$scopeId} for scope_type {$scopeType}");
        }

        $this->roles()->attach($role->id, [
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
        ]);
    }

    public function hasAccessToScope(string $scopeType, int $scopeId): bool
    {
        if ($this->roles()->wherePivot('scope_type', $scopeType)->wherePivot('scope_id', $scopeId)->exists()) {
            return true;
        }

        if ($scopeType === BusinessUnit::class) {
            $bu = BusinessUnit::find($scopeId);
            if ($bu && $this->roles()->wherePivot('scope_type', Organization::class)->wherePivot('scope_id', $bu->organization_id)->exists()) {
                return true;
            }
        } elseif ($scopeType === Branch::class) {
            $branch = Branch::with('businessUnit')->find($scopeId);
            if ($branch) {
                if ($this->hasAccessToScope(BusinessUnit::class, $branch->business_unit_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasPermissionTo(string $permissionName, string $scopeType, int $scopeId): bool
    {
        $scopesToCheck = [
            ['type' => $scopeType, 'id' => $scopeId],
        ];

        if ($scopeType === BusinessUnit::class) {
            $bu = BusinessUnit::find($scopeId);
            if ($bu) {
                $scopesToCheck[] = ['type' => Organization::class, 'id' => $bu->organization_id];
            }
        } elseif ($scopeType === Branch::class) {
            $branch = Branch::with('businessUnit')->find($scopeId);
            if ($branch) {
                $scopesToCheck[] = ['type' => BusinessUnit::class, 'id' => $branch->business_unit_id];
                $scopesToCheck[] = ['type' => Organization::class, 'id' => $branch->businessUnit->organization_id];
            }
        }

        foreach ($scopesToCheck as $scope) {
            $roles = $this->roles()
                ->wherePivot('scope_type', $scope['type'])
                ->wherePivot('scope_id', $scope['id'])
                ->with('permissions')
                ->get();

            foreach ($roles as $role) {
                if ($role->permissions->contains('name', $permissionName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
