<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('role.view'), 403);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->withCount('users')
            ->latest()
            ->paginate(20);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('role.create'), 403);

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('role.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $permissionIds = $validated['permissions'] ?? [];
        $role->syncPermissions(Permission::query()->whereIn('id', $permissionIds)->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('role.edit'), 403);

        $role = Role::query()->where('guard_name', 'web')->findOrFail($id);
        $permissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get();
        $rolePermissionIds = $role->permissions()->pluck('id')->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('role.edit'), 403);

        $role = Role::query()->where('guard_name', 'web')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);

        $permissionIds = $validated['permissions'] ?? [];
        $role->syncPermissions(Permission::query()->whereIn('id', $permissionIds)->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('role.delete'), 403);

        $role = Role::query()->where('guard_name', 'web')->findOrFail($id);

        // Prevent deleting super-admin by name? Requirement says no hard-coded logic; allow delete.
        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
