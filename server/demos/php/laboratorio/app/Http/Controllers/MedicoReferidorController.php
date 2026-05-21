<?php

namespace App\Http\Controllers;

use App\Models\MedicoReferidor;
use Illuminate\Http\Request;

class MedicoReferidorController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicoReferidor::query();

        if ($request->search) {
            $s = $request->search;
            $query->where('nombres', 'like', "%{$s}%")
                  ->orWhere('apellidos', 'like', "%{$s}%")
                  ->orWhere('cmp', 'like', "%{$s}%")
                  ->orWhere('especialidad', 'like', "%{$s}%");
        }

        $medicos = $query->latest()->paginate(12);
        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        return view('medicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cmp'          => 'nullable|string|max:20|unique:medicos_referidores,cmp',
            'nombres'      => 'required|string|max:100',
            'apellidos'    => 'required|string|max:100',
            'especialidad' => 'nullable|string|max:100',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'institucion'  => 'nullable|string|max:150',
        ]);

        $validated['activo'] = true;
        MedicoReferidor::create($validated);

        return redirect()->route('medicos.index')->with('success', 'Médico referidor registrado correctamente.');
    }

    public function show(MedicoReferidor $medico)
    {
        $medico->load(['ordenes.paciente', 'citas.paciente']);
        $totalOrdenes = $medico->ordenes()->count();
        $totalCitas   = $medico->citas()->count();
        return view('medicos.show', compact('medico', 'totalOrdenes', 'totalCitas'));
    }

    public function edit(MedicoReferidor $medico)
    {
        return view('medicos.edit', compact('medico'));
    }

    public function update(Request $request, MedicoReferidor $medico)
    {
        $validated = $request->validate([
            'cmp'          => 'nullable|string|max:20|unique:medicos_referidores,cmp,' . $medico->id,
            'nombres'      => 'required|string|max:100',
            'apellidos'    => 'required|string|max:100',
            'especialidad' => 'nullable|string|max:100',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'institucion'  => 'nullable|string|max:150',
        ]);

        $medico->update($validated);
        return redirect()->route('medicos.index')->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(MedicoReferidor $medico)
    {
        if ($medico->ordenes()->where('estado', '!=', 'Cancelado')->exists()) {
            $medico->update(['activo' => !$medico->activo]);
            $estado = $medico->activo ? 'activado' : 'desactivado';
            return back()->with('success', "Médico {$estado} correctamente.");
        }

        $medico->delete();
        return redirect()->route('medicos.index')->with('success', 'Médico eliminado del sistema.');
    }
}
