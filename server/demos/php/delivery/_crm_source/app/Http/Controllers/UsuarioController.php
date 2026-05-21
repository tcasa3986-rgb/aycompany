<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q->where('name', 'like', "%{$b}%")->orWhere('email', 'like', "%{$b}%"));
        }
        if ($request->filled('rol')) {
            $query->role($request->rol);
        }

        $usuarios = $query->latest()->paginate(20)->withQueryString();
        $roles    = Role::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'telefono'  => 'nullable|string|max:20',
            'password'  => 'required|min:8|confirmed',
            'rol'       => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'password' => Hash::make($data['password']),
            'activo'   => true,
        ]);

        $user->assignRole($data['rol']);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$user->name} creado exitosamente.");
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$usuario->id}",
            'telefono'  => 'nullable|string|max:20',
            'password'  => 'nullable|min:8|confirmed',
            'rol'       => 'required|exists:roles,name',
            'activo'    => 'boolean',
        ]);

        $usuario->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'activo'   => $request->boolean('activo'),
        ]);

        if (!empty($data['password'])) {
            $usuario->update(['password' => Hash::make($data['password'])]);
        }

        $usuario->syncRoles([$data['rol']]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->update(['activo' => false]);
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario desactivado.');
    }
}
