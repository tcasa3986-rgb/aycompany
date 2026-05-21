<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $query = User::with(['roles', 'sucursal']);

        // Filtro por rol (usando scope de Spatie)
        if ($request->role) {
            $query->role($request->role);
        }

        // Búsqueda por nombre o email
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $roles = Role::all();
        $sucursales = Sucursal::where('estado', 'Activo')->get();

        return view('admin.users.create', compact('roles', 'sucursales'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'], // Validamos ID
            'id_sucursal' => ['nullable', 'exists:sucursales,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Remove role from array before creating user
        $roleId = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);

        // Assign role via Spatie
        $role = Role::findById($roleId);
        $user->assignRole($role);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $user->load(['roles', 'sucursal']);

        $stats = [
            'equipos_asignados' => 0,
            'ultimo_acceso' => $user->updated_at,
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $roles = Role::all();
        $sucursales = Sucursal::where('estado', 'Activo')->get();

        return view('admin.users.edit', compact('user', 'roles', 'sucursales'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,id'],
            'id_sucursal' => ['nullable', 'exists:sucursales,id'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $roleId = $validated['role'];
        unset($validated['role']);

        $user->update($validated);

        // Sync role
        $role = Role::findById($roleId);
        $user->syncRoles($role);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acceso denegado');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes desactivar tu propio usuario.');
        }

        $user->update(['activo' => !$user->activo]);

        $status = $user->activo ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario $status exitosamente.");
    }
}
