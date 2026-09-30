<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Organization;
use App\Domains\Core\Models\Role;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        if ($roleFilter = $request->input('role_id')) {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('roles.id', $roleFilter);
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('management.users.index', compact('users', 'search', 'roles', 'roleFilter'));
    }

    public function create()
    {
        $roles = Role::all();
        $organizations = Organization::all();
        $businessUnits = BusinessUnit::where('status', true)->get();
        $branches = Branch::with('businessUnit')->where('status', true)->get();

        return view('management.users.create', compact('roles', 'organizations', 'businessUnits', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'scope_type' => 'nullable|string|in:organization,business_unit,branch',
            'scope_id' => 'nullable|integer',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (! empty($validated['role_id']) && ! empty($validated['scope_type']) && ! empty($validated['scope_id'])) {
            $role = Role::find($validated['role_id']);
            $scopeClass = match ($validated['scope_type']) {
                'organization' => Organization::class,
                'business_unit' => BusinessUnit::class,
                'branch' => Branch::class,
                default => null,
            };

            if ($role && $scopeClass) {
                $user->assignRole($role, $scopeClass, (int) $validated['scope_id']);
            }
        }

        return redirect()->route('management.users.index')->with('success', 'Staff member created and assigned successfully.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        $organizations = Organization::all();
        $businessUnits = BusinessUnit::where('status', true)->get();
        $branches = Branch::with('businessUnit')->where('status', true)->get();

        $activeRole = $user->roles->first();

        return view('management.users.edit', compact('user', 'roles', 'organizations', 'businessUnits', 'branches', 'activeRole'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'scope_type' => 'nullable|string|in:organization,business_unit,branch',
            'scope_id' => 'nullable|integer',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (! empty($validated['role_id']) && ! empty($validated['scope_type']) && ! empty($validated['scope_id'])) {
            $role = Role::find($validated['role_id']);
            $scopeClass = match ($validated['scope_type']) {
                'organization' => Organization::class,
                'business_unit' => BusinessUnit::class,
                'branch' => Branch::class,
                default => null,
            };

            if ($role && $scopeClass) {
                $user->roles()->detach();
                $user->assignRole($role, $scopeClass, (int) $validated['scope_id']);
            }
        } elseif ($request->has('clear_role')) {
            $user->roles()->detach();
        }

        return redirect()->route('management.users.index')->with('success', 'Staff profile and role updated successfully.');
    }

    public function destroy(User $user)
    {
        // Don't delete the currently authenticated user
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('management.users.index')->with('success', 'Staff account deleted successfully.');
    }
}
