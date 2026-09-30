<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Permission;
use App\Domains\Core\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return view('management.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        return view('management.roles.create', compact('permissions'));
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
        $permissions = Permission::all();
        $role->load('permissions');

        return view('management.roles.edit', compact('role', 'permissions'));
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
}
