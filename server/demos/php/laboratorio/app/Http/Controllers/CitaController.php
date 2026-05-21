<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\MedicoReferidor;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'medico']);

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('paciente', fn($q) => $q->where('nombres', 'like', "%{$s}%")
                ->orWhere('numero_documento', 'like', "%{$s}%"));
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->fecha) {
            $query->whereDate('fecha_hora', $request->fecha);
        }

        $citas = $query->orderBy('fecha_hora')->paginate(12);
        return view('citas.index', compact('citas'));
    }

    public function create()
    {
        $pacientes = Paciente::orderBy('apellido_paterno')->get();
        $medicos   = MedicoReferidor::where('activo', true)->orderBy('apellidos')->get();
        return view('citas.create', compact('pacientes', 'medicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id'   => 'required|exists:pacientes,id',
            'medico_id'     => 'nullable|exists:medicos_referidores,id',
            'fecha_hora'    => 'required|date|after:now',
            'tipo_atencion' => 'required|string|max:80',
            'motivo'        => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $validated['estado'] = 'Programada';
        Cita::create($validated);

        return redirect()->route('citas.index')->with('success', 'Cita programada correctamente.');
    }

    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'medico']);
        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::orderBy('apellido_paterno')->get();
        $medicos   = MedicoReferidor::where('activo', true)->orderBy('apellidos')->get();
        return view('citas.edit', compact('cita', 'pacientes', 'medicos'));
    }

    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'paciente_id'   => 'required|exists:pacientes,id',
            'medico_id'     => 'nullable|exists:medicos_referidores,id',
            'fecha_hora'    => 'required|date',
            'tipo_atencion' => 'required|string|max:80',
            'motivo'        => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        $cita->update($validated);
        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $request->validate(['estado' => 'required|in:Programada,Confirmada,Atendida,Cancelada,No asistió']);
        $cita->update(['estado' => $request->estado]);
        return back()->with('success', "Cita marcada como: {$request->estado}.");
    }

    public function destroy(Cita $cita)
    {
        if ($cita->estado === 'Atendida') {
            return back()->with('error', 'No se puede eliminar una cita ya atendida.');
        }
        $cita->update(['estado' => 'Cancelada']);
        return back()->with('success', 'Cita cancelada correctamente.');
    }
}
