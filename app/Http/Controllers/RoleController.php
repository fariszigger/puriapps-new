<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        return redirect()->route('users.index', [], 302, ['fragment' => 'roles']);
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            if (str_ends_with($permission->name, 'performance reports')) {
                return 'laporan kinerja';
            }
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? end($parts) : $permission->name;
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions(Permission::whereIn('id', $validated['permissions'])->get());
        }

        return redirect(route('users.index') . '#roles')->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            if (str_ends_with($permission->name, 'performance reports')) {
                return 'laporan kinerja';
            }
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? end($parts) : $permission->name;
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        $permissionModels = !empty($validated['permissions'])
            ? Permission::whereIn('id', $validated['permissions'])->get()
            : collect();

        $role->syncPermissions($permissionModels);

        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect(route('users.index') . '#roles')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        // Safety: don't delete roles that have users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus role yang masih memiliki user.');
        }

        $role->delete();

        return redirect(route('users.index') . '#roles')->with('success', 'Role berhasil dihapus.');
    }
}
