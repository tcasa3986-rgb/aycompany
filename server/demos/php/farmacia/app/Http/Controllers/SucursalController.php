<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('sucursales.index', compact('sucursales'));
    }

    public function switch(Sucursal $sucursal)
    {
        $user = Auth::user();
        
        // Verificar si el usuario tiene acceso (o es superadmin)
        if (! $user->hasRole('admin') && ! $user->sucursales->contains($sucursal->id)) {
            return back()->with('error', 'No tienes acceso a esta sucursal.');
        }

        session(['sucursal_id' => $sucursal->id]);
        session(['sucursal_nombre' => $sucursal->nombre]);

        return back()->with('success', "Cambiado a sucursal: {$sucursal->nombre}");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono'  => 'nullable|string|max:20',
        ]);

        Sucursal::create($data);

        return back()->with('success', 'Sucursal creada correctamente.');
    }
}
