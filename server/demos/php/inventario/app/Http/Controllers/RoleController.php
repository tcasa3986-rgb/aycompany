<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('roles.view')) {
            abort(403, 'Acceso denegado');
        }

        $roles = Role::withCount('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Acceso denegado');
        }

        $permissions = Permission::all();
        // Group permissions by module (prefix before .)
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('roles.create')) {
            abort(403, 'Acceso denegado');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Role $role)
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403, 'Acceso denegado');
        }

        $permissions = Permission::all();
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403, 'Acceso denegado');
        }

        // Prevent editing Administrador role name
        if ($role->name === 'Administrador') {
            $validated = $request->validate([
                'permissions' => ['array'],
                'permissions.*' => ['exists:permissions,name'],
            ]);
        } else {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
                'permissions' => ['array'],
                'permissions.*' => ['exists:permissions,name'],
            ]);
            $role->name = $validated['name'];
        }

        $role->save();

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]); // Remove all if none selected
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->can('roles.delete')) {
            abort(403, 'Acceso denegado');
        }

        if ($role->name === 'Administrador') {
            return redirect()->route('admin.roles.index')->with('error', 'No puedes eliminar el rol de Administrador.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'No puedes eliminar un rol que tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}
