<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $proveedores = Proveedor::query()
            ->withCount('productos')
            ->when($q, fn($qry) => $qry->where(function ($w) use ($q) {
                $w->where('razon_social', 'like', "%$q%")
                  ->orWhere('ruc', 'like', "%$q%")
                  ->orWhere('contacto', 'like', "%$q%");
            }))
            ->orderBy('razon_social')
            ->paginate(15)
            ->withQueryString();
        return view('proveedores.index', compact('proveedores', 'q'));
    }

    public function create()
    {
        return view('proveedores.create', ['proveedor' => new Proveedor()]);
    }

    public function store(Request $request)
    {
        Proveedor::create($this->validateData($request));
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $proveedor->update($this->validateData($request, $proveedor->id));
        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'ruc'          => ['nullable', 'string', 'max:20', "unique:proveedores,ruc,{$id}"],
            'razon_social' => ['required', 'string', 'max:200'],
            'contacto'     => ['nullable', 'string', 'max:120'],
            'telefono'     => ['nullable', 'string', 'max:30'],
            'email'        => ['nullable', 'email', 'max:120'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'activo'       => ['nullable', 'boolean'],
        ]);
    }
}
