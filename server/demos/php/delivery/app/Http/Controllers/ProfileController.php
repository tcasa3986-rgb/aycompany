<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mostrar formulario de perfil.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualizar datos del perfil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($request->only('name', 'telefono'));
        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Cambiar contraseña del perfil.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password'              => ['required', 'current_password'],
            'new_password'          => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('status', 'password-updated');
    }
}
