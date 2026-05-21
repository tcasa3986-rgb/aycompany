<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $clientes = Cliente::query()
            ->when($q, fn($qry) => $qry->where(function ($w) use ($q) {
                $w->where('nombres', 'like', "%$q%")
                  ->orWhere('apellidos', 'like', "%$q%")
                  ->orWhere('documento', 'like', "%$q%")
                  ->orWhere('telefono', 'like', "%$q%");
            }))
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'q'));
    }

    public function create()
    {
        return view('clientes.create', ['cliente' => new Cliente()]);
    }

    public function store(Request $request)
    {
        Cliente::create($this->validateData($request));
        return redirect()->route('clientes.index')->with('success', 'Cliente creado.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validateData($request, $cliente->id));
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'documento'             => ['nullable', 'string', 'max:20', "unique:clientes,documento,{$id}"],
            'nombres'               => ['required', 'string', 'max:120'],
            'apellidos'             => ['nullable', 'string', 'max:120'],
            'telefono'              => ['nullable', 'string', 'max:30'],
            'email'                 => ['nullable', 'email', 'max:120'],
            'direccion'             => ['nullable', 'string', 'max:255'],
            'fecha_nacimiento'      => ['nullable', 'date'],
            'genero'                => ['nullable', 'in:M,F,O'],
            'alergias'              => ['nullable', 'string', 'max:500'],
            'enfermedades_cronicas' => ['nullable', 'string', 'max:500'],
            'puntos_fidelidad'      => ['nullable', 'integer', 'min:0'],
            'activo'                => ['nullable', 'boolean'],
        ]);
    }
}
