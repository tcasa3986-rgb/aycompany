<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('numero_documento', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
                  ->orWhere('apellido_paterno', 'like', "%{$search}%");
        }

        $pacientes = $query->latest()->paginate(10);
        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento'   => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:pacientes,numero_documento',
            'nombres'          => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:80',
            'apellido_materno' => 'nullable|string|max:80',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'direccion'        => 'nullable|string|max:250',
            'tipo_sangre'      => 'nullable|string|max:5',
        ]);

        // Generar historia clínica
        $latest  = Paciente::latest('id')->first();
        $nextId  = $latest ? $latest->id + 1 : 1;
        $validated['historia_clinica'] = 'HC-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        Paciente::create($validated);

        return redirect()->route('pacientes.index')->with('success', 'Paciente registrado exitosamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['ordenes' => function ($q) {
            $q->with(['detalles.prueba', 'facturas'])->latest('fecha_registro');
        }]);

        $totalOrdenes    = $paciente->ordenes->count();
        $ordenesActivas  = $paciente->ordenes->whereIn('estado', ['Pendiente', 'En proceso'])->count();
        $totalFacturado  = $paciente->ordenes->sum('total');

        return view('pacientes.show', compact('paciente', 'totalOrdenes', 'ordenesActivas', 'totalFacturado'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'tipo_documento'   => 'required|string|max:20',
            'numero_documento' => 'required|string|max:20|unique:pacientes,numero_documento,' . $paciente->id,
            'nombres'          => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:80',
            'apellido_materno' => 'nullable|string|max:80',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'direccion'        => 'nullable|string|max:250',
            'tipo_sangre'      => 'nullable|string|max:5',
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.show', $paciente->id)->with('success', 'Paciente actualizado exitosamente.');
    }

    public function destroy(Paciente $paciente)
    {
        // Verificar si tiene órdenes activas
        $tieneOrdenesActivas = $paciente->ordenes()
            ->whereIn('estado', ['Pendiente', 'En proceso'])
            ->exists();

        if ($tieneOrdenesActivas) {
            return back()->with('error', 'No se puede eliminar el paciente: tiene órdenes activas en proceso.');
        }

        $paciente->delete();

        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado del sistema.');
    }
}
