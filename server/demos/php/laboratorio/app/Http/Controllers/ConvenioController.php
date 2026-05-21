<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;

class ConvenioController extends Controller
{
    public function index(Request $request)
    {
        $query = Convenio::withCount('ordenes');

        if ($request->search) {
            $query->where('nombre', 'like', "%{$request->search}%")
                  ->orWhere('ruc', 'like', "%{$request->search}%");
        }

        $convenios = $query->latest()->paginate(10);
        return view('convenios.index', compact('convenios'));
    }

    public function create()
    {
        return view('convenios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'ruc'                   => 'nullable|string|max:20',
            'tipo'                  => 'required|in:Aseguradora,Empresa,Clínica,Municipalidad,Otro',
            'descuento_porcentaje'  => 'required|numeric|min:0|max:100',
            'condiciones'           => 'nullable|string',
            'contacto_nombre'       => 'nullable|string|max:100',
            'contacto_telefono'     => 'nullable|string|max:20',
        ]);

        $validated['activo'] = true;
        Convenio::create($validated);

        return redirect()->route('convenios.index')->with('success', 'Convenio registrado correctamente.');
    }

    public function show(Convenio $convenio)
    {
        $convenio->load(['ordenes.paciente']);
        $totalOrdenes   = $convenio->ordenes()->count();
        $totalFacturado = $convenio->ordenes()->sum('total');
        return view('convenios.show', compact('convenio', 'totalOrdenes', 'totalFacturado'));
    }

    public function edit(Convenio $convenio)
    {
        return view('convenios.edit', compact('convenio'));
    }

    public function update(Request $request, Convenio $convenio)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:150',
            'ruc'                   => 'nullable|string|max:20',
            'tipo'                  => 'required|in:Aseguradora,Empresa,Clínica,Municipalidad,Otro',
            'descuento_porcentaje'  => 'required|numeric|min:0|max:100',
            'condiciones'           => 'nullable|string',
            'contacto_nombre'       => 'nullable|string|max:100',
            'contacto_telefono'     => 'nullable|string|max:20',
        ]);

        $convenio->update($validated);
        return redirect()->route('convenios.index')->with('success', 'Convenio actualizado correctamente.');
    }

    public function destroy(Convenio $convenio)
    {
        $convenio->update(['activo' => !$convenio->activo]);
        $estado = $convenio->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Convenio {$estado} correctamente.");
    }
}
