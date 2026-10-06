<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Permission;
use App\Domains\Core\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoleController extends Controller
{
    /**
     * Permission module display metadata.
     *
     * @var array<string, array{label: string, icon: string, color: string}>
     */
    private const MODULE_META = [
        'organization' => ['label' => 'Organization & Core', 'icon' => 'building', 'color' => 'blue'],
        'business_units' => ['label' => 'Business Units', 'icon' => 'briefcase', 'color' => 'blue'],
        'branches' => ['label' => 'Branch Management', 'icon' => 'map-pin', 'color' => 'blue'],
        'users' => ['label' => 'Users & Access Control', 'icon' => 'users', 'color' => 'purple'],
        'roles' => ['label' => 'Role Governance', 'icon' => 'shield', 'color' => 'purple'],
        'audit' => ['label' => 'Audit & Compliance', 'icon' => 'clipboard', 'color' => 'purple'],
        'cleaning' => ['label' => 'Cleaning Module', 'icon' => 'sparkles', 'color' => 'teal'],
        'restaurant' => ['label' => 'Restaurant & Catering', 'icon' => 'utensils', 'color' => 'orange'],
        'pos' => ['label' => 'Point of Sale', 'icon' => 'credit-card', 'color' => 'orange'],
        'kitchen' => ['label' => 'Kitchen Operations', 'icon' => 'flame', 'color' => 'orange'],
    ];

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $moduleGroups = $this->getGroupedPermissions();

        return view('management.roles.index', compact('roles', 'moduleGroups'));
    }

    public function create()
    {
        $moduleGroups = $this->getGroupedPermissions();

        return view('management.roles.create', compact('moduleGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('management.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $moduleGroups = $this->getGroupedPermissions();
        $role->load('permissions');

        return view('management.roles.edit', compact('role', 'moduleGroups'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('management.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Simple protection for Super Admin
        if (strtolower($role->name) === 'super admin') {
            return back()->with('error', 'Cannot delete the Super Admin role.');
        }

        $role->delete();

        return redirect()->route('management.roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Group permissions by their module prefix with display metadata.
     *
     * @return array<int, array{key: string, label: string, icon: string, color: string, permissions: Collection}>
     */
    private function getGroupedPermissions(): array
    {
        $permissions = Permission::orderBy('name')->get();

        $grouped = $permissions->groupBy(function (Permission $permission): string {
            return explode('.', $permission->name)[0];
        });

        $result = [];
        foreach ($grouped as $module => $perms) {
            $meta = self::MODULE_META[$module] ?? [
                'label' => ucfirst(str_replace('_', ' ', $module)),
                'icon' => 'grid',
                'color' => 'slate',
            ];

            $result[] = [
                'key' => $module,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'permissions' => $perms,
            ];
        }

        return $result;
    }
}
