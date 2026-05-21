<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Repartidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'device'   => 'nullable|string|max:60',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['Credenciales inválidas.']]);
        }
        if (property_exists($user, 'activo') && !$user->activo) {
            throw ValidationException::withMessages(['email' => ['Usuario inactivo.']]);
        }

        $token = $user->createToken($request->device ?? 'mobile')->plainTextToken;
        $repartidor = Repartidor::where('user_id', $user->id)->first();

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
            'repartidor' => $repartidor ? [
                'id'       => $repartidor->id,
                'nombre'   => $repartidor->nombre,
                'estado'   => $repartidor->estado,
                'telefono' => $repartidor->telefono,
            ] : null,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user'  => $request->user()->load('roles'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }
}
