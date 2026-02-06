<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authUser = request()->user();
        abort_if(!$authUser || !$authUser->can('user.view'), 403);

        $query = User::query()->with('roles');

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authUser = request()->user();
        abort_if(!$authUser || !$authUser->can('user.create'), 403);

        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $authUser = $request->user();
        abort_if(!$authUser || !$authUser->can('user.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $roleIds = $validated['roles'] ?? [];
        if (!empty($roleIds)) {
            $roles = Role::query()->whereIn('id', $roleIds)->where('guard_name', 'web')->get();
            $user->syncRoles($roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
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
        $authUser = request()->user();
        abort_if(!$authUser || !$authUser->can('user.edit'), 403);

        $user = User::query()->with('roles')->findOrFail($id);
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();
        $userRoleIds = $user->roles->pluck('id')->all();

        return view('admin.users.edit', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $authUser = $request->user();
        abort_if(!$authUser || !$authUser->can('user.edit'), 403);

        $user = User::query()->with('roles')->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $roleIds = $validated['roles'] ?? [];
        $roles = Role::query()->whereIn('id', $roleIds)->where('guard_name', 'web')->get();
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $authUser = request()->user();
        abort_if(!$authUser || !$authUser->can('user.delete'), 403);

        $user = User::query()->findOrFail($id);

        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
