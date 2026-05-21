<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $clientes = $query->withCount('pedidos')->latest()->paginate(20)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellido'    => 'nullable|string|max:100',
            'email'       => 'nullable|email|unique:clientes,email|max:150',
            'telefono'    => 'required|string|max:20',
            'telefono_alt'=> 'nullable|string|max:20',
            'direccion'   => 'required|string',
            'referencia'  => 'nullable|string|max:255',
            'distrito'    => 'nullable|string|max:80',
            'ciudad'      => 'nullable|string|max:80',
            'tipo'        => 'required|in:regular,frecuente,vip',
            'notas'       => 'nullable|string',
        ]);

        $cliente = Cliente::create($data);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', "Cliente {$cliente->nombre_completo} registrado exitosamente.");
    }

    public function show(Cliente $cliente)
    {
        $pedidos = $cliente->pedidos()->with(['repartidor', 'items'])->latest()->limit(20)->get();
        return view('clientes.show', compact('cliente', 'pedidos'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'apellido'    => 'nullable|string|max:100',
            'email'       => "nullable|email|unique:clientes,email,{$cliente->id}|max:150",
            'telefono'    => 'required|string|max:20',
            'telefono_alt'=> 'nullable|string|max:20',
            'direccion'   => 'required|string',
            'referencia'  => 'nullable|string|max:255',
            'distrito'    => 'nullable|string|max:80',
            'ciudad'      => 'nullable|string|max:80',
            'tipo'        => 'required|in:regular,frecuente,vip',
            'notas'       => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->pedidos()->whereNotIn('estado', ['entregado', 'cancelado'])->exists()) {
            return back()->with('error', 'No se puede eliminar: el cliente tiene pedidos activos.');
        }
        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
